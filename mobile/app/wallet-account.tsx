import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router, Stack } from 'expo-router';
import { useRef, useState } from 'react';
import { ActivityIndicator, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { ApiError } from '../src/api/client';
import { mobileApi } from '../src/api/mobileApi';
import { PinInput } from '../src/components/PinInput';
import { Screen } from '../src/components/Screen';
import { colors, fonts } from '../src/theme/colors';

type Selection = { optionId: string; bankCode: string };

export default function WalletAccountScreen() {
  const options = useQuery({ queryKey: ['funding-options'], queryFn: mobileApi.fundingOptions });
  const [selection, setSelection] = useState<Selection | null>(null);
  const [pin, setPin] = useState('');
  const scrollRef = useRef<ScrollView>(null);
  const client = useQueryClient();
  const mutation = useMutation({
    mutationFn: () => mobileApi.createWalletAccount({
      bank_code: selection!.bankCode,
      funding_option_id: selection!.optionId,
      pin,
    }),
    onSuccess: async () => {
      await client.invalidateQueries({ queryKey: ['wallet-accounts'] });
      router.back();
    },
  });
  const error = mutation.error instanceof ApiError ? mutation.error.message : null;

  const revealPin = (delay = 250) => {
    setTimeout(() => scrollRef.current?.scrollToEnd({ animated: true }), delay);
  };

  const selectBank = (nextSelection: Selection) => {
    setSelection(nextSelection);
    setPin('');
    revealPin(100);
  };

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: 'Generate bank account' }} />
      <Screen safeTop={false} scrollRef={scrollRef}>
        <Text style={styles.title}>Choose a bank</Text>
        <Text style={styles.subtitle}>Transfers to the generated account fund your OresamSub wallet.</Text>
        {options.isPending ? (
          <ActivityIndicator color={colors.primary} style={styles.loading} />
        ) : (
          <View style={styles.list}>
            {options.data?.flatMap((option) => option.banks.map((bank) => {
              const active = selection?.optionId === option.id && selection.bankCode === bank.code;
              return (
                <Pressable
                  key={`${option.id}:${bank.code}`}
                  onPress={() => selectBank({ bankCode: bank.code, optionId: option.id })}
                  style={({ pressed }) => [styles.option, active && styles.active, pressed && styles.pressed]}
                >
                  <Text style={styles.optionTitle}>{option.name}</Text>
                  <Text style={styles.optionText}>{bank.description ?? bank.code}</Text>
                  {active ? <Text style={styles.selected}>Selected</Text> : null}
                </Pressable>
              );
            }))}
          </View>
        )}
        {selection ? (
          <View style={styles.pinSection}>
            <PinInput
              label="Transaction PIN"
              onChangeText={setPin}
              onFocus={() => revealPin()}
              value={pin}
            />
            {error ? <Text style={styles.error}>{error}</Text> : null}
            <Pressable
              disabled={pin.length < 4 || mutation.isPending}
              onPress={() => mutation.mutate()}
              style={({ pressed }) => [
                styles.button,
                (pressed || pin.length < 4 || mutation.isPending) && styles.dim,
              ]}
            >
              <Text style={styles.buttonText}>{mutation.isPending ? 'Generating…' : 'Generate account'}</Text>
            </Pressable>
          </View>
        ) : null}
      </Screen>
    </>
  );
}

const styles = StyleSheet.create({
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 25 },
  subtitle: { color: colors.muted, fontFamily: fonts.regular, lineHeight: 20, marginTop: 6 },
  loading: { marginTop: 45 },
  list: { gap: 10, marginTop: 22 },
  option: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 14, borderWidth: 1, padding: 16 },
  active: { backgroundColor: colors.primarySoft, borderColor: colors.primary, borderWidth: 2 },
  pressed: { opacity: 0.75 },
  optionTitle: { color: colors.text, fontFamily: fonts.extraBold },
  optionText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 12, marginTop: 4 },
  selected: { color: colors.primary, fontFamily: fonts.bold, fontSize: 9, marginTop: 7, textTransform: 'uppercase' },
  pinSection: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 18, borderWidth: 1, marginTop: 22, padding: 16 },
  error: { color: colors.danger, fontFamily: fonts.medium, marginTop: 12 },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 13, marginTop: 20, padding: 15 },
  dim: { opacity: 0.5 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold },
});
