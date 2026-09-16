import * as SecureStore from 'expo-secure-store';

const passcodeKey = 'oresamsub.app_lock.passcode';

export const appLock = {
  async isConfigured() {
    return !!(await SecureStore.getItemAsync(passcodeKey));
  },
  async setPasscode(passcode: string) {
    if (!/^\d{6}$/.test(passcode)) throw new Error('Enter a six-digit app passcode.');
    await SecureStore.setItemAsync(passcodeKey, passcode);
  },
  async verify(passcode: string) {
    const stored = await SecureStore.getItemAsync(passcodeKey);
    return !!stored && stored === passcode;
  },
  async clear() {
    await SecureStore.deleteItemAsync(passcodeKey);
  },
};
