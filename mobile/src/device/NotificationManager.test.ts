jest.mock('expo-router', () => ({ router: { push: jest.fn() } }));
jest.mock('expo-notifications', () => ({ setNotificationHandler: jest.fn(), addNotificationResponseReceivedListener: jest.fn(), AndroidImportance: { HIGH: 4 } }));
jest.mock('expo-device', () => ({ isDevice: false }));

import { router } from 'expo-router';
import { routeNotificationData } from './NotificationManager';

it('deep-links transaction notifications to authenticated transaction details', () => {
  routeNotificationData({ transaction_id: 'transaction-123' });
  expect(router.push).toHaveBeenCalledWith({ pathname: '/transaction/[id]', params: { id: 'transaction-123' } });
});

it('deep-links wallet funding notifications to the wallet tab', () => {
  routeNotificationData({ screen: 'wallet' });
  expect(router.push).toHaveBeenCalledWith('/wallet');
});

it.each([
  ['dashboard', '/(tabs)'],
  ['services', '/services'],
  ['transactions', '/transactions'],
  ['profile', '/account'],
])('deep-links %s campaigns to the selected tab', (screen, path) => {
  routeNotificationData({ screen });
  expect(router.push).toHaveBeenCalledWith(path);
});

it('deep-links data campaigns to the data purchase page', () => {
  routeNotificationData({ screen: 'data' });
  expect(router.push).toHaveBeenCalledWith({ pathname: '/service/[slug]', params: { slug: 'data', name: 'Data' } });
});
