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
        <View style={styles.heroTop}><View style={styles.heroBadge}><MaterialIcon color="#BFEBDC" name="verified_user" size={15} /><Text style={styles.heroBadgeText}>FAST & SECURE</Text></View><Pressable onPress={() => router.replace('/(tabs)')} style={styles.homeButton}><MaterialIcon color={colors.white} name="home" size={17} /><Text style={styles.homeButtonText}>Home</Text></Pressable></View>
        <Text style={styles.heroTitle}>Pay bills. Stay connected.</Text>
        <Text style={styles.heroText}>Choose a service and complete it securely in a few taps.</Text>
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
                <Text numberOfLines={1} style={styles.description}>{visual.description}</Text>
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

      <View style={styles.promise}><MaterialIcon color={colors.primary} name="verified_user" size={18} /><Text style={styles.promiseText}>Review the recipient and amount before every payment.</Text></View>
    </Screen>
  );
}

const styles = StyleSheet.create({
  hero: { backgroundColor: colors.primaryDark, borderRadius: 22, minHeight: 130, overflow: 'hidden', padding: 17 },
  heroOrbLarge: { backgroundColor: '#159570', borderRadius: 120, height: 210, opacity: 0.32, position: 'absolute', right: -72, top: -76, width: 210 },
  heroOrbSmall: { backgroundColor: colors.accent, borderRadius: 35, bottom: -28, height: 70, opacity: 0.2, position: 'absolute', right: 76, width: 70 },
  heroTop: { alignItems: 'center', flexDirection: 'row', justifyContent: 'space-between' },
  heroBadge: { alignItems: 'center', alignSelf: 'flex-start', backgroundColor: 'rgba(255,255,255,0.11)', borderColor: 'rgba(255,255,255,0.14)', borderRadius: 20, borderWidth: 1, flexDirection: 'row', gap: 6, paddingHorizontal: 9, paddingVertical: 6 },
  heroBadgeText: { color: '#D8F4EB', fontFamily: fonts.extraBold, fontSize: 8, letterSpacing: 1.1 },
  homeButton: { alignItems: 'center', backgroundColor: 'rgba(255,255,255,0.12)', borderRadius: 14, flexDirection: 'row', gap: 4, paddingHorizontal: 9, paddingVertical: 6 }, homeButtonText: { color: colors.white, fontFamily: fonts.bold, fontSize: 9 },
  heroTitle: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 21, letterSpacing: -0.7, marginTop: 13 },
  heroText: { color: '#BFE0D5', fontFamily: fonts.medium, fontSize: 10, lineHeight: 15, marginTop: 6, maxWidth: '88%' },
  sectionHeader: { alignItems: 'flex-end', flexDirection: 'row', justifyContent: 'space-between', marginBottom: 12, marginTop: 18 },
  sectionEyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 9, letterSpacing: 1.4 },
  sectionTitle: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 18, letterSpacing: -0.5, marginTop: 2 },
  secureMark: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 15, flexDirection: 'row', gap: 4, paddingHorizontal: 9, paddingVertical: 6 },
  secureText: { color: colors.primaryDark, fontFamily: fonts.bold, fontSize: 8 },
  loading: { alignItems: 'center', minHeight: 260, paddingTop: 72 },
  loadingText: { color: colors.muted, fontFamily: fonts.medium, fontSize: 11, marginTop: 12 },
  grid: { flexDirection: 'row', flexWrap: 'wrap', gap: 10 },
  card: { borderColor: 'rgba(16,35,29,0.045)', borderRadius: 19, borderWidth: 1, flexBasis: '48%', flexGrow: 1, minHeight: 150, padding: 13 },
  pressed: { opacity: 0.78, transform: [{ scale: 0.975 }] },
  iconBox: { alignItems: 'center', borderRadius: 14, height: 42, justifyContent: 'center', width: 42 },
  name: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 13, letterSpacing: -0.2, marginTop: 11 },
  description: { color: colors.muted, fontFamily: fonts.medium, fontSize: 8, marginTop: 4 },
  cardFooter: { alignItems: 'center', flexDirection: 'row', justifyContent: 'space-between', marginTop: 10 },
  openText: { fontFamily: fonts.bold, fontSize: 9 },
  arrowBox: { alignItems: 'center', borderRadius: 12, height: 27, justifyContent: 'center', width: 27 },
  errorCard: { alignItems: 'center', backgroundColor: '#FFF3F3', borderRadius: 20, flexDirection: 'row', padding: 18 },
  errorCopy: { marginLeft: 12 },
  errorTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 13 },
  errorText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10, marginTop: 2 },
  promise: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, flexDirection: 'row', gap: 8, marginTop: 13, padding: 11 },
  promiseIcon: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 14, height: 42, justifyContent: 'center', marginRight: 12, width: 42 },
  promiseCopy: { flex: 1 },
  promiseTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 11 },
  promiseText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, lineHeight: 14, marginTop: 3 },
});
