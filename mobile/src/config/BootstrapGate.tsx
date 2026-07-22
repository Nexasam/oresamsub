import { useQuery } from '@tanstack/react-query';
import Constants from 'expo-constants';
import { Linking, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { apiRequest } from '../api/client';
import { colors } from '../theme/colors';

type MobileConfig = { minimum_app_version: string; latest_app_version: string; force_update: boolean; maintenance_mode: boolean; maintenance_message: string; store_urls: { android: string | null; ios: string | null } };

export function BootstrapGate({ children }: { children: React.ReactNode }) {
  const query = useQuery({ queryKey: ['mobile-config'], queryFn: async () => (await apiRequest<MobileConfig>('/config')).data, staleTime: 5 * 60_000 });
  const config = query.data;
  const current = Constants.expoConfig?.version ?? '0.0.0';
  const mustUpdate = !!config && config.force_update && compareVersions(current, config.minimum_app_version) < 0;
  if (config?.maintenance_mode) return <Block title="We’ll be back shortly" message={config.maintenance_message} action="Try again" onPress={() => void query.refetch()} />;
  if (mustUpdate) {
    const url = Platform.OS === 'ios' ? config.store_urls.ios : config.store_urls.android;
    return <Block title="Update required" message="Install the latest OresamSub version to continue securely." action="Update app" onPress={() => url && void Linking.openURL(url)} />;
  }
  return <>{children}</>;
}

function Block({ title, message, action, onPress }: { title: string; message: string; action: string; onPress: () => void }) { return <View style={styles.screen}><View style={styles.mark}><Text style={styles.markText}>O</Text></View><Text style={styles.title}>{title}</Text><Text style={styles.message}>{message}</Text><Pressable onPress={onPress} style={styles.button}><Text style={styles.buttonText}>{action}</Text></Pressable></View>; }
function compareVersions(a: string, b: string) { const left = a.split('.').map(Number); const right = b.split('.').map(Number); for (let i = 0; i < 3; i += 1) { const difference = (left[i] ?? 0) - (right[i] ?? 0); if (difference) return difference; } return 0; }
const styles = StyleSheet.create({ screen: { alignItems: 'center', backgroundColor: colors.background, flex: 1, justifyContent: 'center', padding: 28 }, mark: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 20, height: 62, justifyContent: 'center', width: 62 }, markText: { color: colors.white, fontSize: 30, fontWeight: '800' }, title: { color: colors.text, fontSize: 25, fontWeight: '800', marginTop: 20, textAlign: 'center' }, message: { color: colors.muted, lineHeight: 21, marginTop: 9, textAlign: 'center' }, button: { backgroundColor: colors.primary, borderRadius: 12, marginTop: 24, paddingHorizontal: 24, paddingVertical: 14 }, buttonText: { color: colors.white, fontWeight: '800' } });
