import { zodResolver } from '@hookform/resolvers/zod';
import { router } from 'expo-router';
import { Controller, useForm } from 'react-hook-form';
import { ActivityIndicator, KeyboardAvoidingView, Platform, Pressable, ScrollView, StyleSheet, Text, TextInput, View } from 'react-native';
import { z } from 'zod';

import { ApiError } from '../../src/api/client';
import { useAuthStore } from '../../src/auth/authStore';
import { colors } from '../../src/theme/colors';

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
const fields: Array<{ name: keyof FormValues; label: string; secure?: boolean; keyboard?: 'email-address' | 'phone-pad' | 'default' }> = [
  { name: 'first_name', label: 'First name' }, { name: 'last_name', label: 'Last name' }, { name: 'username', label: 'Username' },
  { name: 'email', label: 'Email address', keyboard: 'email-address' }, { name: 'password', label: 'Password', secure: true },
  { name: 'password_confirmation', label: 'Confirm password', secure: true }, { name: 'referral_phone_number', label: 'Referral phone (optional)', keyboard: 'phone-pad' },
];

export default function RegisterScreen() {
  const register = useAuthStore((state) => state.register);
  const { control, handleSubmit, setError, formState: { errors, isSubmitting } } = useForm<FormValues>({ resolver: zodResolver(schema), defaultValues: { first_name: '', last_name: '', username: '', email: '', password: '', password_confirmation: '', referral_phone_number: '' } });
  const submit = handleSubmit(async (values) => {
    try {
      await register({ ...values, referral_phone_number: values.referral_phone_number || undefined });
      router.replace('/(onboarding)/phone');
    } catch (error) {
      if (error instanceof ApiError && error.errors) Object.entries(error.errors).forEach(([name, messages]) => setError(name as keyof FormValues, { message: messages[0] }));
      else setError('root', { message: error instanceof ApiError ? error.message : 'Unable to create your account.' });
    }
  });

  return <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.screen}><ScrollView contentContainerStyle={styles.content} keyboardShouldPersistTaps="handled"><Text style={styles.title}>Create your account</Text><Text style={styles.subtitle}>Get started with secure telecom services.</Text>{fields.map((field) => <Controller key={field.name} control={control} name={field.name} render={({ field: input }) => <View style={styles.field}><Text style={styles.label}>{field.label}</Text><TextInput autoCapitalize="none" keyboardType={field.keyboard} onBlur={input.onBlur} onChangeText={input.onChange} secureTextEntry={field.secure} style={styles.input} value={input.value} />{errors[field.name] && <Text style={styles.error}>{errors[field.name]?.message}</Text>}</View>} />)}{errors.root && <Text style={styles.rootError}>{errors.root.message}</Text>}<Pressable disabled={isSubmitting} onPress={submit} style={styles.button}>{isSubmitting ? <ActivityIndicator color={colors.white} /> : <Text style={styles.buttonText}>Create account</Text>}</Pressable><Pressable onPress={() => router.back()}><Text style={styles.link}>Already registered? Sign in</Text></Pressable></ScrollView></KeyboardAvoidingView>;
}

const styles = StyleSheet.create({ screen: { backgroundColor: colors.background, flex: 1 }, content: { padding: 24, paddingBottom: 52, paddingTop: 54 }, title: { color: colors.text, fontSize: 31, fontWeight: '800', letterSpacing: -0.9 }, subtitle: { color: colors.muted, fontSize: 14, lineHeight: 21, marginBottom: 28, marginTop: 7 }, field: { marginBottom: 15 }, label: { color: colors.text, fontSize: 12, fontWeight: '800', marginBottom: 7 }, input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, color: colors.text, fontSize: 15, paddingHorizontal: 16, paddingVertical: 15 }, error: { color: colors.danger, fontSize: 12, marginTop: 5 }, rootError: { color: colors.danger, marginBottom: 14 }, button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 16, elevation: 5, minHeight: 54, justifyContent: 'center', shadowColor: colors.primary, shadowOffset: { width: 0, height: 7 }, shadowOpacity: 0.18, shadowRadius: 12 }, buttonText: { color: colors.white, fontSize: 15, fontWeight: '800' }, link: { color: colors.primary, fontSize: 13, fontWeight: '700', marginTop: 22, textAlign: 'center' } });
