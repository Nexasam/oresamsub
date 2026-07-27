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
    void registerForPushNotifications().catch((error) => {
      console.warn('Push notification registration failed', error);
    });
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

export async function registerForPushNotifications() {
  if (!Device.isDevice) throw new Error('Push notifications require a physical phone.');
  if (Platform.OS === 'android') {
    await Notifications.setNotificationChannelAsync('transactions', {
      name: 'Transaction alerts',
      importance: Notifications.AndroidImportance.HIGH,
      sound: 'default',
      vibrationPattern: [0, 250, 150, 250],
    });
  }
  const existing = await Notifications.getPermissionsAsync();
  const permission = existing.status === 'granted' ? existing : await Notifications.requestPermissionsAsync();
  if (permission.status !== 'granted') throw new Error('Notification permission is disabled. Enable it in your phone settings.');
  const configuredProjectId = Constants.expoConfig?.extra?.eas?.projectId;
  const projectId = process.env.EXPO_PUBLIC_EAS_PROJECT_ID
    ?? (typeof configuredProjectId === 'string' ? configuredProjectId : undefined)
    ?? Constants.easConfig?.projectId;
  if (!projectId) throw new Error('The EAS project ID is missing from this build.');
  const token = await Notifications.getExpoPushTokenAsync({ projectId });
  const response = await deviceApi.register({
    device_uuid: await getDeviceUuid(),
    expo_push_token: token.data,
    platform: Platform.OS as 'ios' | 'android',
    app_version: ApplicationVersion(),
    device_name: Device.deviceName,
  });
  return response.data.device;
}

function ApplicationVersion() { return Constants.expoConfig?.version ?? null; }
