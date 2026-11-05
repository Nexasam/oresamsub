import { requireNativeModule } from 'expo-modules-core';

export type GatewayStatus = {
  state: string;
  step: number;
  message: string;
  updatedAt: number;
  accessibilityEnabled: boolean;
  hasPendingJob: boolean;
  hasPendingResult: boolean;
};

export type GatewayConfiguration = {
  baseUrl: string;
  token: string;
  installationId: string;
};

export type GatewaySubscription = {
  simSlot: number;
  subscriptionId: number;
  carrierName?: string | null;
};

export type GatewayIdentity = {
  installationId: string;
  deviceModel: string;
  subscriptions: GatewaySubscription[];
};

export type PendingJob = {
  attemptId: string;
  recipientNumber: string;
  dispatchToken: string;
  clientResultId: string;
  simSlot: number;
  subscriptionId: number;
  resultReady: boolean;
  outcome?: string | null;
  responseText?: string | null;
};

type UssdGatewayNativeModule = {
  getStatus(): GatewayStatus;
  getConfiguration(): GatewayConfiguration;
  saveConfiguration(baseUrl: string, token: string): void;
  getDeviceIdentity(): GatewayIdentity;
  getPendingJob(): PendingJob | null;
  createId(): string;
  prepareJob(attemptId: string, recipient: string, dispatchToken: string, clientResultId: string, simSlot: number, subscriptionId: number): void;
  startPreparedJob(): void;
  storeLocalResult(outcome: string, responseText: string): void;
  clearPendingJob(): void;
  openAccessibilitySettings(): void;
  startCompatibilityTest(simSlot: number): void;
  cancelTest(): void;
};

export default requireNativeModule<UssdGatewayNativeModule>('UssdGateway');
