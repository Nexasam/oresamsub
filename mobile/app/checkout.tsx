import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Contact } from 'expo-contacts';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { useRef, useState } from 'react';
import { Alert, KeyboardAvoidingView, Platform, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../src/api/client';
import { mobileApi } from '../src/api/mobileApi';
import { Screen } from '../src/components/Screen';
import { colors } from '../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);
const reference = () => `MOB-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;

type Validation = { name: string | null; address: string | null; extra_info: string };

export default function CheckoutScreen() {
  const params = useLocalSearchParams<{ product: string; planId: string; planName: string; price: string; provider: string }>();
  const isAirtime = params.product === 'airtime';
  const isCable = params.product === 'cable_subscription';
  const isElectricity = params.product === 'utility_bills';
  const isBiller = isCable || isElectricity;
  const [customerNumber, setCustomerNumber] = useState('');
  const [contactName, setContactName] = useState<string | null>(null);
  const [pickingContact, setPickingContact] = useState(false);
  const [pin, setPin] = useState('');
  const [amount, setAmount] = useState(isAirtime || isElectricity ? '' : params.price);
  const [validation, setValidation] = useState<Validation | null>(null);
  const [purchaseReference] = useState(reference);
  const submissionLocked = useRef(false);
  const queryClient = useQueryClient();
  const mutation = useMutation({
    mutationFn: async () => {
      if (isBiller && !validation) {
        const result = await mobileApi.validateBiller(isCable ? 'cable' : 'electricity', { product_plan_id: params.planId, customer_number: customerNumber });
        setValidation(result);
        return { validationOnly: true };
      }
      if (isAirtime) await mobileApi.purchaseAirtime({ product_plan_id: params.planId, phone_number: customerNumber, amount: Number(amount), pin, reference: purchaseReference });
      else if (isCable) await mobileApi.purchaseCable({ product_plan_id: params.planId, smart_card_number: customerNumber, customer_name: validation?.name ?? 'Validated customer', pin, reference: purchaseReference });
      else if (isElectricity) await mobileApi.purchaseElectricity({ product_plan_id: params.planId, metre_number: customerNumber, amount: Number(amount), validation_extra_info: validation?.extra_info ?? '', validated_address: validation?.address ?? undefined, pin, reference: purchaseReference });
      else await mobileApi.purchaseData({ product_plan_id: params.planId, phone_number: customerNumber, pin, reference: purchaseReference });
      return { validationOnly: false };
    },
    onSuccess: async (result) => {
      if (result.validationOnly) return;
      await queryClient.invalidateQueries({ queryKey: ['dashboard'] });
      await queryClient.invalidateQueries({ queryKey: ['transactions'] });
      Alert.alert('Purchase submitted', 'Your transaction has been processed.', [{ text: 'View history', onPress: () => router.replace('/transactions') }]);
    },
  });
  const reconcile = useMutation({ mutationFn: () => mobileApi.purchaseStatus(purchaseReference), onSuccess: (transaction) => router.replace({ pathname: '/transaction/[id]', params: { id: transaction.id } }) });
  const error = mutation.error instanceof ApiError ? mutation.error.message : mutation.error ? 'Unable to complete this request.' : null;
  const needsVariableAmount = isAirtime || isElectricity;
  const validAmount = !needsVariableAmount || Number(amount) >= (isElectricity ? 500 : 50);
  const canSubmit = customerNumber.length >= (isBiller ? 5 : 11) && (isBiller && !validation ? true : pin.length >= 4 && validAmount);
  const label = isBiller && !validation ? 'Validate customer' : mutation.isPending ? 'Processing…' : 'Pay securely';
  const submit = () => {
    if (submissionLocked.current) return;
    submissionLocked.current = true;
    mutation.mutate(undefined, { onSettled: () => { submissionLocked.current = false; } });
  };
  const chooseContact = async () => {
    setPickingContact(true);
    try {
      const contact = await Contact.presentPicker();
      if (!contact) return;
      const phones = await contact.getPhones();
      const normalized = phones.map((phone) => normalizeNigerianPhone(phone.number ?? '')).find(Boolean);
      if (!normalized) {
        Alert.alert('No supported number', 'This contact does not have a valid Nigerian mobile number.');
        return;
      }
      setCustomerNumber(normalized);
      setContactName((await contact.getFullName()) || 'Selected contact');
      setValidation(null);
    } catch {
      Alert.alert('Contacts unavailable', 'Unable to open your contacts. Check the app permission in your phone settings.');
    } finally {
      setPickingContact(false);
    }
  };

  return <><Stack.Screen options={{ headerShown: true, title: 'Confirm purchase' }} /><KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.flex}><Screen><Text style={styles.heading}>Confirm purchase</Text><View style={styles.summary}><Row label="Service" value={serviceName(params.product)} /><Row label="Provider" value={params.provider} /><Row label="Plan" value={params.planName} /><Row label="Price" value={needsVariableAmount ? 'Enter amount below' : money(Number(params.price))} /></View><View style={styles.labelRow}><Text style={styles.label}>{isCable ? 'Smart card number' : isElectricity ? 'Meter number' : 'Beneficiary phone'}</Text>{!isBiller && <Pressable disabled={pickingContact} onPress={() => void chooseContact()} style={styles.contactButton}><Text style={styles.contactButtonText}>{pickingContact ? 'Opening…' : '♙  Contacts'}</Text></Pressable>}</View><View style={styles.inputShell}><TextInput keyboardType="number-pad" maxLength={isBiller ? 30 : 11} onChangeText={(value) => { setCustomerNumber(value); setContactName(null); setValidation(null); }} placeholder={isCable ? 'Enter smart card number' : isElectricity ? 'Enter meter number' : '08030000000'} style={styles.phoneInput} value={customerNumber} />{contactName && <View style={styles.contactTick}><Text style={styles.contactTickText}>✓</Text></View>}</View>{contactName && <Text style={styles.selectedContact}>Sending to {contactName}</Text>}{validation && <View style={styles.validated}><Text style={styles.validatedTitle}>✓ Customer verified</Text><Text style={styles.validatedText}>{validation.name ?? 'Verified customer'}</Text>{validation.address && <Text style={styles.validatedText}>{validation.address}</Text>}</View>}{needsVariableAmount && <><Text style={styles.label}>Amount</Text><TextInput keyboardType="decimal-pad" onChangeText={setAmount} placeholder={isElectricity ? 'Minimum ₦500' : 'Minimum ₦50'} style={styles.input} value={amount} /></>}{(!isBiller || validation) && <><Text style={styles.label}>Transaction PIN</Text><TextInput keyboardType="number-pad" maxLength={5} onChangeText={setPin} placeholder="••••" secureTextEntry style={styles.input} value={pin} /></>}{error && <Text style={styles.error}>{error}</Text>}{mutation.error && !(mutation.error instanceof ApiError) && <Pressable onPress={() => reconcile.mutate()}><Text style={styles.checkStatus}>{reconcile.isPending ? 'Checking…' : 'Check whether payment was received'}</Text></Pressable>}<Pressable disabled={mutation.isPending || !canSubmit} onPress={submit} style={({ pressed }) => [styles.button, (pressed || mutation.isPending || !canSubmit) && styles.dim]}><Text style={styles.buttonText}>{label}</Text></Pressable><Text style={styles.note}>Financial requests are never automatically retried. Each payment uses a unique reference.</Text></Screen></KeyboardAvoidingView></>;
}

function serviceName(slug: string) { return slug === 'airtime' ? 'Airtime' : slug === 'cable_subscription' ? 'Cable TV' : slug === 'utility_bills' ? 'Electricity' : 'Mobile Data'; }
function normalizeNigerianPhone(value: string) { const digits = value.replace(/\D/g, ''); if (/^234[789][01]\d{8}$/.test(digits)) return `0${digits.slice(3)}`; if (/^[789][01]\d{8}$/.test(digits)) return `0${digits}`; if (/^0[789][01]\d{8}$/.test(digits)) return digits; return null; }
function Row({ label, value }: { label: string; value: string }) { return <View style={styles.row}><Text style={styles.rowLabel}>{label}</Text><Text numberOfLines={2} style={styles.rowValue}>{value}</Text></View>; }
const styles = StyleSheet.create({ flex: { flex: 1 }, heading: { color: colors.text, fontSize: 25, fontWeight: '800' }, summary: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, marginBottom: 22, marginTop: 20, padding: 16 }, row: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 7 }, rowLabel: { color: colors.muted }, rowValue: { color: colors.text, flex: 1, fontWeight: '700', marginLeft: 20, textAlign: 'right' }, labelRow: { alignItems: 'flex-end', flexDirection: 'row', justifyContent: 'space-between', marginTop: 12 }, label: { color: colors.text, fontSize: 13, fontWeight: '700', marginBottom: 7, marginTop: 12 }, contactButton: { backgroundColor: colors.primarySoft, borderRadius: 10, marginBottom: 7, paddingHorizontal: 10, paddingVertical: 7 }, contactButtonText: { color: colors.primary, fontSize: 10, fontWeight: '800' }, inputShell: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, flexDirection: 'row' }, phoneInput: { color: colors.text, flex: 1, fontSize: 16, padding: 14 }, contactTick: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 12, height: 28, justifyContent: 'center', marginRight: 10, width: 28 }, contactTickText: { color: colors.primary, fontWeight: '800' }, selectedContact: { color: colors.primary, fontSize: 10, fontWeight: '700', marginTop: 6 }, input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, color: colors.text, fontSize: 16, padding: 14 }, validated: { backgroundColor: '#ecfdf5', borderRadius: 12, marginTop: 12, padding: 13 }, validatedTitle: { color: colors.success, fontWeight: '800' }, validatedText: { color: colors.text, fontSize: 12, marginTop: 4 }, error: { color: colors.danger, marginTop: 14 }, checkStatus: { color: colors.primary, fontWeight: '800', marginTop: 12, textAlign: 'center' }, button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 13, marginTop: 24, padding: 16 }, dim: { opacity: 0.55 }, buttonText: { color: colors.white, fontWeight: '800' }, note: { color: colors.muted, fontSize: 11, marginTop: 12, textAlign: 'center' } });
