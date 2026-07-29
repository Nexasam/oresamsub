import { router, Stack, useLocalSearchParams } from 'expo-router';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { BrandLogo } from '../../src/components/BrandLogo';
import { MaterialIcon } from '../../src/components/MaterialIcon';
import { Screen } from '../../src/components/Screen';
import { colors, fonts } from '../../src/theme/colors';

type LegalSection = {
  bullets?: string[];
  heading: string;
  paragraphs?: string[];
};

type LegalDocument = {
  date: string;
  icon: string;
  introduction: string;
  sections: LegalSection[];
  title: string;
};

const documents: Record<string, LegalDocument> = {
  privacy: {
    date: 'Effective June 29, 2026',
    icon: 'privacy_tip',
    introduction: 'OresamSub values your privacy. This policy explains how we collect, use, store and protect information when you use our digital services.',
    title: 'Privacy Policy',
    sections: [
      {
        heading: 'Information we collect',
        bullets: [
          'Full name, email address and phone number.',
          'Account credentials and profile details.',
          'Transaction and payment information.',
          'Device, browser, usage, IP address and security-related information.',
          'A privacy-protected device identifier and IP address may be used to prevent duplicate-account abuse, enforce promotional reward limits and investigate fraud.',
        ],
      },
      {
        heading: 'How we use your information',
        bullets: [
          'Provide airtime, data, cable TV and utility-payment services.',
          'Process and verify transactions.',
          'Manage and secure user accounts.',
          'Send service updates, notifications and support responses.',
          'Prevent fraud, abuse and unauthorized access.',
          'Improve our products and customer experience.',
        ],
      },
      {
        heading: 'Transaction processing',
        paragraphs: ['Transaction details may be shared with trusted service providers and payment processors only as needed to complete your request.'],
      },
      {
        heading: 'Data security',
        paragraphs: ['We use reasonable administrative, technical and physical safeguards to protect information against unauthorized access, alteration, disclosure or destruction. No internet transmission method is completely secure.'],
      },
      {
        heading: 'Third-party services',
        paragraphs: ['Trusted providers may support payment processing, service delivery, analytics, notifications and infrastructure. They receive only the information necessary to perform those services.'],
      },
      {
        heading: 'Data retention and your rights',
        paragraphs: [
          'We retain records as necessary to provide services, meet legal obligations, resolve disputes and enforce agreements.',
          'You may request access, correction or deletion of personal information where applicable and permitted by law.',
        ],
      },
      {
        heading: 'Contact us',
        paragraphs: ['Email: info@oresamsub.com\nPhone: 08168509044\nWebsite: oresamsub.com'],
      },
    ],
  },
  terms: {
    date: 'Last updated June 29, 2026',
    icon: 'gavel',
    introduction: 'OresamSub is operated by Oresam Telecoms Global Concept. By using our website, mobile application, WhatsApp services or other services, you agree to these Terms of Service.',
    title: 'Terms and Conditions',
    sections: [
      {
        heading: '1. About our services',
        paragraphs: ['We provide digital services including airtime, mobile data, cable TV subscriptions, electricity payments and other value-added services. Services may be modified, suspended or discontinued when necessary.'],
      },
      {
        heading: '2. Eligibility',
        bullets: [
          'You are legally capable of entering a binding agreement.',
          'The information you provide is accurate and current.',
          'You will use OresamSub only for lawful purposes.',
          'You will not engage in fraudulent or abusive activity.',
        ],
      },
      {
        heading: '3. User responsibilities',
        bullets: [
          'Verify phone, meter, smart-card and other recipient details before payment.',
          'Maintain the confidentiality of your credentials and transaction PIN.',
          'Secure your device and account.',
        ],
        paragraphs: ['OresamSub is not responsible for losses caused by incorrect information submitted by a user.'],
      },
      {
        heading: '4. Transactions',
        paragraphs: ['Review the selected provider, plan, recipient and amount before confirming. Once successfully delivered, a transaction may not be reversible.'],
      },
      {
        heading: '5. Pricing and payments',
        paragraphs: ['Prices may change because of provider, network, aggregator or regulatory changes. The displayed price at purchase time applies. Payments are processed through trusted providers; OresamSub does not store debit-card CVV or card PIN details.'],
      },
      {
        heading: '6. Failed transactions and refunds',
        paragraphs: ['Failures may occur because of network or provider interruptions. Eligible failed transactions may be retried or refunded to the user’s wallet after investigation. Timelines can depend on the affected provider.'],
      },
      {
        heading: '7. Service availability',
        paragraphs: ['We aim for reliable service but cannot guarantee uninterrupted availability. Maintenance, network outages, third-party failures and circumstances outside our control may cause disruptions.'],
      },
      {
        heading: '8. Prohibited activities',
        bullets: [
          'Fraudulent transactions or unauthorized system access.',
          'Abuse of promotions or platform features.',
          'Automated exploitation of the platform.',
          'Any activity that violates applicable law.',
        ],
      },
      {
        heading: '9. Limitation of liability',
        paragraphs: ['To the fullest extent permitted by law, Oresam Telecoms Global Concept is not liable for indirect losses, lost opportunities, provider delays or disruptions outside our reasonable control.'],
      },
      {
        heading: '10. Changes and contact',
        paragraphs: ['We may update these terms by publishing a revised version. Continued use after an update constitutes acceptance.\n\nEmail: info@oresamsub.com\nPhone: 08168509044\nWebsite: oresamsub.com'],
      },
    ],
  },
  'account-deletion': {
    date: 'Last updated July 20, 2026',
    icon: 'person_remove',
    introduction: 'You can submit a formal account-deletion request inside the mobile app or contact OresamSub from the public web instructions.',
    title: 'Account Deletion',
    sections: [
      {
        heading: 'Request deletion from the app',
        bullets: [
          'Open Account, then Help, support and policies.',
          'Open Account deletion instructions and select Request account deletion.',
          'Enter your password and type DELETE MY ACCOUNT to confirm.',
        ],
        paragraphs: ['This records a formal deletion request, immediately blocks account access and revokes active mobile sessions. The request is reviewed within 30 days.'],
      },
      {
        heading: 'Request deletion by email',
        paragraphs: ['Email info@oresamsub.com from your registered email address with the subject “Account deletion request”. We may ask you to verify account ownership.'],
      },
      {
        heading: 'Data retention',
        paragraphs: ['Profile information not required for security, disputes, fraud prevention, financial recordkeeping or another legal obligation will be deleted or anonymized after verification. Required transaction and compliance records may be retained for the legally required period.'],
      },
    ],
  },
};

export default function LegalDocumentScreen() {
  const { document } = useLocalSearchParams<{ document: string }>();
  const content = documents[document];

  return (
    <>
      <Stack.Screen options={{ headerShown: true, title: content?.title ?? 'Legal information' }} />
      <Screen safeTop={false}>
        {content ? (
          <>
            <View style={styles.hero}>
              <View style={styles.brand}><BrandLogo size={38} /></View>
              <View style={styles.heroCopy}>
                <Text style={styles.eyebrow}>ORESAMSUB LEGAL</Text>
                <Text style={styles.title}>{content.title}</Text>
                <Text style={styles.date}>{content.date}</Text>
              </View>
              <View style={styles.icon}><MaterialIcon color={colors.primary} name={content.icon} size={23} /></View>
            </View>
            <Text style={styles.introduction}>{content.introduction}</Text>
            {content.sections.map((section) => (
              <View key={section.heading} style={styles.section}>
                <Text style={styles.heading}>{section.heading}</Text>
                {section.paragraphs?.map((paragraph) => (
                  <Text key={paragraph} style={styles.paragraph}>{paragraph}</Text>
                ))}
                {section.bullets?.map((bullet) => (
                  <View key={bullet} style={styles.bulletRow}>
                    <View style={styles.bullet} />
                    <Text style={styles.bulletText}>{bullet}</Text>
                  </View>
                ))}
              </View>
            ))}
            {document === 'account-deletion' ? (
              <Pressable onPress={() => router.push('/request-account-deletion')} style={({ pressed }) => [styles.deactivate, pressed && styles.pressed]}>
                <MaterialIcon color={colors.white} name="no_accounts" size={19} />
                <Text style={styles.deactivateText}>Request account deletion</Text>
              </Pressable>
            ) : null}
            <Text style={styles.footer}>OresamSub is operated by Oresam Telecoms Global Concept.</Text>
          </>
        ) : (
          <View style={styles.notFound}>
            <MaterialIcon color={colors.muted} name="description" size={32} />
            <Text style={styles.notFoundTitle}>Document unavailable</Text>
            <Text style={styles.notFoundText}>Return to Help and choose one of the available legal documents.</Text>
          </View>
        )}
      </Screen>
    </>
  );
}

const styles = StyleSheet.create({
  hero: { alignItems: 'center', backgroundColor: colors.primarySoft, borderColor: '#C7ECDD', borderRadius: 20, borderWidth: 1, flexDirection: 'row', padding: 14 },
  brand: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 15, height: 48, justifyContent: 'center', width: 48 },
  heroCopy: { flex: 1, marginHorizontal: 11 },
  eyebrow: { color: colors.primary, fontFamily: fonts.extraBold, fontSize: 8, letterSpacing: 1.2 },
  title: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 19, marginTop: 2 },
  date: { color: colors.muted, fontFamily: fonts.medium, fontSize: 8, marginTop: 3 },
  icon: { alignItems: 'center', backgroundColor: colors.surface, borderRadius: 15, height: 42, justifyContent: 'center', width: 42 },
  introduction: { color: colors.text, fontFamily: fonts.medium, fontSize: 12, lineHeight: 20, marginTop: 18 },
  section: { backgroundColor: colors.surface, borderColor: colors.border, borderRadius: 17, borderWidth: 1, marginTop: 12, padding: 15 },
  heading: { color: colors.primaryDark, fontFamily: fonts.extraBold, fontSize: 14, marginBottom: 7 },
  paragraph: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, lineHeight: 19, marginTop: 4 },
  bulletRow: { alignItems: 'flex-start', flexDirection: 'row', marginTop: 7 },
  bullet: { backgroundColor: colors.primary, borderRadius: 3, height: 6, marginRight: 9, marginTop: 6, width: 6 },
  bulletText: { color: colors.muted, flex: 1, fontFamily: fonts.regular, fontSize: 11, lineHeight: 18 },
  deactivate: { alignItems: 'center', backgroundColor: colors.danger, borderRadius: 14, flexDirection: 'row', gap: 8, justifyContent: 'center', marginTop: 16, minHeight: 52 },
  deactivateText: { color: colors.white, fontFamily: fonts.extraBold, fontSize: 12 },
  pressed: { opacity: 0.78 },
  footer: { color: colors.muted, fontFamily: fonts.regular, fontSize: 9, lineHeight: 15, marginTop: 20, textAlign: 'center' },
  notFound: { alignItems: 'center', paddingHorizontal: 30, paddingTop: 70 },
  notFoundTitle: { color: colors.text, fontFamily: fonts.extraBold, fontSize: 16, marginTop: 12 },
  notFoundText: { color: colors.muted, fontFamily: fonts.regular, fontSize: 11, lineHeight: 17, marginTop: 5, textAlign: 'center' },
});
