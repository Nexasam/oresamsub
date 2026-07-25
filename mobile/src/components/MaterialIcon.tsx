import { StyleSheet, Text, type ColorValue, type TextStyle } from 'react-native';

type Props = {
  color?: ColorValue;
  name: string;
  size?: number;
  style?: TextStyle;
};

export function MaterialIcon({ color = '#10231D', name, size = 24, style }: Props) {
  return <Text style={[styles.icon, { color, fontSize: size, lineHeight: size }, style]}>{name}</Text>;
}

const styles = StyleSheet.create({
  icon: {
    fontFamily: 'MaterialSymbols_400Regular',
    includeFontPadding: false,
    textAlign: 'center',
  },
});
