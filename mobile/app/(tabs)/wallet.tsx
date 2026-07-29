import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { ActivityIndicator, Alert, Pressable, StyleSheet, Text, View } from 'react-native';
import { mobileApi } from '../../src/api/mobileApi';
import { Screen } from '../../src/components/Screen';
import { TabPageHeader } from '../../src/components/TabPageHeader';
import { colors, fonts } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);

export default function WalletScreen() {
  const queryClient = useQueryClient();
  const wallet = useQuery({ queryKey: ['wallet'], queryFn: mobileApi.wallet });
  const accounts = useQuery({ queryKey: ['wallet-accounts'], queryFn: mobileApi.walletAccounts });
  const history = useQuery({ queryKey: ['funding-history'], queryFn: mobileApi.fundingHistory });
  const convertBonus = useMutation({
    mutationFn: mobileApi.convertBonus,
    onSuccess: async (result) => {
      await Promise.all([
        queryClient.invalidateQueries({ queryKey: ['wallet'] }),
        queryClient.invalidateQueries({ queryKey: ['dashboard'] }),
      ]);
      Alert.alert('Bonus moved', `${money(result.converted_amount)} is now in your main wallet.`);
    },
    onError: (error: Error) => Alert.alert('Could not move bonus', error.message),
  });
  const confirmBonusConversion = () => Alert.alert(
    'Move bonus to main wallet?',
    `You are about to move ${money(wallet.data?.bonus_balance ?? 0)}.`,
    [
      { text: 'Cancel', style: 'cancel' },
      { text: 'Move bonus', onPress: () => convertBonus.mutate() },
    ],
  );
  return (
    <Screen>
      <TabPageHeader eyebrow="YOUR MONEY" title="Wallet" />
      <View style={styles.card}>
        <View style={styles.cardOrb} />
        <Text style={styles.label}>TOTAL AVAILABLE</Text>
        {wallet.isPending ? (
          <ActivityIndicator color={colors.white} style={styles.loading} />
        ) : (
          <Text style={styles.balance}>{money(wallet.data?.balance ?? 0)}</Text>
        )}
        <Text style={styles.cardNote}>Ready for bills and airtime</Text>
      </View>
      <View style={styles.bonusCard}>
        <View style={styles.bonusCopy}>
          <Text style={styles.bonusLabel}>BONUS WALLET</Text>
          <Text style={styles.bonusBalance}>{money(wallet.data?.bonus_balance ?? 0)}</Text>
          <Text style={styles.bonusNote}>{wallet.data?.bonus?.active_rewards[0]?.title ?? 'Your campaign rewards appear here'}</Text>
        </View>
        <Pressable
          disabled={!wallet.data?.bonus?.convertible || convertBonus.isPending}
          onPress={confirmBonusConversion}
          style={({ pressed }) => [styles.moveButton, (!wallet.data?.bonus?.convertible || convertBonus.isPending) && styles.moveButtonDisabled, pressed && { opacity: 0.8 }]}
        >
          {convertBonus.isPending ? <ActivityIndicator color={colors.white} size="small" /> : <Text style={styles.moveButtonText}>Move to wallet</Text>}
        </Pressable>
      </View>
      <Text style={styles.section}>Bank accounts</Text>
      {accounts.isPending ? (
        <ActivityIndicator color={colors.primary} />
      ) : accounts.data?.length ? (
        accounts.data.map((account) => (
          <Pressable key={account.id} style={styles.account}>
            <View style={styles.accountCopy}>
              <Text style={styles.bank}>{account.bank_name}</Text>
              <Text style={styles.accountName}>{account.account_name}</Text>
            </View>
            <Text selectable style={styles.number}>{account.account_number}</Text>
          </Pressable>
        ))
      ) : (
        <View style={styles.emptyCard}><Text style={styles.empty}>No bank account is available yet.</Text></View>
      )}
      <Text style={styles.section}>Recent funding</Text>
      <View style={styles.historyCard}>
        {history.data?.length ? history.data.slice(0, 5).map((item) => (
          <View key={item.id} style={styles.history}>
            <View>
              <Text style={styles.bank}>{item.bank_name}</Text>
              <Text style={styles.accountName}>{new Date(item.created_at).toLocaleDateString()} · {item.status}</Text>
            </View>
            <Text style={styles.credit}>+{money(item.amount_settled)}</Text>
          </View>
        )) : !history.isPending && <Text style={styles.empty}>No wallet funding yet.</Text>}
      </View>
    </Screen>
  );
}

const styles = StyleSheet.create({
  card: { backgroundColor: colors.primaryDark, borderRadius: 24, elevation: 8, overflow: 'hidden', padding: 20, shadowColor: colors.primaryDark, shadowOffset: { width: 0, height: 10 }, shadowOpacity: 0.2, shadowRadius: 18 },
  cardOrb: { backgroundColor: '#159570', borderRadius: 90, height: 150, opacity: 0.4, position: 'absolute', right: -45, top: -70, width: 150 },
  label: { color: '#A7DCCB', fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 1.1 },
  balance: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 29, letterSpacing: -0.8, marginTop: 7 },
  cardNote: { color: '#BEE5D8', fontFamily: fonts.regular, fontSize: 11, marginTop: 9 },
  bonusCard: { alignItems: 'center', backgroundColor: '#FFF7E6', borderColor: '#F1D397', borderRadius: 18, borderWidth: 1, flexDirection: 'row', marginTop: 12, padding: 15 },
  bonusCopy: { flex: 1, marginRight: 10 },
  bonusLabel: { color: '#9B630F', fontFamily: fonts.extraBold, fontSize: 9, letterSpacing: 1 },
  bonusBalance: { color: '#5D3A05', fontFamily: fonts.extraBold, fontSize: 20, marginTop: 3 },
  bonusNote: { color: '#916D35', fontFamily: fonts.regular, fontSize: 9, marginTop: 3 },
  moveButton: { alignItems: 'center', backgroundColor: '#A85A00', borderRadius: 12, justifyContent: 'center', minHeight: 40, minWidth: 102, paddingHorizontal: 12 },
  moveButtonDisabled: { opacity: 0.38 },
  moveButtonText: { color: colors.white, fontFamily: fonts.bold, fontSize: 10 },
  loading: { alignSelf: 'flex-start', marginTop: 14 },
  section: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 17, letterSpacing: -0.3, marginBottom: 12, marginTop: 25 },
  account: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 18, elevation: 2, flexDirection: 'row', justifyContent: 'space-between', marginBottom: 10, padding: 16, shadowColor: '#193E33', shadowOffset: { width: 0, height: 5 }, shadowOpacity: 0.05, shadowRadius: 10 },
  accountCopy: { flex: 1, marginRight: 12 },
  bank: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 13 },
  accountName: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10, marginTop: 4, textTransform: 'capitalize' },
  number: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 15 },
  emptyCard: { backgroundColor: colors.surface, borderRadius: 18, padding: 20 },
  empty: { color: colors.muted, fontFamily: fonts.regular, paddingVertical: 12, textAlign: 'center' },
  historyCard: { backgroundColor: colors.surface, borderRadius: 18, overflow: 'hidden', paddingHorizontal: 15 },
  history: { alignItems: 'center', borderBottomColor: colors.border, borderBottomWidth: 1, flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 14 },
  credit: { color: colors.success, fontFamily: fonts.extraBold, fontSize: 12 },
});
