import { StatusBar } from 'expo-status-bar';
import { useCallback, useEffect, useRef, useState } from 'react';
import {
  Alert,
  PermissionsAndroid,
  Platform,
  Pressable,
  SafeAreaView,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
} from 'react-native';

import UssdGateway, {
  GatewayConfiguration,
  GatewayIdentity,
  GatewayStatus,
  PendingJob,
} from './modules/ussd-gateway';

const initialStatus: GatewayStatus = {
  state: 'idle', step: 0, message: 'Ready.', updatedAt: 0,
  accessibilityEnabled: false, hasPendingJob: false, hasPendingResult: false,
};

type ApiResponse<T> = { success: boolean; message: string; data: T };
type Claim = {
  attempt_id: string; recipient_number: string; dispatch_token: string;
  sim_slot: number; subscription_id: number;
};
type GatewayServerStatus = {
  device: { sim_slot: number; subscription_id: number; processing_mode: 'manual' | 'automatic' };
};

export default function App() {
  const [status, setStatus] = useState(initialStatus);
  const [identity, setIdentity] = useState<GatewayIdentity | null>(null);
  const [baseUrl, setBaseUrl] = useState('https://oresamsub.com/api/ussd-gateway/v1');
  const [token, setToken] = useState('');
  const [configured, setConfigured] = useState(false);
  const [serverMode, setServerMode] = useState<'manual' | 'automatic' | null>(null);
  const [automaticRunning, setAutomaticRunning] = useState(false);
  const [automaticCycle, setAutomaticCycle] = useState(0);
  const automaticTickInFlight = useRef(false);
  const nextAutomaticAt = useRef(0);
  const [busy, setBusy] = useState(false);
  const [lastMessage, setLastMessage] = useState('Configure and pair this phone before processing recipients.');

  const refresh = useCallback(() => {
    if (Platform.OS !== 'android') return;
    try {
      setStatus(UssdGateway.getStatus());
      setIdentity(UssdGateway.getDeviceIdentity());
    } catch (error) {
      setLastMessage(messageOf(error));
    }
  }, []);

  useEffect(() => {
    if (Platform.OS === 'android') {
      const saved = UssdGateway.getConfiguration();
      setBaseUrl(saved.baseUrl);
      setToken(saved.token);
      setConfigured(Boolean(saved.token));
    }
    refresh();
    const timer = setInterval(refresh, 1000);
    return () => clearInterval(timer);
  }, [refresh]);

  const requestPhonePermissions = async () => {
    const result = await PermissionsAndroid.requestMultiple([
      PermissionsAndroid.PERMISSIONS.CALL_PHONE,
      PermissionsAndroid.PERMISSIONS.READ_PHONE_STATE,
    ]);
    const granted = Object.values(result).every((value) => value === PermissionsAndroid.RESULTS.GRANTED);
    if (!granted) throw new Error('Phone and SIM permissions are required.');
    refresh();
  };

  const request = async <T,>(config: GatewayConfiguration, path: string, options: RequestInit = {}) => {
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 20_000);
    try {
      const response = await fetch(`${config.baseUrl}${path}`, {
        ...options,
        signal: controller.signal,
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          Authorization: `Bearer ${config.token}`,
          'X-Gateway-Installation': config.installationId,
          ...(options.headers ?? {}),
        },
      });
      const body = await response.json().catch(() => null) as ApiResponse<T> | null;
      if (!response.ok || !body?.success) throw new Error(body?.message || `Server returned HTTP ${response.status}.`);
      return body;
    } finally {
      clearTimeout(timeout);
    }
  };

  useEffect(() => {
    if (!configured || Platform.OS !== 'android') return;
    const config = UssdGateway.getConfiguration();
    request<GatewayServerStatus>(config, '/status')
      .then((response) => setServerMode(response.data.device.processing_mode))
      .catch(() => { /* Pairing/availability errors are shown when processing starts. */ });
  }, [configured]);

  const pairPhone = async () => {
    setBusy(true);
    try {
      await requestPhonePermissions();
      UssdGateway.saveConfiguration(baseUrl, token);
      const config = UssdGateway.getConfiguration();
      const device = UssdGateway.getDeviceIdentity();
      if (device.subscriptions.length === 0) throw new Error('No active SIM was detected. Check permission and SIM status.');

      // The server decides which configured slot is accepted. Trying each active SIM does not claim work.
      let paired = false;
      let finalError = 'No configured SIM matched this phone.';
      for (const subscription of device.subscriptions) {
        try {
          const response = await request<unknown>(config, '/bind', {
            method: 'POST',
            body: JSON.stringify({
              installation_id: device.installationId,
              sim_slot: subscription.simSlot,
              subscription_id: subscription.subscriptionId,
              device_model: device.deviceModel,
            }),
          });
          setLastMessage(response.message);
          paired = true;
          break;
        } catch (error) {
          finalError = messageOf(error);
        }
      }
      if (!paired) throw new Error(finalError);
      setConfigured(true);
      setIdentity(device);
    } catch (error) {
      Alert.alert('Pairing failed', messageOf(error));
      setLastMessage(messageOf(error));
    } finally {
      setBusy(false);
    }
  };

  const simHeaders = (simSlot: number, subscriptionId: number) => ({
    'X-Gateway-Sim-Slot': String(simSlot),
    'X-Gateway-Subscription': String(subscriptionId),
  });

  const uploadResult = async (config: GatewayConfiguration, job: PendingJob) => {
    if (!job.resultReady || !job.outcome) throw new Error('The stored job does not have a result yet.');
    const response = await request<{ status: string }>(config, `/jobs/${job.attemptId}/result`, {
      method: 'POST',
      headers: simHeaders(job.simSlot, job.subscriptionId),
      body: JSON.stringify({
        dispatch_token: job.dispatchToken,
        client_result_id: job.clientResultId,
        outcome: job.outcome,
        response_text: job.responseText || null,
      }),
    });
    UssdGateway.clearPendingJob();
    setLastMessage(`${response.message} Admin review is required.`);
    setAutomaticCycle((cycle) => cycle + 1);
    refresh();
  };

  const processNext = async () => {
    if (!configured) return Alert.alert('Pair first', 'Save the gateway token and pair this phone first.');
    if (!status.accessibilityEnabled) {
      UssdGateway.openAccessibilitySettings();
      return Alert.alert('Accessibility required', 'Enable OresamSub USSD Gateway, then return here.');
    }

    setBusy(true);
    try {
      await requestPhonePermissions();
      const config = UssdGateway.getConfiguration();
      const existing = UssdGateway.getPendingJob();

      if (existing?.resultReady) {
        await uploadResult(config, existing);
        // Give the carrier/USSD stack a moment to settle before dialing again.
        nextAutomaticAt.current = Date.now() + 4000;
        return;
      }
      if (existing) {
        if (status.state === 'prepared') {
          UssdGateway.storeLocalResult('not_dispatched', 'The job was stored but USSD dispatch was not confirmed on this phone.');
          await uploadResult(config, UssdGateway.getPendingJob()!);
          return;
        }
        throw new Error('A USSD session is already in progress. Wait for its result; do not start another recipient.');
      }

      const serverStatus = await request<GatewayServerStatus>(config, '/status');
      setServerMode(serverStatus.data.device.processing_mode);
      const expectedSlot = serverStatus.data.device.sim_slot;
      const expectedSubscription = serverStatus.data.device.subscription_id;
      const activeMatch = UssdGateway.getDeviceIdentity().subscriptions.some(
        (item) => item.simSlot === expectedSlot && item.subscriptionId === expectedSubscription,
      );
      if (!activeMatch) {
        throw new Error(`The paired SIM is not active in SIM ${expectedSlot + 1}. No recipient was claimed.`);
      }

      const claimResponse = await request<Claim | null>(config, '/jobs/claim', {
        method: 'POST',
        headers: simHeaders(expectedSlot, expectedSubscription),
        body: '{}',
      });
      const claim = claimResponse.data;
      if (!claim) {
        setLastMessage('No queued recipient is available for this paired SIM.');
        return;
      }

      UssdGateway.prepareJob(
        claim.attempt_id,
        claim.recipient_number,
        claim.dispatch_token,
        UssdGateway.createId(),
        claim.sim_slot,
        claim.subscription_id,
      );

      try {
        await request(config, `/jobs/${claim.attempt_id}/started`, {
          method: 'POST',
          headers: simHeaders(claim.sim_slot, claim.subscription_id),
          body: JSON.stringify({ dispatch_token: claim.dispatch_token }),
        });
      } catch (error) {
        UssdGateway.storeLocalResult('not_dispatched', `USSD was not dialled because dispatch acknowledgement failed: ${messageOf(error)}`);
        setLastMessage('Nothing was dialled. The safe local result will upload when the server is reachable.');
        return;
      }

      try {
        UssdGateway.startPreparedJob();
        setLastMessage(`Processing ${claim.recipient_number}. Do not close the USSD screen.`);
      } catch (error) {
        UssdGateway.storeLocalResult('not_dispatched', `USSD was not dialled: ${messageOf(error)}`);
        throw error;
      }
    } catch (error) {
      if (automaticRunning && /too many|rate.?limit|429/i.test(messageOf(error))) {
        setAutomaticRunning(false);
        setLastMessage('Automatic processing paused because the server or carrier rate-limited this device. Wait and resume manually.');
      }
      Alert.alert('Processing stopped safely', messageOf(error));
      setLastMessage(messageOf(error));
    } finally {
      setBusy(false);
      refresh();
    }
  };

  useEffect(() => {
    if (!automaticRunning || serverMode !== 'automatic' || busy || !configured || !status.accessibilityEnabled) return;
    if (status.hasPendingJob && !status.hasPendingResult && status.state !== 'prepared') return;
    if (Date.now() < nextAutomaticAt.current || automaticTickInFlight.current) return;
    automaticTickInFlight.current = true;
    // The native service owns the active USSD session. We only advance after its
    // result is present locally; uploadResult clears that result before the next claim.
    processNext().finally(() => { automaticTickInFlight.current = false; });
    // Polling status changes retrigger this effect and safely advance one job at a time.
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [automaticRunning, automaticCycle, serverMode, busy, configured, status.accessibilityEnabled, status.state, status.hasPendingResult, status.hasPendingJob]);

  const pending = status.hasPendingJob;
  const currentSubscription = identity?.subscriptions.map((item) =>
    `SIM ${item.simSlot + 1}: ${item.carrierName || 'Carrier'} (subscription ${item.subscriptionId})`
  ).join('\n') || 'Grant phone permission to detect SIMs.';

  return (
    <SafeAreaView style={styles.safeArea}>
      <StatusBar style="dark" />
      <ScrollView contentContainerStyle={styles.container} keyboardShouldPersistTaps="handled">
        <View style={styles.brandRow}>
          <View style={styles.logo}><Text style={styles.logoText}>O</Text></View>
          <View><Text style={styles.brand}>OresamSub USSD Gateway</Text><Text style={styles.caption}>ONE SIM · ONE JOB · NO AUTO RETRY</Text></View>
        </View>

        <View style={styles.hero}>
          <Text style={styles.eyebrow}>DEVICE/SIM-BOUND PROCESSING</Text>
          <Text style={styles.title}>Recharge safely, one recipient at a time</Text>
          <Text style={styles.subtitle}>The server locks every picked recipient to this paired phone and SIM until its result is uploaded.</Text>
        </View>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>1. Gateway setup</Text>
          <Text style={styles.label}>Server URL</Text>
          <TextInput value={baseUrl} onChangeText={setBaseUrl} autoCapitalize="none" keyboardType="url" style={styles.input} />
          <Text style={styles.label}>One-time gateway token from admin</Text>
          <TextInput value={token} onChangeText={setToken} autoCapitalize="none" secureTextEntry style={styles.input} placeholder="urg_…" />
          <Pressable disabled={busy} style={styles.secondaryButton} onPress={pairPhone}>
            <Text style={styles.secondaryButtonText}>{configured ? 'Verify pairing again' : 'Save and pair phone'}</Text>
          </Pressable>
          <Text style={styles.detail}>Installation: {identity?.installationId || 'Loading…'}</Text>
          <Text style={styles.detail}>{currentSubscription}</Text>
        </View>

        <View style={styles.card}>
          <View style={styles.rowBetween}>
            <Text style={styles.cardTitle}>2. Device readiness</Text>
            <Text style={[styles.badge, status.accessibilityEnabled ? styles.good : styles.warn]}>{status.accessibilityEnabled ? 'READY' : 'SETUP'}</Text>
          </View>
          <Text style={styles.detail}>Accessibility: {status.accessibilityEnabled ? 'Enabled' : 'Not enabled'}</Text>
          {serverMode && <Text style={styles.detail}>Server mode: {serverMode === 'automatic' ? 'Automatic' : 'Manual'}{serverMode === 'automatic' && automaticRunning ? ' · Running' : ''}</Text>}
          {!status.accessibilityEnabled && <Pressable style={styles.secondaryButton} onPress={() => UssdGateway.openAccessibilitySettings()}><Text style={styles.secondaryButtonText}>Open accessibility settings</Text></Pressable>}
        </View>

        <View style={styles.card}>
          <Text style={styles.cardTitle}>Current state</Text>
          <Text style={styles.state}>{status.state.replaceAll('_', ' ').toUpperCase()}</Text>
          <Text style={styles.message}>{status.message}</Text>
          <Text style={styles.message}>{lastMessage}</Text>
          {pending && <Text style={styles.locked}>A recipient is stored on this phone. No new recipient can be claimed until its result is acknowledged.</Text>}
        </View>

        <Pressable disabled={busy} onPress={() => {
          if (serverMode === 'automatic') {
            setAutomaticRunning((running) => !running);
            return;
          }
          processNext();
        }} style={({ pressed }) => [styles.primaryButton, busy && styles.disabled, pressed && styles.pressed]}>
          <Text style={styles.primaryButtonText}>{busy ? 'Please wait…' : serverMode === 'automatic' ? (automaticRunning ? 'Pause automatic processing' : 'Start automatic processing') : status.hasPendingResult ? 'Upload stored result' : pending ? 'Check current job' : 'Process next recipient'}</Text>
        </Pressable>

        <Text style={styles.footnote}>Never repeat an uncertain recipient. If the server or network fails, this app preserves the attempt locally and stops.</Text>
      </ScrollView>
    </SafeAreaView>
  );
}

const messageOf = (error: unknown) => error instanceof Error ? error.message : 'Unexpected error.';

const styles = StyleSheet.create({
  safeArea: { flex: 1, backgroundColor: '#f3f7f5' },
  container: { padding: 20, paddingTop: 30, paddingBottom: 48, gap: 16 },
  brandRow: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  logo: { width: 44, height: 44, borderRadius: 22, backgroundColor: '#087a56', alignItems: 'center', justifyContent: 'center' },
  logoText: { color: '#fff', fontSize: 24, fontWeight: '900' },
  brand: { color: '#10231c', fontSize: 18, fontWeight: '800' },
  caption: { color: '#668078', fontSize: 9, fontWeight: '800', letterSpacing: 1.1, marginTop: 2 },
  hero: { backgroundColor: '#0b6b4d', borderRadius: 24, padding: 24 },
  eyebrow: { color: '#bff4dc', fontSize: 11, fontWeight: '800', letterSpacing: 1.2 },
  title: { color: '#fff', fontSize: 27, lineHeight: 33, fontWeight: '900', marginTop: 10 },
  subtitle: { color: '#d9f3e8', fontSize: 14, lineHeight: 21, marginTop: 10 },
  card: { backgroundColor: '#fff', borderRadius: 20, padding: 20, borderWidth: 1, borderColor: '#e0ebe6' },
  cardTitle: { color: '#172c24', fontSize: 17, fontWeight: '800' },
  label: { color: '#71827c', fontSize: 12, marginTop: 14, marginBottom: 5 },
  input: { borderWidth: 1, borderColor: '#cbdcd5', borderRadius: 12, paddingHorizontal: 13, paddingVertical: 11, color: '#172c24' },
  secondaryButton: { borderWidth: 1, borderColor: '#0b805b', borderRadius: 12, padding: 13, alignItems: 'center', marginTop: 16 },
  secondaryButtonText: { color: '#087a56', fontWeight: '800' },
  detail: { color: '#5a7067', fontSize: 12, lineHeight: 18, marginTop: 12 },
  rowBetween: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  badge: { overflow: 'hidden', borderRadius: 999, paddingVertical: 5, paddingHorizontal: 9, fontSize: 9, fontWeight: '900' },
  good: { backgroundColor: '#d9f8e9', color: '#176848' },
  warn: { backgroundColor: '#fff0c7', color: '#865e00' },
  state: { color: '#087a56', fontSize: 13, fontWeight: '900', letterSpacing: 1, marginTop: 14 },
  message: { color: '#425950', fontSize: 13, lineHeight: 19, marginTop: 7 },
  locked: { color: '#8a3d12', backgroundColor: '#fff1e8', borderRadius: 10, padding: 10, fontWeight: '700', fontSize: 12, lineHeight: 18, marginTop: 12 },
  primaryButton: { backgroundColor: '#0a8f68', borderRadius: 16, padding: 17, alignItems: 'center' },
  primaryButtonText: { color: '#fff', fontSize: 16, fontWeight: '900' },
  disabled: { opacity: 0.55 },
  pressed: { transform: [{ scale: 0.99 }] },
  footnote: { color: '#71827c', fontSize: 11, lineHeight: 17, textAlign: 'center', paddingHorizontal: 10 },
});
