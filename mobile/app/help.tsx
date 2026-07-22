import { useQuery } from '@tanstack/react-query';
import { router, Stack } from 'expo-router';
import { Linking, Pressable, StyleSheet, Text, View } from 'react-native';
import { apiRequest } from '../src/api/client';
import { Screen } from '../src/components/Screen';
import { colors } from '../src/theme/colors';

type Support = { email: string | null; phone: string | null; whatsapp: string | null };
type Config = { legal: { privacy_url: string; terms_url: string; account_deletion_url: string } };
export default function HelpScreen() {
  const support = useQuery({ queryKey: ['support'], queryFn: async () => (await apiRequest<Support>('/support')).data });
  const config = useQuery({ queryKey: ['mobile-config-help'], queryFn: async () => (await apiRequest<Config>('/config')).data });
  return <><Stack.Screen options={{ headerShown: true, title: 'Help and policies' }} /><Screen><Text style={styles.heading}>We’re here to help</Text><View style={styles.list}>{support.data?.email && <Link label="Email support" value={support.data.email} url={`mailto:${support.data.email}`} />}{support.data?.phone && <Link label="Call support" value={support.data.phone} url={`tel:${support.data.phone}`} />}{support.data?.whatsapp && <Link label="WhatsApp" value={support.data.whatsapp} url={`https://wa.me/${support.data.whatsapp.replace(/\D/g, '')}`} />}</View><Text style={styles.section}>Legal and account</Text><View style={styles.list}>{config.data && <><Link label="Privacy policy" url={config.data.legal.privacy_url} /><Link label="Terms and conditions" url={config.data.legal.terms_url} /><Link label="Account deletion instructions" url={config.data.legal.account_deletion_url} /></>}<Pressable onPress={() => router.push('/delete-account')} style={styles.link}><Text style={styles.danger}>Deactivate my account</Text><Text style={styles.arrow}>›</Text></Pressable></View></Screen></>;
}
function Link({ label, value, url }: { label: string; value?: string; url: string }) { return <Pressable onPress={() => void Linking.openURL(url)} style={styles.link}><View><Text style={styles.label}>{label}</Text>{value && <Text style={styles.value}>{value}</Text>}</View><Text style={styles.arrow}>›</Text></Pressable>; }
const styles = StyleSheet.create({ heading: { color: colors.text, fontSize: 25, fontWeight: '800' }, section: { color: colors.text, fontSize: 18, fontWeight: '800', marginTop: 28 }, list: { marginTop: 15 }, link: { alignItems: 'center', backgroundColor: colors.surface, borderBottomColor: colors.border, borderBottomWidth: 1, flexDirection: 'row', justifyContent: 'space-between', padding: 16 }, label: { color: colors.text, fontWeight: '800' }, value: { color: colors.muted, fontSize: 12, marginTop: 4 }, danger: { color: colors.danger, fontWeight: '800' }, arrow: { color: colors.primary, fontSize: 25 } });
