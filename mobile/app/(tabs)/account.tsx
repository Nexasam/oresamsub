import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useEffect, useState } from 'react';
import { Alert, Pressable, StyleSheet, Switch, Text, View } from 'react-native';
import { useAuthStore } from '../../src/auth/authStore';
import { biometricLock } from '../../src/auth/biometricLock';
import { Screen } from '../../src/components/Screen';
import { TabPageHeader } from '../../src/components/TabPageHeader';
import { deviceApi } from '../../src/device/deviceApi';
import { colors, fonts } from '../../src/theme/colors';

export default function AccountScreen() {
  const user = useAuthStore((state) => state.user);
  const signOut = useAuthStore((state) => state.signOut);
  const queryClient = useQueryClient();
  const [biometric, setBiometric] = useState(false);
  const preferences = useQuery({
    queryKey: ['notification-preferences'],
    queryFn: async () => (await deviceApi.preferences()).data,
  });
  const updatePreferences = useMutation({
    mutationFn: deviceApi.updatePreferences,
    onSuccess: (response) => preferences.refetch().then(() => response),
  });

  useEffect(() => {
    void biometricLock.isEnabled().then(setBiometric);
  }, []);

  const toggleBiometric = async (enabled: boolean) => {
    const availability = await biometricLock.availability();
    if (enabled && !availability.available) {
      Alert.alert(
        'Fingerprint unlock unavailable',
        availability.hasHardware
          ? 'Your phone supports biometrics, but no fingerprint or face is enrolled. Set one up in your phone settings and try again.'
          : 'This phone or emulator does not report supported biometric hardware.',
      );
      return;
    }
    if (enabled && !(await biometricLock.unlock())) return;
    await biometricLock.setEnabled(enabled);
    setBiometric(enabled);
    if (enabled) {
      Alert.alert(
        'Fingerprint unlock enabled',
        'Close and reopen OresamSub to test it. Signing out removes the saved session, so fingerprint sign-in will no longer appear after logout.',
      );
    }
  };

  const logout = async () => {
    await signOut();
    queryClient.clear();
    router.replace('/(auth)/login');
  };

  return (
    <Screen>
      <TabPageHeader title="Account" />
      <View style={styles.profile}>
        <View style={styles.avatar}>
          <Text style={styles.initial}>{user?.first_name?.[0]}{user?.last_name?.[0]}</Text>
        </View>
        <View style={styles.profileCopy}>
          <Text style={styles.name}>{user?.first_name} {user?.last_name}</Text>
          <Text style={styles.email}>{user?.email}</Text>
        </View>
      </View>
      <Pressable onPress={() => router.push('/edit-profile')} style={styles.edit}>
        <Text style={styles.editText}>Edit profile</Text>
      </Pressable>
      <View style={styles.details}>
        <Detail label="Username" value={`@${user?.username}`} />
        <Detail label="Phone" value={user?.phone_number ?? 'Not added'} />
        <Detail label="Phone verified" value={user?.phone_verified ? 'Yes' : 'No'} />
      </View>
      <Text style={styles.section}>Security & notifications</Text>
      <Pressable onPress={() => router.push('/security-settings')} style={styles.navigation}>
        <Text style={styles.settingLabel}>Password and transaction PIN</Text>
        <Text style={styles.arrow}>›</Text>
      </Pressable>
      <Setting
        description="Unlocks your saved session when reopening the app. Signing out still requires your password."
        label="Fingerprint / Face unlock"
        onChange={(value) => void toggleBiometric(value)}
        value={biometric}
      />
      <Setting
        label="Transaction alerts"
        onChange={(transactional_enabled) => updatePreferences.mutate({
          promotional_enabled: preferences.data?.promotional_enabled ?? false,
          transactional_enabled,
        })}
        value={preferences.data?.transactional_enabled ?? true}
      />
      <Setting
        label="Offers and announcements"
        onChange={(promotional_enabled) => updatePreferences.mutate({
          promotional_enabled,
          transactional_enabled: preferences.data?.transactional_enabled ?? true,
        })}
        value={preferences.data?.promotional_enabled ?? false}
      />
      <Pressable onPress={() => router.push('/help')} style={styles.navigation}>
        <Text style={styles.settingLabel}>Help, support and policies</Text>
        <Text style={styles.arrow}>›</Text>
      </Pressable>
      <Pressable onPress={() => void logout()} style={styles.logout}>
        <Text style={styles.logoutText}>Sign out</Text>
      </Pressable>
    </Screen>
  );
}

function Detail({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.detail}>
      <Text style={styles.detailLabel}>{label}</Text>
      <Text style={styles.detailValue}>{value}</Text>
    </View>
  );
}

function Setting({ description, label, value, onChange }: {
  description?: string;
  label: string;
  value: boolean;
  onChange: (value: boolean) => void;
}) {
  return (
    <View style={styles.setting}>
      <View style={styles.settingCopy}>
        <Text style={styles.settingLabel}>{label}</Text>
        {description ? <Text style={styles.settingDescription}>{description}</Text> : null}
      </View>
      <Switch
        onValueChange={onChange}
        thumbColor={value ? colors.primary : '#f4f4f5'}
        trackColor={{ true: '#86efac' }}
        value={value}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  profile: { alignItems: 'center', flexDirection: 'row', marginTop: 4 },
  avatar: { alignItems: 'center', backgroundColor: colors.primarySoft, borderColor: colors.border, borderRadius: 32, borderWidth: 1, height: 64, justifyContent: 'center', width: 64 },
  initial: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 20 },
  profileCopy: { flex: 1, marginLeft: 14 },
  name: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 18, letterSpacing: -0.3 },
  email: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, marginTop: 4 },
  edit: { alignSelf: 'flex-start', backgroundColor: colors.primarySoft, borderRadius: 10, marginLeft: 78, marginTop: 8, paddingHorizontal: 10, paddingVertical: 6 },
  editText: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 10 },
  details: { backgroundColor: colors.surface, borderRadius: 20, elevation: 2, marginTop: 24, paddingHorizontal: 16, shadowColor: '#193E33', shadowOffset: { width: 0, height: 5 }, shadowOpacity: 0.05, shadowRadius: 10 },
  detail: { borderBottomColor: colors.border, borderBottomWidth: 1, paddingVertical: 15 },
  detailLabel: { color: colors.muted, fontFamily: fonts.bold, fontSize: 9, letterSpacing: 0.7, textTransform: 'uppercase' },
  detailValue: { color: colors.text, fontFamily: fonts.bold, fontSize: 13, marginTop: 5 },
  section: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 17, marginBottom: 10, marginTop: 25 },
  setting: { alignItems: 'center', backgroundColor: colors.surface, borderBottomColor: colors.border, borderBottomWidth: 1, flexDirection: 'row', justifyContent: 'space-between', padding: 15 },
  settingCopy: { flex: 1, marginRight: 12 },
  settingDescription: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, lineHeight: 14, marginTop: 4 },
  navigation: { alignItems: 'center', backgroundColor: colors.surface, borderBottomColor: colors.border, borderBottomWidth: 1, flexDirection: 'row', justifyContent: 'space-between', padding: 17 },
  arrow: { color: colors.primary, fontFamily: fonts.regular, fontSize: 24 },
  settingLabel: { color: colors.text, fontFamily: fonts.bold, fontSize: 13 },
  logout: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.danger, borderRadius: 15, borderWidth: 1, marginTop: 25, padding: 15 },
  logoutText: { color: colors.danger, fontFamily: fonts.extraBold, fontSize: 13 },
});
