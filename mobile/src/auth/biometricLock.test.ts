jest.mock('expo-secure-store', () => ({ getItemAsync: jest.fn(), setItemAsync: jest.fn() }));
jest.mock('expo-local-authentication', () => ({ hasHardwareAsync: jest.fn(), isEnrolledAsync: jest.fn(), authenticateAsync: jest.fn() }));

import * as LocalAuthentication from 'expo-local-authentication';
import * as SecureStore from 'expo-secure-store';
import { biometricLock } from './biometricLock';

it('fails safely when biometric authentication is cancelled', async () => {
  jest.mocked(LocalAuthentication.authenticateAsync).mockResolvedValue({ success: false, error: 'user_cancel' });
  await expect(biometricLock.unlock()).resolves.toBe(false);
});

it('persists biometric opt-in only as a local setting', async () => {
  await biometricLock.setEnabled(true);
  expect(SecureStore.setItemAsync).toHaveBeenCalledWith('oresamsub.biometric.enabled', 'true');
});
