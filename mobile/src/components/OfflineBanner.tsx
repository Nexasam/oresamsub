import NetInfo from '@react-native-community/netinfo';
import { useEffect, useState } from 'react';
import { StyleSheet, Text } from 'react-native';
import { colors } from '../theme/colors';

export function OfflineBanner() {
  const [offline, setOffline] = useState(false);
  useEffect(() => NetInfo.addEventListener((state) => setOffline(state.isConnected === false)), []);
  return offline ? <Text accessibilityRole="alert" style={styles.banner}>You are offline. Purchases are unavailable until you reconnect.</Text> : null;
}
const styles = StyleSheet.create({ banner: { backgroundColor: colors.warning, color: colors.white, fontSize: 12, fontWeight: '700', padding: 9, textAlign: 'center' } });
