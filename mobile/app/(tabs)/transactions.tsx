import { useInfiniteQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { ActivityIndicator, FlatList, Pressable, RefreshControl, StyleSheet, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { mobileApi } from '../../src/api/mobileApi';
import type { MobileTransaction } from '../../src/api/types';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { colors, fonts } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);

export default function TransactionsScreen() {
  const query = useInfiniteQuery({
    queryKey: ['transactions'],
    initialPageParam: 1,
    queryFn: ({ pageParam }) => mobileApi.transactions(pageParam),
    getNextPageParam: (last) => last.meta.current_page < last.meta.last_page
      ? last.meta.current_page + 1
      : undefined,
  });
  const items = query.data?.pages.flatMap((page) => page.items) ?? [];
  const total = query.data?.pages[0]?.meta.total ?? items.length;
  const successfulShown = items.filter((item) => item.status === 'successful').length;
  const pendingShown = items.filter((item) => item.status === 'pending' || item.status === 'processing').length;

  return (
    <SafeAreaView edges={['top']} style={styles.safe}>
      <FlatList
        ListHeaderComponent={(
          <>
            <View style={styles.hero}>
              <View style={styles.heroOrbLarge} />
              <View style={styles.heroOrbSmall} />
              <View style={styles.heroTop}>
                <View style={styles.heroBadge}>
                  <MaterialIcon color="#BFEBDC" name="receipt_long" size={15} />
                  <Text style={styles.heroBadgeText}>TRANSACTION HISTORY</Text>
                </View>
                <Pressable
                  accessibilityLabel="Go to dashboard"
                  onPress={() => router.replace('/(tabs)')}
                  style={({ pressed }) => [styles.homeButton, pressed && styles.pressed]}
                >
                  <MaterialIcon color={colors.white} name="home" size={17} />
                  <Text style={styles.homeButtonText}>Home</Text>
                </Pressable>
              </View>
              <Text style={styles.heroTitle}>Your payments, at a glance.</Text>
              <Text style={styles.heroText}>Track every purchase and its current delivery status.</Text>
              <View style={styles.summaryRow}>
                <SummaryChip label="Total" value={total} />
                <SummaryChip label="Successful" value={successfulShown} />
                <SummaryChip label="Pending" value={pendingShown} />
              </View>
            </View>
            <View style={styles.sectionHeader}>
              <View>
                <Text style={styles.sectionEyebrow}>PAYMENTS</Text>
                <Text style={styles.sectionTitle}>Recent transactions</Text>
              </View>
              <View style={styles.secureMark}>
                <MaterialIcon color={colors.primary} name="verified_user" size={15} />
                <Text style={styles.secureText}>Protected</Text>
              </View>
            </View>
          </>
        )}
        contentContainerStyle={styles.list}
        data={items}
        keyExtractor={(item) => item.id}
        ListEmptyComponent={query.isPending ? (
          <ActivityIndicator color={colors.primary} style={styles.loading} />
        ) : query.isError ? (
          <Pressable onPress={() => void query.refetch()} style={styles.emptyCard}>
            <MaterialIcon color={colors.danger} name="refresh" size={25} />
            <Text style={styles.emptyTitle}>Couldn’t load transactions</Text>
            <Text style={styles.empty}>Tap here to try again.</Text>
          </Pressable>
        ) : (
          <View style={styles.emptyCard}>
            <MaterialIcon color={colors.primary} name="receipt_long" size={28} />
            <Text style={styles.emptyTitle}>No transactions yet</Text>
            <Text style={styles.empty}>Your completed purchases will appear here.</Text>
          </View>
        )}
        ListFooterComponent={query.isFetchingNextPage ? <ActivityIndicator color={colors.primary} style={styles.footerLoader} /> : null}
        onEndReached={() => query.hasNextPage && !query.isFetchingNextPage && void query.fetchNextPage()}
        onEndReachedThreshold={0.4}
        refreshControl={(
          <RefreshControl
            onRefresh={() => void query.refetch()}
            refreshing={query.isRefetching}
            tintColor={colors.primary}
          />
        )}
        renderItem={({ item }) => <TransactionRow item={item} />}
      />
    </SafeAreaView>
  );
}

function SummaryChip({ label, value }: { label: string; value: number }) {
  return (
    <View style={styles.summaryChip}>
      <Text style={styles.summaryValue}>{value}</Text>
      <Text style={styles.summaryLabel}>{label}</Text>
    </View>
  );
}

function TransactionRow({ item }: { item: MobileTransaction }) {
  const visual = transactionVisual(item);
  const refunded = item.status === 'refunded';
  return (
    <Pressable
      onPress={() => router.push({ pathname: '/transaction/[id]', params: { id: item.id } })}
      style={({ pressed }) => [styles.row, pressed && styles.pressed]}
    >
      <View style={[styles.icon, { backgroundColor: visual.background }]}>
        <MaterialIcon color={visual.color} name={visual.serviceIcon} size={21} />
      </View>
      <View style={styles.copy}>
        <Text numberOfLines={1} style={styles.name}>{item.description}</Text>
        <View style={styles.recipientRow}>
          <MaterialIcon color={colors.muted} name="person" size={12} />
          <Text numberOfLines={1} style={styles.beneficiary}>{item.beneficiary ?? 'No beneficiary recorded'}</Text>
        </View>
        <Text style={styles.meta}>{new Date(item.created_at).toLocaleString()}</Text>
      </View>
      <View style={styles.amountCopy}>
        <Text style={[styles.amount, { color: visual.color }]}>
          {refunded ? '+' : '−'}{money(item.amount)}
        </Text>
        <View style={[styles.badge, { backgroundColor: visual.background }]}>
          <MaterialIcon color={visual.color} name={visual.statusIcon} size={11} />
          <Text style={[styles.badgeText, { color: visual.color }]}>{item.status}</Text>
        </View>
        <MaterialIcon color={colors.muted} name="chevron_right" size={16} />
      </View>
    </Pressable>
  );
}

function transactionVisual(item: MobileTransaction) {
  const serviceIcon = item.category === 'data'
    ? 'signal_cellular_alt'
    : item.category === 'airtime'
      ? 'phone_in_talk'
      : item.category === 'cable_subscription'
        ? 'live_tv'
        : item.category === 'utility_bills'
          ? 'bolt'
          : 'payments';
  if (item.status === 'successful') {
    return { background: '#E9F9F1', color: colors.success, serviceIcon, statusIcon: 'check_circle' };
  }
  if (item.status === 'failed') {
    return { background: '#FFF0F0', color: colors.danger, serviceIcon, statusIcon: 'cancel' };
  }
  if (item.status === 'refunded') {
    return { background: '#EAF2FF', color: '#2563EB', serviceIcon, statusIcon: 'replay' };
  }
  return { background: '#FFF7E5', color: colors.warning, serviceIcon, statusIcon: 'schedule' };
}

const styles = StyleSheet.create({
  safe: { backgroundColor: colors.background, flex: 1 },
  list: { flexGrow: 1, gap: 9, padding: 20, paddingBottom: 112 },
  hero: { backgroundColor: colors.primaryDark, borderRadius: 22, minHeight: 210, overflow: 'hidden', padding: 17 },
  heroOrbLarge: { backgroundColor: '#159570', borderRadius: 120, height: 210, opacity: 0.32, position: 'absolute', right: -72, top: -76, width: 210 },
  heroOrbSmall: { backgroundColor: colors.accent, borderRadius: 35, bottom: -28, height: 70, opacity: 0.2, position: 'absolute', right: 76, width: 70 },
  heroTop: { alignItems: 'center', flexDirection: 'row', justifyContent: 'space-between' },
  heroBadge: { alignItems: 'center', backgroundColor: 'rgba(255,255,255,0.11)', borderColor: 'rgba(255,255,255,0.14)', borderRadius: 20, borderWidth: 1, flexDirection: 'row', gap: 6, paddingHorizontal: 9, paddingVertical: 6 },
  heroBadgeText: { color: '#D8F4EB', fontFamily: fonts.extraBold, fontSize: 8, letterSpacing: 1.1 },
  homeButton: { alignItems: 'center', backgroundColor: 'rgba(255,255,255,0.12)', borderRadius: 14, flexDirection: 'row', gap: 4, paddingHorizontal: 9, paddingVertical: 6 },
  homeButtonText: { color: colors.white, fontFamily: fonts.bold, fontSize: 9 },
  heroTitle: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 22, letterSpacing: -0.7, marginTop: 15 },
  heroText: { color: '#BFE0D5', fontFamily: fonts.medium, fontSize: 10, lineHeight: 15, marginTop: 5, maxWidth: '88%' },
  summaryRow: { flexDirection: 'row', gap: 7, marginTop: 15 },
  summaryChip: { backgroundColor: 'rgba(255,255,255,0.11)', borderColor: 'rgba(255,255,255,0.12)', borderRadius: 13, borderWidth: 1, flex: 1, paddingHorizontal: 9, paddingVertical: 8 },
  summaryValue: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 14 },
  summaryLabel: { color: '#CBE8DF', fontFamily: fonts.medium, fontSize: 7, marginTop: 2, textTransform: 'uppercase' },
  sectionHeader: { alignItems: 'flex-end', flexDirection: 'row', justifyContent: 'space-between', marginBottom: 4, marginTop: 10 },
  sectionEyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 9, letterSpacing: 1.4 },
  sectionTitle: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 18, letterSpacing: -0.5, marginTop: 2 },
  secureMark: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 15, flexDirection: 'row', gap: 4, paddingHorizontal: 9, paddingVertical: 6 },
  secureText: { color: colors.primaryDark, fontFamily: fonts.bold, fontSize: 8 },
  row: { alignItems: 'center', backgroundColor: colors.surface, borderColor: 'rgba(16,35,29,0.045)', borderRadius: 18, borderWidth: 1, elevation: 2, flexDirection: 'row', padding: 13, shadowColor: '#193E33', shadowOffset: { width: 0, height: 5 }, shadowOpacity: 0.05, shadowRadius: 10 },
  icon: { alignItems: 'center', borderRadius: 14, height: 44, justifyContent: 'center', marginRight: 11, width: 44 },
  copy: { flex: 1, marginRight: 8 },
  name: { color: colors.text, fontFamily: fonts.bold, fontSize: 12 },
  recipientRow: { alignItems: 'center', flexDirection: 'row', gap: 3, marginTop: 4 },
  beneficiary: { color: colors.text, flex: 1, fontFamily: fonts.semiBold, fontSize: 9 },
  meta: { color: colors.muted, fontFamily: fonts.regular, fontSize: 8, marginTop: 3 },
  amountCopy: { alignItems: 'flex-end' },
  amount: { fontFamily: fonts.extraBold, fontSize: 10, textAlign: 'right' },
  badge: { alignItems: 'center', borderRadius: 10, flexDirection: 'row', gap: 3, marginTop: 6, paddingHorizontal: 6, paddingVertical: 4 },
  badgeText: { fontFamily: fonts.bold, fontSize: 7, textTransform: 'uppercase' },
  pressed: { opacity: 0.75, transform: [{ scale: 0.985 }] },
  loading: { marginTop: 40 },
  footerLoader: { marginVertical: 16 },
  emptyCard: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 20, marginTop: 8, padding: 34 },
  emptyTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 13, marginTop: 10 },
  empty: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10, marginTop: 4, textAlign: 'center' },
});
