import { router } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, KeyboardAvoidingView, Platform, Pressable, ScrollView, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../../src/api/client';
import { authApi } from '../../src/auth/authApi';
import { useAuthStore } from '../../src/auth/authStore';
import { colors, fonts } from '../../src/theme/colors';

export default function PhoneScreen() {
  const refreshSession = useAuthStore((state) => state.refreshSession);
  const [step, setStep] = useState<'phone' | 'otp'>('phone');
  const [phone, setPhone] = useState('');
  const [otp, setOtp] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');

  const submit = async () => {
    setBusy(true);
    setError('');
    try {
      if (step === 'phone') {
        await authApi.sendPhoneOtp(phone);
        setStep('otp');
      } else {
        await authApi.verifyPhoneOtp(otp);
        const session = await refreshSession();
        router.replace(session.onboarding.transaction_pin_set ? '/(tabs)' : '/(onboarding)/pin');
      }
    } catch (reason) {
      setError(reason instanceof ApiError ? reason.message : 'Unable to complete verification.');
    } finally {
      setBusy(false);
    }
  };

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.screen}>
      <ScrollView
        automaticallyAdjustKeyboardInsets={Platform.OS === 'ios'}
        contentContainerStyle={styles.content}
        keyboardDismissMode={Platform.OS === 'ios' ? 'interactive' : 'on-drag'}
        keyboardShouldPersistTaps="handled"
      >
        <View style={styles.form}>
          <Text style={styles.title}>{step === 'phone' ? 'Verify phone number' : 'Enter verification code'}</Text>
          <Text style={styles.subtitle}>
            {step === 'phone'
              ? 'We will send a six-digit code to your Nigerian number.'
              : `Enter the code sent to ${phone}.`}
          </Text>
          <TextInput
            autoFocus
            keyboardType={step === 'phone' ? 'phone-pad' : 'number-pad'}
            maxLength={step === 'otp' ? 6 : 14}
            onChangeText={step === 'phone' ? setPhone : setOtp}
            placeholder={step === 'phone' ? '08012345678' : '123456'}
            placeholderTextColor={colors.muted}
            selectionColor={colors.primary}
            style={styles.input}
            value={step === 'phone' ? phone : otp}
          />
          {error ? <Text style={styles.error}>{error}</Text> : null}
          <Pressable disabled={busy} onPress={() => void submit()} style={styles.button}>
            {busy ? (
              <ActivityIndicator color={colors.white} />
            ) : (
              <Text style={styles.buttonText}>{step === 'phone' ? 'Send code' : 'Verify number'}</Text>
            )}
          </Pressable>
          {step === 'otp' ? (
            <Pressable onPress={() => setStep('phone')}>
              <Text style={styles.link}>Change phone number</Text>
            </Pressable>
          ) : null}
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  screen: { backgroundColor: colors.background, flex: 1 },
  content: { flexGrow: 1, justifyContent: 'center', padding: 24 },
  form: { width: '100%' },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 28 },
  subtitle: { color: colors.muted, fontFamily: fonts.regular, lineHeight: 21, marginBottom: 24, marginTop: 8 },
  input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, color: colors.text, fontFamily: fonts.semiBold, fontSize: 18, padding: 15 },
  error: { color: colors.danger, fontFamily: fonts.medium, marginTop: 10 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 12, justifyContent: 'center', marginTop: 20, minHeight: 52 },
  buttonText: { color: colors.white, fontFamily: fonts.bold },
  link: { color: colors.primary, fontFamily: fonts.semiBold, marginTop: 18, textAlign: 'center' },
});
