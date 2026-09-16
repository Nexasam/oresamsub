import { router, useLocalSearchParams } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, Alert, Pressable, StyleSheet, Text, View } from 'react-native';

import { ApiError } from '../../src/api/client';
import { authApi } from '../../src/auth/authApi';
import { useAuthStore } from '../../src/auth/authStore';
import { BrandLogo } from '../../src/components/BrandLogo';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { PinInput } from '../../src/components/PinInput';
import { colors, fonts } from '../../src/theme/colors';

export default function VerifyEmailScreen() {
  const { email = '' } = useLocalSearchParams<{ email?: string }>();
  const [sending, setSending] = useState(false);
  const [verifying, setVerifying] = useState(false);
  const [otp, setOtp] = useState('');
  const [error, setError] = useState('');
  const verifyEmailOtp = useAuthStore((state) => state.verifyEmailOtp);

  const verify = async () => {
    if (!email || otp.length !== 6 || verifying) return;
    setVerifying(true);
    setError('');
    try {
      await verifyEmailOtp(email, otp);
      router.replace('/(onboarding)/app-passcode');
    } catch (reason) {
      setError(reason instanceof ApiError ? reason.message : 'Unable to verify this code.');
    } finally {
      setVerifying(false);
    }
  };

  const resend = async () => {
    if (!email || sending) return;
    setSending(true);
    try {
      const response = await authApi.resendEmailVerification(email);
      Alert.alert('Verification code sent', response.message);
    } catch (error) {
      Alert.alert('Could not resend email', error instanceof ApiError ? error.message : 'Please try again shortly.');
    } finally {
      setSending(false);
    }
  };

  return (
    <View style={styles.screen}>
      <BrandLogo size={58} />
      <View style={styles.icon}>
        <MaterialIcon color={colors.primary} name="mark_email_unread" size={38} />
      </View>
      <Text style={styles.title}>Verify your email</Text>
      <Text style={styles.copy}>
        Enter the six-digit code sent to{'\n'}
        <Text style={styles.email}>{email}</Text>
      </Text>
      <View style={styles.otpCard}>
        <PinInput autoFocus label="Email verification code" length={6} onChangeText={setOtp} secure={false} value={otp} />
        {error ? <Text style={styles.error}>{error}</Text> : null}
      </View>
      <Text style={styles.hint}>The code expires in 10 minutes. Never share it with anyone.</Text>
      <Pressable disabled={verifying || otp.length !== 6} onPress={() => void verify()} style={({ pressed }) => [styles.primary, pressed && styles.pressed, (verifying || otp.length !== 6) && styles.disabled]}>
        {verifying ? <ActivityIndicator color={colors.white} /> : <Text style={styles.primaryText}>Verify and continue</Text>}
        <MaterialIcon color={colors.white} name="arrow_forward" size={19} />
      </Pressable>
      <Pressable disabled={sending} onPress={() => void resend()} style={({ pressed }) => [styles.secondary, pressed && styles.pressed]}>
        {sending ? <ActivityIndicator color={colors.primary} /> : <Text style={styles.secondaryText}>Resend code</Text>}
      </Pressable>
      <Pressable onPress={() => router.replace('/(auth)/login')}><Text style={styles.spam}>Use a different account</Text></Pressable>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { alignItems: 'center', backgroundColor: colors.background, flex: 1, justifyContent: 'center', paddingHorizontal: 28 },
  icon: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 30, height: 68, justifyContent: 'center', marginTop: 30, width: 68 },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 27, letterSpacing: -0.7, marginTop: 20 },
  copy: { color: colors.muted, fontFamily: fonts.regular, fontSize: 13, lineHeight: 21, marginTop: 10, textAlign: 'center' },
  email: { color: colors.text, fontFamily: fonts.bold },
  hint: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, lineHeight: 17, marginTop: 18, maxWidth: 330, textAlign: 'center' },
  otpCard: { marginTop: 22, width: '100%' },
  error: { color: colors.danger, fontFamily: fonts.medium, fontSize: 10, marginTop: 8 },
  primary: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, flexDirection: 'row', gap: 8, justifyContent: 'center', marginTop: 28, minHeight: 54, width: '100%' },
  primaryText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
  secondary: { alignItems: 'center', borderColor: colors.border, borderRadius: 15, borderWidth: 1, justifyContent: 'center', marginTop: 10, minHeight: 52, width: '100%' },
  secondaryText: { color: colors.primary, fontFamily: fonts.bold, fontSize: 12 },
  spam: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, marginTop: 18 },
  pressed: { opacity: 0.75, transform: [{ scale: 0.99 }] },
  disabled: { opacity: 0.55 },
});
