import { router } from 'expo-router';
import { useEffect, useState } from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text, View } from 'react-native';

import { appLock } from '../../src/auth/appLock';
import { useAuthStore } from '../../src/auth/authStore';
import { biometricLock } from '../../src/auth/biometricLock';
import { PinInput } from '../../src/components/PinInput';
import { colors, fonts } from '../../src/theme/colors';

export default function UnlockScreen() {
  const restore = useAuthStore((state) => state.restore);
  const [passcode, setPasscode] = useState('');
  const [busy, setBusy] = useState(false);
  const [biometricReady, setBiometricReady] = useState(false);
  const [error, setError] = useState('');

  const finishUnlock = async () => {
    await restore();
    if (useAuthStore.getState().status === 'authenticated') router.replace('/(tabs)');
    else router.replace('/(auth)/login');
  };

  const useBiometric = async () => {
    setBusy(true);
    if (await biometricLock.unlock()) await finishUnlock();
    setBusy(false);
  };

  useEffect(() => {
    void Promise.all([biometricLock.isEnabled(), biometricLock.isAvailable()]).then(([enabled, available]) => {
      setBiometricReady(enabled && available);
      if (enabled && available) void useBiometric();
    });
  }, []);

  const unlock = async () => {
    if (!(await appLock.verify(passcode))) {
      setError('Incorrect app passcode.');
      setPasscode('');
      return;
    }
    setBusy(true);
    await finishUnlock();
    setBusy(false);
  };

  return <View style={styles.screen}>
    <Text style={styles.eyebrow}>WELCOME BACK</Text>
    <Text style={styles.title}>Unlock OresamSub</Text>
    <Text style={styles.subtitle}>Enter your app passcode or use your fingerprint/Face ID.</Text>
    <View style={styles.card}>
      <PinInput autoFocus label="App passcode" length={6} onChangeText={setPasscode} value={passcode} />
      {error ? <Text style={styles.error}>{error}</Text> : null}
      <Pressable disabled={busy || passcode.length !== 6} onPress={() => void unlock()} style={styles.button}>
        {busy ? <ActivityIndicator color={colors.white} /> : <Text style={styles.buttonText}>Unlock</Text>}
      </Pressable>
      {biometricReady ? <Pressable disabled={busy} onPress={() => void useBiometric()} style={styles.biometric}><Text style={styles.biometricText}>Use fingerprint / Face ID</Text></Pressable> : null}
    </View>
    <Pressable onPress={() => router.replace('/(auth)/login')}><Text style={styles.password}>Sign in with password instead</Text></Pressable>
  </View>;
}

const styles = StyleSheet.create({
  screen: { backgroundColor: colors.background, flex: 1, justifyContent: 'center', padding: 24 },
  eyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 9, letterSpacing: 1.4, textAlign: 'center' },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 29, marginTop: 5, textAlign: 'center' },
  subtitle: { color: colors.muted, fontFamily: fonts.regular, fontSize: 12, marginTop: 8, textAlign: 'center' },
  card: { backgroundColor: colors.surface, borderRadius: 22, marginTop: 25, padding: 18 },
  error: { color: colors.danger, fontFamily: fonts.medium, fontSize: 10, marginTop: 9 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, justifyContent: 'center', marginTop: 18, minHeight: 54 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
  biometric: { alignItems: 'center', marginTop: 16 },
  biometricText: { color: colors.primary, fontFamily: fonts.bold, fontSize: 12 },
  password: { color: colors.muted, fontFamily: fonts.bold, fontSize: 11, marginTop: 22, textAlign: 'center' },
});
