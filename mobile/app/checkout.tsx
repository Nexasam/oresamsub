import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Contact } from 'expo-contacts';
import { router, Stack, useLocalSearchParams } from 'expo-router';
import { useRef, useState } from 'react';
import { Alert, Keyboard, KeyboardAvoidingView, Modal, Platform, Pressable, ScrollView, StyleSheet, Text, TextInput, View } from 'react-native';
import { ApiError } from '../src/api/client';
import { mobileApi } from '../src/api/mobileApi';
import { MaterialIcon } from '../src/components/MaterialIcon';
import { PinInput } from '../src/components/PinInput';
import { Screen } from '../src/components/Screen';
import { colors, fonts } from '../src/theme/colors';

const money = (amount: number) => new Intl.NumberFormat('en-NG', { style: 'currency', currency: 'NGN' }).format(amount);
const reference = () => `MOB-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;
const airtimeAmounts = [50, 100, 200, 500];

type Validation = { name: string | null; address: string | null; extra_info: string };

export default function CheckoutScreen() {
  const params = useLocalSearchParams<{
    product: string;
    planId: string;
    planName: string;
    price: string;
    provider: string;
    amount?: string;
  }>();
  const isAirtime = params.product === 'airtime';
  const isCable = params.product === 'cable_subscription';
  const isElectricity = params.product === 'utility_bills';
  const isBiller = isCable || isElectricity;
  const [customerNumber, setCustomerNumber] = useState('');
  const [contactName, setContactName] = useState<string | null>(null);
  const [pickingContact, setPickingContact] = useState(false);
  const [pin, setPin] = useState('');
  const [amount, setAmount] = useState(isAirtime || isElectricity ? (params.amount ?? '') : params.price);
  const [validation, setValidation] = useState<Validation | null>(null);
  const [confirmationOpen, setConfirmationOpen] = useState(false);
  const [purchaseReference] = useState(reference);
  const submissionLocked = useRef(false);
  const scrollRef = useRef<ScrollView>(null);
  const queryClient = useQueryClient();

  const mutation = useMutation({
    mutationFn: async () => {
      if (isBiller && !validation) {
        const result = await mobileApi.validateBiller(isCable ? 'cable' : 'electricity', {
          customer_number: customerNumber,
          product_plan_id: params.planId,
        });
        setValidation(result);
        return { validationOnly: true };
      }
      if (isAirtime) {
        await mobileApi.purchaseAirtime({
          amount: Number(amount),
          phone_number: customerNumber,
          pin,
          product_plan_id: params.planId,
          reference: purchaseReference,
        });
      } else if (isCable) {
        await mobileApi.purchaseCable({
          customer_name: validation?.name ?? 'Validated customer',
          pin,
          product_plan_id: params.planId,
          reference: purchaseReference,
          smart_card_number: customerNumber,
        });
      } else if (isElectricity) {
        await mobileApi.purchaseElectricity({
          amount: Number(amount),
          metre_number: customerNumber,
          pin,
          product_plan_id: params.planId,
          reference: purchaseReference,
          validated_address: validation?.address ?? undefined,
          validation_extra_info: validation?.extra_info ?? '',
        });
      } else {
        await mobileApi.purchaseData({
          phone_number: customerNumber,
          pin,
          product_plan_id: params.planId,
          reference: purchaseReference,
        });
      }
      return { validationOnly: false };
    },
    onSuccess: async (result) => {
      if (result.validationOnly) return;
      await queryClient.invalidateQueries({ queryKey: ['dashboard'] });
      await queryClient.invalidateQueries({ queryKey: ['transactions'] });
      Alert.alert('Purchase submitted', 'Your transaction has been processed.', [
        { text: 'View history', onPress: () => router.replace('/transactions') },
      ]);
    },
  });
  const reconcile = useMutation({
    mutationFn: () => mobileApi.purchaseStatus(purchaseReference),
    onSuccess: (transaction) => router.replace({
      pathname: '/transaction/[id]',
      params: { id: transaction.id },
    }),
  });
  const error = mutation.error instanceof ApiError
    ? mutation.error.message
    : mutation.error
      ? 'Unable to complete this request.'
      : null;
  const needsVariableAmount = isAirtime || isElectricity;
  const validAmount = !needsVariableAmount || Number(amount) >= (isElectricity ? 500 : 50);
  const canSubmit = customerNumber.length >= (isBiller ? 5 : 11)
    && (isBiller && !validation ? true : pin.length >= 4 && validAmount);
  const label = isBiller && !validation
    ? 'Validate customer'
    : mutation.isPending
      ? 'Processing…'
      : 'Pay securely';

  const revealPin = () => {
    setTimeout(() => scrollRef.current?.scrollToEnd({ animated: true }), 250);
  };

  const submit = () => {
    if (submissionLocked.current) return;
    submissionLocked.current = true;
    mutation.mutate(undefined, {
      onSettled: () => {
        submissionLocked.current = false;
      },
    });
  };

  const requestSubmit = () => {
    if (isBiller && !validation) {
      submit();
      return;
    }
    Keyboard.dismiss();
    setConfirmationOpen(true);
  };

  const confirmSubmit = () => {
    setConfirmationOpen(false);
    submit();
  };

  const chooseContact = async () => {
    setPickingContact(true);
    try {
      const contact = await Contact.presentPicker();
      if (!contact) return;
      const phones = await contact.getPhones();
      const normalized = phones
        .map((phone) => normalizeNigerianPhone(phone.number ?? ''))
        .find(Boolean);
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

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: 'Confirm purchase' }} />
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.flex}>
        <Screen safeTop={false} scrollRef={scrollRef}>
          <Text style={styles.heading}>Confirm purchase</Text>
          <View style={styles.summary}>
            <Row label="Service" value={serviceName(params.product)} />
            <Row label="Provider" value={params.provider} />
            <Row label="Plan" value={params.planName} />
            <Row label="Price" value={needsVariableAmount ? 'Enter amount below' : money(Number(params.price))} />
          </View>

          <View style={styles.labelRow}>
            <Text style={styles.label}>{isCable ? 'Smart card number' : isElectricity ? 'Meter number' : 'Beneficiary phone'}</Text>
            {!isBiller ? (
              <Pressable disabled={pickingContact} onPress={() => void chooseContact()} style={styles.contactButton}>
                <Text style={styles.contactButtonText}>{pickingContact ? 'Opening…' : '♙  Contacts'}</Text>
              </Pressable>
            ) : null}
          </View>
          <View style={styles.inputShell}>
            <TextInput
              keyboardType="number-pad"
              maxLength={isBiller ? 30 : 11}
              onChangeText={(value) => {
                setCustomerNumber(value);
                setContactName(null);
                setValidation(null);
              }}
              placeholder={isCable ? 'Enter smart card number' : isElectricity ? 'Enter meter number' : '08030000000'}
              placeholderTextColor={colors.muted}
              selectionColor={colors.primary}
              style={styles.phoneInput}
              value={customerNumber}
            />
            {contactName ? (
              <View style={styles.contactTick}><Text style={styles.contactTickText}>✓</Text></View>
            ) : null}
          </View>
          {contactName ? <Text style={styles.selectedContact}>Sending to {contactName}</Text> : null}
          {validation ? (
            <View style={styles.validated}>
              <Text style={styles.validatedTitle}>✓ Customer verified</Text>
              <Text style={styles.validatedText}>{validation.name ?? 'Verified customer'}</Text>
              {validation.address ? <Text style={styles.validatedText}>{validation.address}</Text> : null}
            </View>
          ) : null}

          {needsVariableAmount ? (
            <>
              <Text style={styles.label}>Amount</Text>
              {isAirtime ? (
                <View style={styles.quickAmounts}>
                  {airtimeAmounts.map((value) => {
                    const active = Number(amount) === value;
                    return (
                      <Pressable
                        accessibilityLabel={`Select ${money(value)} airtime`}
                        accessibilityRole="button"
                        key={value}
                        onPress={() => setAmount(String(value))}
                        style={({ pressed }) => [
                          styles.quickAmount,
                          active && styles.quickAmountActive,
                          pressed && styles.dim,
                        ]}
                      >
                        <Text style={[styles.quickAmountText, active && styles.quickAmountTextActive]}>
                          ₦{value}
                        </Text>
                      </Pressable>
                    );
                  })}
                </View>
              ) : null}
              <TextInput
                keyboardType="decimal-pad"
                onChangeText={setAmount}
                placeholder={isElectricity ? 'Minimum ₦500' : 'Minimum ₦50'}
                placeholderTextColor={colors.muted}
                selectionColor={colors.primary}
                style={styles.input}
                value={amount}
              />
            </>
          ) : null}

          {(!isBiller || validation) ? (
            <PinInput
              label="Transaction PIN"
              onChangeText={setPin}
              onFocus={revealPin}
              style={styles.pinInput}
              value={pin}
            />
          ) : null}
          {error ? <Text style={styles.error}>{error}</Text> : null}
          {mutation.error && !(mutation.error instanceof ApiError) ? (
            <Pressable onPress={() => reconcile.mutate()}>
              <Text style={styles.checkStatus}>{reconcile.isPending ? 'Checking…' : 'Check whether payment was received'}</Text>
            </Pressable>
          ) : null}
          <Pressable
            disabled={mutation.isPending || !canSubmit}
            onPress={requestSubmit}
            style={({ pressed }) => [styles.button, (pressed || mutation.isPending || !canSubmit) && styles.dim]}
          >
            <Text style={styles.buttonText}>{label}</Text>
          </Pressable>
          <Text style={styles.note}>Financial requests are never automatically retried. Each payment uses a unique reference.</Text>
        </Screen>
      </KeyboardAvoidingView>
      <Modal
        animationType="slide"
        onRequestClose={() => setConfirmationOpen(false)}
        transparent
        visible={confirmationOpen}
      >
        <View style={styles.modalRoot}>
          <Pressable accessibilityLabel="Cancel payment confirmation" onPress={() => setConfirmationOpen(false)} style={styles.backdrop} />
          <View style={styles.confirmationSheet}>
            <View style={styles.sheetHandle} />
            <View style={styles.confirmationIcon}>
              <MaterialIcon color={colors.primary} name="verified_user" size={27} />
            </View>
            <Text style={styles.confirmationTitle}>Confirm your payment</Text>
            <Text style={styles.confirmationText}>Check the details carefully. Payments may be delivered immediately.</Text>
            <View style={styles.confirmationDetails}>
              <ConfirmationRow label="Service" value={serviceName(params.product)} />
              <ConfirmationRow label="Provider" value={params.provider} />
              <ConfirmationRow label="Plan" value={params.planName} />
              <ConfirmationRow label={isBiller ? 'Customer number' : 'Beneficiary'} value={customerNumber} />
              <ConfirmationRow
                label="Amount"
                value={money(needsVariableAmount ? Number(amount) : Number(params.price))}
                last
              />
            </View>
            <View style={styles.confirmationWarning}>
              <MaterialIcon color={colors.warning} name="info" size={17} />
              <Text style={styles.confirmationWarningText}>Please verify the beneficiary before continuing.</Text>
            </View>
            <Pressable
              accessibilityRole="button"
              disabled={mutation.isPending}
              onPress={confirmSubmit}
              style={({ pressed }) => [styles.confirmButton, (pressed || mutation.isPending) && styles.dim]}
            >
              <Text style={styles.confirmButtonText}>{mutation.isPending ? 'Processing…' : 'Confirm payment'}</Text>
              <MaterialIcon color={colors.white} name="arrow_forward" size={19} />
            </Pressable>
            <Pressable
              accessibilityRole="button"
              disabled={mutation.isPending}
              onPress={() => setConfirmationOpen(false)}
              style={styles.cancelButton}
            >
              <Text style={styles.cancelButtonText}>Go back and edit</Text>
            </Pressable>
          </View>
        </View>
      </Modal>
    </>
  );
}

function serviceName(slug: string) {
  return slug === 'airtime'
    ? 'Airtime'
    : slug === 'cable_subscription'
      ? 'Cable TV'
      : slug === 'utility_bills'
        ? 'Electricity'
        : 'Mobile Data';
}

function normalizeNigerianPhone(value: string) {
  const digits = value.replace(/\D/g, '');
  if (/^234[789][01]\d{8}$/.test(digits)) return `0${digits.slice(3)}`;
  if (/^[789][01]\d{8}$/.test(digits)) return `0${digits}`;
  if (/^0[789][01]\d{8}$/.test(digits)) return digits;
  return null;
}

function Row({ label, value }: { label: string; value: string }) {
  return (
    <View style={styles.row}>
      <Text style={styles.rowLabel}>{label}</Text>
      <Text numberOfLines={2} style={styles.rowValue}>{value}</Text>
    </View>
  );
}

function ConfirmationRow({ label, last = false, value }: { label: string; last?: boolean; value: string }) {
  return (
    <View style={[styles.confirmationRow, last && styles.confirmationLastRow]}>
      <Text style={styles.confirmationLabel}>{label}</Text>
      <Text numberOfLines={2} style={styles.confirmationValue}>{value}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  flex: { flex: 1 },
  heading: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 23 },
  summary: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 16, borderWidth: 1, marginBottom: 16, marginTop: 12, paddingHorizontal: 15, paddingVertical: 10 },
  row: { flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 6 },
  rowLabel: { color: colors.muted, fontFamily: fonts.regular },
  rowValue: { color: colors.text, flex: 1, fontFamily: fonts.bold, marginLeft: 20, textAlign: 'right' },
  labelRow: { alignItems: 'flex-end', flexDirection: 'row', justifyContent: 'space-between', marginTop: 7 },
  label: { color: colors.text, fontFamily: fonts.bold, fontSize: 13, marginBottom: 7, marginTop: 9 },
  contactButton: { backgroundColor: colors.primarySoft, borderRadius: 10, marginBottom: 7, paddingHorizontal: 10, paddingVertical: 7 },
  contactButtonText: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 10 },
  inputShell: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, flexDirection: 'row' },
  phoneInput: { color: colors.text, flex: 1, fontFamily: fonts.medium, fontSize: 16, padding: 14 },
  contactTick: { alignItems: 'center', backgroundColor: colors.primarySoft, borderRadius: 12, height: 28, justifyContent: 'center', marginRight: 10, width: 28 },
  contactTickText: { color: colors.primary, fontFamily: fonts.extraBold },
  selectedContact: { color: colors.primary, fontFamily: fonts.bold, fontSize: 10, marginTop: 6 },
  quickAmounts: { flexDirection: 'row', gap: 8, marginBottom: 10 },
  quickAmount: { alignItems: 'center', backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, flex: 1, paddingHorizontal: 8, paddingVertical: 11 },
  quickAmountActive: { backgroundColor: colors.primaryDark, borderColor: colors.primaryDark },
  quickAmountText: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 12 },
  quickAmountTextActive: { color: colors.white },
  input: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 12, borderWidth: 1, color: colors.text, fontFamily: fonts.medium, fontSize: 16, padding: 14 },
  pinInput: { marginTop: 16 },
  validated: { backgroundColor: '#ecfdf5', borderRadius: 12, marginTop: 12, padding: 13 },
  validatedTitle: { color: colors.success, fontFamily: fonts.extraBold },
  validatedText: { color: colors.text, fontFamily: fonts.regular, fontSize: 12, marginTop: 4 },
  error: { color: colors.danger, fontFamily: fonts.medium, marginTop: 14 },
  checkStatus: { color: colors.primary, fontFamily: fonts.extraBold, marginTop: 12, textAlign: 'center' },
  button: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 13, marginTop: 24, padding: 16 },
  dim: { opacity: 0.55 },
  buttonText: { color: colors.white, fontFamily: fonts.extraBold },
  note: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, marginTop: 12, textAlign: 'center' },
  modalRoot: { flex: 1, justifyContent: 'flex-end' },
  backdrop: { backgroundColor: 'rgba(5, 20, 15, 0.56)', bottom: 0, left: 0, position: 'absolute', right: 0, top: 0 },
  confirmationSheet: { backgroundColor: colors.background, borderTopLeftRadius: 28, borderTopRightRadius: 28, paddingBottom: Platform.OS === 'ios' ? 32 : 24, paddingHorizontal: 20, paddingTop: 10 },
  sheetHandle: { alignSelf: 'center', backgroundColor: colors.border, borderRadius: 3, height: 5, marginBottom: 15, width: 44 },
  confirmationIcon: { alignItems: 'center', alignSelf: 'center', backgroundColor: colors.primarySoft, borderRadius: 23, height: 50, justifyContent: 'center', width: 50 },
  confirmationTitle: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 20, marginTop: 11, textAlign: 'center' },
  confirmationText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, lineHeight: 17, marginTop: 5, paddingHorizontal: 20, textAlign: 'center' },
  confirmationDetails: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 17, borderWidth: 1, marginTop: 16, paddingHorizontal: 14 },
  confirmationRow: { alignItems: 'center', borderBottomColor: colors.border, borderBottomWidth: 1, flexDirection: 'row', justifyContent: 'space-between', paddingVertical: 10 },
  confirmationLastRow: { borderBottomWidth: 0 },
  confirmationLabel: { color: colors.muted, fontFamily: fonts.medium, fontSize: 10 },
  confirmationValue: { color: colors.text, flex: 1, fontFamily: fonts.extraBold, fontSize: 11, marginLeft: 20, textAlign: 'right' },
  confirmationWarning: { alignItems: 'center', backgroundColor: '#FFF7E5', borderRadius: 12, flexDirection: 'row', gap: 8, marginTop: 12, padding: 11 },
  confirmationWarningText: { color: colors.text, flex: 1, fontFamily: fonts.medium, fontSize: 10, lineHeight: 15 },
  confirmButton: { alignItems: 'center', backgroundColor: colors.primary, borderRadius: 14, flexDirection: 'row', gap: 8, justifyContent: 'center', marginTop: 14, minHeight: 52 },
  confirmButtonText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 13 },
  cancelButton: { alignItems: 'center', minHeight: 42, paddingTop: 13 },
  cancelButtonText: { color: colors.muted, fontFamily: fonts.bold, fontSize: 12 },
});
