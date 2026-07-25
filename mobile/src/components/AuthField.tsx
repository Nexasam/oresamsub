import { Pressable, StyleSheet, Text, TextInput, type TextInputProps, View, type ViewStyle } from 'react-native';
import { colors, fonts } from '../theme/colors';
import { MaterialIcon } from './MaterialIcon';

type Props = TextInputProps & {
  error?: string;
  icon: string;
  label: string;
  onToggleSecure?: () => void;
  wrapperStyle?: ViewStyle;
};

export function AuthField({ error, icon, label, onToggleSecure, wrapperStyle, ...inputProps }: Props) {
  return (
    <View style={[styles.field, wrapperStyle]}>
      <Text style={styles.label}>{label}</Text>
      <View style={[styles.inputShell, error && styles.inputShellError]}>
        <MaterialIcon color={error ? colors.danger : '#879790'} name={icon} size={20} />
        <TextInput
          {...inputProps}
          placeholderTextColor="#9AA9A3"
          selectionColor={colors.primary}
          style={styles.input}
        />
        {onToggleSecure && (
          <Pressable hitSlop={10} onPress={onToggleSecure} style={styles.eye}>
            <MaterialIcon color={colors.muted} name={inputProps.secureTextEntry ? 'visibility' : 'visibility_off'} size={20} />
          </Pressable>
        )}
      </View>
      {!!error && <Text style={styles.error}>{error}</Text>}
    </View>
  );
}

const styles = StyleSheet.create({
  field: { marginBottom: 16 },
  label: { color: colors.text, fontFamily: fonts.bold, fontSize: 11, marginBottom: 7 },
  inputShell: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 15, borderWidth: 1, flexDirection: 'row', minHeight: 54, paddingHorizontal: 15 },
  inputShellError: { borderColor: '#F3A7A7' },
  input: { color: colors.text, flex: 1, fontFamily: fonts.medium, fontSize: 14, paddingHorizontal: 11, paddingVertical: 14 },
  eye: { alignItems: 'center', height: 34, justifyContent: 'center', width: 34 },
  error: { color: colors.danger, fontFamily: fonts.medium, fontSize: 10, lineHeight: 15, marginTop: 5 },
});
