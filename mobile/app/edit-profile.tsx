import { useMutation } from '@tanstack/react-query';
import { router, Stack } from 'expo-router';
import { useState } from 'react';
import { Pressable, StyleSheet, Text, TextInput } from 'react-native';
import { ApiError } from '../src/api/client';
import { mobileApi } from '../src/api/mobileApi';
import { useAuthStore } from '../src/auth/authStore';
import { Screen } from '../src/components/Screen';
import { colors } from '../src/theme/colors';

export default function EditProfileScreen() {
  const user = useAuthStore((state) => state.user)!; const refresh = useAuthStore((state) => state.refreshSession);
  const [firstName, setFirstName] = useState(user.first_name); const [lastName, setLastName] = useState(user.last_name); const [otherNames, setOtherNames] = useState(user.other_names ?? ''); const [username, setUsername] = useState(user.username); const [landmark, setLandmark] = useState(user.customer_landmark ?? '');
  const mutation = useMutation({ mutationFn: () => mobileApi.updateProfile({ first_name: firstName, last_name: lastName, other_names: otherNames, username, customer_landmark: landmark }), onSuccess: async () => { await refresh(); router.back(); } });
  return <><Stack.Screen options={{ headerShown: true, title: 'Edit profile' }} /><Screen><Field label="First name" value={firstName} onChange={setFirstName} /><Field label="Last name" value={lastName} onChange={setLastName} /><Field label="Other names" value={otherNames} onChange={setOtherNames} /><Field label="Username" value={username} onChange={setUsername} /><Field label="Landmark" value={landmark} onChange={setLandmark} />{mutation.error && <Text style={styles.error}>{mutation.error instanceof ApiError ? mutation.error.message : 'Unable to update profile.'}</Text>}<Pressable disabled={mutation.isPending || !firstName || !lastName || !username} onPress={() => mutation.mutate()} style={[styles.button, mutation.isPending && styles.dim]}><Text style={styles.buttonText}>{mutation.isPending ? 'Saving…' : 'Save profile'}</Text></Pressable></Screen></>;
}
function Field({ label, value, onChange }: { label: string; value: string; onChange: (value: string) => void }) { return <><Text style={styles.label}>{label}</Text><TextInput autoCapitalize={label === 'Username' ? 'none' : 'words'} onChangeText={onChange} style={styles.input} value={value} /></>; }
const styles = StyleSheet.create({ label: { color: colors.text, fontSize: 12, fontWeight: '700', marginBottom: 6, marginTop: 13 }, input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 11, borderWidth: 1, color: colors.text, padding: 13 }, error: { color: colors.danger, marginTop: 12 }, button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 12, marginTop: 24, padding: 15 }, buttonText: { color: colors.white, fontWeight: '800' }, dim: { opacity: 0.5 } });
