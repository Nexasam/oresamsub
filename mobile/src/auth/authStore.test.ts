jest.mock('./tokenVault', () => ({ tokenVault: { readRefreshToken: jest.fn(), save: jest.fn(), clear: jest.fn() } }));
jest.mock('./authApi', () => ({ authApi: { session: jest.fn(), login: jest.fn(), register: jest.fn(), logout: jest.fn() } }));

import { authApi } from './authApi';
import { useAuthStore } from './authStore';
import { tokenVault } from './tokenVault';

const user = { id: 'u1', first_name: 'Ore', last_name: 'User', other_names: null, username: 'ore', email: 'ore@example.com', phone_number: '08030000000', phone_verified: true, transaction_pin_set: true, is_deactivated: false, customer_landmark: null };
const onboarding = { phone_verified: true, transaction_pin_set: true, profile_complete: true };

describe('auth session restoration', () => {
  beforeEach(() => useAuthStore.setState({ status: 'loading', user: null, onboarding: null }));

  it('becomes a guest without a stored refresh token', async () => {
    jest.mocked(tokenVault.readRefreshToken).mockResolvedValue(null);
    await useAuthStore.getState().restore();
    expect(useAuthStore.getState().status).toBe('guest');
    expect(authApi.session).not.toHaveBeenCalled();
  });

  it('restores an authenticated server session', async () => {
    jest.mocked(tokenVault.readRefreshToken).mockResolvedValue('refresh-token');
    jest.mocked(authApi.session).mockResolvedValue({ success: true, message: 'ok', data: { user, onboarding }, meta: null, errors: null });
    await useAuthStore.getState().restore();
    expect(useAuthStore.getState().status).toBe('authenticated');
    expect(useAuthStore.getState().user?.id).toBe('u1');
  });

  it('clears an invalid saved session', async () => {
    jest.mocked(tokenVault.readRefreshToken).mockResolvedValue('expired');
    jest.mocked(authApi.session).mockRejectedValue(new Error('expired'));
    await useAuthStore.getState().restore();
    expect(tokenVault.clear).toHaveBeenCalled();
    expect(useAuthStore.getState().status).toBe('guest');
  });
});
