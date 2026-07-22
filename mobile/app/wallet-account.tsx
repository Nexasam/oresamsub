import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { Stack, router } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../src/api/client';
import { mobileApi } from '../src/api/mobileApi';
import { Screen } from '../src/components/Screen';
import { colors } from '../src/theme/colors';

export default function WalletAccountScreen() {
  const options = useQuery({ queryKey: ['funding-options'], queryFn: mobileApi.fundingOptions });
  const [selection, setSelection] = useState<{ optionId: string; bankCode: string } | null>(null);
  const [pin, setPin] = useState('');
  const client = useQueryClient();
  const mutation = useMutation({ mutationFn: () => mobileApi.createWalletAccount({ funding_option_id: selection!.optionId, bank_code: selection!.bankCode, pin }), onSuccess: async () => { await client.invalidateQueries({ queryKey: ['wallet-accounts'] }); router.back(); } });
  const error = mutation.error instanceof ApiError ? mutation.error.message : null;
  return <><Stack.Screen options={{ headerShown: true, title: 'Generate bank account' }} /><Screen><Text style={styles.title}>Choose a bank</Text><Text style={styles.subtitle}>Transfers to the generated account fund your OresamSub wallet.</Text>{options.isPending ? <ActivityIndicator color={colors.primary} style={styles.loading} /> : <View style={styles.list}>{options.data?.flatMap((option) => option.banks.map((bank) => { const active = selection?.optionId === option.id && selection.bankCode === bank.code; return <Pressable key={`${option.id}:${bank.code}`} onPress={() => setSelection({ optionId: option.id, bankCode: bank.code })} style={[styles.option, active && styles.active]}><Text style={styles.optionTitle}>{option.name}</Text><Text style={styles.optionText}>{bank.description ?? bank.code}</Text></Pressable>; }))}</View>}{selection && <><Text style={styles.label}>Transaction PIN</Text><TextInput keyboardType="number-pad" maxLength={5} onChangeText={setPin} secureTextEntry style={styles.input} value={pin} />{error && <Text style={styles.error}>{error}</Text>}<Pressable disabled={pin.length < 4 || mutation.isPending} onPress={() => mutation.mutate()} style={[styles.button, (pin.length < 4 || mutation.isPending) && styles.dim]}><Text style={styles.buttonText}>{mutation.isPending ? 'Generating…' : 'Generate account'}</Text></Pressable></>}</Screen></>;
}
const styles = StyleSheet.create({ title: { color: colors.text, fontSize: 25, fontWeight: '800' }, subtitle: { color: colors.muted, lineHeight: 20, marginTop: 6 }, loading: { marginTop: 45 }, list: { gap: 10, marginTop: 22 }, option: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 14, borderWidth: 1, padding: 16 }, active: { borderColor: colors.primary, borderWidth: 2 }, optionTitle: { color: colors.text, fontWeight: '800' }, optionText: { color: colors.muted, fontSize: 12, marginTop: 4 }, label: { color: colors.text, fontWeight: '700', marginBottom: 7, marginTop: 24 }, input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, fontSize: 18, padding: 14 }, error: { color: colors.danger, marginTop: 12 }, button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 13, marginTop: 20, padding: 15 }, dim: { opacity: 0.5 }, buttonText: { color: colors.white, fontWeight: '800' } });
