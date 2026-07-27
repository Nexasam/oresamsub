import { useQuery } from '@tanstack/react-query';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { ActivityIndicator, Pressable, StyleSheet, Text, View } from 'react-native';
import { mobileApi } from '../../src/api/mobileApi';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { Screen } from '../../src/components/Screen';
import { colors, fonts } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);

export default function TransactionDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const query = useQuery({ queryKey: ['transaction', id], queryFn: () => mobileApi.transaction(id) });
  const buyAgain = () => {
    const repeat = query.data?.repeat_purchase;
    if (!repeat) return;
    router.push({ pathname: '/checkout', params: { product: repeat.product, planId: repeat.plan_id, planName: repeat.plan_name, price: String(repeat.price), provider: repeat.provider } });
  };
  return <><Stack.Screen options={{ headerShown: true, title: 'Transaction details' }} /><Screen>{query.isPending ? <ActivityIndicator color={colors.primary} style={styles.loading} /> : query.isError ? <Text style={styles.empty}>This transaction could not be loaded.</Text> : query.data && <><View style={styles.hero}><Text style={styles.amount}>{money(query.data.amount)}</Text><Text style={[styles.status, query.data.status === 'successful' ? styles.good : query.data.status === 'failed' ? styles.bad : styles.wait]}>{query.data.status}</Text></View><View style={styles.card}><Row label="Description" value={query.data.description} /><Row label="Beneficiary" value={query.data.beneficiary ?? '—'} /><Row label="Category" value={query.data.category ?? '—'} /><Row label="Date" value={new Date(query.data.created_at).toLocaleString()} /><Row label="Reference" value={query.data.id} /></View>{query.data.repeat_purchase && <View style={styles.repeatCard}><View style={styles.repeatIcon}><MaterialIcon color={colors.primary} name="replay" size={23} /></View><View style={styles.repeatCopy}><Text style={styles.repeatTitle}>Need the same data plan?</Text><Text style={styles.repeatText}>We’ll reuse only the plan. You must intentionally enter or select the new beneficiary before paying.</Text></View><Pressable onPress={buyAgain} style={styles.repeatButton}><Text style={styles.repeatButtonText}>Buy again</Text><MaterialIcon color={colors.white} name="arrow_forward" size={18} /></Pressable></View>}{query.data.message && <Text style={styles.message}>{query.data.message}</Text>}</>}</Screen></>;
}

function Row({ label, value }: { label: string; value: string }) { return <View style={styles.row}><Text style={styles.label}>{label}</Text><Text selectable style={styles.value}>{value}</Text></View>; }
const styles = StyleSheet.create({ loading: { marginTop: 60 }, empty: { color: colors.muted, marginTop: 60, textAlign: 'center' }, hero: { alignItems: 'center', paddingVertical: 28 }, amount: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 32 }, status: { fontFamily: fonts.bold, fontSize: 13, marginTop: 8, textTransform: 'capitalize' }, good: { color: colors.success }, bad: { color: colors.danger }, wait: { color: colors.warning }, card: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, padding: 16 }, row: { borderBottomColor: colors.border, borderBottomWidth: 1, paddingVertical: 12 }, label: { color: colors.muted, fontFamily: fonts.semiBold, fontSize: 11, textTransform: 'uppercase' }, value: { color: colors.text, fontFamily: fonts.bold, marginTop: 5 }, repeatCard: { backgroundColor: colors.primarySoft, borderRadius: 18, marginTop: 18, padding: 16 }, repeatIcon: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 12, height: 42, justifyContent: 'center', marginBottom: 12, width: 42 }, repeatCopy: { marginBottom: 14 }, repeatTitle: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 15 }, repeatText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, lineHeight: 17, marginTop: 4 }, repeatButton: { alignItems: 'center', alignSelf: 'flex-start', backgroundColor: colors.primary, borderRadius: 12, flexDirection: 'row', gap: 7, paddingHorizontal: 15, paddingVertical: 11 }, repeatButtonText: { color: colors.white, fontFamily: fonts.bold, fontSize: 12 }, message: { color: colors.muted, marginTop: 18, textAlign: 'center' } });
