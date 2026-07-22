import { apiRequest } from './client';
import type { CatalogueCategory, CataloguePlan, CatalogueProduct, Dashboard, FundingHistoryItem, FundingOption, MobileTransaction, MobileUser, PaginationMeta, WalletAccount } from './types';

export const mobileApi = {
  async dashboard() {
    return (await apiRequest<Dashboard>('/dashboard', { authenticated: true })).data;
  },
  async profile() {
    return (await apiRequest<{ user: MobileUser }>('/profile', { authenticated: true })).data.user;
  },
  async updateProfile(input: { first_name: string; last_name: string; other_names?: string; username: string; customer_landmark?: string }) {
    return (await apiRequest<{ user: MobileUser }>('/profile', { authenticated: true, method: 'PUT', body: JSON.stringify(input) })).data.user;
  },
  async products() {
    return (await apiRequest<CatalogueProduct[]>('/catalogue/products', { authenticated: true })).data;
  },
  async categories(product?: string) {
    const query = product ? `?product=${encodeURIComponent(product)}` : '';
    return (await apiRequest<CatalogueCategory[]>(`/catalogue/categories${query}`, { authenticated: true })).data;
  },
  async plans(categoryId: string) {
    return (await apiRequest<CataloguePlan[]>(`/catalogue/plans?category_id=${encodeURIComponent(categoryId)}`, { authenticated: true })).data;
  },
  async purchaseData(input: { product_plan_id: string; phone_number: string; pin: string; reference: string }) {
    return (await apiRequest<{ status: string; message: string | null }>('/purchases/data', { authenticated: true, method: 'POST', body: JSON.stringify(input) })).data;
  },
  async purchaseAirtime(input: { product_plan_id: string; phone_number: string; amount: number; pin: string; reference: string }) {
    return (await apiRequest<{ status: string; message: string | null }>('/purchases/airtime', { authenticated: true, method: 'POST', body: JSON.stringify(input) })).data;
  },
  async validateBiller(kind: 'cable' | 'electricity', input: { product_plan_id: string; customer_number: string }) {
    return (await apiRequest<{ name: string | null; address: string | null; extra_info: string }>(`/${kind}/validate`, { authenticated: true, method: 'POST', body: JSON.stringify(input) })).data;
  },
  async purchaseCable(input: { product_plan_id: string; smart_card_number: string; customer_name: string; pin: string; reference: string }) {
    return (await apiRequest<{ status: string; message: string | null }>('/purchases/cable', { authenticated: true, method: 'POST', body: JSON.stringify(input) })).data;
  },
  async purchaseElectricity(input: { product_plan_id: string; metre_number: string; amount: number; validation_extra_info: string; validated_address?: string; pin: string; reference: string }) {
    return (await apiRequest<{ status: string; message: string | null }>('/purchases/electricity', { authenticated: true, method: 'POST', body: JSON.stringify(input) })).data;
  },
  async transactions(page = 1, status?: string) {
    const query = new URLSearchParams({ page: String(page), per_page: '20' });
    if (status) query.set('status', status);
    const response = await apiRequest<MobileTransaction[]>(`/transactions?${query}`, { authenticated: true });
    return { items: response.data, meta: response.meta as PaginationMeta };
  },
  async transaction(id: string) {
    return (await apiRequest<{ transaction: MobileTransaction }>(`/transactions/${id}`, { authenticated: true })).data.transaction;
  },
  async wallet() {
    return (await apiRequest<{ currency: string; balance: number; accounts_count: number }>('/wallet', { authenticated: true })).data;
  },
  async walletAccounts() {
    return (await apiRequest<WalletAccount[]>('/wallet/accounts', { authenticated: true })).data;
  },
  async fundingHistory() {
    return (await apiRequest<FundingHistoryItem[]>('/wallet/funding-history', { authenticated: true })).data;
  },
  async fundingOptions() {
    return (await apiRequest<FundingOption[]>('/wallet/funding-options', { authenticated: true })).data;
  },
  async createWalletAccount(input: { funding_option_id: string; bank_code: string; pin: string }) {
    return (await apiRequest<{ account: WalletAccount }>('/wallet/accounts', { authenticated: true, method: 'POST', body: JSON.stringify(input) })).data.account;
  },
  async purchaseStatus(reference: string) {
    return (await apiRequest<{ transaction: MobileTransaction }>(`/purchases/status/${encodeURIComponent(reference)}`, { authenticated: true })).data.transaction;
  },
  async changePassword(input: { current_password: string; password: string; password_confirmation: string }) {
    return apiRequest<null>('/security/password', { authenticated: true, method: 'PUT', body: JSON.stringify(input) });
  },
  async changePin(input: { current_pin: string; pin: string; pin_confirmation: string }) {
    return apiRequest<null>('/security/pin', { authenticated: true, method: 'PUT', body: JSON.stringify(input) });
  },
  async deactivateAccount(input: { password: string; confirmation: 'DELETE' }) {
    return apiRequest<null>('/account', { authenticated: true, method: 'DELETE', body: JSON.stringify(input) });
  },
};
