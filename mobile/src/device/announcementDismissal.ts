import * as SecureStore from 'expo-secure-store';

const key = 'oresamsub.announcements.dismissed_until';
const oneDayMs = 24 * 60 * 60 * 1000;

export const announcementDismissal = {
  async shouldAutoOpen() {
    const stored = await SecureStore.getItemAsync(key);
    const dismissedUntil = stored ? Number(stored) : 0;
    return !Number.isFinite(dismissedUntil) || Date.now() >= dismissedUntil;
  },

  async dismissForOneDay() {
    await SecureStore.setItemAsync(key, String(Date.now() + oneDayMs));
  },
};
