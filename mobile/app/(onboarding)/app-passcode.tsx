import { router } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, KeyboardAvoidingView, Platform, Pressable, StyleSheet, Text, View } from 'react-native';

import { appLock } from '../../src/auth/appLock';
import { useAuthStore } from '../../src/auth/authStore';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { PinInput } from '../../src/components/PinInput';
import { colors, fonts } from '../../src/theme/colors';

export default function AppPasscodeScreen() {
  const restore = useAuthStore((state) => state.restore);
  const [passcode, setPasscode] = useState('');
  const [confirmation, setConfirmation] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');

  const submit = async () => {
    if (passcode.length !== 6 || passcode !== confirmation) {
      setError('Enter matching six-digit app passcodes.');
      return;
    }
    setBusy(true);
    setError('');
    try {
      await appLock.setPasscode(passcode);
      if (useAuthStore.getState().status !== 'authenticated') await restore();
      const onboarding = useAuthStore.getState().onboarding;
      if (!onboarding?.phone_verified) router.replace('/(onboarding)/phone');
      else if (!onboarding.transaction_pin_set) router.replace('/(onboarding)/pin');
      else router.replace('/(tabs)');
    } catch {
      setError('Unable to save your app passcode. Please try again.');
    } finally {
      setBusy(false);
    }
  };

  return <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.screen}>
    <View style={styles.content}>
      <View style={styles.icon}><MaterialIcon color={colors.primary} name="password" size={34} /></View>
      <Text style={styles.eyebrow}>QUICK, SECURE ACCESS</Text>
      <Text style={styles.title}>Create app passcode</Text>
      <Text style={styles.subtitle}>Use this six-digit code to unlock OresamSub on this device. It is separate from your transaction PIN.</Text>
      <View style={styles.card}>
        <PinInput autoFocus label="New app passcode" length={6} onChangeText={setPasscode} value={passcode} />
        <PinInput label="Confirm app passcode" length={6} onChangeText={setConfirmation} style={styles.second} value={confirmation} />
        {error ? <Text style={styles.error}>{error}</Text> : null}
        <Pressable disabled={busy || passcode.length !== 6 || confirmation.length !== 6} onPress={() => void submit()} style={[styles.button, busy && styles.disabled]}>
          {busy ? <ActivityIndicator color={colors.white} /> : <Text style={styles.buttonText}>Save and continue</Text>}
        </Pressable>
      </View>
    </View>
  </KeyboardAvoidingView>;
}

const styles = StyleSheet.create({
  screen: { backgroundColor: colors.background, flex: 1 },
  content: { flex: 1, justifyContent: 'center', padding: 24 },
  icon: { alignItems: 'center', alignSelf: 'center', backgroundColor: colors.primarySoft, borderRadius: 32, height: 68, justifyContent: 'center', width: 68 },
  eyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 9, letterSpacing: 1.4, marginTop: 22, textAlign: 'center' },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 27, marginTop: 5, textAlign: 'center' },
  subtitle: { color: colors.muted, fontFamily: fonts.regular, fontSize: 12, lineHeight: 19, marginTop: 9, textAlign: 'center' },
  card: { backgroundColor: colors.surface, borderRadius: 22, marginTop: 25, padding: 18 },
  second: { marginTop: 16 },
  error: { color: colors.danger, fontFamily: fonts.medium, fontSize: 10, marginTop: 12 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, justifyContent: 'center', marginTop: 20, minHeight: 54 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
  disabled: { opacity: 0.6 },
});
