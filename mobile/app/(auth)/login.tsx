import { zodResolver } from '@hookform/resolvers/zod';
import { router } from 'expo-router';
import { Controller, useForm } from 'react-hook-form';
import { useEffect, useState } from 'react';
import { ActivityIndicator, KeyboardAvoidingView, Platform, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { z } from 'zod';

import { ApiError } from '../../src/api/client';
import { useAuthStore } from '../../src/auth/authStore';
import { biometricLock } from '../../src/auth/biometricLock';
import { tokenVault } from '../../src/auth/tokenVault';
import { AuthField } from '../../src/components/AuthField';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { colors, fonts } from '../../src/theme/colors';

const schema = z.object({
  login: z.string().trim().min(1, 'Enter your email, username or phone number.'),
  password: z.string().min(1, 'Enter your password.'),
});

type FormValues = z.infer<typeof schema>;

export default function LoginScreen() {
  const signIn = useAuthStore((state) => state.signIn);
  const restore = useAuthStore((state) => state.restore);
  const [biometricReady, setBiometricReady] = useState(false);
  const [biometricBusy, setBiometricBusy] = useState(false);
  const [passwordVisible, setPasswordVisible] = useState(false);
  const { control, handleSubmit, setError, formState: { errors, isSubmitting } } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { login: '', password: '' },
  });

  useEffect(() => {
    void Promise.all([biometricLock.isEnabled(), biometricLock.isAvailable(), tokenVault.readRefreshToken()])
      .then(([enabled, available, refreshToken]) => setBiometricReady(enabled && available && !!refreshToken));
  }, []);

  const biometricSignIn = async () => {
    setBiometricBusy(true);
    try {
      if (!(await biometricLock.unlock())) return;
      await restore();
      const session = useAuthStore.getState();
      if (session.status === 'authenticated') router.replace('/(tabs)');
      else setError('root', { message: 'Your saved session has expired. Sign in with your password.' });
    } finally {
      setBiometricBusy(false);
    }
  };

  const submit = handleSubmit(async (values) => {
    try {
      const session = await signIn(values.login, values.password);
      if (!session.onboarding.phone_verified) router.replace('/(onboarding)/phone');
      else if (!session.onboarding.transaction_pin_set) router.replace('/(onboarding)/pin');
      else router.replace('/(tabs)');
    } catch (error) {
      setError('root', { message: error instanceof ApiError ? error.message : 'Unable to sign in. Please try again.' });
    }
  });

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.screen}>
      <ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled">
        <View style={styles.brandRow}>
          <View style={styles.mark}><Text style={styles.markText}>O</Text></View>
          <View>
            <Text style={styles.brand}>OresamSub</Text>
            <Text style={styles.brandNote}>Payments made simple</Text>
          </View>
          <View style={styles.secureBadge}><MaterialIcon color={colors.primary} name="verified_user" size={18} /></View>
        </View>

        <View style={styles.intro}>
          <Text style={styles.eyebrow}>WELCOME BACK</Text>
          <Text style={styles.title}>Good to see you.</Text>
          <Text style={styles.subtitle}>Sign in to manage your wallet and everyday payments.</Text>
        </View>

        <View style={styles.formCard}>
          <Controller control={control} name="login" render={({ field: { onBlur, onChange, value } }) => (
            <AuthField
              autoCapitalize="none"
              autoComplete="username"
              error={errors.login?.message}
              icon="person"
              label="Email, username or phone"
              onBlur={onBlur}
              onChangeText={onChange}
              placeholder="Enter your account details"
              returnKeyType="next"
              value={value}
            />
          )} />

          <Controller control={control} name="password" render={({ field: { onBlur, onChange, value } }) => (
            <AuthField
              autoCapitalize="none"
              autoComplete="current-password"
              error={errors.password?.message}
              icon="lock"
              label="Password"
              onBlur={onBlur}
              onChangeText={onChange}
              onSubmitEditing={() => void submit()}
              onToggleSecure={() => setPasswordVisible((visible) => !visible)}
              placeholder="Enter your password"
              returnKeyType="done"
              secureTextEntry={!passwordVisible}
              value={value}
            />
          )} />

          {errors.root && (
            <View style={styles.rootError}>
              <MaterialIcon color={colors.danger} name="error" size={18} />
              <Text style={styles.rootErrorText}>{errors.root.message}</Text>
            </View>
          )}

          <Pressable disabled={isSubmitting} onPress={submit} style={({ pressed }) => [styles.button, pressed && styles.buttonPressed, isSubmitting && styles.buttonDisabled]}>
            {isSubmitting ? <ActivityIndicator color={colors.white} /> : <><Text style={styles.buttonText}>Sign in</Text><MaterialIcon color={colors.white} name="arrow_forward" size={19} /></>}
          </Pressable>

          {biometricReady && (
            <>
              <View style={styles.divider}><View style={styles.line} /><Text style={styles.or}>OR</Text><View style={styles.line} /></View>
              <Pressable disabled={biometricBusy} onPress={() => void biometricSignIn()} style={({ pressed }) => [styles.biometricButton, pressed && styles.biometricPressed]}>
                <View style={styles.fingerprint}><MaterialIcon color={colors.primaryDark} name="fingerprint" size={25} /></View>
                <View style={styles.biometricCopy}>
                  <Text style={styles.biometricTitle}>{biometricBusy ? 'Checking fingerprint…' : 'Use fingerprint'}</Text>
                  <Text style={styles.biometricNote}>Unlock your saved secure session</Text>
                </View>
                <MaterialIcon color={colors.muted} name="chevron_right" size={20} />
              </Pressable>
            </>
          )}
        </View>

        <View style={styles.signupRow}>
          <Text style={styles.signupPrompt}>New to OresamSub?</Text>
          <Pressable onPress={() => router.push('/(auth)/register')}><Text style={styles.signupLink}>Create an account</Text></Pressable>
        </View>
        <View style={styles.trust}><MaterialIcon color={colors.muted} name="lock" size={13} /><Text style={styles.trustText}>Your login is encrypted and protected</Text></View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  screen: { backgroundColor: colors.background, flex: 1 },
  content: { flexGrow: 1, paddingBottom: 34, paddingHorizontal: 22, paddingTop: Platform.OS === 'android' ? 48 : 62 },
  brandRow: { alignItems: 'center', flexDirection: 'row' },
  mark: { alignItems: 'center', backgroundColor: colors.primaryDark, borderRadius: 14, height: 44, justifyContent: 'center', marginRight: 11, width: 44 },
  markText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 22 },
  brand: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 15 },
  brandNote: { color: colors.muted, fontFamily: fonts.medium, fontSize: 8, marginTop: 2 },
  secureBadge: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 15, height: 32, justifyContent: 'center', marginLeft: 'auto', width: 32 },
  intro: { marginBottom: 25, marginTop: 38 },
  eyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 9, letterSpacing: 1.5 },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 31, letterSpacing: -1, marginTop: 4 },
  subtitle: { color: colors.muted, fontFamily: fonts.medium, fontSize: 12, lineHeight: 19, marginTop: 7, maxWidth: 310 },
  formCard: { backgroundColor: colors.surface, borderColor: 'rgba(16,35,29,0.04)', borderRadius: 24, borderWidth: 1, elevation: 2, padding: 17, shadowColor: '#173B30', shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.06, shadowRadius: 18 },
  rootError: { alignItems: 'flex-start', backgroundColor: '#FFF1F1', borderRadius: 12, flexDirection: 'row', gap: 8, marginBottom: 14, padding: 11 },
  rootErrorText: { color: colors.danger, flex: 1, fontFamily: fonts.medium, fontSize: 10, lineHeight: 15 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, flexDirection: 'row', gap: 8, justifyContent: 'center', minHeight: 55, shadowColor: colors.primary, shadowOffset: { width: 0, height: 7 }, shadowOpacity: 0.2, shadowRadius: 12 },
  buttonPressed: { backgroundColor: colors.primaryDark, transform: [{ scale: 0.99 }] },
  buttonDisabled: { opacity: 0.65 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 14 },
  divider: { alignItems: 'center', flexDirection: 'row', marginVertical: 16 },
  line: { backgroundColor: colors.border, flex: 1, height: 1 },
  or: { color: '#9AA9A3', fontFamily: fonts.bold, fontSize: 8, marginHorizontal: 10 },
  biometricButton: { alignItems: 'center', backgroundColor: colors.surfaceMuted, borderRadius: 15, flexDirection: 'row', padding: 10 },
  biometricPressed: { opacity: 0.72 },
  fingerprint: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 12, height: 40, justifyContent: 'center', marginRight: 10, width: 40 },
  biometricCopy: { flex: 1 },
  biometricTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 11 },
  biometricNote: { color: colors.muted, fontFamily: fonts.regular, fontSize: 8, marginTop: 2 },
  signupRow: { alignItems: 'center', flexDirection: 'row', justifyContent: 'center', marginTop: 24 },
  signupPrompt: { color: colors.muted, fontFamily: fonts.medium, fontSize: 11 },
  signupLink: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 11, marginLeft: 5 },
  trust: { alignItems: 'center', flexDirection: 'row', gap: 5, justifyContent: 'center', marginTop: 15 },
  trustText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 8 },
});
