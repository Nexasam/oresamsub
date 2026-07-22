import { useQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { ActivityIndicator, Pressable, StyleSheet, Text, View } from 'react-native';
import { mobileApi } from '../../src/api/mobileApi';
import { Screen } from '../../src/components/Screen';
import { colors } from '../../src/theme/colors';

const symbols: Record<string, string> = { data: '📶', airtime: '☎', cable: '▣', electricity: '⚡' };

export default function ServicesScreen() {
  const query = useQuery({ queryKey: ['products'], queryFn: mobileApi.products });
  return <Screen><Text style={styles.eyebrow}>EVERYDAY PAYMENTS</Text><Text style={styles.title}>Services</Text><Text style={styles.subtitle}>Fast, secure payments in a few taps.</Text>{query.isPending ? <ActivityIndicator color={colors.primary} style={{ marginTop: 40 }} /> : <View style={styles.list}>{query.data?.map((service, index) => <Pressable key={service.id} onPress={() => router.push({ pathname: '/service/[slug]', params: { slug: service.slug, name: service.name } })} style={({ pressed }) => [styles.card, pressed && styles.pressed]}><View style={[styles.iconBox, index % 2 ? styles.iconAlt : null]}><Text style={styles.icon}>{symbols[service.slug] ?? '◈'}</Text></View><View style={styles.copy}><Text style={styles.name}>{service.name}</Text><Text style={styles.note}>Plans and instant payment</Text></View><View style={styles.arrowBox}><Text style={styles.arrow}>›</Text></View></Pressable>)}</View>}</Screen>;
}

const styles = StyleSheet.create({ eyebrow: { color: colors.primary, fontSize: 10, fontWeight: '800', letterSpacing: 1.4, marginTop: 4 }, title: { color: colors.text, fontSize: 30, fontWeight: '800', letterSpacing: -0.8, marginTop: 2 }, subtitle: { color: colors.muted, fontSize: 14, marginTop: 5 }, list: { gap: 12, marginTop: 26 }, card: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 20, elevation: 2, flexDirection: 'row', padding: 15, shadowColor: '#193E33', shadowOffset: { width: 0, height: 6 }, shadowOpacity: 0.06, shadowRadius: 12 }, pressed: { opacity: 0.72, transform: [{ scale: 0.985 }] }, iconBox: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 16, height: 52, justifyContent: 'center', width: 52 }, iconAlt: { backgroundColor: '#FFF2D7' }, icon: { color: colors.primaryDark, fontSize: 24, fontWeight: '800' }, copy: { flex: 1, marginLeft: 14 }, name: { color: colors.text, fontSize: 16, fontWeight: '800', letterSpacing: -0.2 }, note: { color: colors.muted, fontSize: 11, marginTop: 4 }, arrowBox: { alignItems: 'center', backgroundColor: colors.surfaceMuted, borderRadius: 14, height: 30, justifyContent: 'center', width: 30 }, arrow: { color: colors.primary, fontSize: 22, marginTop: -2 } });
