import { zodResolver } from '@hookform/resolvers/zod';
import { router } from 'expo-router';
import { Controller, useForm } from 'react-hook-form';
import { useState } from 'react';
import { ActivityIndicator, KeyboardAvoidingView, Platform, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { z } from 'zod';

import { ApiError } from '../../src/api/client';
import { useAuthStore } from '../../src/auth/authStore';
import { AuthField } from '../../src/components/AuthField';
import { BrandLogo } from '../../src/components/BrandLogo';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { colors, fonts } from '../../src/theme/colors';

const schema = z.object({
  first_name: z.string().trim().min(2, 'Enter your first name.'),
  last_name: z.string().trim().min(2, 'Enter your last name.'),
  username: z.string().trim().min(3, 'Username must have at least 3 characters.').regex(/^[a-zA-Z0-9_-]+$/, 'Use only letters, numbers, dashes and underscores.'),
  email: z.email('Enter a valid email address.'),
  password: z.string().min(8, 'Password must have at least 8 characters.'),
  password_confirmation: z.string(),
  referral_phone_number: z.string(),
}).refine((values) => values.password === values.password_confirmation, { path: ['password_confirmation'], message: 'Passwords do not match.' });

type FormValues = z.infer<typeof schema>;

export default function RegisterScreen() {
  const register = useAuthStore((state) => state.register);
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [referralOpen, setReferralOpen] = useState(false);
  const { control, handleSubmit, setError, formState: { errors, isSubmitting } } = useForm<FormValues>({
    resolver: zodResolver(schema),
    defaultValues: { first_name: '', last_name: '', username: '', email: '', password: '', password_confirmation: '', referral_phone_number: '' },
  });

  const submit = handleSubmit(async (values) => {
    try {
      const result = await register({ ...values, referral_phone_number: values.referral_phone_number || undefined });
      router.replace({ pathname: '/(auth)/verify-email', params: { email: result.email } });
    } catch (error) {
      if (error instanceof ApiError && error.errors) Object.entries(error.errors).forEach(([name, messages]) => setError(name as keyof FormValues, { message: messages[0] }));
      else setError('root', { message: error instanceof ApiError ? error.message : 'Unable to create your account.' });
    }
  });

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={styles.screen}>
      <ScrollView
        automaticallyAdjustKeyboardInsets={Platform.OS === 'ios'}
        contentContainerStyle={styles.content}
        keyboardDismissMode={Platform.OS === 'ios' ? 'interactive' : 'on-drag'}
        keyboardShouldPersistTaps="handled"
      >
        <View style={styles.topRow}>
          <Pressable hitSlop={10} onPress={() => router.back()} style={styles.back}>
            <MaterialIcon color={colors.text} name="arrow_back" size={21} />
          </Pressable>
          <BrandLogo size={42} />
        </View>

        <Text style={styles.title}>Create your account</Text>
        <Text style={styles.subtitle}>One account for airtime, data and everyday payments.</Text>

        <View style={styles.formCard}>
          <SectionTitle icon="person" title="Your details" />
          <View style={styles.nameRow}>
            <Controller control={control} name="first_name" render={({ field }) => (
              <AuthField autoCapitalize="words" error={errors.first_name?.message} icon="badge" label="First name" onBlur={field.onBlur} onChangeText={field.onChange} placeholder="First name" value={field.value} wrapperStyle={styles.half} />
            )} />
            <Controller control={control} name="last_name" render={({ field }) => (
              <AuthField autoCapitalize="words" error={errors.last_name?.message} icon="badge" label="Last name" onBlur={field.onBlur} onChangeText={field.onChange} placeholder="Last name" value={field.value} wrapperStyle={styles.half} />
            )} />
          </View>

          <Controller control={control} name="email" render={({ field }) => (
            <AuthField autoCapitalize="none" autoComplete="email" error={errors.email?.message} icon="mail" keyboardType="email-address" label="Email address" onBlur={field.onBlur} onChangeText={field.onChange} placeholder="you@example.com" value={field.value} />
          )} />
          <Controller control={control} name="username" render={({ field }) => (
            <AuthField autoCapitalize="none" autoComplete="username-new" error={errors.username?.message} icon="alternate_email" label="Choose a username" onBlur={field.onBlur} onChangeText={field.onChange} placeholder="e.g. adebsholey" value={field.value} />
          )} />

          <View style={styles.sectionDivider} />
          <SectionTitle icon="lock" title="Secure your account" />
          <Controller control={control} name="password" render={({ field }) => (
            <AuthField autoCapitalize="none" autoComplete="new-password" error={errors.password?.message} icon="lock" label="Password" onBlur={field.onBlur} onChangeText={field.onChange} onToggleSecure={() => setPasswordVisible((visible) => !visible)} placeholder="At least 8 characters" secureTextEntry={!passwordVisible} value={field.value} />
          )} />
          <Controller control={control} name="password_confirmation" render={({ field }) => (
            <AuthField autoCapitalize="none" autoComplete="new-password" error={errors.password_confirmation?.message} icon="lock_reset" label="Confirm password" onBlur={field.onBlur} onChangeText={field.onChange} onToggleSecure={() => setPasswordVisible((visible) => !visible)} placeholder="Enter password again" secureTextEntry={!passwordVisible} value={field.value} />
          )} />

          {!referralOpen ? (
            <Pressable onPress={() => setReferralOpen(true)} style={styles.referralToggle}>
              <MaterialIcon color={colors.primary} name="redeem" size={19} />
              <Text style={styles.referralToggleText}>I have a referral phone number</Text>
              <MaterialIcon color={colors.muted} name="add" size={18} />
            </Pressable>
          ) : (
            <Controller control={control} name="referral_phone_number" render={({ field }) => (
              <AuthField error={errors.referral_phone_number?.message} icon="redeem" keyboardType="phone-pad" label="Referral phone (optional)" onBlur={field.onBlur} onChangeText={field.onChange} placeholder="0803 000 0000" value={field.value} />
            )} />
          )}

          {errors.root && <View style={styles.rootError}><MaterialIcon color={colors.danger} name="error" size={18} /><Text style={styles.rootErrorText}>{errors.root.message}</Text></View>}

          <Pressable disabled={isSubmitting} onPress={submit} style={({ pressed }) => [styles.button, pressed && styles.buttonPressed, isSubmitting && styles.buttonDisabled]}>
            {isSubmitting ? <ActivityIndicator color={colors.white} /> : <><Text style={styles.buttonText}>Create account</Text><MaterialIcon color={colors.white} name="arrow_forward" size={19} /></>}
          </Pressable>
          <Text style={styles.terms}>By continuing, you agree to our Terms and acknowledge our Privacy Policy.</Text>
        </View>

        <View style={styles.signinRow}><Text style={styles.signinPrompt}>Already have an account?</Text><Pressable onPress={() => router.back()}><Text style={styles.signinLink}>Sign in</Text></Pressable></View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}

function SectionTitle({ icon, title }: { icon: string; title: string }) {
  return <View style={styles.sectionTitle}><View style={styles.sectionIcon}><MaterialIcon color={colors.primaryDark} name={icon} size={17} /></View><Text style={styles.sectionTitleText}>{title}</Text></View>;
}

const styles = StyleSheet.create({
  screen: { backgroundColor: colors.background, flex: 1 },
  content: { paddingBottom: 42, paddingHorizontal: 20, paddingTop: Platform.OS === 'android' ? 38 : 54 },
  topRow: { alignItems: 'center', flexDirection: 'row', justifyContent: 'space-between' },
  back: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 15, borderWidth: 1, height: 42, justifyContent: 'center', width: 42 },
  step: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 16, flexDirection: 'row', gap: 6, paddingHorizontal: 10, paddingVertical: 7 },
  stepText: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 8, letterSpacing: 0.8 },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 29, letterSpacing: -0.9, marginTop: 28 },
  subtitle: { color: colors.muted, fontFamily: fonts.medium, fontSize: 12, lineHeight: 18, marginBottom: 24, marginTop: 7 },
  formCard: { backgroundColor: colors.surface, borderColor: 'rgba(16,35,29,0.04)', borderRadius: 24, borderWidth: 1, padding: 16 },
  sectionTitle: { alignItems: 'center', flexDirection: 'row', marginBottom: 17 },
  sectionIcon: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 11, height: 32, justifyContent: 'center', marginRight: 9, width: 32 },
  sectionTitleText: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 13 },
  nameRow: { flexDirection: 'row', gap: 10 },
  half: { flex: 1 },
  sectionDivider: { backgroundColor: colors.border, height: 1, marginBottom: 18, marginTop: 3 },
  referralToggle: { alignItems: 'center', backgroundColor: colors.surfaceMuted, borderRadius: 14, flexDirection: 'row', gap: 8, marginBottom: 16, padding: 12 },
  referralToggleText: { color: colors.primaryDark, flex: 1, fontFamily: fonts.bold, fontSize: 9 },
  rootError: { alignItems: 'flex-start', backgroundColor: '#FFF1F1', borderRadius: 12, flexDirection: 'row', gap: 8, marginBottom: 14, padding: 11 },
  rootErrorText: { color: colors.danger, flex: 1, fontFamily: fonts.medium, fontSize: 10, lineHeight: 15 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, flexDirection: 'row', gap: 8, justifyContent: 'center', minHeight: 56, shadowColor: colors.primary, shadowOffset: { width: 0, height: 7 }, shadowOpacity: 0.18, shadowRadius: 12 },
  buttonPressed: { backgroundColor: colors.primaryDark, transform: [{ scale: 0.99 }] },
  buttonDisabled: { opacity: 0.65 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 14 },
  terms: { color: colors.muted, fontFamily: fonts.regular, fontSize: 8, lineHeight: 13, marginTop: 12, paddingHorizontal: 12, textAlign: 'center' },
  signinRow: { alignItems: 'center', flexDirection: 'row', justifyContent: 'center', marginTop: 22 },
  signinPrompt: { color: colors.muted, fontFamily: fonts.medium, fontSize: 11 },
  signinLink: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 11, marginLeft: 5 },
});
