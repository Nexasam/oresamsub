import { zodResolver } from '@hookform/resolvers/zod';
import { router } from 'expo-router';
import { Controller, useForm } from 'react-hook-form';
import { useEffect, useState } from 'react';
import { ActivityIndicator, KeyboardAvoidingView, Platform, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';
import { z } from 'zod';

import { ApiError } from '../../src/api/client';
import { useAuthStore } from '../../src/auth/authStore';
import { biometricLock } from '../../src/auth/biometricLock';
import { tokenVault } from '../../src/auth/tokenVault';
import { colors } from '../../src/theme/colors';

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
      <View style={styles.content}>
        <View style={styles.mark}><Text style={styles.markText}>O</Text></View>
        <Text style={styles.title}>Welcome back</Text>
        <Text style={styles.subtitle}>Sign in securely to your OresamSub account.</Text>

        <Controller control={control} name="login" render={({ field: { onBlur, onChange, value } }) => (
          <View style={styles.field}>
            <Text style={styles.label}>Email, username or phone</Text>
            <TextInput autoCapitalize="none" autoComplete="username" onBlur={onBlur} onChangeText={onChange} placeholder="you@example.com" style={styles.input} value={value} />
            {errors.login && <Text style={styles.error}>{errors.login.message}</Text>}
          </View>
        )} />

        <Controller control={control} name="password" render={({ field: { onBlur, onChange, value } }) => (
          <View style={styles.field}>
            <Text style={styles.label}>Password</Text>
            <TextInput autoCapitalize="none" autoComplete="current-password" onBlur={onBlur} onChangeText={onChange} placeholder="Your password" secureTextEntry style={styles.input} value={value} />
            {errors.password && <Text style={styles.error}>{errors.password.message}</Text>}
          </View>
        )} />

        {errors.root && <Text style={styles.rootError}>{errors.root.message}</Text>}

        <Pressable disabled={isSubmitting} onPress={submit} style={({ pressed }) => [styles.button, pressed && styles.buttonPressed, isSubmitting && styles.buttonDisabled]}>
          {isSubmitting ? <ActivityIndicator color={colors.white} /> : <Text style={styles.buttonText}>Sign in</Text>}
        </Pressable>

        {biometricReady && <Pressable disabled={biometricBusy} onPress={() => void biometricSignIn()} style={({ pressed }) => [styles.biometricButton, pressed && styles.biometricPressed]}><View style={styles.fingerprint}><Text style={styles.fingerprintText}>◎</Text></View><View><Text style={styles.biometricTitle}>{biometricBusy ? 'Checking fingerprint…' : 'Sign in with fingerprint'}</Text><Text style={styles.biometricNote}>Use your saved secure session</Text></View></Pressable>}

        <Pressable onPress={() => router.push('/(auth)/register')}><Text style={styles.link}>New to OresamSub? Create an account</Text></Pressable>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  screen: { backgroundColor: colors.background, flex: 1 },
  content: { flex: 1, justifyContent: 'center', padding: 26 },
  mark: { alignItems: 'center', backgroundColor: colors.primaryDark, borderRadius: 20, elevation: 8, height: 60, justifyContent: 'center', marginBottom: 26, shadowColor: colors.primaryDark, shadowOffset: { width: 0, height: 8 }, shadowOpacity: 0.2, shadowRadius: 14, width: 60 },
  markText: { color: colors.white, fontSize: 30, fontWeight: '800' },
  title: { color: colors.text, fontSize: 32, fontWeight: '800', letterSpacing: -1 },
  subtitle: { color: colors.muted, fontSize: 14, lineHeight: 21, marginBottom: 32, marginTop: 7 },
  field: { marginBottom: 18 },
  label: { color: colors.text, fontSize: 12, fontWeight: '800', marginBottom: 8 },
  input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, color: colors.text, fontSize: 15, paddingHorizontal: 16, paddingVertical: 15 },
  error: { color: colors.danger, fontSize: 12, marginTop: 6 },
  rootError: { backgroundColor: '#fef2f2', borderRadius: 10, color: colors.danger, marginBottom: 16, padding: 12 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 16, elevation: 5, minHeight: 54, justifyContent: 'center', shadowColor: colors.primary, shadowOffset: { width: 0, height: 7 }, shadowOpacity: 0.18, shadowRadius: 12 },
  buttonPressed: { backgroundColor: colors.primaryDark },
  buttonDisabled: { opacity: 0.65 },
  buttonText: { color: colors.white, fontSize: 16, fontWeight: '700' },
  biometricButton: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, flexDirection: 'row', marginTop: 13, padding: 12 },
  biometricPressed: { opacity: 0.7 },
  fingerprint: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 14, height: 40, justifyContent: 'center', marginRight: 11, width: 40 },
  fingerprintText: { color: colors.primary, fontSize: 24, fontWeight: '800' },
  biometricTitle: { color: colors.text, fontSize: 12, fontWeight: '800' },
  biometricNote: { color: colors.muted, fontSize: 9, marginTop: 3 },
  link: { color: colors.primary, fontSize: 14, fontWeight: '600', marginTop: 22, textAlign: 'center' },
});
