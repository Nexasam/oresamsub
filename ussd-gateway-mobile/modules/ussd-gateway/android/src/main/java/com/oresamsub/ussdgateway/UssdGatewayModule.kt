package com.oresamsub.gatewaymodule

import android.Manifest
import android.content.Context
import android.content.Intent
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.provider.Settings
import android.telecom.TelecomManager
import android.telephony.SubscriptionManager
import android.text.TextUtils
import androidx.core.content.ContextCompat
import expo.modules.kotlin.modules.Module
import expo.modules.kotlin.modules.ModuleDefinition
import java.util.UUID

class UssdGatewayModule : Module() {
  override fun definition() = ModuleDefinition {
    Name("UssdGateway")

    Function("getStatus") {
      val context = context()
      GatewayState.get(context) + mapOf("accessibilityEnabled" to isAccessibilityEnabled(context))
    }

    Function("getConfiguration") { GatewayState.configuration(context()) }

    Function("saveConfiguration") { baseUrl: String, token: String ->
      require(baseUrl.startsWith("https://") || baseUrl.startsWith("http://10.") || baseUrl.startsWith("http://192.168.")) {
        "Use an HTTPS server URL (local private-network URLs are allowed for testing)."
      }
      require(token.startsWith("urg_") && token.length >= 32) { "Enter the gateway token created by the admin." }
      GatewayState.saveConfiguration(context(), baseUrl, token)
    }

    Function("getDeviceIdentity") {
      val context = context()
      mapOf(
        "installationId" to GatewayState.installationId(context),
        "deviceModel" to "${Build.MANUFACTURER} ${Build.MODEL}".trim(),
        "subscriptions" to activeSubscriptions(context)
      )
    }

    Function("getPendingJob") { GatewayState.pendingJob(context()) }
    Function("createId") { UUID.randomUUID().toString() }

    Function("prepareJob") {
      attemptId: String,
      recipient: String,
      dispatchToken: String,
      clientResultId: String,
      simSlot: Int,
      subscriptionId: Int ->
      val context = context()
      require(recipient.matches(Regex("0[0-9]{10}"))) { "Invalid recipient number." }
      assertExpectedSubscription(context, simSlot, subscriptionId)
      GatewayState.prepareJob(context, attemptId, recipient, dispatchToken, clientResultId, simSlot, subscriptionId)
    }

    Function("startPreparedJob") {
      val context = context()
      require(isAccessibilityEnabled(context)) { "Enable OresamSub USSD Gateway in Accessibility settings first." }
      val job = GatewayState.pendingJob(context) ?: error("No locally stored job is ready.")
      require(job["resultReady"] != true) { "Upload the stored result before starting another USSD session." }
      val simSlot = job["simSlot"] as Int
      val subscriptionId = job["subscriptionId"] as Int
      assertExpectedSubscription(context, simSlot, subscriptionId)
      GatewayState.set(context, "starting", 0, "Dialling *312# on SIM ${simSlot + 1}.")
      dial(context, simSlot)
    }

    Function("storeLocalResult") { outcome: String, responseText: String ->
      GatewayState.saveResult(context(), outcome, responseText)
    }

    Function("clearPendingJob") { GatewayState.clearJob(context()) }

    Function("openAccessibilitySettings") {
      context().startActivity(Intent(Settings.ACTION_ACCESSIBILITY_SETTINGS).apply {
        addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
      })
    }

    Function("startCompatibilityTest") { simSlot: Int ->
      val context = context()
      require(isAccessibilityEnabled(context)) { "Enable OresamSub USSD Gateway in Accessibility settings first." }
      GatewayState.set(context, "starting", 0, "Dialling *312# on SIM ${simSlot + 1}.")
      dial(context, simSlot)
    }

    Function("cancelTest") {
      val context = context()
      if (GatewayState.pendingJob(context) != null) {
        GatewayState.saveResult(context, "device_error", "Operator cancelled the local USSD session.")
      } else {
        GatewayState.set(context, "cancelled", GatewayState.step(context), "Test cancelled.")
      }
    }
  }

  private fun context() = appContext.reactContext ?: error("Android context is unavailable")

  private fun activeSubscriptions(context: Context): List<Map<String, Any?>> {
    if (ContextCompat.checkSelfPermission(context, Manifest.permission.READ_PHONE_STATE) != PackageManager.PERMISSION_GRANTED) {
      return emptyList()
    }
    val manager = context.getSystemService(Context.TELEPHONY_SUBSCRIPTION_SERVICE) as SubscriptionManager
    return (manager.activeSubscriptionInfoList ?: emptyList()).map {
      mapOf(
        "simSlot" to it.simSlotIndex,
        "subscriptionId" to it.subscriptionId,
        "carrierName" to it.carrierName?.toString()
      )
    }
  }

  private fun assertExpectedSubscription(context: Context, simSlot: Int, subscriptionId: Int) {
    val match = activeSubscriptions(context).any {
      it["simSlot"] == simSlot && it["subscriptionId"] == subscriptionId
    }
    require(match) {
      "The paired SIM is no longer in SIM ${simSlot + 1}. Processing is blocked; reset pairing from admin if the SIM was replaced."
    }
  }

  private fun dial(context: Context, simSlot: Int) {
    val intent = Intent(Intent.ACTION_CALL, Uri.parse("tel:" + Uri.encode("*312#"))).apply {
      addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
    }
    val telecom = context.getSystemService(Context.TELECOM_SERVICE) as TelecomManager
    val accounts = telecom.callCapablePhoneAccounts
    require(accounts.isNotEmpty()) { "No call-capable SIM is available." }
    intent.putExtra(TelecomManager.EXTRA_PHONE_ACCOUNT_HANDLE, accounts.getOrNull(simSlot) ?: error("SIM ${simSlot + 1} cannot place calls."))
    context.startActivity(intent)
  }

  private fun isAccessibilityEnabled(context: Context): Boolean {
    val expected = "${context.packageName}/${UssdAccessibilityService::class.java.name}"
    val enabled = Settings.Secure.getString(context.contentResolver, Settings.Secure.ENABLED_ACCESSIBILITY_SERVICES) ?: return false
    return TextUtils.SimpleStringSplitter(':').run {
      setString(enabled)
      any { it.equals(expected, ignoreCase = true) }
    }
  }
}
