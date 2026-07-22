import { Tabs } from 'expo-router';
import { type ColorValue, StyleSheet, Text } from 'react-native';
import { colors } from '../../src/theme/colors';

const icon = (symbol: string) => ({ color }: { color: ColorValue }) => <Text style={[styles.icon, { color }]}>{symbol}</Text>;

export default function TabsLayout() {
  return (
    <Tabs screenOptions={{ headerShown: false, tabBarActiveTintColor: colors.primary, tabBarInactiveTintColor: '#8A9B95', tabBarLabelStyle: styles.label, tabBarStyle: styles.bar, tabBarItemStyle: styles.item }}>
      <Tabs.Screen name="index" options={{ title: 'Home', tabBarIcon: icon('⌂') }} />
      <Tabs.Screen name="services" options={{ title: 'Services', tabBarIcon: icon('◇') }} />
      <Tabs.Screen name="transactions" options={{ title: 'Activity', tabBarIcon: icon('↕') }} />
      <Tabs.Screen name="wallet" options={{ title: 'Wallet', tabBarIcon: icon('₦') }} />
      <Tabs.Screen name="account" options={{ title: 'Profile', tabBarIcon: icon('○') }} />
    </Tabs>
  );
}

const styles = StyleSheet.create({
  bar: { backgroundColor: colors.surface, borderTopColor: 'transparent', elevation: 18, height: 78, paddingBottom: 10, paddingTop: 9, shadowColor: '#123D30', shadowOffset: { width: 0, height: -6 }, shadowOpacity: 0.08, shadowRadius: 18 },
  item: { borderRadius: 16 },
  icon: { fontSize: 22, fontWeight: '700' },
  label: { fontSize: 10, fontWeight: '800', letterSpacing: 0.1 },
});
