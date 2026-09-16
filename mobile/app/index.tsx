import { Redirect } from 'expo-router';
import { useEffect, useState } from 'react';
import { ActivityIndicator, StyleSheet, Text, View } from 'react-native';

import { useAuthStore } from '../src/auth/authStore';
import { appLock } from '../src/auth/appLock';
import { tokenVault } from '../src/auth/tokenVault';
import { BrandLogo } from '../src/components/BrandLogo';
import { colors } from '../src/theme/colors';

export default function FoundationScreen() {
  const status = useAuthStore((state) => state.status);
  const restore = useAuthStore((state) => state.restore);
  const [destination, setDestination] = useState<'unlock' | 'setup' | null>(null);

  useEffect(() => {
    void (async () => {
      const refreshToken = await tokenVault.readRefreshToken();
      if (!refreshToken) { await restore(); return; }
      setDestination(await appLock.isConfigured() ? 'unlock' : 'setup');
    })();
  }, [restore]);

  if (destination === 'unlock') return <Redirect href="/(auth)/unlock" />;
  if (destination === 'setup') return <Redirect href="/(onboarding)/app-passcode" />;
  if (status === 'guest') return <Redirect href="/(auth)/login" />;
  if (status === 'authenticated') return <Redirect href="/(tabs)" />;

  return (
    <View style={styles.container}>
      <BrandLogo size={92} />
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
  title: {
    color: colors.text,
    fontSize: 30,
    fontWeight: '800',
    marginTop: 18,
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
