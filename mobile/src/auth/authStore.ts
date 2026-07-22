import { create } from 'zustand';

import type { AuthSession, MobileUser, OnboardingState } from '../api/types';
import { authApi } from './authApi';
import { tokenVault } from './tokenVault';

type AuthStatus = 'loading' | 'authenticated' | 'guest';

type AuthState = {
  status: AuthStatus;
  user: MobileUser | null;
  onboarding: OnboardingState | null;
  restore: () => Promise<void>;
  signIn: (login: string, password: string) => Promise<AuthSession>;
  register: (input: Parameters<typeof authApi.register>[0]) => Promise<AuthSession>;
  refreshSession: () => Promise<AuthSession>;
  signOut: () => Promise<void>;
  declineRestore: () => void;
};

export const useAuthStore = create<AuthState>((set) => ({
  status: 'loading',
  user: null,
  onboarding: null,

  restore: async () => {
    const refreshToken = await tokenVault.readRefreshToken();
    if (!refreshToken) {
      set({ status: 'guest', user: null, onboarding: null });
      return;
    }

    try {
      const response = await authApi.session();
      set({ status: 'authenticated', user: response.data.user, onboarding: response.data.onboarding });
    } catch {
      await tokenVault.clear();
      set({ status: 'guest', user: null, onboarding: null });
    }
  },

  signIn: async (login, password) => {
    const response = await authApi.login(login, password);
    if (!response.data.tokens) throw new Error('The server did not return session tokens.');
    await tokenVault.save(response.data.tokens);
    set({ status: 'authenticated', user: response.data.user, onboarding: response.data.onboarding });
    return response.data;
  },

  register: async (input) => {
    const response = await authApi.register(input);
    if (!response.data.tokens) throw new Error('The server did not return session tokens.');
    await tokenVault.save(response.data.tokens);
    set({ status: 'authenticated', user: response.data.user, onboarding: response.data.onboarding });
    return response.data;
  },

  refreshSession: async () => {
    const response = await authApi.session();
    set({ status: 'authenticated', user: response.data.user, onboarding: response.data.onboarding });
    return response.data;
  },

  signOut: async () => {
    try {
      await authApi.logout();
    } finally {
      await tokenVault.clear();
      set({ status: 'guest', user: null, onboarding: null });
    }
  },
  declineRestore: () => set({ status: 'guest', user: null, onboarding: null }),
}));
