import * as LocalAuthentication from 'expo-local-authentication';
import * as SecureStore from 'expo-secure-store';

const key = 'oresamsub.biometric.enabled';

export const biometricLock = {
  async isEnabled() { return (await SecureStore.getItemAsync(key)) === 'true'; },
  async setEnabled(enabled: boolean) { await SecureStore.setItemAsync(key, String(enabled)); },
  async isAvailable() { return (await LocalAuthentication.hasHardwareAsync()) && (await LocalAuthentication.isEnrolledAsync()); },
  async unlock() {
    const result = await LocalAuthentication.authenticateAsync({ promptMessage: 'Unlock OresamSub', cancelLabel: 'Use password', disableDeviceFallback: false });
    return result.success;
  },
};
