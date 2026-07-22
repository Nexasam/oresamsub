import { useQueries, useQuery } from '@tanstack/react-query';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { useMemo, useState } from 'react';
import { ActivityIndicator, Image, type ImageSourcePropType, Pressable, StyleSheet, Text, View } from 'react-native';
import { mobileApi } from '../../src/api/mobileApi';
import type { CatalogueCategory } from '../../src/api/types';
import { Screen } from '../../src/components/Screen';
import { colors, fonts } from '../../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);
type ProviderOption = { key: string; title: string; categories: CatalogueCategory[]; logo?: ImageSourcePropType };
const networkLogos: Record<string, ImageSourcePropType> = {
  mtn: require('../../assets/networks/mtn.png'), airtel: require('../../assets/networks/airtel.png'),
  glo: require('../../assets/networks/glo.png'), '9mobile': require('../../assets/networks/9mobile.png'),
};

export default function ServiceScreen() {
  const params = useLocalSearchParams<{ slug: string; name?: string }>();
  const categories = useQuery({ queryKey: ['categories', params.slug], queryFn: () => mobileApi.categories(params.slug) });
  const [selected, setSelected] = useState<ProviderOption | null>(null);
  const isData = params.slug === 'data';
  const isNetworkService = isData || params.slug === 'airtime';
  const providers = useMemo(() => buildProviders(categories.data ?? [], isNetworkService), [categories.data, isNetworkService]);
  const planQueries = useQueries({ queries: (selected?.categories ?? []).map((category) => ({ queryKey: ['plans', category.id], queryFn: () => mobileApi.plans(category.id) })) });
  const plans = planQueries.flatMap((query) => query.data ?? []).filter((plan, index, all) => all.findIndex((item) => item.id === plan.id) === index);
  const plansPending = planQueries.some((query) => query.isPending);
  return <><Stack.Screen options={{ headerShown: true, title: params.name ?? 'Choose provider' }} /><Screen><Text style={styles.eyebrow}>{isData ? 'BUY DATA' : 'SELECT SERVICE'}</Text><Text style={styles.heading}>{isData ? 'Stay connected' : 'Choose a provider'}</Text><Text style={styles.subheading}>{isData ? 'Pick a network, choose the plan that fits, and send it instantly.' : 'Select a provider to see available plans.'}</Text>{categories.isPending ? <ActivityIndicator color={colors.primary} style={styles.loading} /> : <><Text style={styles.stepLabel}>1  Choose provider</Text><View style={styles.providers}>{providers.map((provider) => { const active = selected?.key === provider.key; return <Pressable key={provider.key} onPress={() => setSelected(provider)} style={({ pressed }) => [styles.provider, active && styles.providerActive, pressed && styles.pressed]}><View style={styles.providerMark}>{provider.logo ? <Image resizeMode="contain" source={provider.logo} style={styles.providerLogo} /> : <Text style={styles.providerFallback}>◆</Text>}</View><Text numberOfLines={1} style={[styles.providerName, active && styles.providerNameActive]}>{provider.title}</Text></Pressable>; })}</View>{!providers.length && <Text style={styles.empty}>No plans are currently available.</Text>}{selected ? <><View style={styles.planHeading}><Text style={styles.stepLabel}>2  Choose a plan</Text><Text style={styles.planCount}>{plans.length} available</Text></View>{plansPending ? <ActivityIndicator color={colors.primary} style={styles.loading} /> : <View style={styles.planList}>{plans.map((plan) => <Pressable key={plan.id} onPress={() => router.push({ pathname: '/checkout', params: { product: params.slug, planId: plan.id, planName: plan.name, price: String(plan.price), provider: selected.title } })} style={({ pressed }) => [styles.plan, pressed && styles.pressed]}><View style={styles.planCopy}><Text style={styles.planName}>{plan.name}</Text><Text style={styles.planMeta}>{plan.validity_days ? `Valid for ${plan.validity_days} days` : 'Instant delivery'}</Text></View><View><Text style={styles.price}>{money(plan.price)}</Text><Text style={styles.selectText}>Select ›</Text></View></Pressable>)}</View>}</> : <View style={styles.prompt}><View style={styles.promptIcon}><Text style={styles.promptIconText}>⌁</Text></View><Text style={styles.promptTitle}>Select your network</Text><Text style={styles.promptText}>Available plans will appear here.</Text></View>}</>}</Screen></>;
}

function buildProviders(categories: CatalogueCategory[], dataOnly: boolean): ProviderOption[] {
  if (!dataOnly) return categories.map((category) => ({ key: category.id, title: category.network?.name ?? category.name, categories: [category] }));
  const order = ['mtn', 'airtel', 'glo', '9mobile'];
  const groups = new Map<string, CatalogueCategory[]>();
  categories.forEach((category) => { const key = networkKey(category.network?.name ?? category.name); if (key) groups.set(key, [...(groups.get(key) ?? []), category]); });
  return order.flatMap((key) => groups.has(key) ? [{ key, title: key === '9mobile' ? '9mobile' : key[0].toUpperCase() + key.slice(1), categories: groups.get(key)!, logo: networkLogos[key] }] : []);
}

function networkKey(value: string) {
  const name = value.toLowerCase();
  if (name.includes('mtn')) return 'mtn';
  if (name.includes('airtel')) return 'airtel';
  if (name.includes('glo')) return 'glo';
  if (name.includes('9mobile') || name.includes('etisalat')) return '9mobile';
  return null;
}

const styles = StyleSheet.create({ eyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 10, letterSpacing: 1.4 }, heading: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 28, letterSpacing: -0.8, marginTop: 3 }, subheading: { color: colors.muted, fontFamily: fonts.regular, lineHeight: 20, marginTop: 6 }, loading: { marginTop: 38 }, stepLabel: { color: colors.text, fontFamily: fonts.bold, fontSize: 13, marginTop: 26 }, providers: { flexDirection: 'row', gap: 8, paddingTop: 13 }, provider: { alignItems: 'center', backgroundColor: colors.surface, borderColor: 'transparent', borderRadius: 18, borderWidth: 2, flex: 1, height: 96, justifyContent: 'center', minWidth: 0, paddingHorizontal: 5, paddingVertical: 9 }, providerActive: { backgroundColor: '#F2FCF8', borderColor: colors.primary }, providerMark: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 15, height: 46, justifyContent: 'center', overflow: 'hidden', width: 46 }, providerLogo: { height: 40, width: 40 }, providerFallback: { color: colors.primary, fontSize: 18 }, providerName: { color: colors.text, fontFamily: fonts.semiBold, fontSize: 9, marginTop: 6, maxWidth: 64 }, providerNameActive: { color: colors.primaryDark, fontFamily: fonts.bold }, pressed: { opacity: 0.72, transform: [{ scale: 0.985 }] }, planHeading: { alignItems: 'baseline', flexDirection: 'row', justifyContent: 'space-between' }, planCount: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10 }, planList: { gap: 10, marginTop: 13 }, plan: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 18, elevation: 2, flexDirection: 'row', padding: 16, shadowColor: '#193E33', shadowOffset: { width: 0, height: 5 }, shadowOpacity: 0.05, shadowRadius: 10 }, planCopy: { flex: 1, marginRight: 10 }, planName: { color: colors.text, fontFamily: fonts.bold, fontSize: 13 }, planMeta: { color: colors.muted, fontFamily: fonts.regular, fontSize: 10, marginTop: 5 }, price: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 14, textAlign: 'right' }, selectText: { color: colors.primary, fontFamily: fonts.bold, fontSize: 9, marginTop: 4, textAlign: 'right' }, prompt: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 20, marginTop: 24, padding: 28 }, promptIcon: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 22, height: 48, justifyContent: 'center', width: 48 }, promptIconText: { color: colors.primary, fontSize: 22, fontWeight: '800' }, promptTitle: { color: colors.text, fontFamily: fonts.bold, fontSize: 14, marginTop: 12 }, promptText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, marginTop: 4 }, empty: { color: colors.muted, fontFamily: fonts.regular, padding: 30, textAlign: 'center' } });
