import { apiRequest } from '../api/client';

export const deviceApi = {
  register(input: { device_uuid: string; expo_push_token: string; platform: 'ios' | 'android'; app_version: string | null; device_name: string | null }) {
    return apiRequest<{ device: { id: string; enabled: boolean } }>('/devices', { authenticated: true, method: 'POST', body: JSON.stringify(input) });
  },
  preferences() {
    return apiRequest<{ transactional_enabled: boolean; promotional_enabled: boolean }>('/notification-preferences', { authenticated: true });
  },
  updatePreferences(input: { transactional_enabled: boolean; promotional_enabled: boolean }) {
    return apiRequest<{ transactional_enabled: boolean; promotional_enabled: boolean }>('/notification-preferences', { authenticated: true, method: 'PUT', body: JSON.stringify(input) });
  },
};
