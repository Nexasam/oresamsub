jest.mock('expo-secure-store', () => ({ getItemAsync: jest.fn(), setItemAsync: jest.fn(), deleteItemAsync: jest.fn() }));

import * as SecureStore from 'expo-secure-store';
import { appLock } from './appLock';

it('stores and verifies a six-digit device app passcode', async () => {
  jest.mocked(SecureStore.getItemAsync).mockResolvedValue('654321');

  await appLock.setPasscode('654321');

  expect(SecureStore.setItemAsync).toHaveBeenCalledWith('oresamsub.app_lock.passcode', '654321');
  await expect(appLock.verify('654321')).resolves.toBe(true);
  await expect(appLock.verify('123456')).resolves.toBe(false);
});

it('rejects passcodes that are not exactly six digits', async () => {
  await expect(appLock.setPasscode('1234')).rejects.toThrow('six-digit');
});
