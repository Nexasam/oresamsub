jest.mock('expo-secure-store', () => ({ getItemAsync: jest.fn(), setItemAsync: jest.fn() }));

import * as SecureStore from 'expo-secure-store';
import { announcementDismissal } from './announcementDismissal';

const getItemAsync = SecureStore.getItemAsync as jest.MockedFunction<typeof SecureStore.getItemAsync>;
const setItemAsync = SecureStore.setItemAsync as jest.MockedFunction<typeof SecureStore.setItemAsync>;

beforeEach(() => {
  jest.clearAllMocks();
  jest.spyOn(Date, 'now').mockReturnValue(1_000_000);
});

afterEach(() => {
  jest.restoreAllMocks();
});

it('auto-opens when an announcement has not been dismissed', async () => {
  getItemAsync.mockResolvedValue(null);
  await expect(announcementDismissal.shouldAutoOpen()).resolves.toBe(true);
});

it('keeps announcements hidden until the dismissal expires', async () => {
  getItemAsync.mockResolvedValue(String(1_000_001));
  await expect(announcementDismissal.shouldAutoOpen()).resolves.toBe(false);

  getItemAsync.mockResolvedValue(String(999_999));
  await expect(announcementDismissal.shouldAutoOpen()).resolves.toBe(true);
});

it('stores a 24-hour dismissal deadline', async () => {
  await announcementDismissal.dismissForOneDay();
  expect(setItemAsync).toHaveBeenCalledWith(
    'oresamsub.announcements.dismissed_until',
    String(1_000_000 + 24 * 60 * 60 * 1000),
  );
});
