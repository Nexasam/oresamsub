import type { PropsWithChildren, RefObject } from 'react';
import { KeyboardAvoidingView, Platform, ScrollView, StyleSheet, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { colors } from '../theme/colors';

type Props = PropsWithChildren<{
  safeTop?: boolean;
  scroll?: boolean;
  scrollRef?: RefObject<ScrollView | null>;
}>;

export function Screen({ children, safeTop = true, scroll = true, scrollRef }: Props) {
  return (
    <KeyboardAvoidingView behavior="height" enabled={Platform.OS === 'android'} style={styles.flex}>
      <SafeAreaView edges={safeTop ? ['top'] : []} style={styles.safe}>
        {scroll ? (
          <ScrollView
            automaticallyAdjustKeyboardInsets={Platform.OS === 'ios'}
            contentContainerStyle={styles.content}
            keyboardDismissMode={Platform.OS === 'ios' ? 'interactive' : 'on-drag'}
            keyboardShouldPersistTaps="handled"
            ref={scrollRef}
          >
            {children}
          </ScrollView>
        ) : (
          <View style={styles.content}>{children}</View>
        )}
      </SafeAreaView>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  flex: { backgroundColor: colors.background, flex: 1 },
  safe: { backgroundColor: colors.background, flex: 1 },
  content: { flexGrow: 1, paddingHorizontal: 20, paddingTop: 14, paddingBottom: 112 },
});
