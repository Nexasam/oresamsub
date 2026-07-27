import { useState } from 'react';
import type { StyleProp, ViewStyle } from 'react-native';
import { Pressable, StyleSheet, Text, TextInput, View } from 'react-native';

import { colors, fonts } from '../theme/colors';
import { MaterialIcon } from './MaterialIcon';

type PinInputProps = {
  autoFocus?: boolean;
  label: string;
  onChangeText: (value: string) => void;
  onFocus?: () => void;
  style?: StyleProp<ViewStyle>;
  value: string;
};

export function PinInput({ autoFocus, label, onChangeText, onFocus, style, value }: PinInputProps) {
  const [visible, setVisible] = useState(false);

  return (
    <View style={style}>
      <Text style={styles.label}>{label}</Text>
      <View style={styles.shell}>
        <MaterialIcon color={colors.muted} name="lock" size={20} />
        <TextInput
          autoFocus={autoFocus}
          cursorColor={colors.primary}
          keyboardType="number-pad"
          maxLength={4}
          onChangeText={(nextValue) => onChangeText(nextValue.replace(/\D/g, '').slice(0, 4))}
          onFocus={onFocus}
          placeholder="Enter 4 digits"
          placeholderTextColor={colors.muted}
          secureTextEntry={!visible}
          selectionColor={colors.primary}
          style={styles.input}
          value={value}
        />
        <Pressable
          accessibilityLabel={visible ? `Hide ${label}` : `Show ${label}`}
          accessibilityRole="button"
          hitSlop={10}
          onPress={() => setVisible((current) => !current)}
          style={styles.visibility}
        >
          <MaterialIcon color={colors.primary} name={visible ? 'visibility_off' : 'visibility'} size={21} />
        </Pressable>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  label: {
    color: colors.text,
    fontFamily: fonts.semiBold,
    fontSize: 13,
    marginBottom: 7,
  },
  shell: {
    alignItems: 'center',
    backgroundColor: colors.surface,
    borderColor: colors.border,
    borderRadius: 14,
    borderWidth: 1,
    flexDirection: 'row',
    minHeight: 56,
    paddingHorizontal: 15,
  },
  input: {
    color: colors.text,
    flex: 1,
    fontFamily: fonts.bold,
    fontSize: 18,
    letterSpacing: 7,
    paddingHorizontal: 12,
    paddingVertical: 14,
  },
  visibility: {
    alignItems: 'center',
    height: 38,
    justifyContent: 'center',
    width: 38,
  },
});
