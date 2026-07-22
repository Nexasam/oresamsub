import { Platform } from 'react-native';

import { apiRequest } from '../api/client';
import type { AuthSession } from '../api/types';

const deviceName = `OresamSub ${Platform.OS}`;

export const authApi = {
  login(login: string, password: string) {
    return apiRequest<AuthSession>('/auth/login', {
      method: 'POST',
      body: JSON.stringify({ login, password, device_name: deviceName }),
    });
  },

  register(input: {
    first_name: string;
    last_name: string;
    username: string;
    email: string;
    password: string;
    password_confirmation: string;
    referral_phone_number?: string;
  }) {
    return apiRequest<AuthSession>('/auth/register', {
      method: 'POST',
      body: JSON.stringify({ ...input, device_name: deviceName, terms_accepted: true }),
    });
  },

  session() {
    return apiRequest<AuthSession>('/auth/session', { authenticated: true });
  },

  logout() {
    return apiRequest<null>('/auth/logout', { method: 'POST', authenticated: true });
  },

  sendPhoneOtp(phone_number: string) {
    return apiRequest<null>('/auth/phone/send-otp', { method: 'POST', authenticated: true, body: JSON.stringify({ phone_number }) });
  },

  verifyPhoneOtp(otp: string) {
    return apiRequest<null>('/auth/phone/verify-otp', { method: 'POST', authenticated: true, body: JSON.stringify({ otp }) });
  },

  setTransactionPin(pin: string) {
    return apiRequest<null>('/security/pin', { method: 'POST', authenticated: true, body: JSON.stringify({ pin, pin_confirmation: pin }) });
  },
};
