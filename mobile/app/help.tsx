import { useQuery } from '@tanstack/react-query';
import { router, Stack } from 'expo-router';
import { ActivityIndicator, Alert, Linking, Pressable, StyleSheet, Text, View } from 'react-native';
import { apiRequest } from '../src/api/client';
import { MaterialIcon } from '../src/components/MaterialIcon';
import { Screen } from '../src/components/Screen';
import { colors, fonts } from '../src/theme/colors';

type Support = { email: string | null; phone: string | null; whatsapp: string | null };
type Config = { legal: { privacy_url: string | null; terms_url: string | null; account_deletion_url: string | null } };

export default function HelpScreen() {
  const support = useQuery({
    queryKey: ['support'],
    queryFn: async () => (await apiRequest<Support>('/support')).data,
  });
  const config = useQuery({
    queryKey: ['mobile-config-help'],
    queryFn: async () => (await apiRequest<Config>('/config')).data,
  });
  const whatsapp = whatsappUrl(support.data?.whatsapp);

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: 'Help and policies' }} />
      <Screen safeTop={false}>
        <View style={styles.hero}>
          <View style={styles.heroIcon}><MaterialIcon color={colors.primary} name="support_agent" size={28} /></View>
          <View style={styles.heroCopy}>
            <Text style={styles.heading}>We’re here to help</Text>
            <Text style={styles.subtitle}>Choose the most convenient way to reach OresamSub support.</Text>
          </View>
        </View>

        <Text style={styles.section}>Contact support</Text>
        {support.isPending ? (
          <ActivityIndicator color={colors.primary} style={styles.loading} />
        ) : (
          <View style={styles.list}>
            <ActionRow
              icon="mail"
              label="Email support"
              unavailable={!support.data?.email}
              value={support.data?.email ?? 'Not available yet'}
              url={support.data?.email ? `mailto:${support.data.email}?subject=${encodeURIComponent('OresamSub mobile support')}` : null}
            />
            <ActionRow
              icon="call"
              label="Call support"
              unavailable={!support.data?.phone}
              value={support.data?.phone ?? 'Not available yet'}
              url={support.data?.phone ? `tel:${support.data.phone.replace(/[^\d+]/g, '')}` : null}
            />
            <ActionRow
              icon="chat"
              label="WhatsApp"
              unavailable={!whatsapp}
              value={support.data?.whatsapp ?? 'Not available yet'}
              url={whatsapp}
            />
          </View>
        )}
        {support.isError ? (
          <Pressable onPress={() => void support.refetch()} style={styles.notice}>
            <MaterialIcon color={colors.warning} name="error" size={18} />
            <Text style={styles.noticeText}>Support details could not be loaded. Tap to retry.</Text>
          </Pressable>
        ) : null}

        <Text style={styles.section}>Legal and account</Text>
        <View style={styles.policyNotice}>
          <MaterialIcon color={colors.primary} name="open_in_browser" size={18} />
          <Text style={styles.policyNoticeText}>
            These pages open securely on oresamsub.com. If one is unavailable, the app will tell you.
          </Text>
        </View>
        {config.isPending ? (
          <ActivityIndicator color={colors.primary} style={styles.loading} />
        ) : (
          <View style={styles.list}>
            <ActionRow
              icon="privacy_tip"
              label="Privacy policy"
              unavailable={!config.data?.legal.privacy_url}
              value={config.data?.legal.privacy_url ? 'Read on oresamsub.com' : 'Page not available yet'}
              url={config.data?.legal.privacy_url}
            />
            <ActionRow
              icon="gavel"
              label="Terms and conditions"
              unavailable={!config.data?.legal.terms_url}
              value={config.data?.legal.terms_url ? 'Read on oresamsub.com' : 'Page not available yet'}
              url={config.data?.legal.terms_url}
            />
            <ActionRow
              icon="person_remove"
              label="Account deletion instructions"
              unavailable={!config.data?.legal.account_deletion_url}
              value={config.data?.legal.account_deletion_url ? 'View request instructions' : 'Page not available yet'}
              url={config.data?.legal.account_deletion_url}
            />
          </View>
        )}
        {config.isError ? (
          <Pressable onPress={() => void config.refetch()} style={styles.notice}>
            <MaterialIcon color={colors.warning} name="error" size={18} />
            <Text style={styles.noticeText}>Policy pages could not be loaded. Tap to retry.</Text>
          </Pressable>
        ) : null}

        <Pressable onPress={() => router.push('/delete-account')} style={({ pressed }) => [styles.deactivate, pressed && styles.pressed]}>
          <View style={styles.deactivateIcon}><MaterialIcon color={colors.danger} name="no_accounts" size={22} /></View>
          <View style={styles.deactivateCopy}>
            <Text style={styles.danger}>Deactivate my account</Text>
            <Text style={styles.deactivateText}>Blocks account access and signs out every device. It does not immediately delete financial records.</Text>
          </View>
          <MaterialIcon color={colors.danger} name="chevron_right" size={22} />
        </Pressable>
      </Screen>
    </>
  );
}

function ActionRow({ icon, label, unavailable, value, url }: {
  icon: string;
  label: string;
  unavailable: boolean;
  value: string;
  url: string | null | undefined;
}) {
  const open = async () => {
    if (!url) {
      Alert.alert(`${label} unavailable`, 'This option is not available yet. Please use another support method.');
      return;
    }
    try {
      await Linking.openURL(url);
    } catch {
      Alert.alert(
        `Unable to open ${label.toLowerCase()}`,
        'This option could not be opened. Please check your connection and try again.',
      );
    }
  };

  return (
    <Pressable onPress={() => void open()} style={({ pressed }) => [styles.link, pressed && styles.pressed]}>
      <View style={[styles.linkIcon, unavailable && styles.linkIconUnavailable]}>
        <MaterialIcon color={unavailable ? colors.muted : colors.primary} name={icon} size={20} />
      </View>
      <View style={styles.linkCopy}>
        <Text style={[styles.label, unavailable && styles.unavailable]}>{label}</Text>
        <Text style={[styles.value, unavailable && styles.unavailable]}>{value}</Text>
      </View>
      <MaterialIcon color={unavailable ? colors.muted : colors.primary} name="chevron_right" size={22} />
    </Pressable>
  );
}

function whatsappUrl(value: string | null | undefined) {
  if (!value) return null;
  let digits = value.replace(/\D/g, '');
  if (digits.startsWith('0')) digits = `234${digits.slice(1)}`;
  else if (/^[789]\d{9}$/.test(digits)) digits = `234${digits}`;
  if (!digits.startsWith('234') || digits.length !== 13) return null;
  return `https://wa.me/${digits}?text=Hello%20OresamSub%20Support%2C%20I%20need%20help%20with%20the%20mobile%20app.`;
}

const styles = StyleSheet.create({
  hero: { alignItems: 'center', backgroundColor: colors.primarySoft, borderColor: '#C7ECDD', borderRadius: 20, borderWidth: 1, flexDirection: 'row', padding: 16 },
  heroIcon: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 17, height: 52, justifyContent: 'center', marginRight: 12, width: 52 },
  heroCopy: { flex: 1 },
  heading: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 20 },
  subtitle: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10, lineHeight: 15, marginTop: 4 },
  section: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 17, marginTop: 25 },
  list: { borderColor: colors.border, borderRadius: 18, borderWidth: 1, marginTop: 12, overflow: 'hidden' },
  loading: { marginVertical: 24 },
  link: { alignItems: 'center', backgroundColor: colors.surface, borderBottomColor: colors.border, borderBottomWidth: 1, flexDirection: 'row', minHeight: 68, padding: 13 },
  linkIcon: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 13, height: 40, justifyContent: 'center', marginRight: 11, width: 40 },
  linkIconUnavailable: { backgroundColor: colors.surfaceMuted },
  linkCopy: { flex: 1, marginRight: 8 },
  label: { color: colors.text, fontFamily: fonts.bold, fontSize: 12 },
  value: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, marginTop: 4 },
  unavailable: { color: colors.muted },
  notice: { alignItems: 'center', backgroundColor: '#FFF7E5', borderRadius: 13, flexDirection: 'row', gap: 8, marginTop: 10, padding: 12 },
  noticeText: { color: colors.muted, flex: 1, fontFamily: fonts.medium, fontSize: 10, lineHeight: 15 },
  policyNotice: { alignItems: 'flex-start', flexDirection: 'row', gap: 8, marginTop: 10, paddingHorizontal: 2 },
  policyNoticeText: { color: colors.muted, flex: 1, fontFamily: fonts.regular, fontSize: 9, lineHeight: 14 },
  deactivate: { alignItems: 'center', backgroundColor: '#FFF3F3', borderColor: '#F5CACA', borderRadius: 18, borderWidth: 1, flexDirection: 'row', marginTop: 15, padding: 14 },
  deactivateIcon: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 13, height: 42, justifyContent: 'center', marginRight: 11, width: 42 },
  deactivateCopy: { flex: 1, marginRight: 8 },
  danger: { color: colors.danger, fontFamily: fonts.extraBold, fontSize: 12 },
  deactivateText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, lineHeight: 14, marginTop: 4 },
  pressed: { opacity: 0.72 },
});
