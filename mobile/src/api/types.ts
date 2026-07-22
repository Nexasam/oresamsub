export type ApiEnvelope<T> = {
  success: boolean;
  message: string;
  data: T;
  meta: unknown;
  errors: Record<string, string[]> | null;
};

export type MobileUser = {
  id: string;
  first_name: string;
  last_name: string;
  other_names: string | null;
  username: string;
  email: string;
  phone_number: string | null;
  phone_verified: boolean;
  transaction_pin_set: boolean;
  is_deactivated: boolean;
  customer_landmark: string | null;
};

export type MobileTransaction = {
  id: string;
  category: string | null;
  status: 'successful' | 'failed' | 'refunded' | 'processing' | 'pending';
  amount: number;
  description: string;
  beneficiary: string | null;
  message: string | null;
  created_at: string;
};

export type Dashboard = {
  wallet: { currency: 'NGN'; balance: number };
  summary: {
    total_transactions: number;
    successful_transactions: number;
    pending_transactions: number;
  };
  recent_transactions: MobileTransaction[];
};

export type CatalogueProduct = { id: string; slug: string; name: string };
export type CatalogueCategory = {
  id: string;
  name: string;
  is_hot_sale: boolean;
  product: CatalogueProduct;
  network: { id: string; name: string } | null;
};
export type CataloguePlan = {
  id: string;
  name: string;
  price: number;
  data_size_mb: number | null;
  validity_days: number | null;
};

export type PaginationMeta = { current_page: number; last_page: number; per_page?: number; total: number };
export type WalletAccount = { id: string; provider: string | null; bank_name: string | null; account_name: string | null; account_number: string | null };
export type FundingHistoryItem = { id: string; status: string; amount: number; amount_settled: number; currency: string; bank_name: string; reference: string; created_at: string };
export type FundingOption = { id: string; name: string; slug: string; banks: { code: string; description: string | null }[] };

export type AuthTokens = {
  access_token: string;
  access_token_expires_at: string;
  refresh_token: string;
  refresh_token_expires_at: string;
  token_type: 'Bearer';
};

export type OnboardingState = {
  phone_verified: boolean;
  transaction_pin_set: boolean;
  profile_complete: boolean;
};

export type AuthSession = {
  user: MobileUser;
  tokens?: AuthTokens;
  onboarding: OnboardingState;
};
