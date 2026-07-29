import * as Application from 'expo-application';
import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

const key = 'oresamsub.device.uuid';
const randomUuid = () => 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (char) => {
  const value = Math.floor(Math.random() * 16);
  return (char === 'x' ? value : (value & 0x3) | 0x8).toString(16);
});

let deviceUuidPromise: Promise<string> | null = null;

export async function getDeviceUuid() {
  deviceUuidPromise ??= resolveDeviceUuid();
  return deviceUuidPromise;
}

async function resolveDeviceUuid() {
  if (Platform.OS === 'web') {
    const webKey = `web.${key}`;
    const saved = typeof localStorage === 'undefined' ? null : localStorage.getItem(webKey);
    if (saved) return saved;
    const uuid = randomUuid();
    if (typeof localStorage !== 'undefined') localStorage.setItem(webKey, uuid);
    return uuid;
  }

  const saved = await SecureStore.getItemAsync(key);
  if (saved) return saved;
  const nativeId = Platform.OS === 'android' ? Application.getAndroidId() : await Application.getIosIdForVendorAsync();
  const uuid = nativeId && /^[0-9a-f-]{36}$/i.test(nativeId) ? nativeId : randomUuid();
  await SecureStore.setItemAsync(key, uuid);
  return uuid;
}
