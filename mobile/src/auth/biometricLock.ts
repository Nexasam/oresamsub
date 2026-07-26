import * as LocalAuthentication from 'expo-local-authentication';
import * as SecureStore from 'expo-secure-store';

const key = 'oresamsub.biometric.enabled';

export const biometricLock = {
  async isEnabled() { return (await SecureStore.getItemAsync(key)) === 'true'; },
  async setEnabled(enabled: boolean) { await SecureStore.setItemAsync(key, String(enabled)); },
  async availability() {
    try {
      const hasHardware = await LocalAuthentication.hasHardwareAsync();
      const isEnrolled = hasHardware ? await LocalAuthentication.isEnrolledAsync() : false;
      return { available: hasHardware && isEnrolled, hasHardware, isEnrolled };
    } catch {
      return { available: false, hasHardware: false, isEnrolled: false };
    }
  },
  async isAvailable() { return (await this.availability()).available; },
  async unlock() {
    try {
      const result = await LocalAuthentication.authenticateAsync({
        promptMessage: 'Unlock OresamSub',
        promptSubtitle: 'Confirm it is you to restore your secure session',
        cancelLabel: 'Use password',
        disableDeviceFallback: false,
      });
      return result.success;
    } catch {
      return false;
    }
  },
};
