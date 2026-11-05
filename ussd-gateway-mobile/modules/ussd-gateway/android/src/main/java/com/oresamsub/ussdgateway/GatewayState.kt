package com.oresamsub.gatewaymodule

import android.content.Context
import java.util.UUID

internal object GatewayState {
  private const val PREFS = "oresamsub_gateway"
  private val fixedReplies = listOf("3", "1", "99", "13", "2", "3")

  fun fixedReplies(): List<String> = fixedReplies

  fun replies(context: Context): List<String> {
    val recipient = prefs(context).getString("recipient_number", "") ?: ""
    return if (recipient.isBlank()) fixedReplies else fixedReplies + listOf(recipient, recipient)
  }

  fun set(context: Context, state: String, step: Int, message: String) {
    prefs(context).edit()
      .putString("state", state)
      .putInt("step", step)
      .putString("message", message.take(500))
      .putLong("updated_at", System.currentTimeMillis())
      .apply()
  }

  fun get(context: Context): Map<String, Any> {
    val prefs = prefs(context)
    return mapOf(
      "state" to (prefs.getString("state", "idle") ?: "idle"),
      "step" to prefs.getInt("step", 0),
      "message" to (prefs.getString("message", "Ready.") ?: ""),
      "updatedAt" to prefs.getLong("updated_at", 0L),
      "hasPendingJob" to prefs.getBoolean("job_active", false),
      "hasPendingResult" to prefs.getBoolean("result_ready", false)
    )
  }

  fun state(context: Context): String = get(context)["state"] as String
  fun step(context: Context): Int = get(context)["step"] as Int

  fun installationId(context: Context): String {
    val existing = prefs(context).getString("installation_id", null)
    if (!existing.isNullOrBlank()) return existing
    return UUID.randomUUID().toString().also {
      prefs(context).edit().putString("installation_id", it).apply()
    }
  }

  fun saveConfiguration(context: Context, baseUrl: String, token: String) {
    prefs(context).edit()
      .putString("base_url", baseUrl.trim().trimEnd('/'))
      .putString("gateway_token", token.trim())
      .apply()
  }

  fun configuration(context: Context): Map<String, Any> = mapOf(
    "baseUrl" to (prefs(context).getString("base_url", "https://oresamsub.com/api/ussd-gateway/v1") ?: ""),
    "token" to (prefs(context).getString("gateway_token", "") ?: ""),
    "installationId" to installationId(context)
  )

  fun prepareJob(
    context: Context,
    attemptId: String,
    recipient: String,
    dispatchToken: String,
    clientResultId: String,
    simSlot: Int,
    subscriptionId: Int
  ) {
    if (prefs(context).getBoolean("job_active", false)) {
      error("A previous job is still stored on this phone. Upload its result first.")
    }

    prefs(context).edit()
      .putBoolean("job_active", true)
      .putBoolean("result_ready", false)
      .putString("attempt_id", attemptId)
      .putString("recipient_number", recipient)
      .putString("dispatch_token", dispatchToken)
      .putString("client_result_id", clientResultId)
      .putInt("sim_slot", simSlot)
      .putInt("subscription_id", subscriptionId)
      .remove("result_outcome")
      .remove("result_text")
      .apply()
    set(context, "prepared", 0, "Recipient stored locally. Waiting for dispatch acknowledgement.")
  }

  fun pendingJob(context: Context): Map<String, Any?>? {
    val prefs = prefs(context)
    if (!prefs.getBoolean("job_active", false)) return null
    return mapOf(
      "attemptId" to prefs.getString("attempt_id", ""),
      "recipientNumber" to prefs.getString("recipient_number", ""),
      "dispatchToken" to prefs.getString("dispatch_token", ""),
      "clientResultId" to prefs.getString("client_result_id", ""),
      "simSlot" to prefs.getInt("sim_slot", -1),
      "subscriptionId" to prefs.getInt("subscription_id", -1),
      "resultReady" to prefs.getBoolean("result_ready", false),
      "outcome" to prefs.getString("result_outcome", null),
      "responseText" to prefs.getString("result_text", null)
    )
  }

  fun saveResult(context: Context, outcome: String, responseText: String?) {
    if (!prefs(context).getBoolean("job_active", false)) return
    prefs(context).edit()
      .putBoolean("result_ready", true)
      .putString("result_outcome", outcome)
      .putString("result_text", responseText?.take(4000))
      .apply()
    set(context, "result_ready", step(context), "USSD result stored safely. Connect to the server to upload it.")
  }

  fun clearJob(context: Context) {
    prefs(context).edit()
      .remove("job_active")
      .remove("result_ready")
      .remove("attempt_id")
      .remove("recipient_number")
      .remove("dispatch_token")
      .remove("client_result_id")
      .remove("sim_slot")
      .remove("subscription_id")
      .remove("result_outcome")
      .remove("result_text")
      .apply()
    set(context, "idle", 0, "Result acknowledged by the server. Ready for the next recipient.")
  }

  private fun prefs(context: Context) = context.getSharedPreferences(PREFS, Context.MODE_PRIVATE)
}
