import { appEnvironment } from '../config/environment';
import { tokenVault } from '../auth/tokenVault';
import type { ApiEnvelope, AuthSession } from './types';

export class ApiError extends Error {
  constructor(
    message: string,
    readonly status: number,
    readonly errors: Record<string, string[]> | null = null,
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

let refreshPromise: Promise<string | null> | null = null;

async function parseResponse<T>(response: Response): Promise<ApiEnvelope<T>> {
  const payload = (await response.json().catch(() => null)) as ApiEnvelope<T> | null;

  if (!response.ok || !payload?.success) {
    throw new ApiError(payload?.message ?? 'Unable to complete the request.', response.status, payload?.errors ?? null);
  }

  return payload;
}

async function refreshAccessToken(): Promise<string | null> {
  const refreshToken = await tokenVault.readRefreshToken();

  if (!refreshToken) return null;

  const response = await fetch(`${appEnvironment.apiUrl}/auth/refresh`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: JSON.stringify({ refresh_token: refreshToken }),
  });

  try {
    const payload = await parseResponse<AuthSession>(response);
    if (!payload.data.tokens) return null;
    await tokenVault.save(payload.data.tokens);
    return payload.data.tokens.access_token;
  } catch (error) {
    await tokenVault.clear();
    throw error;
  }
}

async function getRefreshedAccessToken() {
  refreshPromise ??= refreshAccessToken().finally(() => {
    refreshPromise = null;
  });
  return refreshPromise;
}

type RequestOptions = RequestInit & { authenticated?: boolean; retryAfterRefresh?: boolean };

export async function apiRequest<T>(path: string, options: RequestOptions = {}): Promise<ApiEnvelope<T>> {
  const { authenticated = false, retryAfterRefresh = true, headers, ...requestOptions } = options;
  const accessToken = authenticated ? await tokenVault.readAccessToken() : null;

  const response = await fetch(`${appEnvironment.apiUrl}${path}`, {
    ...requestOptions,
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      ...headers,
      ...(accessToken ? { Authorization: `Bearer ${accessToken}` } : {}),
    },
  });

  if (authenticated && response.status === 401 && retryAfterRefresh) {
    const refreshedToken = await getRefreshedAccessToken();
    if (!refreshedToken) throw new ApiError('Your session has expired. Please sign in again.', 401);

    return apiRequest<T>(path, { ...options, retryAfterRefresh: false });
  }

  return parseResponse<T>(response);
}
