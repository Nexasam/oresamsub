import { Image, StyleSheet, View } from 'react-native';

type BrandLogoProps = {
  size?: number;
};

export function BrandLogo({ size = 44 }: BrandLogoProps) {
  return (
    <View style={[styles.shell, { borderRadius: size * 0.28, height: size, width: size }]}>
      <Image resizeMode="contain" source={require('../../assets/icon.png')} style={{ height: size, width: size }} />
    </View>
  );
}

const styles = StyleSheet.create({
  shell: {
    backgroundColor: '#FFFFFF',
    overflow: 'hidden',
  },
});
