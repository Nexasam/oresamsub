import { Tabs } from 'expo-router';
import { type ColorValue, StyleSheet, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { colors, fonts } from '../../src/theme/colors';

const icon = (name: string) => ({ color, focused }: { color: ColorValue; focused: boolean }) => (
  <View style={[styles.iconShell, focused && styles.iconShellActive]}>
    <MaterialIcon color={color} name={name} size={focused ? 23 : 22} />
  </View>
);

export default function TabsLayout() {
  const insets = useSafeAreaInsets();
  const bottomPadding = Math.max(insets.bottom, 8);

  return (
    <Tabs screenOptions={{ headerShown: false, tabBarActiveTintColor: colors.primaryDark, tabBarHideOnKeyboard: true, tabBarInactiveTintColor: '#8A9B95', tabBarLabelStyle: styles.label, tabBarStyle: [styles.bar, { height: 66 + bottomPadding, paddingBottom: bottomPadding }], tabBarItemStyle: styles.item }}>
      <Tabs.Screen name="index" options={{ title: 'Home', tabBarIcon: icon('home') }} />
      <Tabs.Screen name="services" options={{ title: 'Services', tabBarIcon: icon('grid_view') }} />
      <Tabs.Screen name="transactions" options={{ title: 'Transactions', tabBarIcon: icon('receipt_long') }} />
      <Tabs.Screen name="wallet" options={{ title: 'Wallet', tabBarIcon: icon('account_balance_wallet') }} />
      <Tabs.Screen name="account" options={{ title: 'Profile', tabBarIcon: icon('person') }} />
    </Tabs>
  );
}

const styles = StyleSheet.create({
  bar: { backgroundColor: colors.surface, borderTopColor: colors.border, borderTopWidth: StyleSheet.hairlineWidth, elevation: 20, paddingHorizontal: 8, paddingTop: 7, shadowColor: '#0B2F24', shadowOffset: { width: 0, height: -8 }, shadowOpacity: 0.09, shadowRadius: 22 },
  item: { borderRadius: 18 },
  iconShell: { alignItems: 'center', borderRadius: 14, height: 34, justifyContent: 'center', width: 46 },
  iconShellActive: { backgroundColor: colors.primarySoft },
  label: { fontFamily: fonts.bold, fontSize: 9, letterSpacing: 0.1, marginTop: 1 },
});
