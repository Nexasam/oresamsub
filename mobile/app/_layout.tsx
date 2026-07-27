import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useState } from 'react';
import { useFonts } from 'expo-font';
import { Manrope_400Regular } from '@expo-google-fonts/manrope/400Regular';
import { Manrope_500Medium } from '@expo-google-fonts/manrope/500Medium';
import { Manrope_600SemiBold } from '@expo-google-fonts/manrope/600SemiBold';
import { Manrope_700Bold } from '@expo-google-fonts/manrope/700Bold';
import { Manrope_800ExtraBold } from '@expo-google-fonts/manrope/800ExtraBold';
import { MaterialSymbols_400Regular } from '@expo-google-fonts/material-symbols/400Regular';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { NotificationManager } from '../src/device/NotificationManager';
import { OfflineBanner } from '../src/components/OfflineBanner';
import { BootstrapGate } from '../src/config/BootstrapGate';
import { colors, fonts } from '../src/theme/colors';

export default function RootLayout() {
  const [fontsLoaded] = useFonts({ Manrope_400Regular, Manrope_500Medium, Manrope_600SemiBold, Manrope_700Bold, Manrope_800ExtraBold, MaterialSymbols_400Regular });
  const [queryClient] = useState(
    () =>
      new QueryClient({
        defaultOptions: {
          queries: {
            retry: 1,
            staleTime: 30_000,
          },
          mutations: {
            retry: false,
          },
        },
      }),
  );

  if (!fontsLoaded) return null;

  return (
    <SafeAreaProvider>
      <QueryClientProvider client={queryClient}>
        <StatusBar style="dark" />
        <BootstrapGate>
          <OfflineBanner />
          <NotificationManager />
          <Stack screenOptions={{ headerShown: false, headerBackButtonDisplayMode: 'minimal', headerShadowVisible: false, headerStyle: { backgroundColor: colors.background }, headerTintColor: colors.primaryDark, headerTitleStyle: { fontFamily: fonts.bold, fontSize: 15 } }} />
        </BootstrapGate>
      </QueryClientProvider>
    </SafeAreaProvider>
  );
}
