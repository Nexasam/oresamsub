import { useQuery } from '@tanstack/react-query';
import Constants from 'expo-constants';
import * as Updates from 'expo-updates';
import { useEffect, useState } from 'react';
import { Linking, Modal, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { apiRequest } from '../api/client';
import { colors } from '../theme/colors';

type MobileConfig = { minimum_app_version: string; latest_app_version: string; force_update: boolean; maintenance_mode: boolean; maintenance_message: string; store_urls: { android: string | null; ios: string | null } };

export function BootstrapGate({ children }: { children: React.ReactNode }) {
  const [installingUpdate, setInstallingUpdate] = useState(false);
  const [optionalUpdateDismissed, setOptionalUpdateDismissed] = useState(false);
  const query = useQuery({ queryKey: ['mobile-config'], queryFn: async () => (await apiRequest<MobileConfig>('/config')).data, staleTime: 5 * 60_000 });
  const config = query.data;
  const current = Constants.expoConfig?.version ?? '0.0.0';
  const mustUpdate = !!config && config.force_update && compareVersions(current, config.minimum_app_version) < 0;

  useEffect(() => {
    if (!Updates.isEnabled) return;

    let active = true;
    void Updates.checkForUpdateAsync()
      .then(async (result) => {
        if (!active || !result.isAvailable) return;
        setInstallingUpdate(true);
        await Updates.fetchUpdateAsync();
        if (active) await Updates.reloadAsync();
      })
      .catch(() => {
        if (active) setInstallingUpdate(false);
      });

    return () => { active = false; };
  }, []);

  const updateUrl = config ? (Platform.OS === 'ios' ? config.store_urls.ios : config.store_urls.android) : null;
  const hasNewNativeVersion = !!config && compareVersions(current, config.latest_app_version) < 0;

  if (installingUpdate) return <Block title="Updating OresamSub" message="Installing the latest improvements. The app will reopen automatically." />;
  if (config?.maintenance_mode) return <Block title="We’ll be back shortly" message={config.maintenance_message} action="Try again" onPress={() => void query.refetch()} />;
  if (mustUpdate) {
    return <Block title="Update required" message="Install the latest OresamSub version to continue securely." action="Get latest app" onPress={() => updateUrl && void Linking.openURL(updateUrl)} />;
  }
  return <>
    {children}
    <Modal animationType="fade" onRequestClose={() => setOptionalUpdateDismissed(true)} transparent visible={hasNewNativeVersion && !optionalUpdateDismissed}>
      <View style={styles.modalRoot}>
        <View style={styles.modalCard}>
          <View style={styles.mark}><Text style={styles.markText}>O</Text></View>
          <Text style={styles.title}>A new version is ready</Text>
          <Text style={styles.message}>Update OresamSub for the latest features, fixes and security improvements.</Text>
          <Pressable disabled={!updateUrl} onPress={() => updateUrl && void Linking.openURL(updateUrl)} style={[styles.button, !updateUrl && styles.buttonDisabled]}><Text style={styles.buttonText}>Get latest app</Text></Pressable>
          <Pressable onPress={() => setOptionalUpdateDismissed(true)} style={styles.laterButton}><Text style={styles.laterText}>Maybe later</Text></Pressable>
        </View>
      </View>
    </Modal>
  </>;
}

function Block({ title, message, action, onPress }: { title: string; message: string; action?: string; onPress?: () => void }) { return <View style={styles.screen}><View style={styles.mark}><Text style={styles.markText}>O</Text></View><Text style={styles.title}>{title}</Text><Text style={styles.message}>{message}</Text>{action && onPress && <Pressable onPress={onPress} style={styles.button}><Text style={styles.buttonText}>{action}</Text></Pressable>}</View>; }
function compareVersions(a: string, b: string) { const left = a.split('.').map(Number); const right = b.split('.').map(Number); for (let i = 0; i < 3; i += 1) { const difference = (left[i] ?? 0) - (right[i] ?? 0); if (difference) return difference; } return 0; }
const styles = StyleSheet.create({ screen: { alignItems: 'center', backgroundColor: colors.background, flex: 1, justifyContent: 'center', padding: 28 }, modalRoot: { alignItems: 'center', backgroundColor: 'rgba(5, 25, 20, 0.55)', flex: 1, justifyContent: 'center', padding: 24 }, modalCard: { alignItems: 'center', backgroundColor: colors.white, borderRadius: 24, maxWidth: 420, padding: 28, width: '100%' }, mark: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 20, height: 62, justifyContent: 'center', width: 62 }, markText: { color: colors.white, fontSize: 30, fontWeight: '800' }, title: { color: colors.text, fontSize: 25, fontWeight: '800', marginTop: 20, textAlign: 'center' }, message: { color: colors.muted, lineHeight: 21, marginTop: 9, textAlign: 'center' }, button: { backgroundColor: colors.primary, borderRadius: 12, marginTop: 24, paddingHorizontal: 24, paddingVertical: 14 }, buttonDisabled: { opacity: 0.45 }, buttonText: { color: colors.white, fontWeight: '800' }, laterButton: { marginTop: 12, padding: 10 }, laterText: { color: colors.muted, fontWeight: '700' } });
