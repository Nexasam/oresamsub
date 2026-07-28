import { useMutation } from '@tanstack/react-query';
import { router, Stack } from 'expo-router';
import { useState } from 'react';
import { Alert, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../src/api/client';
import { mobileApi } from '../src/api/mobileApi';
import { useAuthStore } from '../src/auth/authStore';
import { MaterialIcon } from '../src/components/MaterialIcon';
import { Screen } from '../src/components/Screen';
import { colors, fonts } from '../src/theme/colors';

export default function DeleteAccountScreen() {
  const [password, setPassword] = useState('');
  const [confirmation, setConfirmation] = useState('');
  const signOut = useAuthStore((state) => state.signOut);
  const mutation = useMutation({
    mutationFn: () => mobileApi.deactivateAccount({ password, confirmation: 'DELETE' }),
    onSuccess: async () => {
      await signOut();
      Alert.alert(
        'Account deactivated',
        'Sign-in has been disabled and all mobile sessions have been revoked. Contact support if this was a mistake.',
        [{ text: 'Return to sign in', onPress: () => router.replace('/(auth)/login') }],
      );
    },
  });
  const ready = confirmation === 'DELETE' && !!password && !mutation.isPending;

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: 'Deactivate account' }} />
      <Screen safeTop={false}>
        <View style={styles.icon}><MaterialIcon color={colors.danger} name="no_accounts" size={28} /></View>
        <Text style={styles.title}>Deactivate your account</Text>
        <Text style={styles.warning}>
          Deactivation is not immediate permanent deletion. It disables sign-in, revokes every mobile session, and prevents the account from being used.
        </Text>
        <View style={styles.info}>
          <Info text="Your wallet and transaction records are not erased by this action." />
          <Info text="Contact support to restore an account deactivated by mistake." />
          <Info text="For permanent deletion, follow Account deletion instructions on the Help page." />
        </View>
        <Text style={styles.label}>Password</Text>
        <TextInput
          autoCapitalize="none"
          onChangeText={setPassword}
          placeholder="Enter your account password"
          placeholderTextColor={colors.muted}
          secureTextEntry
          selectionColor={colors.primary}
          style={styles.input}
          value={password}
        />
        <Text style={styles.label}>Type DELETE to confirm</Text>
        <TextInput
          autoCapitalize="characters"
          autoCorrect={false}
          onChangeText={setConfirmation}
          placeholder="DELETE"
          placeholderTextColor={colors.muted}
          selectionColor={colors.primary}
          style={styles.input}
          value={confirmation}
        />
        {mutation.error ? (
          <Text style={styles.error}>
            {mutation.error instanceof ApiError ? mutation.error.message : 'Unable to deactivate your account.'}
          </Text>
        ) : null}
        <Pressable
          disabled={!ready}
          onPress={() => mutation.mutate()}
          style={({ pressed }) => [styles.button, (!ready || pressed) && styles.dim]}
        >
          <Text style={styles.buttonText}>{mutation.isPending ? 'Deactivating…' : 'Deactivate account'}</Text>
        </Pressable>
        <Pressable onPress={() => router.back()} style={styles.cancel}>
          <Text style={styles.cancelText}>Keep my account</Text>
        </Pressable>
      </Screen>
    </>
  );
}

function Info({ text }: { text: string }) {
  return (
    <View style={styles.infoRow}>
      <MaterialIcon color={colors.warning} name="info" size={16} />
      <Text style={styles.infoText}>{text}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  icon: { alignItems: 'center', backgroundColor: '#FFF0F0', borderRadius: 20, height: 56, justifyContent: 'center', width: 56 },
  title: { color: colors.danger, fontFamily: fonts.extraBold, fontSize: 24, marginTop: 16 },
  warning: { color: colors.muted, fontFamily: fonts.regular, lineHeight: 21, marginTop: 9 },
  info: { backgroundColor: '#FFF8E8', borderRadius: 16, gap: 10, marginTop: 18, padding: 14 },
  infoRow: { alignItems: 'flex-start', flexDirection: 'row', gap: 8 },
  infoText: { color: colors.muted, flex: 1, fontFamily: fonts.medium, fontSize: 10, lineHeight: 15 },
  label: { color: colors.text, fontFamily: fonts.bold, marginBottom: 7, marginTop: 20 },
  input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, color: colors.text, fontFamily: fonts.medium, padding: 14 },
  error: { color: colors.danger, fontFamily: fonts.medium, marginTop: 12 },
  button: { alignItems: 'center', backgroundColor: colors.danger, borderRadius: 12, marginTop: 24, padding: 15 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold },
  dim: { opacity: 0.5 },
  cancel: { alignItems: 'center', marginTop: 14, padding: 12 },
  cancelText: { color: colors.primary, fontFamily: fonts.bold },
});
