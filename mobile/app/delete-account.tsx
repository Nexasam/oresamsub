import { useMutation } from '@tanstack/react-query';
import { Stack } from 'expo-router';
import { useState } from 'react';
import { Alert, Pressable, StyleSheet, Text, TextInput } from 'react-native';
import { ApiError } from '../src/api/client';
import { mobileApi } from '../src/api/mobileApi';
import { useAuthStore } from '../src/auth/authStore';
import { Screen } from '../src/components/Screen';
import { colors } from '../src/theme/colors';

export default function DeleteAccountScreen() {
  const [password, setPassword] = useState(''); const [confirmation, setConfirmation] = useState(''); const declineRestore = useAuthStore((state) => state.declineRestore);
  const mutation = useMutation({ mutationFn: () => mobileApi.deactivateAccount({ password, confirmation: 'DELETE' }), onSuccess: () => { declineRestore(); Alert.alert('Account deactivated', 'Contact support if you need help restoring your account.'); } });
  return <><Stack.Screen options={{ headerShown: true, title: 'Deactivate account' }} /><Screen><Text style={styles.title}>Deactivate your account</Text><Text style={styles.warning}>You will immediately lose access on every device. Your financial records are retained where legally required, and support can explain restoration or deletion timelines.</Text><Text style={styles.label}>Password</Text><TextInput onChangeText={setPassword} secureTextEntry style={styles.input} value={password} /><Text style={styles.label}>Type DELETE to confirm</Text><TextInput autoCapitalize="characters" onChangeText={setConfirmation} style={styles.input} value={confirmation} />{mutation.error && <Text style={styles.error}>{mutation.error instanceof ApiError ? mutation.error.message : 'Unable to deactivate your account.'}</Text>}<Pressable disabled={mutation.isPending || confirmation !== 'DELETE' || !password} onPress={() => mutation.mutate()} style={[styles.button, (confirmation !== 'DELETE' || !password) && styles.dim]}><Text style={styles.buttonText}>{mutation.isPending ? 'Deactivating…' : 'Deactivate account'}</Text></Pressable></Screen></>;
}
const styles = StyleSheet.create({ title: { color: colors.danger, fontSize: 24, fontWeight: '800' }, warning: { color: colors.muted, lineHeight: 21, marginTop: 12 }, label: { color: colors.text, fontWeight: '700', marginBottom: 7, marginTop: 20 }, input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 11, borderWidth: 1, padding: 14 }, error: { color: colors.danger, marginTop: 12 }, button: { alignItems: 'center', backgroundColor: colors.danger, borderRadius: 12, marginTop: 24, padding: 15 }, buttonText: { color: colors.white, fontWeight: '800' }, dim: { opacity: 0.5 } });
