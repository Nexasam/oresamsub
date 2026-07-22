import { router } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../../src/api/client';
import { authApi } from '../../src/auth/authApi';
import { useAuthStore } from '../../src/auth/authStore';
import { colors } from '../../src/theme/colors';

export default function PinScreen() {
  const refreshSession = useAuthStore((state) => state.refreshSession); const [pin, setPin] = useState(''); const [confirmation, setConfirmation] = useState(''); const [busy, setBusy] = useState(false); const [error, setError] = useState('');
  const submit = async () => { if (pin.length !== 4 || pin !== confirmation) { setError('Enter matching four-digit PINs.'); return; } setBusy(true); setError(''); try { await authApi.setTransactionPin(pin); await refreshSession(); router.replace('/(tabs)'); } catch (reason) { setError(reason instanceof ApiError ? reason.message : 'Unable to create your PIN.'); } finally { setBusy(false); } };
  return <View style={styles.screen}><Text style={styles.title}>Create transaction PIN</Text><Text style={styles.subtitle}>You will use this PIN to authorize purchases. Avoid repeated digits and 1234.</Text><TextInput keyboardType="number-pad" maxLength={4} onChangeText={setPin} placeholder="PIN" secureTextEntry style={styles.input} value={pin} /><TextInput keyboardType="number-pad" maxLength={4} onChangeText={setConfirmation} placeholder="Confirm PIN" secureTextEntry style={[styles.input, styles.second]} value={confirmation} />{error ? <Text style={styles.error}>{error}</Text> : null}<Pressable disabled={busy} onPress={() => void submit()} style={styles.button}>{busy ? <ActivityIndicator color={colors.white} /> : <Text style={styles.buttonText}>Create PIN</Text>}</Pressable></View>;
}
const styles = StyleSheet.create({ screen: { backgroundColor: colors.background, flex: 1, justifyContent: 'center', padding: 24 }, title: { color: colors.text, fontSize: 28, fontWeight: '800' }, subtitle: { color: colors.muted, lineHeight: 21, marginBottom: 24, marginTop: 8 }, input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, fontSize: 20, letterSpacing: 8, padding: 15, textAlign: 'center' }, second: { marginTop: 14 }, error: { color: colors.danger, marginTop: 10 }, button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 12, marginTop: 20, minHeight: 52, justifyContent: 'center' }, buttonText: { color: colors.white, fontWeight: '700' } });
