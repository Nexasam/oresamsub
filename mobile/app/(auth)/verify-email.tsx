import { router, useLocalSearchParams } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, Alert, Pressable, StyleSheet, Text, View } from 'react-native';

import { ApiError } from '../../src/api/client';
import { authApi } from '../../src/auth/authApi';
import { BrandLogo } from '../../src/components/BrandLogo';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { colors, fonts } from '../../src/theme/colors';

export default function VerifyEmailScreen() {
  const { email = '' } = useLocalSearchParams<{ email?: string }>();
  const [sending, setSending] = useState(false);

  const resend = async () => {
    if (!email || sending) return;
    setSending(true);
    try {
      const response = await authApi.resendEmailVerification(email);
      Alert.alert('Verification email sent', response.message);
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
        We sent a secure verification link to{'\n'}
        <Text style={styles.email}>{email}</Text>
      </Text>
      <Text style={styles.hint}>Open the link in your email, then return here and sign in. Access is granted only after verification.</Text>
      <Pressable onPress={() => router.replace('/(auth)/login')} style={({ pressed }) => [styles.primary, pressed && styles.pressed]}>
        <Text style={styles.primaryText}>Go to sign in</Text>
        <MaterialIcon color={colors.white} name="arrow_forward" size={19} />
      </Pressable>
      <Pressable disabled={sending} onPress={() => void resend()} style={({ pressed }) => [styles.secondary, pressed && styles.pressed]}>
        {sending ? <ActivityIndicator color={colors.primary} /> : <Text style={styles.secondaryText}>Resend verification email</Text>}
      </Pressable>
      <Text style={styles.spam}>Can’t find it? Check your spam or junk folder.</Text>
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
  primary: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, flexDirection: 'row', gap: 8, justifyContent: 'center', marginTop: 28, minHeight: 54, width: '100%' },
  primaryText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
  secondary: { alignItems: 'center', borderColor: colors.border, borderRadius: 15, borderWidth: 1, justifyContent: 'center', marginTop: 10, minHeight: 52, width: '100%' },
  secondaryText: { color: colors.primary, fontFamily: fonts.bold, fontSize: 12 },
  spam: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, marginTop: 18 },
  pressed: { opacity: 0.75, transform: [{ scale: 0.99 }] },
});
