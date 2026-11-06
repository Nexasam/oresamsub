package com.oresamsub.gatewaymodule

import android.accessibilityservice.AccessibilityService
import android.os.Bundle
import android.os.Handler
import android.os.Looper
import android.view.accessibility.AccessibilityEvent
import android.view.accessibility.AccessibilityNodeInfo

class UssdAccessibilityService : AccessibilityService() {
  private val handler = Handler(Looper.getMainLooper())
  private var candidateSignature: String? = null
  private var candidateGeneration = 0
  private var lastSubmittedSignature: String? = null
  private var observedRunMarker = 0L
  private var timeoutGeneration = 0

  override fun onAccessibilityEvent(event: AccessibilityEvent?) {
    val state = GatewayState.state(this)
    if (state !in ACTIVE_STATES) return

    val status = GatewayState.get(this)
    val step = status["step"] as Int
    val updatedAt = status["updatedAt"] as Long

    if (state == "starting" && step == 0 && updatedAt != observedRunMarker) {
      resetForRun(updatedAt)
    }

    inspectCurrentPrompt()
  }

  override fun onInterrupt() {
    invalidatePendingWork()
    if (GatewayState.state(this) in ACTIVE_STATES) {
      if (GatewayState.pendingJob(this) != null) {
        GatewayState.saveResult(this, "device_error", "Android interrupted the accessibility service during the USSD session.")
      } else {
        GatewayState.set(this, "interrupted", GatewayState.step(this), "Android interrupted the accessibility service.")
      }
    }
  }

  override fun onDestroy() {
    invalidatePendingWork()
    super.onDestroy()
  }

  private fun resetForRun(runMarker: Long) {
    observedRunMarker = runMarker
    candidateSignature = null
    lastSubmittedSignature = null
    candidateGeneration++
    timeoutGeneration++
  }

  private fun inspectCurrentPrompt() {
    val root = rootInActiveWindow ?: return
    val signature = promptSignature(root) ?: return

    if (signature == lastSubmittedSignature) return
    if (signature == candidateSignature) return

    candidateSignature = signature
    val generation = ++candidateGeneration

    handler.postDelayed({
      if (generation != candidateGeneration) return@postDelayed
      processStablePrompt(signature)
    }, PROMPT_STABILITY_MS)
  }

  private fun processStablePrompt(expectedSignature: String) {
    if (GatewayState.state(this) !in ACTIVE_STATES) return

    val root = rootInActiveWindow ?: return
    val currentSignature = promptSignature(root) ?: return
    if (currentSignature != expectedSignature || currentSignature == lastSubmittedSignature) {
      inspectCurrentPrompt()
      return
    }

    val step = GatewayState.step(this)
    val replies = GatewayState.replies(this)
    val pendingJob = GatewayState.pendingJob(this)

    // Some MTN menus show a failure and an alternative-plan selector instead of
    // closing the dialog. Treat that as the terminal response; never guess a
    // menu choice or accidentally purchase an alternative plan.
    if (pendingJob != null) {
      val visible = visibleText(root)
      if (FAILURE_RESPONSE.containsMatchIn(visible)) {
        // Dismiss MTN's alternative-plan dialog. Never choose an alternative
        // bundle automatically; canceling leaves the failed attempt auditable.
        findCancel(root)?.performAction(AccessibilityNodeInfo.ACTION_CLICK)
        invalidatePendingWork()
        GatewayState.saveResult(this, "response_captured", visible)
        return
      }
    }

    if (GatewayState.state(this) == "awaiting_result" && pendingJob != null) {
      val response = visibleText(root)
      if (response.isNotBlank()) {
        invalidatePendingWork()
        GatewayState.saveResult(this, "response_captured", response)
      }
      return
    }

    if (step >= replies.size) {
      invalidatePendingWork()
      GatewayState.set(
        this,
        "recipient_required",
        step,
        "MTN displayed the next prompt after the fixed menu sequence. Test stopped safely before entering a recipient number."
      )
      return
    }

    val input = findEditable(root) ?: return
    val submit = findSubmit(root) ?: return
    val reply = replies[step]
    val arguments = Bundle().apply {
      putCharSequence(AccessibilityNodeInfo.ACTION_ARGUMENT_SET_TEXT_CHARSEQUENCE, reply)
    }

    if (!input.performAction(AccessibilityNodeInfo.ACTION_SET_TEXT, arguments)) {
      invalidatePendingWork()
      GatewayState.set(this, "unsupported", step, "Tecno USSD input was found but could not be filled at step ${step + 1}.")
      return
    }

    if (!submit.performAction(AccessibilityNodeInfo.ACTION_CLICK)) {
      invalidatePendingWork()
      GatewayState.set(this, "unsupported", step, "Reply $reply was filled, but the USSD send button could not be pressed.")
      return
    }

    lastSubmittedSignature = currentSignature
    candidateSignature = null
    candidateGeneration++

    val nextStep = step + 1
    val nextState = when {
      pendingJob != null && nextStep == replies.size -> "awaiting_result"
      pendingJob == null && nextStep == GatewayState.fixedReplies().size -> "awaiting_recipient_prompt"
      else -> "running"
    }
    GatewayState.set(
      this,
      nextState,
      nextStep,
      if (nextState == "awaiting_result") "Recipient confirmed. Waiting for MTN's final response." else "Submitted the next reply. Waiting for MTN to display a different menu."
    )
    armResponseTimeout(nextStep)
  }

  private fun armResponseTimeout(expectedStep: Int) {
    val generation = ++timeoutGeneration
    handler.postDelayed({
      if (generation != timeoutGeneration) return@postDelayed
      if (GatewayState.state(this) !in ACTIVE_STATES) return@postDelayed
      if (GatewayState.step(this) != expectedStep) return@postDelayed

      candidateGeneration++
      if (GatewayState.pendingJob(this) != null) {
        GatewayState.saveResult(
          this,
          "session_ended",
          "No final USSD response became visible within ${RESPONSE_TIMEOUT_MS / 1000} seconds after the last submitted step."
        )
      } else {
        GatewayState.set(
          this,
          "response_timeout",
          expectedStep,
          "MTN did not display a new USSD menu within ${RESPONSE_TIMEOUT_MS / 1000} seconds. Nothing else was submitted."
        )
      }
    }, RESPONSE_TIMEOUT_MS)
  }

  private fun invalidatePendingWork() {
    candidateGeneration++
    timeoutGeneration++
    candidateSignature = null
  }

  private fun promptSignature(node: AccessibilityNodeInfo): String? {
    val values = mutableListOf<String>()
    collectPromptText(node, values)
    val normalized = values
      .map { it.trim().replace(Regex("\\s+"), " ").lowercase() }
      .filter { it.isNotBlank() && it !in SUBMIT_LABELS }
      .joinToString("|")

    return normalized.takeIf { it.isNotBlank() }?.hashCode()?.toString()
  }

  private fun collectPromptText(node: AccessibilityNodeInfo, values: MutableList<String>) {
    if (!node.isEditable) {
      node.text?.toString()?.let(values::add)
      node.contentDescription?.toString()?.let(values::add)
    }
    for (index in 0 until node.childCount) {
      collectPromptText(node.getChild(index) ?: continue, values)
    }
  }

  private fun visibleText(node: AccessibilityNodeInfo): String {
    val values = mutableListOf<String>()
    collectPromptText(node, values)
    return values
      .map { it.trim().replace(Regex("\\s+"), " ") }
      .filter { it.isNotBlank() && it.lowercase() !in SUBMIT_LABELS }
      .distinct()
      .joinToString("\n")
      .take(4000)
  }

  private fun findEditable(node: AccessibilityNodeInfo): AccessibilityNodeInfo? {
    if (node.isEditable || node.className?.toString()?.contains("EditText") == true) return node
    for (index in 0 until node.childCount) {
      findEditable(node.getChild(index) ?: continue)?.let { return it }
    }
    return null
  }

  private fun findSubmit(node: AccessibilityNodeInfo): AccessibilityNodeInfo? {
    val label = listOf(node.text, node.contentDescription)
      .filterNotNull()
      .joinToString(" ")
      .trim()
      .lowercase()
    if (node.isClickable && SUBMIT_LABELS.any { label == it || label.contains(it) }) {
      return node
    }
    for (index in 0 until node.childCount) {
      findSubmit(node.getChild(index) ?: continue)?.let { return it }
    }
    return null
  }

  private fun findCancel(node: AccessibilityNodeInfo): AccessibilityNodeInfo? {
    val label = listOf(node.text, node.contentDescription)
      .filterNotNull()
      .joinToString(" ")
      .trim()
      .lowercase()
    if (node.isClickable && CANCEL_LABELS.any { label == it || label.contains(it) }) return node
    for (index in 0 until node.childCount) {
      findCancel(node.getChild(index) ?: continue)?.let { return it }
    }
    return null
  }

  companion object {
    private val ACTIVE_STATES = setOf("starting", "running", "awaiting_recipient_prompt", "awaiting_result")
    private val SUBMIT_LABELS = setOf("send", "ok", "next", "reply", "continue")
    private val CANCEL_LABELS = setOf("cancel", "dismiss", "close")
    private val FAILURE_RESPONSE = Regex("insufficient balance|failed due to|not enough balance|unable to complete", RegexOption.IGNORE_CASE)
    private const val PROMPT_STABILITY_MS = 900L
    private const val RESPONSE_TIMEOUT_MS = 30_000L
  }
}
