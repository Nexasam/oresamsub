import { useQuery } from '@tanstack/react-query';
import { Stack, useLocalSearchParams } from 'expo-router';
import { ActivityIndicator, Alert, Pressable, Share, StyleSheet, Text, View } from 'react-native';

import { mobileApi } from '../../src/api/mobileApi';
import type { MobileReceipt } from '../../src/api/types';
import { BrandLogo } from '../../src/components/BrandLogo';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { Screen } from '../../src/components/Screen';
import { colors, fonts } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);

export default function ReceiptScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const query = useQuery({ queryKey: ['receipt', id], queryFn: () => mobileApi.receipt(id) });

  const share = async () => {
    if (!query.data) return;
    try {
      await Share.share({ title: 'OresamSub receipt', message: receiptText(query.data) });
    } catch {
      Alert.alert('Could not share receipt', 'Please try again.');
    }
  };

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: 'Receipt' }} />
      <Screen>
        {query.isPending ? (
          <ActivityIndicator color={colors.primary} style={styles.loading} />
        ) : query.isError ? (
          <Text style={styles.empty}>This receipt could not be loaded.</Text>
        ) : query.data ? (
          <>
            <View style={styles.receipt}>
              <View style={styles.brandRow}>
                <BrandLogo size={43} />
                <View style={styles.brandCopy}>
                  <Text style={styles.brand}>OresamSub</Text>
                  <Text style={styles.eyebrow}>PAYMENT RECEIPT</Text>
                </View>
              </View>
              <View style={styles.divider} />
              <View style={styles.statusIcon}>
                <MaterialIcon color={statusColor(query.data.transaction.status)} name={query.data.transaction.status === 'successful' ? 'check_circle' : query.data.transaction.status === 'failed' ? 'cancel' : 'schedule'} size={33} />
              </View>
              <Text style={styles.amount}>{money(query.data.transaction.amount)}</Text>
              <Text style={[styles.status, { color: statusColor(query.data.transaction.status) }]}>{query.data.transaction.status}</Text>
              <Text style={styles.description}>{query.data.transaction.description}</Text>

              <View style={styles.details}>
                {query.data.transaction.plan?.name ? <Row label="Plan" value={query.data.transaction.plan.name} /> : null}
                {query.data.transaction.plan?.provider ? <Row label="Provider" value={query.data.transaction.plan.provider} /> : null}
                <Row label="Beneficiary" value={query.data.transaction.beneficiary ?? '—'} />
                <Row label="Wallet" value={query.data.wallet} />
                <Row label="Balance before" value={money(query.data.balance_before)} />
                <Row label="Balance after" value={money(query.data.balance_after)} />
                <Row label="Date" value={new Date(query.data.transaction.created_at).toLocaleString()} />
                <Row label="Reference" value={query.data.reference} last />
              </View>
              <Text style={styles.note}>Generated securely by OresamSub</Text>
            </View>
            <Pressable onPress={() => void share()} style={({ pressed }) => [styles.share, pressed && styles.pressed]}>
              <MaterialIcon color={colors.white} name="share" size={19} />
              <Text style={styles.shareText}>Share receipt</Text>
            </Pressable>
          </>
        ) : null}
      </Screen>
    </>
  );
}

function Row({ label, last = false, value }: { label: string; last?: boolean; value: string }) {
  return <View style={[styles.row, last && styles.lastRow]}><Text style={styles.label}>{label}</Text><Text selectable style={styles.value}>{value}</Text></View>;
}

function receiptText(receipt: MobileReceipt) {
  const transaction = receipt.transaction;
  return [
    'ORESAMSUB PAYMENT RECEIPT',
    `Status: ${transaction.status.toUpperCase()}`,
    `Amount: ${money(transaction.amount)}`,
    `Description: ${transaction.description}`,
    transaction.plan?.name ? `Plan: ${transaction.plan.name}` : null,
    `Beneficiary: ${transaction.beneficiary ?? '—'}`,
    `Date: ${new Date(transaction.created_at).toLocaleString()}`,
    `Reference: ${receipt.reference}`,
  ].filter(Boolean).join('\n');
}

function statusColor(status: string) {
  if (status === 'successful') return colors.success;
  if (status === 'failed') return colors.danger;
  return colors.warning;
}

const styles = StyleSheet.create({
  loading: { marginTop: 70 },
  empty: { color: colors.muted, fontFamily: fonts.regular, marginTop: 70, textAlign: 'center' },
  receipt: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 25, borderWidth: 1, padding: 20 },
  brandRow: { alignItems: 'center', flexDirection: 'row' },
  brandCopy: { marginLeft: 10 },
  brand: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 17 },
  eyebrow: { color: colors.muted, fontFamily: fonts.bold, fontSize: 8, letterSpacing: 1.1, marginTop: 2 },
  divider: { borderBottomColor: colors.border, borderBottomWidth: 1, borderStyle: 'dashed', marginVertical: 19 },
  statusIcon: { alignItems: 'center' },
  amount: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 30, marginTop: 8, textAlign: 'center' },
  status: { fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 0.8, marginTop: 5, textAlign: 'center', textTransform: 'uppercase' },
  description: { color: colors.muted, fontFamily: fonts.medium, fontSize: 11, marginTop: 8, textAlign: 'center' },
  details: { backgroundColor: colors.surfaceMuted, borderRadius: 16, marginTop: 20, paddingHorizontal: 14 },
  row: { borderBottomColor: colors.border, borderBottomWidth: 1, paddingVertical: 11 },
  lastRow: { borderBottomWidth: 0 },
  label: { color: colors.muted, fontFamily: fonts.semiBold, fontSize: 8, letterSpacing: 0.6, textTransform: 'uppercase' },
  value: { color: colors.text, fontFamily: fonts.bold, fontSize: 11, lineHeight: 16, marginTop: 4 },
  note: { color: colors.muted, fontFamily: fonts.regular, fontSize: 8, marginTop: 17, textAlign: 'center' },
  share: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 15, flexDirection: 'row', gap: 8, justifyContent: 'center', marginTop: 14, minHeight: 54 },
  shareText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
  pressed: { opacity: 0.78, transform: [{ scale: 0.99 }] },
});
