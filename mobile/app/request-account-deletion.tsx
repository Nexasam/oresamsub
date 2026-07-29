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

const confirmationPhrase = 'DELETE MY ACCOUNT';

export default function RequestAccountDeletionScreen() {
  const [password, setPassword] = useState('');
  const [confirmation, setConfirmation] = useState('');
  const [reason, setReason] = useState('');
  const signOut = useAuthStore((state) => state.signOut);
  const mutation = useMutation({
    mutationFn: () => mobileApi.requestAccountDeletion({
      confirmation: confirmationPhrase,
      password,
      reason: reason.trim() || undefined,
    }),
    onSuccess: async () => {
      await signOut();
      Alert.alert(
        'Deletion request submitted',
        'Account access has been disabled. OresamSub will review the request within 30 days and retain only records required for security, financial or legal obligations.',
        [{ text: 'Return to sign in', onPress: () => router.replace('/(auth)/login') }],
      );
    },
  });
  const ready = confirmation === confirmationPhrase && !!password && !mutation.isPending;

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: 'Request account deletion' }} />
      <Screen safeTop={false}>
        <View style={styles.icon}><MaterialIcon color={colors.danger} name="person_remove" size={28} /></View>
        <Text style={styles.title}>Request account deletion</Text>
        <Text style={styles.warning}>
          This is a formal request to delete your OresamSub account and associated personal data. It is different from temporary deactivation.
        </Text>
        <View style={styles.info}>
          <Info text="Account access and all active mobile sessions will be disabled immediately." />
          <Info text="The request will be recorded and reviewed within 30 days." />
          <Info text="Data not required for security, fraud prevention, disputes, financial recordkeeping or legal compliance will be deleted or anonymized." />
          <Info text="Transaction and compliance records may be retained for the legally required period." />
        </View>

        <Text style={styles.label}>Reason (optional)</Text>
        <TextInput
          maxLength={500}
          multiline
          onChangeText={setReason}
          placeholder="Tell us why you are leaving"
          placeholderTextColor={colors.muted}
          selectionColor={colors.primary}
          style={[styles.input, styles.reason]}
          textAlignVertical="top"
          value={reason}
        />
        <Text style={styles.counter}>{reason.length}/500</Text>

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
        <Text style={styles.label}>Type DELETE MY ACCOUNT to confirm</Text>
        <TextInput
          autoCapitalize="characters"
          autoCorrect={false}
          maxLength={confirmationPhrase.length}
          onChangeText={setConfirmation}
          placeholder={confirmationPhrase}
          placeholderTextColor={colors.muted}
          selectionColor={colors.primary}
          style={styles.input}
          value={confirmation}
        />

        {mutation.error ? (
          <Text style={styles.error}>
            {mutation.error instanceof ApiError ? mutation.error.message : 'Unable to submit your deletion request.'}
          </Text>
        ) : null}
        <Pressable
          disabled={!ready}
          onPress={() => mutation.mutate()}
          style={({ pressed }) => [styles.button, (!ready || pressed) && styles.dim]}
        >
          <Text style={styles.buttonText}>{mutation.isPending ? 'Submitting request…' : 'Submit deletion request'}</Text>
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
  reason: { minHeight: 92 },
  counter: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, marginTop: 5, textAlign: 'right' },
  error: { color: colors.danger, fontFamily: fonts.medium, marginTop: 12 },
  button: { alignItems: 'center', backgroundColor: colors.danger, borderRadius: 12, marginTop: 24, padding: 15 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold },
  dim: { opacity: 0.5 },
  cancel: { alignItems: 'center', marginTop: 14, padding: 12 },
  cancelText: { color: colors.primary, fontFamily: fonts.bold },
});
