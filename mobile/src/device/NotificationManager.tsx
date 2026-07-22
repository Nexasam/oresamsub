import Constants from 'expo-constants';
import * as Device from 'expo-device';
import * as Notifications from 'expo-notifications';
import { router } from 'expo-router';
import { useEffect } from 'react';
import { Platform } from 'react-native';
import { useAuthStore } from '../auth/authStore';
import { deviceApi } from './deviceApi';
import { getDeviceUuid } from './deviceIdentity';

Notifications.setNotificationHandler({ handleNotification: async () => ({ shouldPlaySound: true, shouldSetBadge: true, shouldShowBanner: true, shouldShowList: true }) });

export function NotificationManager() {
  const authenticated = useAuthStore((state) => state.status === 'authenticated');

  useEffect(() => {
    if (!authenticated) return;
    void registerForPushNotifications().catch(() => undefined);
    const subscription = Notifications.addNotificationResponseReceivedListener((response) => {
      routeNotificationData(response.notification.request.content.data);
    });
    return () => subscription.remove();
  }, [authenticated]);

  return null;
}

export function routeNotificationData(data: Record<string, unknown> | undefined) {
  const transactionId = data?.transaction_id;
  if (typeof transactionId === 'string') router.push({ pathname: '/transaction/[id]', params: { id: transactionId } });
  else if (data?.screen === 'wallet') router.push('/wallet');
}

async function registerForPushNotifications() {
  if (!Device.isDevice) return;
  if (Platform.OS === 'android') await Notifications.setNotificationChannelAsync('transactions', { name: 'Transactions', importance: Notifications.AndroidImportance.HIGH });
  const existing = await Notifications.getPermissionsAsync();
  const permission = existing.status === 'granted' ? existing : await Notifications.requestPermissionsAsync();
  if (permission.status !== 'granted') return;
  const projectId = process.env.EXPO_PUBLIC_EAS_PROJECT_ID ?? Constants.easConfig?.projectId;
  if (!projectId) return;
  const token = await Notifications.getExpoPushTokenAsync({ projectId });
  await deviceApi.register({
    device_uuid: await getDeviceUuid(),
    expo_push_token: token.data,
    platform: Platform.OS as 'ios' | 'android',
    app_version: ApplicationVersion(),
    device_name: Device.deviceName,
  });
}

function ApplicationVersion() { return Constants.expoConfig?.version ?? null; }
