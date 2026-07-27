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
    router.push({
      pathname: '/checkout',
      params: {
        planId: repeat.plan_id,
        planName: repeat.plan_name,
        price: String(repeat.price),
        product: repeat.product,
        provider: repeat.provider,
      },
    });
  };

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: 'Transaction details' }} />
      <Screen>
        {query.isPending ? (
          <ActivityIndicator color={colors.primary} style={styles.loading} />
        ) : query.isError ? (
          <Text style={styles.empty}>This transaction could not be loaded.</Text>
        ) : query.data ? (
          <>
            <View style={styles.hero}>
              <View style={styles.heroIcon}>
                <MaterialIcon
                  color={statusColor(query.data.status)}
                  name={query.data.status === 'successful' ? 'check_circle' : query.data.status === 'failed' ? 'cancel' : 'schedule'}
                  size={30}
                />
              </View>
              <Text style={styles.amount}>{money(query.data.amount)}</Text>
              <Text style={[styles.status, { color: statusColor(query.data.status) }]}>{query.data.status}</Text>
            </View>

            {query.data.plan ? (
              <View style={styles.planCard}>
                <View style={styles.planIcon}>
                  <MaterialIcon color={colors.primary} name={serviceIcon(query.data.category)} size={25} />
                </View>
                <View style={styles.planCopy}>
                  <Text style={styles.planEyebrow}>PURCHASED PLAN</Text>
                  <Text style={styles.planName}>{query.data.plan.name}</Text>
                  {query.data.plan.provider ? <Text style={styles.provider}>{query.data.plan.provider}</Text> : null}
                </View>
                <View style={styles.planFacts}>
                  {query.data.plan.data_size_mb ? (
                    <Fact icon="database" label={formatDataSize(query.data.plan.data_size_mb)} />
                  ) : null}
                  {query.data.plan.validity_days ? (
                    <Fact icon="calendar_month" label={`${query.data.plan.validity_days} day${query.data.plan.validity_days === 1 ? '' : 's'}`} />
                  ) : null}
                </View>
              </View>
            ) : null}

            <Text style={styles.sectionTitle}>Payment details</Text>
            <View style={styles.card}>
              <Row label="Description" value={query.data.description} />
              <Row label="Beneficiary" value={query.data.beneficiary ?? '—'} />
              <Row label="Category" value={categoryName(query.data.category)} />
              <Row label="Date" value={new Date(query.data.created_at).toLocaleString()} />
              <Row label="Reference" value={query.data.id} last />
            </View>

            <Pressable onPress={() => router.push({ pathname: '/receipt/[id]', params: { id: query.data.id } })} style={({ pressed }) => [styles.receiptButton, pressed && styles.pressed]}>
              <MaterialIcon color={colors.primaryDark} name="receipt_long" size={20} />
              <Text style={styles.receiptButtonText}>View mobile receipt</Text>
              <MaterialIcon color={colors.primaryDark} name="chevron_right" size={20} />
            </Pressable>

            {query.data.repeat_purchase ? (
              <View style={styles.repeatCard}>
                <View style={styles.repeatIcon}><MaterialIcon color={colors.primary} name="replay" size={23} /></View>
                <View style={styles.repeatCopy}>
                  <Text style={styles.repeatTitle}>Need the same data plan?</Text>
                  <Text style={styles.repeatText}>
                    We’ll reuse only the plan. You must intentionally enter or select the new beneficiary before paying.
                  </Text>
                </View>
                <Pressable onPress={buyAgain} style={({ pressed }) => [styles.repeatButton, pressed && styles.pressed]}>
                  <Text style={styles.repeatButtonText}>Buy again</Text>
                  <MaterialIcon color={colors.white} name="arrow_forward" size={18} />
                </Pressable>
              </View>
            ) : null}
            {query.data.message ? <Text style={styles.message}>{query.data.message}</Text> : null}
          </>
        ) : null}
      </Screen>
    </>
  );
}

function Fact({ icon, label }: { icon: string; label: string }) {
  return (
    <View style={styles.fact}>
      <MaterialIcon color={colors.primaryDark} name={icon} size={13} />
      <Text style={styles.factText}>{label}</Text>
    </View>
  );
}

function Row({ label, last = false, value }: { label: string; last?: boolean; value: string }) {
  return (
    <View style={[styles.row, last && styles.lastRow]}>
      <Text style={styles.label}>{label}</Text>
      <Text selectable style={styles.value}>{value}</Text>
    </View>
  );
}

function formatDataSize(sizeMb: number) {
  if (sizeMb < 1024) return `${sizeMb} MB`;
  const sizeGb = sizeMb / 1024;
  return `${Number.isInteger(sizeGb) ? sizeGb : sizeGb.toFixed(1)} GB`;
}

function categoryName(category: string | null) {
  if (category === 'data') return 'Mobile data';
  if (category === 'airtime') return 'Airtime';
  if (category === 'cable_subscription') return 'Cable TV';
  if (category === 'utility_bills') return 'Electricity';
  return category?.replaceAll('_', ' ') ?? '—';
}

function serviceIcon(category: string | null) {
  if (category === 'data') return 'signal_cellular_alt';
  if (category === 'airtime') return 'phone_in_talk';
  if (category === 'cable_subscription') return 'live_tv';
  if (category === 'utility_bills') return 'bolt';
  return 'payments';
}

function statusColor(status: string) {
  if (status === 'successful') return colors.success;
  if (status === 'failed') return colors.danger;
  if (status === 'refunded') return '#2563EB';
  return colors.warning;
}

const styles = StyleSheet.create({
  loading: { marginTop: 60 },
  empty: { color: colors.muted, fontFamily: fonts.regular, marginTop: 60, textAlign: 'center' },
  hero: { alignItems: 'center', paddingBottom: 25, paddingTop: 12 },
  heroIcon: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 27, height: 54, justifyContent: 'center', marginBottom: 11, width: 54 },
  amount: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 32 },
  status: { fontFamily: fonts.bold, fontSize: 12, marginTop: 7, textTransform: 'capitalize' },
  planCard: { alignItems: 'center', backgroundColor: colors.primarySoft, borderColor: '#C7ECDD', borderRadius: 20, borderWidth: 1, flexDirection: 'row', flexWrap: 'wrap', padding: 15 },
  planIcon: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 15, height: 48, justifyContent: 'center', marginRight: 12, width: 48 },
  planCopy: { flex: 1, minWidth: 130 },
  planEyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 8, letterSpacing: 1.1 },
  planName: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 15, marginTop: 3 },
  provider: { color: colors.muted, fontFamily: fonts.medium, fontSize: 9, marginTop: 3 },
  planFacts: { flexDirection: 'row', flexWrap: 'wrap', gap: 6, marginTop: 12, width: '100%' },
  fact: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 11, flexDirection: 'row', gap: 4, paddingHorizontal: 9, paddingVertical: 7 },
  factText: { color: colors.primaryDark, fontFamily: fonts.bold, fontSize: 9 },
  sectionTitle: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 16, marginBottom: 10, marginTop: 22 },
  card: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 18, borderWidth: 1, paddingHorizontal: 16 },
  row: { borderBottomColor: colors.border, borderBottomWidth: 1, paddingVertical: 12 },
  lastRow: { borderBottomWidth: 0 },
  label: { color: colors.muted, fontFamily: fonts.semiBold, fontSize: 9, letterSpacing: 0.6, textTransform: 'uppercase' },
  value: { color: colors.text, fontFamily: fonts.bold, fontSize: 12, marginTop: 5, textTransform: 'capitalize' },
  repeatCard: { backgroundColor: colors.primarySoft, borderRadius: 18, marginTop: 18, padding: 16 },
  receiptButton: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 15, borderWidth: 1, flexDirection: 'row', gap: 9, marginTop: 13, paddingHorizontal: 15, paddingVertical: 14 },
  receiptButtonText: { color: colors.primaryDark, flex: 1, fontFamily: fonts.extraBold, fontSize: 12 },
  repeatIcon: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 12, height: 42, justifyContent: 'center', marginBottom: 12, width: 42 },
  repeatCopy: { marginBottom: 14 },
  repeatTitle: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 15 },
  repeatText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, lineHeight: 17, marginTop: 4 },
  repeatButton: { alignItems: 'center', alignSelf: 'flex-start', backgroundColor: colors.primary, borderRadius: 12, flexDirection: 'row', gap: 7, paddingHorizontal: 15, paddingVertical: 11 },
  repeatButtonText: { color: colors.white, fontFamily: fonts.bold, fontSize: 12 },
  pressed: { opacity: 0.75, transform: [{ scale: 0.98 }] },
  message: { color: colors.muted, fontFamily: fonts.regular, marginTop: 18, textAlign: 'center' },
});
