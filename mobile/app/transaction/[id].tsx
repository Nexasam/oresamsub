import { useQuery } from '@tanstack/react-query';
import { Stack, useLocalSearchParams } from 'expo-router';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';
import { mobileApi } from '../../src/api/mobileApi';
import { Screen } from '../../src/components/Screen';
import { colors } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);

export default function TransactionDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const query = useQuery({ queryKey: ['transaction', id], queryFn: () => mobileApi.transaction(id) });
  return <><Stack.Screen options={{ headerShown: true, title: 'Transaction details' }} /><Screen>{query.isPending ? <ActivityIndicator color={colors.primary} style={styles.loading} /> : query.isError ? <Text style={styles.empty}>This transaction could not be loaded.</Text> : query.data && <><View style={styles.hero}><Text style={styles.amount}>{money(query.data.amount)}</Text><Text style={[styles.status, query.data.status === 'successful' ? styles.good : query.data.status === 'failed' ? styles.bad : styles.wait]}>{query.data.status}</Text></View><View style={styles.card}><Row label="Description" value={query.data.description} /><Row label="Beneficiary" value={query.data.beneficiary ?? '—'} /><Row label="Category" value={query.data.category ?? '—'} /><Row label="Date" value={new Date(query.data.created_at).toLocaleString()} /><Row label="Reference" value={query.data.id} /></View>{query.data.message && <Text style={styles.message}>{query.data.message}</Text>}</>}</Screen></>;
}

function Row({ label, value }: { label: string; value: string }) { return <View style={styles.row}><Text style={styles.label}>{label}</Text><Text selectable style={styles.value}>{value}</Text></View>; }
const styles = StyleSheet.create({ loading: { marginTop: 60 }, empty: { color: colors.muted, marginTop: 60, textAlign: 'center' }, hero: { alignItems: 'center', paddingVertical: 28 }, amount: { color: colors.text, fontSize: 32, fontWeight: '800' }, status: { fontSize: 13, fontWeight: '800', marginTop: 8, textTransform: 'capitalize' }, good: { color: colors.success }, bad: { color: colors.danger }, wait: { color: colors.warning }, card: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, padding: 16 }, row: { borderBottomColor: colors.border, borderBottomWidth: 1, paddingVertical: 12 }, label: { color: colors.muted, fontSize: 11, textTransform: 'uppercase' }, value: { color: colors.text, fontWeight: '700', marginTop: 5 }, message: { color: colors.muted, marginTop: 18, textAlign: 'center' } });
