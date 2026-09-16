import { router } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text, View } from 'react-native';

import { ApiError } from '../../src/api/client';
import { authApi } from '../../src/auth/authApi';
import { AuthField } from '../../src/components/AuthField';
import { BrandLogo } from '../../src/components/BrandLogo';
import { colors, fonts } from '../../src/theme/colors';

export default function ForgotPasswordScreen() {
  const [email, setEmail] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');

  const submit = async () => {
    if (!email.trim() || busy) return;
    setBusy(true); setError('');
    try {
      await authApi.forgotPassword(email.trim());
      router.push({ pathname: '/(auth)/reset-password', params: { email: email.trim() } });
    } catch (reason) {
      setError(reason instanceof ApiError ? reason.message : 'Unable to request a reset code.');
    } finally { setBusy(false); }
  };

  return <View style={styles.screen}>
    <BrandLogo size={58} />
    <Text style={styles.title}>Reset your password</Text>
    <Text style={styles.subtitle}>We’ll send a six-digit reset code to your email.</Text>
    <View style={styles.card}>
      <AuthField autoCapitalize="none" autoComplete="email" icon="mail" keyboardType="email-address" label="Email address" onChangeText={setEmail} placeholder="you@example.com" value={email} />
      {error ? <Text style={styles.error}>{error}</Text> : null}
      <Pressable disabled={busy || !email.trim()} onPress={() => void submit()} style={styles.button}>{busy ? <ActivityIndicator color={colors.white} /> : <Text style={styles.buttonText}>Send reset code</Text>}</Pressable>
    </View>
    <Pressable onPress={() => router.back()}><Text style={styles.back}>Back to sign in</Text></Pressable>
  </View>;
}

const styles = StyleSheet.create({
  screen: { alignItems: 'center', backgroundColor: colors.background, flex: 1, justifyContent: 'center', padding: 24 },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 27, marginTop: 22 },
  subtitle: { color: colors.muted, fontFamily: fonts.regular, fontSize: 12, marginTop: 8, textAlign: 'center' },
  card: { backgroundColor: colors.surface, borderRadius: 22, marginTop: 25, padding: 18, width: '100%' },
  error: { color: colors.danger, fontFamily: fonts.medium, fontSize: 10, marginBottom: 10 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, justifyContent: 'center', minHeight: 54 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
  back: { color: colors.primary, fontFamily: fonts.bold, fontSize: 11, marginTop: 22 },
});
