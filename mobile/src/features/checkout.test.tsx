jest.mock('expo-router', () => ({
  router: { replace: jest.fn() },
  Stack: { Screen: () => null },
  useLocalSearchParams: () => ({ product: 'data', planId: 'plan-1', planName: '1GB Monthly', price: '500', provider: 'MTN' }),
}));
jest.mock('../api/mobileApi', () => ({ mobileApi: { purchaseData: jest.fn(), purchaseAirtime: jest.fn(), purchaseCable: jest.fn(), purchaseElectricity: jest.fn(), validateBiller: jest.fn(), purchaseStatus: jest.fn() } }));
jest.mock('expo-contacts', () => ({ Contact: { presentPicker: jest.fn() } }));

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { act, fireEvent, render, waitFor } from '@testing-library/react-native';
import CheckoutScreen from '../../app/checkout';
import { mobileApi } from '../api/mobileApi';
import { Alert } from 'react-native';

it('prevents a double-tap from submitting a financial purchase twice', async () => {
  let finish!: (value: { status: string; message: null }) => void;
  jest.mocked(mobileApi.purchaseData).mockImplementation(() => new Promise((resolve) => { finish = resolve; }));
  const client = new QueryClient({ defaultOptions: { queries: { retry: false, gcTime: Infinity } } });
  jest.spyOn(Alert, 'alert').mockImplementation(() => undefined);
  const screen = await render(<QueryClientProvider client={client}><CheckoutScreen /></QueryClientProvider>);
  await fireEvent.changeText(screen.getByPlaceholderText('08030000000'), '08030000000');
  await fireEvent.changeText(screen.getByPlaceholderText('••••'), '1234');
  const pay = screen.getByText('Pay securely');
  await fireEvent.press(pay);
  await fireEvent.press(pay);
  await waitFor(() => expect(mobileApi.purchaseData).toHaveBeenCalledTimes(1));
  await act(async () => { finish({ status: 'processed', message: null }); });
  await waitFor(() => expect(Alert.alert).toHaveBeenCalled());
  client.clear();
  await screen.unmount();
});
