import { Redirect } from 'expo-router';
import { useEffect } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';

import { useAuthStore } from '../src/auth/authStore';
import { biometricLock } from '../src/auth/biometricLock';
import { colors } from '../src/theme/colors';

export default function FoundationScreen() {
  const status = useAuthStore((state) => state.status);
  const restore = useAuthStore((state) => state.restore);
  const declineRestore = useAuthStore((state) => state.declineRestore);

  useEffect(() => {
    void (async () => {
      if (await biometricLock.isEnabled()) {
        const unlocked = await biometricLock.unlock();
        if (!unlocked) { declineRestore(); return; }
      }
      await restore();
    })();
  }, [declineRestore, restore]);

  if (status === 'guest') return <Redirect href="/(auth)/login" />;
  if (status === 'authenticated') return <Redirect href="/(tabs)" />;

  return (
    <View style={styles.container}>
      <View style={styles.mark}>
        <Text style={styles.markText}>O</Text>
      </View>
      <Text style={styles.title}>OresamSub</Text>
      <Text style={styles.subtitle}>Secure telecom services, right from your phone.</Text>
      <ActivityIndicator color={colors.primary} size="small" style={styles.loader} />
      <Text style={styles.status}>Restoring your secure session…</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    alignItems: 'center',
    backgroundColor: colors.background,
    flex: 1,
    justifyContent: 'center',
    padding: 24,
  },
  mark: {
    alignItems: 'center',
    backgroundColor: colors.primary,
    borderRadius: 24,
    height: 72,
    justifyContent: 'center',
    marginBottom: 20,
    width: 72,
  },
  markText: {
    color: colors.white,
    fontSize: 38,
    fontWeight: '800',
  },
  title: {
    color: colors.text,
    fontSize: 30,
    fontWeight: '800',
  },
  subtitle: {
    color: colors.muted,
    fontSize: 15,
    lineHeight: 22,
    marginTop: 8,
    maxWidth: 300,
    textAlign: 'center',
  },
  loader: {
    marginTop: 32,
  },
  status: {
    color: colors.primary,
    fontSize: 13,
    fontWeight: '600',
    marginTop: 10,
  },
});
