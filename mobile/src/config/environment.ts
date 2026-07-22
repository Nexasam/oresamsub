import Constants from 'expo-constants';

type EnvironmentName = 'development' | 'staging' | 'production';

const environment = (process.env.EXPO_PUBLIC_APP_ENV ?? 'development') as EnvironmentName;
const configuredApiUrl = process.env.EXPO_PUBLIC_API_URL;

if (!configuredApiUrl && environment !== 'development') {
  throw new Error('EXPO_PUBLIC_API_URL is required outside development.');
}

if (environment === 'production' && !configuredApiUrl?.startsWith('https://')) {
  throw new Error('Production mobile API traffic must use HTTPS.');
}

export const appEnvironment = {
  environment,
  apiUrl: configuredApiUrl ?? 'http://127.0.0.1:8000/api/mobile/v1',
  appVersion: Constants.expoConfig?.version ?? '1.0.0',
} as const;
