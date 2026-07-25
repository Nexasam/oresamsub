import { useQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { ActivityIndicator, Pressable, StyleSheet, Text, View } from 'react-native';
import { mobileApi } from '../../src/api/mobileApi';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { Screen } from '../../src/components/Screen';
import { colors, fonts } from '../../src/theme/colors';

type ServiceVisual = {
  accent: string;
  description: string;
  icon: string;
  iconBackground: string;
  wash: string;
};

const fallbackVisual: ServiceVisual = {
  accent: colors.primary,
  description: 'Make a secure payment',
  icon: 'payments',
  iconBackground: colors.primarySoft,
  wash: '#F7FBF9',
};

const visuals: Record<string, ServiceVisual> = {
  data: {
    accent: '#1373E6',
    description: 'Stay online with instant data plans',
    icon: 'signal_cellular_alt',
    iconBackground: '#DCEEFF',
    wash: '#F4F9FF',
  },
  airtime: {
    accent: '#0A8F68',
    description: 'Top up any Nigerian mobile line',
    icon: 'phone_in_talk',
    iconBackground: '#DDF8EE',
    wash: '#F3FBF8',
  },
  cable_subscription: {
    accent: '#7450C7',
    description: 'Renew your favourite TV package',
    icon: 'live_tv',
    iconBackground: '#EEE6FF',
    wash: '#F9F7FF',
  },
  utility_bills: {
    accent: '#DB8616',
    description: 'Pay electricity bills in seconds',
    icon: 'bolt',
    iconBackground: '#FFF0CF',
    wash: '#FFFAF0',
  },
};

export default function ServicesScreen() {
  const query = useQuery({ queryKey: ['products'], queryFn: mobileApi.products });

  return (
    <Screen>
      <View style={styles.hero}>
        <View style={styles.heroOrbLarge} />
        <View style={styles.heroOrbSmall} />
        <View style={styles.heroBadge}>
          <MaterialIcon color="#BFEBDC" name="verified_user" size={15} />
          <Text style={styles.heroBadgeText}>FAST & SECURE</Text>
        </View>
        <Text style={styles.heroTitle}>Everything you need,{'\n'}in one place.</Text>
        <Text style={styles.heroText}>Top up, stay connected and settle everyday bills without the stress.</Text>
      </View>

      <View style={styles.sectionHeader}>
        <View>
          <Text style={styles.sectionEyebrow}>PAYMENTS</Text>
          <Text style={styles.sectionTitle}>Choose a service</Text>
        </View>
        <View style={styles.secureMark}>
          <MaterialIcon color={colors.primary} name="lock" size={15} />
          <Text style={styles.secureText}>Protected</Text>
        </View>
      </View>

      {query.isPending ? (
        <View style={styles.loading}>
          <ActivityIndicator color={colors.primary} size="large" />
          <Text style={styles.loadingText}>Loading your services…</Text>
        </View>
      ) : query.isError ? (
        <Pressable onPress={() => void query.refetch()} style={styles.errorCard}>
          <MaterialIcon color={colors.danger} name="refresh" size={24} />
          <View style={styles.errorCopy}>
            <Text style={styles.errorTitle}>Couldn’t load services</Text>
            <Text style={styles.errorText}>Tap here to try again.</Text>
          </View>
        </Pressable>
      ) : (
        <View style={styles.grid}>
          {query.data?.map((service) => {
            const visual = visuals[service.slug] ?? fallbackVisual;
            return (
              <Pressable
                key={service.id}
                onPress={() => router.push({ pathname: '/service/[slug]', params: { slug: service.slug, name: service.name } })}
                style={({ pressed }) => [styles.card, { backgroundColor: visual.wash }, pressed && styles.pressed]}
              >
                <View style={[styles.iconBox, { backgroundColor: visual.iconBackground }]}>
                  <MaterialIcon color={visual.accent} name={visual.icon} size={27} />
                </View>
                <Text numberOfLines={1} style={styles.name}>{service.name}</Text>
                <Text numberOfLines={2} style={styles.description}>{visual.description}</Text>
                <View style={styles.cardFooter}>
                  <Text style={[styles.openText, { color: visual.accent }]}>Get started</Text>
                  <View style={[styles.arrowBox, { backgroundColor: visual.iconBackground }]}>
                    <MaterialIcon color={visual.accent} name="arrow_forward" size={16} />
                  </View>
                </View>
              </Pressable>
            );
          })}
        </View>
      )}

      <View style={styles.promise}>
        <View style={styles.promiseIcon}>
          <MaterialIcon color={colors.primaryDark} name="bolt" size={20} />
        </View>
        <View style={styles.promiseCopy}>
          <Text style={styles.promiseTitle}>Quick confirmation</Text>
          <Text style={styles.promiseText}>You’ll see the amount and recipient before every payment.</Text>
        </View>
      </View>
    </Screen>
  );
}

const styles = StyleSheet.create({
  hero: { backgroundColor: colors.primaryDark, borderRadius: 28, minHeight: 210, overflow: 'hidden', padding: 22 },
  heroOrbLarge: { backgroundColor: '#159570', borderRadius: 120, height: 210, opacity: 0.32, position: 'absolute', right: -72, top: -76, width: 210 },
  heroOrbSmall: { backgroundColor: colors.accent, borderRadius: 35, bottom: -28, height: 70, opacity: 0.2, position: 'absolute', right: 76, width: 70 },
  heroBadge: { alignItems: 'center', alignSelf: 'flex-start', backgroundColor: 'rgba(255,255,255,0.11)', borderColor: 'rgba(255,255,255,0.14)', borderRadius: 20, borderWidth: 1, flexDirection: 'row', gap: 6, paddingHorizontal: 10, paddingVertical: 7 },
  heroBadgeText: { color: '#D8F4EB', fontFamily: fonts.extraBold, fontSize: 8, letterSpacing: 1.1 },
  heroTitle: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 27, letterSpacing: -0.9, lineHeight: 34, marginTop: 18 },
  heroText: { color: '#BFE0D5', fontFamily: fonts.medium, fontSize: 11, lineHeight: 17, marginTop: 9, maxWidth: '82%' },
  sectionHeader: { alignItems: 'flex-end', flexDirection: 'row', justifyContent: 'space-between', marginBottom: 15, marginTop: 27 },
  sectionEyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 9, letterSpacing: 1.4 },
  sectionTitle: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 20, letterSpacing: -0.5, marginTop: 2 },
  secureMark: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 15, flexDirection: 'row', gap: 4, paddingHorizontal: 9, paddingVertical: 6 },
  secureText: { color: colors.primaryDark, fontFamily: fonts.bold, fontSize: 8 },
  loading: { alignItems: 'center', minHeight: 260, paddingTop: 72 },
  loadingText: { color: colors.muted, fontFamily: fonts.medium, fontSize: 11, marginTop: 12 },
  grid: { flexDirection: 'row', flexWrap: 'wrap', gap: 12 },
  card: { borderColor: 'rgba(16,35,29,0.045)', borderRadius: 23, borderWidth: 1, minHeight: 214, padding: 16, width: '48%' },
  pressed: { opacity: 0.78, transform: [{ scale: 0.975 }] },
  iconBox: { alignItems: 'center', borderRadius: 17, height: 52, justifyContent: 'center', width: 52 },
  name: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 15, letterSpacing: -0.25, marginTop: 17 },
  description: { color: colors.muted, fontFamily: fonts.medium, fontSize: 9, lineHeight: 14, marginTop: 5, minHeight: 28 },
  cardFooter: { alignItems: 'center', flexDirection: 'row', justifyContent: 'space-between', marginTop: 15 },
  openText: { fontFamily: fonts.bold, fontSize: 9 },
  arrowBox: { alignItems: 'center', borderRadius: 12, height: 27, justifyContent: 'center', width: 27 },
  errorCard: { alignItems: 'center', backgroundColor: '#FFF3F3', borderRadius: 20, flexDirection: 'row', padding: 18 },
  errorCopy: { marginLeft: 12 },
  errorTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 13 },
  errorText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10, marginTop: 2 },
  promise: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 20, borderWidth: 1, flexDirection: 'row', marginTop: 20, padding: 14 },
  promiseIcon: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 14, height: 42, justifyContent: 'center', marginRight: 12, width: 42 },
  promiseCopy: { flex: 1 },
  promiseTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 11 },
  promiseText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, lineHeight: 14, marginTop: 3 },
});
