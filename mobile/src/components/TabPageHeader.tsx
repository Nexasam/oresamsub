import { router } from 'expo-router';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { colors, fonts } from '../theme/colors';
import { MaterialIcon } from './MaterialIcon';

type Props = {
  eyebrow?: string;
  subtitle?: string;
  title: string;
};

export function TabPageHeader({ eyebrow, subtitle, title }: Props) {
  return (
    <View style={styles.header}>
      <View style={styles.copy}>
        {eyebrow ? <Text style={styles.eyebrow}>{eyebrow}</Text> : null}
        <Text style={styles.title}>{title}</Text>
        {subtitle ? <Text style={styles.subtitle}>{subtitle}</Text> : null}
      </View>
      <Pressable
        accessibilityLabel="Go to dashboard"
        accessibilityRole="button"
        onPress={() => router.replace('/(tabs)')}
        style={({ pressed }) => [styles.home, pressed && styles.pressed]}
      >
        <MaterialIcon color={colors.primaryDark} name="home" size={18} />
        <Text style={styles.homeText}>Home</Text>
      </Pressable>
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    alignItems: 'flex-start',
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 18,
  },
  copy: { flex: 1, marginRight: 12 },
  eyebrow: {
    color: colors.primary,
    fontFamily: fonts.extraBold,
    fontSize: 9,
    letterSpacing: 1.4,
    marginBottom: 2,
  },
  title: {
    color: colors.text,
    fontFamily: fonts.extraBold,
    fontSize: 28,
    letterSpacing: -0.8,
  },
  subtitle: {
    color: colors.muted,
    fontFamily: fonts.regular,
    fontSize: 11,
    lineHeight: 16,
    marginTop: 4,
  },
  home: {
    alignItems: 'center',
    backgroundColor: colors.primarySoft,
    borderColor: '#C7ECDD',
    borderRadius: 14,
    borderWidth: 1,
    flexDirection: 'row',
    gap: 5,
    marginTop: 2,
    paddingHorizontal: 11,
    paddingVertical: 9,
  },
  homeText: {
    color: colors.primaryDark,
    fontFamily: fonts.bold,
    fontSize: 10,
  },
  pressed: { opacity: 0.7, transform: [{ scale: 0.97 }] },
});
