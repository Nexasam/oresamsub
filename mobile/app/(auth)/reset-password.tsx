import { router, useLocalSearchParams } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, Alert, Pressable, StyleSheet, Text, View } from 'react-native';

import { ApiError } from '../../src/api/client';
import { authApi } from '../../src/auth/authApi';
import { AuthField } from '../../src/components/AuthField';
import { PinInput } from '../../src/components/PinInput';
import { colors, fonts } from '../../src/theme/colors';

export default function ResetPasswordScreen() {
  const { email = '' } = useLocalSearchParams<{ email?: string }>();
  const [otp, setOtp] = useState('');
  const [password, setPassword] = useState('');
  const [confirmation, setConfirmation] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');

  const submit = async () => {
    if (otp.length !== 6 || password !== confirmation || password.length < 8) { setError('Enter the six-digit code and matching secure passwords.'); return; }
    setBusy(true); setError('');
    try {
      await authApi.resetPassword(email, otp, password);
      Alert.alert('Password changed', 'Sign in with your new password.', [{ text: 'Sign in', onPress: () => router.replace('/(auth)/login') }]);
    } catch (reason) { setError(reason instanceof ApiError ? reason.message : 'Unable to reset your password.'); }
    finally { setBusy(false); }
  };

  return <View style={styles.screen}>
    <Text style={styles.title}>Enter reset code</Text>
    <Text style={styles.subtitle}>Sent to {email}</Text>
    <View style={styles.card}>
      <PinInput autoFocus label="Password reset code" length={6} onChangeText={setOtp} secure={false} value={otp} />
      <View style={styles.field}><AuthField autoCapitalize="none" autoComplete="new-password" icon="lock" label="New password" onChangeText={setPassword} secureTextEntry value={password} /></View>
      <AuthField autoCapitalize="none" autoComplete="new-password" icon="lock" label="Confirm new password" onChangeText={setConfirmation} secureTextEntry value={confirmation} />
      <Text style={styles.hint}>Use at least 8 characters with uppercase, lowercase, number and symbol.</Text>
      {error ? <Text style={styles.error}>{error}</Text> : null}
      <Pressable disabled={busy} onPress={() => void submit()} style={styles.button}>{busy ? <ActivityIndicator color={colors.white} /> : <Text style={styles.buttonText}>Reset password</Text>}</Pressable>
    </View>
  </View>;
}

const styles = StyleSheet.create({
  screen: { backgroundColor: colors.background, flex: 1, justifyContent: 'center', padding: 24 },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 27, textAlign: 'center' },
  subtitle: { color: colors.muted, fontFamily: fonts.regular, fontSize: 12, marginTop: 7, textAlign: 'center' },
  card: { backgroundColor: colors.surface, borderRadius: 22, marginTop: 24, padding: 18 },
  field: { marginTop: 16 },
  hint: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, lineHeight: 14, marginTop: -4 },
  error: { color: colors.danger, fontFamily: fonts.medium, fontSize: 10, marginTop: 10 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, justifyContent: 'center', marginTop: 18, minHeight: 54 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
});
