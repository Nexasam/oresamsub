import { useInfiniteQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { ActivityIndicator, FlatList, Pressable, RefreshControl, StyleSheet, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { mobileApi } from '../../src/api/mobileApi';
import type { MobileTransaction } from '../../src/api/types';
import { colors } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);

export default function TransactionsScreen() {
  const query = useInfiniteQuery({ queryKey: ['transactions'], initialPageParam: 1, queryFn: ({ pageParam }) => mobileApi.transactions(pageParam), getNextPageParam: (last) => last.meta.current_page < last.meta.last_page ? last.meta.current_page + 1 : undefined });
  const items = query.data?.pages.flatMap((page) => page.items) ?? [];
  return <SafeAreaView edges={['top']} style={styles.safe}><View style={styles.header}><Text style={styles.title}>Transactions</Text><Text style={styles.subtitle}>Your payments and their current status</Text></View><FlatList data={items} keyExtractor={(item) => item.id} contentContainerStyle={styles.list} refreshControl={<RefreshControl refreshing={query.isRefetching} onRefresh={() => void query.refetch()} tintColor={colors.primary} />} renderItem={({ item }) => <TransactionRow item={item} />} onEndReached={() => query.hasNextPage && !query.isFetchingNextPage && void query.fetchNextPage()} onEndReachedThreshold={0.4} ListEmptyComponent={query.isPending ? <ActivityIndicator color={colors.primary} style={styles.loading} /> : query.isError ? <Pressable onPress={() => void query.refetch()}><Text style={styles.empty}>Could not load transactions. Tap to retry.</Text></Pressable> : <Text style={styles.empty}>No transactions yet.</Text>} ListFooterComponent={query.isFetchingNextPage ? <ActivityIndicator color={colors.primary} /> : null} /></SafeAreaView>;
}

function TransactionRow({ item }: { item: MobileTransaction }) { return <Pressable onPress={() => router.push({ pathname: '/transaction/[id]', params: { id: item.id } })} style={styles.row}><View style={styles.copy}><Text style={styles.name}>{item.description}</Text><Text style={styles.meta}>{new Date(item.created_at).toLocaleString()} · {item.status}</Text></View><View><Text style={styles.amount}>{money(item.amount)}</Text><Text style={[styles.badge, item.status === 'successful' ? styles.good : item.status === 'failed' ? styles.bad : styles.wait]}>{item.status}</Text></View></Pressable>; }

const styles = StyleSheet.create({ safe: { backgroundColor: colors.background, flex: 1 }, header: { paddingHorizontal: 20, paddingTop: 14 }, title: { color: colors.text, fontSize: 30, fontWeight: '800', letterSpacing: -0.8 }, subtitle: { color: colors.muted, fontSize: 13, marginTop: 5 }, list: { flexGrow: 1, gap: 10, padding: 20, paddingBottom: 112 }, row: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 18, elevation: 2, flexDirection: 'row', padding: 16, shadowColor: '#193E33', shadowOffset: { width: 0, height: 5 }, shadowOpacity: 0.05, shadowRadius: 10 }, copy: { flex: 1, marginRight: 12 }, name: { color: colors.text, fontSize: 13, fontWeight: '800' }, meta: { color: colors.muted, fontSize: 10, marginTop: 5, textTransform: 'capitalize' }, amount: { color: colors.text, fontSize: 12, fontWeight: '800', textAlign: 'right' }, badge: { fontSize: 9, fontWeight: '800', marginTop: 5, textAlign: 'right', textTransform: 'uppercase' }, good: { color: colors.success }, bad: { color: colors.danger }, wait: { color: colors.warning }, loading: { marginTop: 50 }, empty: { color: colors.muted, padding: 40, textAlign: 'center' } });
