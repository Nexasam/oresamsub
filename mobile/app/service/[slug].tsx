import { useQueries, useQuery } from '@tanstack/react-query';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { useMemo, useState } from 'react';
import { ActivityIndicator, Image, type ImageSourcePropType, Pressable, StyleSheet, Text, View } from 'react-native';
import { mobileApi } from '../../src/api/mobileApi';
import type { CatalogueCategory } from '../../src/api/types';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { Screen } from '../../src/components/Screen';
import { colors, fonts } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);
type ProviderOption = { key: string; title: string; categories: CatalogueCategory[]; logo?: ImageSourcePropType };
type SizeFilter = 'all' | number;
const networkLogos: Record<string, ImageSourcePropType> = {
  mtn: require('../../assets/networks/mtn.png'),
  airtel: require('../../assets/networks/airtel.png'),
  glo: require('../../assets/networks/glo.png'),
  '9mobile': require('../../assets/networks/9mobile.png'),
};
const cableLogos: Record<string, ImageSourcePropType> = {
  dstv: { uri: 'https://oresamsub.com/assets/template2/images/dstv.png' },
  gotv: { uri: 'https://oresamsub.com/assets/template2/images/gotv.png' },
  startimes: { uri: 'https://oresamsub.com/assets/template2/images/startimes.png' },
};

export default function ServiceScreen() {
  const params = useLocalSearchParams<{ slug: string; name?: string }>();
  const categories = useQuery({
    queryKey: ['categories', params.slug],
    queryFn: () => mobileApi.categories(params.slug),
  });
  const [selected, setSelected] = useState<ProviderOption | null>(null);
  const [sizeFilter, setSizeFilter] = useState<SizeFilter>('all');
  const isData = params.slug === 'data';
  const isCable = params.slug === 'cable_subscription';
  const isElectricity = params.slug === 'utility_bills';
  const isNetworkService = isData || params.slug === 'airtime';
  const providers = useMemo(
    () => buildProviders(categories.data ?? [], isNetworkService, isCable),
    [categories.data, isCable, isNetworkService],
  );
  const planQueries = useQueries({
    queries: (selected?.categories ?? []).map((category) => ({
      queryKey: ['plans', category.id],
      queryFn: () => mobileApi.plans(category.id),
    })),
  });
  const plans = planQueries
    .flatMap((query) => query.data ?? [])
    .filter((plan, index, all) => all.findIndex((item) => item.id === plan.id) === index);
  const plansPending = planQueries.some((query) => query.isPending);
  const availableSizes = Array.from(new Set(
    plans
      .map((plan) => plan.data_size_mb)
      .filter((size): size is number => typeof size === 'number' && size > 0),
  )).sort((left, right) => left - right);
  const visiblePlans = isData && sizeFilter !== 'all'
    ? plans.filter((plan) => plan.data_size_mb === sizeFilter)
    : plans;
  const providerLabel = isData ? 'network' : isCable ? 'TV provider' : isElectricity ? 'electricity provider' : 'provider';
  const promptIcon = isCable ? 'live_tv' : isElectricity ? 'bolt' : isNetworkService ? 'signal_cellular_alt' : 'payments';

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: params.name ?? 'Choose provider' }} />
      <Screen safeTop={false}>
        <Text style={styles.eyebrow}>{isData ? 'BUY DATA' : isCable ? 'CABLE TV' : isElectricity ? 'ELECTRICITY' : 'SELECT SERVICE'}</Text>
        <Text style={styles.heading}>{isData ? 'Stay connected' : `Choose your ${providerLabel}`}</Text>
        <Text style={styles.subheading}>
          {isData
            ? 'Pick a network, choose the plan that fits, and send it instantly.'
            : `Select your ${providerLabel} to see the available options.`}
        </Text>
        {categories.isPending ? (
          <ActivityIndicator color={colors.primary} style={styles.loading} />
        ) : (
          <>
            <Text style={styles.stepLabel}>1  Choose {providerLabel}</Text>
            <View style={styles.providers}>
              {providers.map((provider) => {
                const active = selected?.key === provider.key;
                return (
                  <Pressable
                    accessibilityLabel={provider.title}
                    key={provider.key}
                    onPress={() => {
                      setSelected(provider);
                      setSizeFilter('all');
                    }}
                    style={({ pressed }) => [
                      styles.provider,
                      !isNetworkService && styles.providerWide,
                      active && styles.providerActive,
                      pressed && styles.pressed,
                    ]}
                  >
                    <ProviderMark
                      kind={isCable ? 'cable' : isElectricity ? 'electricity' : 'generic'}
                      logo={provider.logo}
                      title={provider.title}
                    />
                    <Text numberOfLines={2} style={[styles.providerName, active && styles.providerNameActive]}>
                      {provider.title}
                    </Text>
                    {active ? (
                      <View style={styles.selectedTick}>
                        <MaterialIcon color={colors.white} name="check" size={12} />
                      </View>
                    ) : null}
                  </Pressable>
                );
              })}
            </View>
            {!providers.length ? <Text style={styles.empty}>No plans are currently available.</Text> : null}
            {selected ? (
              <>
                <View style={styles.planHeading}>
                  <Text style={styles.stepLabel}>2  Choose a plan</Text>
                  <Text style={styles.planCount}>{visiblePlans.length} available</Text>
                </View>
                {plansPending ? (
                  <ActivityIndicator color={colors.primary} style={styles.loading} />
                ) : (
                  <>
                    {isData && plans.length ? (
                      <View style={styles.filters}>
                        {(['all', ...availableSizes] as SizeFilter[]).map((filter) => {
                          const active = sizeFilter === filter;
                          return (
                            <Pressable
                              accessibilityRole="button"
                              key={filter}
                              onPress={() => setSizeFilter(filter)}
                              style={({ pressed }) => [styles.filter, active && styles.filterActive, pressed && styles.pressed]}
                            >
                              <Text style={[styles.filterText, active && styles.filterTextActive]}>
                                {filter === 'all' ? 'All sizes' : formatDataSize(filter)}
                              </Text>
                            </Pressable>
                          );
                        })}
                      </View>
                    ) : null}
                    <View style={styles.planList}>
                    {visiblePlans.map((plan) => (
                      <Pressable
                        key={plan.id}
                        onPress={() => router.push({
                          pathname: '/checkout',
                          params: {
                            planId: plan.id,
                            planName: plan.name,
                            price: String(plan.price),
                            product: params.slug,
                            provider: selected.title,
                          },
                        })}
                        style={({ pressed }) => [styles.plan, pressed && styles.pressed]}
                      >
                        <View style={styles.planCopy}>
                          <Text style={styles.planName}>{plan.name}</Text>
                          <Text style={styles.planMeta}>
                            {[plan.data_size_mb ? formatDataSize(plan.data_size_mb) : null, plan.validity_days ? `${plan.validity_days} days` : 'Instant delivery'].filter(Boolean).join('  ·  ')}
                          </Text>
                        </View>
                        <View>
                          <Text style={styles.price}>{money(plan.price)}</Text>
                          <Text style={styles.selectText}>Select ›</Text>
                        </View>
                      </Pressable>
                    ))}
                    {!visiblePlans.length ? <Text style={styles.filteredEmpty}>No plan matches this data size.</Text> : null}
                    </View>
                  </>
                )}
              </>
            ) : (
              <View style={styles.prompt}>
                <View style={styles.promptIcon}>
                  <MaterialIcon color={colors.primary} name={promptIcon} size={25} />
                </View>
                <Text style={styles.promptTitle}>Select your {providerLabel}</Text>
                <Text style={styles.promptText}>Available plans will appear here.</Text>
              </View>
            )}
          </>
        )}
      </Screen>
    </>
  );
}

function ProviderMark({ kind, logo, title }: {
  kind: 'cable' | 'electricity' | 'generic';
  logo?: ImageSourcePropType;
  title: string;
}) {
  const [logoFailed, setLogoFailed] = useState(false);
  if (logo && !logoFailed) {
    return (
      <View style={[styles.providerMark, kind === 'cable' && styles.cableProviderMark]}>
        <Image
          onError={() => setLogoFailed(true)}
          resizeMode="contain"
          source={logo}
          style={[styles.providerLogo, kind === 'cable' && styles.cableLogo]}
        />
      </View>
    );
  }
  const accent = kind === 'electricity' ? '#D7780B' : kind === 'cable' ? '#7450C7' : colors.primary;
  const background = kind === 'electricity' ? '#FFF2D7' : kind === 'cable' ? '#F2EAFE' : colors.primarySoft;
  return (
    <View style={[styles.providerMark, { backgroundColor: background }]}>
      <MaterialIcon color={accent} name={kind === 'electricity' ? 'bolt' : kind === 'cable' ? 'live_tv' : 'payments'} size={21} />
      <Text numberOfLines={1} style={[styles.providerBadge, { color: accent }]}>{providerBadge(title)}</Text>
    </View>
  );
}

function providerBadge(title: string) {
  const normalized = title.trim();
  const known = normalized.match(/\b(?:IBEDC|AEDC|EKEDC|IKEDC|EEDC|BEDC|JED|KAEDCO|KEDCO|PHED|YEDC|DSTV|GOTV)\b/i);
  if (known) return known[0].toUpperCase();
  const words = normalized.replace(/[^a-z0-9 ]/gi, ' ').split(/\s+/).filter(Boolean);
  return (words.length > 1 ? words.map((word) => word[0]).join('') : normalized.slice(0, 5)).toUpperCase();
}

function buildProviders(categories: CatalogueCategory[], dataOnly: boolean, showCableLogos: boolean): ProviderOption[] {
  if (!dataOnly) {
    return categories.map((category) => ({
      categories: [category],
      key: category.id,
      logo: showCableLogos ? cableLogo(category.network?.name ?? category.name) : undefined,
      title: category.network?.name ?? category.name,
    }));
  }
  const order = ['mtn', 'airtel', 'glo', '9mobile'];
  const groups = new Map<string, CatalogueCategory[]>();
  categories.forEach((category) => {
    const key = networkKey(category.network?.name ?? category.name);
    if (key) groups.set(key, [...(groups.get(key) ?? []), category]);
  });
  return order.flatMap((key) => groups.has(key) ? [{
    categories: groups.get(key)!,
    key,
    logo: networkLogos[key],
    title: key === '9mobile' ? '9mobile' : key[0].toUpperCase() + key.slice(1),
  }] : []);
}

function cableLogo(value: string) {
  const normalized = value.toLowerCase().replace(/[^a-z0-9]/g, '');
  if (normalized.includes('dstv')) return cableLogos.dstv;
  if (normalized.includes('gotv')) return cableLogos.gotv;
  if (normalized.includes('startimes')) return cableLogos.startimes;
  return undefined;
}

function networkKey(value: string) {
  const name = value.toLowerCase();
  if (name.includes('mtn')) return 'mtn';
  if (name.includes('airtel')) return 'airtel';
  if (name.includes('glo')) return 'glo';
  if (name.includes('9mobile') || name.includes('etisalat')) return '9mobile';
  return null;
}

function formatDataSize(sizeMb: number) {
  if (sizeMb < 1000) return `${sizeMb} MB`;
  const sizeGb = sizeMb / (sizeMb % 1000 === 0 ? 1000 : 1024);
  return `${Number.isInteger(sizeGb) ? sizeGb : sizeGb.toFixed(1)} GB`;
}

const styles = StyleSheet.create({
  eyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 1.4 },
  heading: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 25, letterSpacing: -0.7, marginTop: 2 },
  subheading: { color: colors.muted, fontFamily: fonts.regular, fontSize: 12, lineHeight: 18, marginTop: 4 },
  loading: { marginTop: 28 },
  stepLabel: { color: colors.text, fontFamily: fonts.bold, fontSize: 12, marginTop: 18, textTransform: 'capitalize' },
  providers: { flexDirection: 'row', flexWrap: 'wrap', gap: 7, paddingTop: 10 },
  provider: { alignItems: 'center', backgroundColor: colors.surface, borderColor: 'transparent', borderRadius: 16, borderWidth: 2, flex: 1, height: 88, justifyContent: 'center', minWidth: 0, paddingHorizontal: 4, paddingVertical: 7 },
  providerWide: { flexBasis: '47%', flexGrow: 1, maxWidth: '49%', minHeight: 94 },
  providerActive: { backgroundColor: '#F2FCF8', borderColor: colors.primary },
  providerMark: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 13, height: 43, justifyContent: 'center', overflow: 'hidden', width: 43 },
  providerLogo: { height: 36, width: 36 },
  cableProviderMark: { paddingHorizontal: 4, width: 60 },
  cableLogo: { height: 32, width: 52 },
  providerBadge: { fontFamily: fonts.extraBold, fontSize: 7, marginTop: -1, maxWidth: 43 },
  providerName: { color: colors.text, fontFamily: fonts.semiBold, fontSize: 9, lineHeight: 12, marginTop: 6, maxWidth: 120, textAlign: 'center' },
  providerNameActive: { color: colors.primaryDark, fontFamily: fonts.bold },
  selectedTick: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 9, height: 18, justifyContent: 'center', position: 'absolute', right: 7, top: 7, width: 18 },
  pressed: { opacity: 0.72, transform: [{ scale: 0.985 }] },
  planHeading: { alignItems: 'baseline', flexDirection: 'row', justifyContent: 'space-between' },
  planCount: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10 },
  filters: { flexDirection: 'row', flexWrap: 'wrap', gap: 7, marginTop: 11 },
  filter: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 18, borderWidth: 1, paddingHorizontal: 11, paddingVertical: 8 },
  filterActive: { backgroundColor: colors.primaryDark, borderColor: colors.primaryDark },
  filterText: { color: colors.muted, fontFamily: fonts.bold, fontSize: 9 },
  filterTextActive: { color: colors.white },
  planList: { gap: 8, marginTop: 11 },
  plan: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 16, elevation: 2, flexDirection: 'row', padding: 14, shadowColor: '#193E33', shadowOffset: { width: 0, height: 5 }, shadowOpacity: 0.05, shadowRadius: 10 },
  planCopy: { flex: 1, marginRight: 10 },
  planName: { color: colors.text, fontFamily: fonts.bold, fontSize: 13 },
  planMeta: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10, marginTop: 5 },
  price: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 14, textAlign: 'right' },
  selectText: { color: colors.primary, fontFamily: fonts.bold, fontSize: 9, marginTop: 4, textAlign: 'right' },
  prompt: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 20, marginTop: 24, padding: 28 },
  promptIcon: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 22, height: 48, justifyContent: 'center', width: 48 },
  promptTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 14, marginTop: 12, textTransform: 'capitalize' },
  promptText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, marginTop: 4 },
  empty: { color: colors.muted, fontFamily: fonts.regular, padding: 30, textAlign: 'center' },
  filteredEmpty: { color: colors.muted, fontFamily: fonts.medium, fontSize: 11, paddingVertical: 24, textAlign: 'center' },
});
