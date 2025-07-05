<?php

namespace App\Http\Services;

use Exception;
use App\Models\User;
use App\Models\CouponCode;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\SiteTemplate;
use App\Models\FundingOption;
use App\Models\UsedUserCouponCode;
use App\Models\UserVirtualAccount;
use App\Models\WalletFundingPromo;
use Illuminate\Support\Facades\DB;
use App\Models\LandingPagesSetting;
use App\Models\FundingOptionBankCodes;
use App\Models\UsedWalletFundingPromo;
use Spatie\TranslationLoader\LanguageLine;

class MultilanguageService{


    public function translate(){
         
         // dd('sssdfawerw');
         $arr1 = [
            'Dashboard' => [
              'en' => 'Dashboard',
              'yo' => 'Dasibodu',
              'ig' => 'Ntanụgharị',
              'ha' => 'Allon Kulawa',
            ],
            "Contact" => [
              "en" => "Contact",
              "yo" => "Kan sí wa",
              "ig" => "Kpọtụrụ",
              "ha" => "Tuntuɓi"
            ],
            'Copy' => [
              'en' => 'Copy',
              'yo' => 'Daakọ',
              'ig' => 'Detuo',
              'ha' => 'Kwafi',
            ],
            'Welcome' => [
              'en' => 'Welcome',
              'yo' => 'Kaabọ',
              'ig' => 'Nnọọ',
              'ha' => 'Barka da zuwa',
            ],
            "We're here to help! Reach out to our customer support team through the following channels" => [
                  "en" => "We're here to help! Reach out to our customer support team through the following channels",
                  "yo" => "A wà níbí láti ràn ẹ́ lọ́wọ́! Kan ẹgbẹ́ ìtìlẹ́yìn oníbàárà wa lórí àwọn ọ̀nà yìí",
                  "ig" => "Anyị dị ebe a iji nyere gị aka! Kpọtụrụ ndị otu nkwado ndị ahịa anyị site na ụzọ ndị a",
                  "ha" => "Muna nan don taimaka! Tuntuɓi ƙungiyar tallafin abokin cinikinmu ta waɗannan hanyoyin"
              ],

            "Email" => [
                "en" => "Email",
                "yo" => "Ìmẹ́lì",
                "ig" => "Email",
                "ha" => "Imel"
            ],

            "Reach us on whatsapp by" => [
                "en" => "Reach us on WhatsApp by",
                "yo" => "Kan wá lórí WhatsApp pẹ̀lú",
                "ig" => "Kpọtụrụ anyị na WhatsApp site na",
                "ha" => "Tuntuɓe mu ta WhatsApp ta"
            ],

            "clicking this link" => [
                "en" => "clicking this link",
                "yo" => "títẹ àsọyé yìí",
                "ig" => "pịnye njikọ a",
                "ha" => "danna wannan mahaɗin"
            ],

            "Office Address" => [
                "en" => "Office Address",
                "yo" => "Àdírẹ́sì Ọ́fíìsì",
                "ig" => "Adreesị ụlọ ọrụ",
                "ha" => "Adireshin ofis"
            ],

            "Developed with ❤️ by" => [
                "en" => "Developed with ❤️ by",
                "yo" => "Dáà ṣe pẹ̀lú ❤️ nipasẹ",
                "ig" => "E mepụtara ya na ❤️ site n'aka",
                "ha" => "An haɓaka shi da ❤️ ta hannun"
            ],

            'Buy Data' => [
              'en' => 'Buy Data',
              'yo' => 'Ra Data',
              'ig' => 'Zụta Data',
              'ha' => 'Sayi Bayanai',
            ],
            'Buy Airtime' => [
              'en' => 'Buy Airtime',
              'yo' => 'Ra Airtime',
              'ig' => 'Zụta Oge Ikuku',
              'ha' => 'Sayi Lokacin Kira',
            ],
            'Buy Electricity' => [
              'en' => 'Buy Electricity',
              'yo' => 'Ra Ina',
              'ig' => 'Zụta ọkụ',
              'ha' => 'Sayi Wutar Lantarki',
            ],
            'Cable Subscription' => [
              'en' => 'Cable Subscription',
              'yo' => 'Iforukọsilẹ Kẹbu',
              'ig' => 'Debanye Kabel',
              'ha' => 'Biyan Kuɗin Kebul',
            ],
            'Enjoy commission using your link' => [
              'en' => 'Enjoy commission using your link',
              'yo' => 'Gbadun komisọnu pẹlu ọna asopọ rẹ',
              'ig' => 'Nwee ụgwọ site na njikọ gị',
              'ha' => 'Ji da komai ta hanyar haɗin ku',
            ],
            'Fund Wallet' => [
              'en' => 'Fund Wallet',
              'yo' => 'Fowọle Apamọwọ',
              'ig' => "Tinye ego n'ime akpa",
              'ha' => 'Saka kuɗi cikin walat',
            ],
            'Balance' => [
              'en' => 'Balance',
              'yo' => 'Iwọn to ku',
              'ig' => 'Ego fọdụrụ',
              'ha' => 'Ma’auni',
            ],
            'Transactions' => [
              'en' => 'Transactions',
              'yo' => 'Awọn iṣowo',
              'ig' => 'Ụgwọ',
              'ha' => "Ma'amaloli",
            ],
            'Quick Data Purchase' => [
              'en' => 'Quick Data Purchase',
              'yo' => 'Ra data ni kiakia',
              'ig' => 'Zụta data ngwa ngwa',
              'ha' => 'Sayi bayanai da sauri',
            ],
            "Data Purchase" => [
                "en" => "Data Purchase",
                "yo" => "Rà Ìnítànẹ́tì",
                "ig" => "Zụta Data",
                "ha" => "Sayi Bayanai"
            ],
            'Data(Bulk)' => [
              'en' => 'Data(Bulk)',
              'yo' => 'Data (apo)',
              'ig' => "Data (n'ọtụtụ)",
              'ha' => 'Bayanai (da yawa)',
            ],
            'Airtime' => [
              'en' => 'Airtime',
              'yo' => 'Aago foonu',
              'ig' => 'Oge Ikuku',
              'ha' => 'Lokacin Kira',
            ],
            'Electricity Subscription' => [
              'en' => 'Electricity Subscription',
              'yo' => 'Iforukọ Ina',
              'ig' => 'Debanye ọkụ',
              'ha' => 'Biyan wuta',
            ],
            "Airtime Transactions" => [
              "en" => "Airtime Transactions",
              "yo" => "Ìṣèpọ̀ Rẹ́lùféònù",
              "ig" => "Ụgwọ Ugboelu",
              "ha" => "Ma'amaloli na Katin Waya"
            ],
            'Commissions' => [
              'en' => 'Commissions',
              'yo' => 'Awọn Komisọnu',
              'ig' => 'Ụgwọ akwụ',
              'ha' => 'Kwamitoci',
            ],
            'API Docs' => [
              'en' => 'API Docs',
              'yo' => 'Awọn iwe API',
              'ig' => 'Akwụkwọ API',
              'ha' => 'Takardun API',
            ],
            'User Settings' => [
              'en' => 'User Settings',
              'yo' => 'Eto Olumulo',
              'ig' => 'Ntọala onye ọrụ',
              'ha' => 'Saitunan mai amfani',
            ],
            'Recent Transactions' => [
              'en' => 'Recent Transactions',
              'yo' => 'Awọn iṣowo to ṣẹṣẹ',
              'ig' => 'Ụgwọ ọhụrụ',
              'ha' => 'Ma\'amaloli na baya-bayan nan',
            ],
            'Please note' => [
              'en' => 'Please note',
              'yo' => 'Jọwọ ṣe akiyesi',
              'ig' => 'Biko rịba ama',
              'ha' => 'Da fatan a lura',
            ],
            'You can also make a direct payment to our bank account and your wallet will be credited.' => [
              'en' => 'You can also make a direct payment to our bank account and your wallet will be credited.',
              'yo' => 'O tun le sanwo taara si akọọlẹ wa ati ka si apamọwọ rẹ.',
              'ig' => 'Ị nwere ike ime akwụ ụgwọ ozugbo n\'akaụntụ anyị, a ga-ebelata akpa gị.',
              'ha' => 'Zaka iya biyan kai tsaye zuwa asusun mu kuma za a caje walat ɗinka.',
            ],
            'Here’s the details' => [
              'en' => 'Here’s the details',
              'yo' => 'Eyi ni awọn alaye',
              'ig' => 'Nke a bụ nkọwa',
              'ha' => 'Ga bayanan',
            ],
            'Forgot your password' => [
              'en' => 'Forgot your password',
              'yo' => 'Ṣe o gbagbe ọrọ aṣínà rẹ',
              'ha' => 'Ka manta kalmar sirrinka',
              'ig' => 'I chefu okwuntughe gi',
          ],
      
          'No problem' => [
              'en' => 'No problem',
              'yo' => 'Ko si iṣoro',
              'ha' => 'Babu matsala',
              'ig' => 'Enweghi nsogbu',
          ],
      
          'Just let us know your email address and we will email you a password reset link that will allow you to choose a new one' => [
              'en' => 'Just let us know your email address and we will email you a password reset link that will allow you to choose a new one',
              'yo' => 'Kan fun wa ni adirẹsi imeeli rẹ, a ó sì fi ọna asopọ atunṣe ọrọ aṣínà ranṣẹ si ọ lati yan tuntun kan',
              'ha' => 'Ka gaya mana adireshin imel ɗinka, za mu turo maka da hanyar canza kalmar sirri ta imel don ka zabi sabuwa',
              'ig' => 'Nye anyi adreesi email gi, anyi ga eziga gi njikọ iji gbanwee okwuntughe gi ka i nwee ike họrọ nke ọhụrụ',
          ],
      
          'Please check your spam folder too in case you dont find the email notification sent to you in your inbox' => [
              'en' => 'Please check your spam folder too in case you dont find the email notification sent to you in your inbox',
              'yo' => 'Jọwọ ṣayẹwo folda imeeli aṣiṣe naa paapaa ti o ko ba ri ifitonileti imeeli naa ninu apo-iwọle rẹ',
              'ha' => 'Da fatan za ka binciki folda na spam idan ba ka ga saƙon imel ɗin da aka turo maka a cikin akwatin saƙon shiga ba',
              'ig' => 'Biko lelee folda spam gi ma, ma obu na inweghị email anyi zitere gi na inbox gi',
          ],
      
          'Email Password Reset Link' => [
              'en' => 'Email Password Reset Link',
              'yo' => 'Firanṣẹ ọna asopọ atunṣe ọrọ aṣínà',
              'ha' => 'Tura hanyar canza kalmar sirri ta imel',
              'ig' => 'Ziga njikọ mgbake okwuntughe na email',
          ],

          'Email Address' => [
              'en' => 'Email Address',
              'yo' => 'Adirẹsi Imeeli',
              'ha' => 'Adireshin Imel',
              'ig' => 'Adreesi Email',
          ],

      
          'Return to login' => [
              'en' => 'Return to login',
              'yo' => 'Pada si wiwọle',
              'ha' => 'Koma zuwa shiga',
              'ig' => 'Laghachi na nbanye',
          ],
      
          'Password Reset' => [
              'en' => 'Password Reset',
              'yo' => 'Atunṣe Ọrọ aṣínà',
              'ha' => 'Canza Kalmar Sirri',
              'ig' => 'Mgbake Okwuntughe',
          ],
            'Account Number' => [
              'en' => 'Account Number',
              'yo' => 'Nọmba Iroyin',
              'ig' => 'Nọmba Akaụntụ',
              'ha' => 'Lambar Asusun',
            ],
            'Account Email' => [
                'en' => 'Account Email',
                'yo' => 'Imeeli Iroyin',
                'ig' => 'Email Akaụntụ',
                'ha' => 'Imel ɗin Asusun',
            ],
           'Generated' => [
              'en' => 'Generated',
              'yo' => 'Ti ṣe',
              'ig' => 'E mepụtara',
              'ha' => 'An ƙirƙira',
            ],
            'Bank Name' => [
              'en' => 'Bank Name',
              'yo' => 'Orukọ Banki',
              'ig' => 'Aha Ụlọ akụ',
              'ha' => 'Sunan Banki',
            ],
            'Account Name' => [
              'en' => 'Account Name',
              'yo' => 'Orukọ Iroyin',
              'ig' => 'Aha Akaụntụ',
              'ha' => 'Sunan Asusun',
            ],
           "Account Details" => [
                "en" => "Account Details",
                "yo" => "Alaye Àkọọlẹ",
                "ig" => "Nkọwa Akaụntụ",
                "ha" => "Cikakken Bayanin Asusun"
            ],
            "Charges" => [
                "en" => "Charges",
                "yo" => "Owó ìsanwó",
                "ig" => "Ụgwọ",
                "ha" => "Kudade"
            ],
            'Click here to reach us on whatsapp' => [
              'en' => 'Click here to reach us on whatsapp',
              'yo' => 'Tẹ nibi lati kan si wa lori WhatsApp',
              'ig' => 'Pịa ebe a iji kpọtụrụ anyị na WhatsApp',
              'ha' => 'Danna nan don tuntuɓar mu a WhatsApp',
            ],
            'Virtual Accounts' => [
              'en' => 'Virtual Accounts',
              'yo' => 'Awọn iroyin foju',
              'ig' => 'Akaụntụ virshual',
              'ha' => 'Asusun na\'ura',
            ],
            'Wallet Transactions' => [
              'en' => 'Wallet Transactions',
              'yo' => 'Awọn iṣowo apamọwọ',
              'ig' => 'Ụgwọ akpa',
              'ha' => 'Ma\'amaloli na walat',
            ],
            'Pending' => [
              'en' => 'Pending',
              'yo' => 'Nduro',
              'ig' => 'Na-echere',
              'ha' => 'A jiran',
            ],
            "Generate" => [
              "en" => "Generate",
              "yo" => "Ṣẹda",
              "ig" => "Mepụta",
              "ha" => "Ƙirƙira"
            ],
            'Wallet Balance' => [
              'en' => 'Wallet Balance',
              'yo' => 'Iwontunwọnsì Apamọwọ',
              'ig' => "Ego fọdụrụ n'akpa",
              'ha' => "Ma'aunin Walat",
            ],
            'Fund wallet using' => [
              'en' => 'Fund wallet using',
              'yo' => 'Fowọle apamọwọ pẹlu',
              'ig' => 'Tinye ego n\'ime akpa site na',
              'ha' => 'Saka kuɗi cikin walat ta hanyar',
            ],
            'Generate Virtual Account for the bank code' => [
              'en' => 'Generate Virtual Account for the bank code',
              'yo' => 'Ṣẹda iroyin foju fun koodu banki',
              'ig' => 'Mepụta akaụntụ virshual maka koodu ụlọ akụ',
              'ha' => 'Ƙirƙiri asusun na\'ura don lambar banki',
            ],
            'Enter your pin to secure your transaction' => [
              'en' => 'Enter your pin to secure your transaction',
              'yo' => 'Tẹ PIN rẹ lati daabobo iṣowo rẹ',
              'ig' => 'Tinye PIN gị iji chebe ọrụ gị',
              'ha' => 'Shigar da PIN ɗinka don kare mu\'amala',
            ],
            'View data transactions' => [
              'en' => 'View data transactions',
              'yo' => 'Wo awọn iṣowo data',
              'ig' => 'Lee ego data',
              'ha' => 'Duba ma\'amaloli na bayanai',
            ],
            "View Airtime Transactions" => [
              "en" => "View Airtime Transactions",
              "yo" => "Wo Ìṣèpọ̀ Rẹ́lùféònù",
              "ig" => "Lee Ụgwọ Ugboelu",
              "ha" => "Duba Ma'amalolin Katin Waya"
            ],
            "Enter Amount" => [
              "en" => "Enter Amount",
              "yo" => "Tẹ iye owó",
              "ig" => "Tinye ego",
              "ha" => "Shigar da adadi"
            ],
            'Phone number to recharge' => [
              'en' => 'Phone number to recharge',
              'yo' => 'Nọmba foonu lati gba agbara',
              'ig' => 'Nọmba ekwentị ịgba ụgwọ',
              'ha' => 'Lambar waya don caji',
            ],
            'Filter by plan categories' => [
              'en' => 'Filter by plan categories',
              'yo' => 'Fọto nipasẹ awọn ẹka eto',
              'ig' => 'Sefe site n\'ụdị atụmatụ',
              'ha' => 'Tace ta rukunin tsari',
            ],
            'Product Plans List' => [
              'en' => 'Product Plans List',
              'yo' => 'Atokọ Awọn Eto Ọja',
              'ig' => 'Ndepụta atụmatụ ngwaahịa',
              'ha' => 'Jerin Shirye-shiryen Samfura',
            ],
            'Select product plan' => [
              'en' => 'Select product plan',
              'yo' => 'Yan eto ọja',
              'ig' => 'Họrọ atụmatụ ngwaahịa',
              'ha' => 'Zaɓi tsarin samfur',
            ]
          ];

          $arr2 =  [
            'Enter your pin to secure transaction' => [
              'en' => 'Enter your pin to secure transaction',
              'yo' => 'Tẹ PIN rẹ lati daabobo iṣowo',
              'ig' => 'Tinye PIN gị iji chekwaa ọrụ',
              'ha' => 'Shigar da PIN ɗinka don tabbatar da ma’amala',
            ],
            'YOUR PIN IS REQUIRED TO ENSURE YOUR TRANSACTION IS SECURE. IF YOU HAVE FORGOTTEN YOUR PIN, KINDLY CLICK HERE TO REACH OUT TO SUPPORT' => [
              'en' => 'YOUR PIN IS REQUIRED TO ENSURE YOUR TRANSACTION IS SECURE. IF YOU HAVE FORGOTTEN YOUR PIN, KINDLY CLICK HERE TO REACH OUT TO SUPPORT',
              'yo' => 'PIN rẹ nilo lati daabobo iṣowo rẹ. Ti o ba gbagbe rẹ, tẹ nibi lati kan si atilẹyin',
              'ig' => 'A chọrọ PIN gị iji chekwaa ọrụ gị. Ọ bụrụ na ịchefuru ya, pịa ebe a iji kpọtụrụ nkwado',
              'ha' => 'Ana buƙatar PIN ɗinka don kare ma’amala. Idan ka manta, danna nan don tuntuɓar goyon bayan',
            ],
            'Show pin' => [
              'en' => 'Show pin',
              'yo' => 'Fi PIN han',
              'ig' => 'Gosi PIN',
              'ha' => 'Nuna PIN',
            ],
            'Network' => [
              'en' => 'Network',
              'yo' => 'Nẹtiwọọki',
              'ig' => 'Netwọk',
              'ha' => 'Sadarwa',
            ],
            'Phone Number(s) to recharge' => [
              'en' => 'Phone Number(s) to recharge',
              'yo' => 'Nọmba foonu lati gba agbara',
              'ig' => 'Nọmba ekwentị ịgba ụgwọ',
              'ha' => 'Lambar waya don caji',
            ],
            'Select' => [
              'en' => 'Select',
              'yo' => 'Yan',
              'ig' => 'Họrọ',
              'ha' => 'Zaɓi',
            ],
            'PIN' => [
              'en' => 'PIN',
              'yo' => 'PIN',
              'ig' => 'PIN',
              'ha' => 'PIN',
            ],
            'Amount' => [
              'en' => 'Amount',
              'yo' => 'Iye',
              'ig' => 'Ego',
              'ha' => 'Adadin',
            ],
            'Product Plan Category' => [
              'en' => 'Product Plan Category',
              'yo' => 'Ẹka Eto Ọja',
              'ig' => 'Ụdị Atụmatụ Ngwaahịa',
              'ha' => 'Rukunin Tsarin Samfura',
            ],
            'Validated name on the card' => [
              'en' => 'Validated name on the card',
              'yo' => 'Orukọ ti a fọwọsi lori kaadi',
              'ig' => 'Aha a kwadoro n’akụkụ kaadị',
              'ha' => 'Sunan da aka tabbatar a kan kati',
            ],
            'Failed Try Again Later' => [
              'en' => 'Failed Try Again Later',
              'yo' => 'Ko ṣaṣeyọri, gbiyanju lẹẹkansi',
              'ig' => 'Megharịrị, gbalịa ọzọ',
              'ha' => 'Ya kasa, gwada baya',
            ],
            'Please validate the details below before payment' => [
              'en' => 'Please validate the details below before payment',
              'yo' => 'Jọwọ jẹrisi awọn alaye ni isalẹ ṣaaju isanwo',
              'ig' => 'Biko nyochaa nkọwa dị n’okpuru tupu ịkwụ ụgwọ',
              'ha' => 'Da fatan a tabbatar da bayanan ƙasa kafin biyan kuɗi',
            ],
            'Name on Card' => [
              'en' => 'Name on Card',
              'yo' => 'Orukọ lori Kaadi',
              'ig' => 'Aha dị na Kaadị',
              'ha' => 'Sunan a kan kati',
            ],
            'Buy Cable TV' => [
              'en' => 'Buy Cable TV',
              'yo' => 'Ra Kẹbu TV',
              'ig' => 'Zụta Kabl TV',
              'ha' => 'Sayi Talabijin na Kebul',
            ],
            'Cable TV Transactions' => [
              'en' => 'Cable TV Transactions',
              'yo' => 'Awọn iṣowo Kẹbu TV',
              'ig' => 'Ụgwọ Kabl TV',
              'ha' => 'Ma’amaloli na Kebul TV',
            ],
            'View Cable TV Transactions' => [
              'en' => 'View Cable TV Transactions',
              'yo' => 'Wo Awọn iṣowo Kẹbu TV',
              'ig' => 'Lee Ugwo Kabl TV',
              'ha' => 'Duba Ma’amaloli na Kebul TV',
            ],
            'Smart Card number / IUC number' => [
              'en' => 'Smart Card number / IUC number',
              'yo' => 'Nọmba Kaadi Smart / Nọmba IUC',
              'ig' => 'Nọmba Smart Kaadị / IUC',
              'ha' => 'Lambar Smart Card / IUC',
            ],
            'Utility Bills Transactions' => [
              'en' => 'Utility Bills Transactions',
              'yo' => 'Awọn iṣowo owo ina',
              'ig' => 'Ụgwọ ụgwọ ọkụ',
              'ha' => 'Ma’amaloli na kuɗin wuta',
            ],
            'View Utility Bills Transactions' => [
              'en' => 'View Utility Bills Transactions',
              'yo' => 'Wo awọn iṣowo owo ina',
              'ig' => 'Lee ụgwọ ọkụ',
              'ha' => 'Duba ma’amaloli kuɗin wuta',
            ],
            'Buy Utility Bills' => [
              'en' => 'Buy Utility Bills',
              'yo' => 'Ra Awọn owo Ina',
              'ig' => 'Zụta ụgwọ ọkụ',
              'ha' => 'Sayi kuɗin wuta',
            ],
            'extra information' => [
              'en' => 'extra information',
              'yo' => 'Alaye afikun',
              'ig' => 'Ozi ọzọ',
              'ha' => 'Ƙarin bayani',
            ],
            'extra address information' => [
              'en' => 'extra address information',
              'yo' => 'Alaye adirẹsi afikun',
              'ig' => 'Ozi adreesị ọzọ',
              'ha' => 'Ƙarin bayanin adireshi',
            ],
            'All Transactions' => [
              'en' => 'All Transactions',
              'yo' => 'Gbogbo Awọn iṣowo',
              'ig' => 'Ụgwọ niile',
              'ha' => 'Dukkanin Ma’amaloli',
            ],
            'Filter Options' => [
              'en' => 'Filter Options',
              'yo' => 'Aṣayan Àlẹmọ',
              'ig' => 'Nhọrọ Sefe',
              'ha' => 'Zaɓuɓɓukan tacewa',
            ],
            'No data available in table' => [
              'en' => 'No data available in table',
              'yo' => 'Ko si data ti o wa ninu tabili',
              'ig' => 'Enweghị data dị n\'ime tebụl',
              'ha' => 'Babu bayanai a cikin tebur',
            ],
            'entries per page' => [
              'en' => 'entries per page',
              'yo' => 'wọnkọọkan fun oju-iwe',
              'ig' => 'ndepụta kwa ibe',
              'ha' => 'shigarwa kowane shafi',
            ],
            'Filter' => [
              'en' => 'Filter',
              'yo' => 'Àlẹmọ',
              'ig' => 'Sefe',
              'ha' => 'Tace',
            ],
            'Basic filter' => [
              'en' => 'Basic filter',
              'yo' => 'Àlẹmọ ipilẹ',
              'ig' => 'Sefe dị mfe',
              'ha' => 'Matattarar asali',
            ],
            'Refresh' => [
              'en' => 'Refresh',
              'yo' => 'Tunse',
              'ig' => 'Megharịa',
              'ha' => 'Sabunta',
            ],
            'Phone recharged' => [
              'en' => 'Phone recharged',
              'yo' => 'Foonu to gba agbara',
              'ig' => 'Ekekọrịta agbadoghị',
              'ha' => 'An caje waya',
            ],
            'Filter by Plan Category' => [
              'en' => 'Filter by Plan Category',
              'yo' => 'Àlẹmọ nipasẹ Ẹka Eto',
              'ig' => 'Sefe site n’usoro atụmatụ',
              'ha' => 'Tace bisa rukunin shiri',
            ],
            'Date Range' => [
              'en' => 'Date Range',
              'yo' => 'Iwọn ọjọ',
              'ig' => 'Mpụta ụbọchị',
              'ha' => 'Tsakanin kwanaki',
            ],
            'Date from' => [
              'en' => 'Date from',
              'yo' => 'Ọjọ lati',
              'ig' => 'Ụbọchị site na',
              'ha' => 'Kwanan daga',
            ],
            'Date to' => [
              'en' => 'Date to',
              'yo' => 'Ọjọ si',
              'ig' => 'Ụbọchị ruo',
              'ha' => 'Kwanan zuwa',
            ],
            'Save changes' => [
              'en' => 'Save changes',
              'yo' => 'Fipamọ awọn ayipada',
              'ig' => 'Chekwa mgbanwe',
              'ha' => 'Ajiye canje-canje',
            ],
            'Wallet' => [
              'en' => 'Wallet',
              'yo' => 'Apamọwọ',
              'ig' => 'Akpa',
              'ha' => 'Walat',
            ],
            'Product Details' => [
              'en' => 'Product Details',
              'yo' => 'Alaye Ọja',
              'ig' => 'Nkọwa Ngwaahịa',
              'ha' => 'Bayanin Samfura',
            ],
            'Txn Category' => [
              'en' => 'Txn Category',
              'yo' => 'Ẹka iṣowo',
              'ig' => 'Ụdị ugwo',
              'ha' => 'Rukunin mu’amala',
            ],
            'Phone' => [
              'en' => 'Phone',
              'yo' => 'Foonu',
              'ig' => 'Ekwentị',
              'ha' => 'Waya',
            ],
            'Amount' => [
              'en' => 'Amount',
              'yo' => 'Iye',
              'ig' => 'Ego',
              'ha' => 'Adadin',
            ],
            'Plan' => [
                'en' => 'Plan',
                'yo' => 'Ètò',
                'ig' => 'Mmemme',
                'ha' => 'Shiri',
            ],
            
                "Discounted Amount" => [
                  "en" => "Discounted Amount",
                  "yo" => "Iye ti a din kuro",
                  "ig" => "Ego ebelatara",
                  "ha" => "Adadin rangwame"
                ],
                "Balance Before" => [
                  "en" => "Balance Before",
                  "yo" => "Iwontunwonsi Ṣaaju",
                  "ig" => "Ego tupu",
                  "ha" => "Adadin kafin"
                ],
                "Balance After" => [
                  "en" => "Balance After",
                  "yo" => "Iwontunwonsi Lẹ́yìnna",
                  "ig" => "Ego mgbe e mesịrị",
                  "ha" => "Adadin bayan haka"
                ],
                "Status" => [
                  "en" => "Status",
                  "yo" => "Ipo",
                  "ig" => "Ogo",
                  "ha" => "Matsayi"
                ],
                "Date Added" => [
                  "en" => "Date Added",
                  "yo" => "Ọjọ tí a fi kun",
                  "ig" => "Ụbọchị agbakwunyere",
                  "ha" => "Ranar da aka ƙara"
                ],
                "Action" => [
                  "en" => "Action",
                  "yo" => "Ìṣe",
                  "ig" => "Omume",
                  "ha" => "Aiki"
                ],
                "Commissions" => [
                  "en" => "Commissions",
                  "yo" => "Èrè àtajà",
                  "ig" => "Ụgwọ uru",
                  "ha" => "Ribar kudi"
                ],
                "Your commission will be converted to your main wallet at the start of the next month for purchase of airtime, data etc" => [
                  "en" => "Your commission will be converted to your main wallet at the start of the next month for purchase of airtime, data etc",
                  "yo" => "Ẹsan rẹ yóò yipada sí apamọwọ àkọ́kọ́ rẹ níbẹ̀rẹ̀ oṣù tó ń bọ̀ fún rira afẹ́fẹ́, data, bẹ́ẹ̀ bẹ́ẹ̀ lọ.",
                  "ig" => "A ga-agbanwe uru gị gaa na akpa ego isi n’mbido ọnwa ọzọ maka ịzụta ekwentị, data wdg.",
                  "ha" => "Za a canza kuɗin kwamiti zuwa babban walat ɗinka a farkon watan gaba don siyan katin waya, bayanai da sauransu."
                ],
                "Alltime Commissions" => [
                  "en" => "Alltime Commissions",
                  "yo" => "Gbogbo Ẹ̀san",
                  "ig" => "Uru niile",
                  "ha" => "Dukkanin kwamitoci"
                ],
                "Pending Commissions" => [
                  "en" => "Pending Commissions",
                  "yo" => "Ẹ̀san tí ń dúró de fífi sílẹ̀",
                  "ig" => "Uru dị n’echiche",
                  "ha" => "Kwamitoci masu jiran lokaci"
                ],
                "Details" => [
                  "en" => "Details",
                  "yo" => "Alaye",
                  "ig" => "Nkọwa",
                  "ha" => "Cikakkun bayanai"
                ],
                "Commission" => [
                  "en" => "Commission",
                  "yo" => "Ẹ̀san",
                  "ig" => "Uru",
                  "ha" => "Kwamitin"
                ],
                "Redemption Status" => [
                  "en" => "Redemption Status",
                  "yo" => "Ipo Ìdápadà",
                  "ig" => "Ogo mgbaputa",
                  "ha" => "Matsayin fansa"
                ],
                "You can now use our apis to create amazing websites for yourself" => [
                  "en" => "You can now use our apis to create amazing websites for yourself",
                  "yo" => "O le lo API wa bayi lati ṣẹda awọn oju opo wẹẹbu alaragbayida fun ara rẹ",
                  "ig" => "Ị nwere ike iji API anyị ugbu a mepụta weebụsaịtị magburu onwe ya maka onwe gị",
                  "ha" => "Zaka iya amfani da API ɗinmu yanzu don ƙirƙirar shafukan yanar gizo masu ban mamaki don kanka"
                ],
                "API Key" => [
                  "en" => "API Key",
                  "yo" => "Bọtini API",
                  "ig" => "Igodo API",
                  "ha" => "Mabudin API"
                ],
                "PLEASE PROTECT THIS KEY AND SHARE ONLY WITH A TRUSTED PERSON" => [
                  "en" => "PLEASE PROTECT THIS KEY AND SHARE ONLY WITH A TRUSTED PERSON",
                  "yo" => "JỌ̀WỌ́, ṢẸ́DÁ BỌ́TÍNÍ YÌI MÚLẸ̀ KÍ O MÁ ṢE PÍN ÀFÍ PẸ̀LÚ ENI TÓ DÁ LÓRÍ",
                  "ig" => "Biko, chekwaa igodo a ma kesaa ya naanị onye a tụkwasịrị obi",
                  "ha" => "Don Allah, kare wannan mabudi kuma ka raba shi kawai da wanda ka yarda da shi"
                ],
                "Copy Api Key" => [
                  "en" => "Copy Api Key",
                  "yo" => "Daakọ Bọtini API",
                  "ig" => "Detuo Igodo API",
                  "ha" => "Kwafi Mabudin API"
                ],
                "Click to see documentation" => [
                  "en" => "Click to see documentation",
                  "yo" => "Tẹ lati wo iwe itọnisọna",
                  "ig" => "Pịa ịhụ akwụkwọ nduzi",
                  "ha" => "Danna don ganin bayanan aiki"
                ],
                "Copyright © 2025 Developed with" => [
                  "en" => "Copyright © 2025 Developed with",
                  "yo" => "Aṣẹ-lẹta © 2025 Ṣe pẹlu",
                  "ig" => "Ikike © 2025 Meputara ya na",
                  "ha" => "Hakkin mallaka © 2025 An ƙirƙira tare da"
                ],
                "All rights reserved" => [
                  "en" => "All rights reserved",
                  "yo" => "Gbogbo ẹtọ wa ni idaabobo",
                  "ig" => "Ejiri ikike niile chebe",
                  "ha" => "Dukkan haƙƙi an tanada"
                ],
                "Profile" => [
                  "en" => "Profile",
                  "yo" => "Profaili",
                  "ig" => "Nkọwa Onwe",
                  "ha" => "Bayanan Kai"
                ],
                "Settings" => [
                  "en" => "Settings",
                  "yo" => "Eto",
                  "ig" => "Ntọala",
                  "ha" => "Saituna"
                ],
        
            "Main Wallet" => [
                "en" => "Main Wallet",
                "yo" => "Apamọwọ Àkọ́kọ́",
                "ig" => "Akpa Ego Isi",
                "ha" => "Babban Walat"
            ],
            "Logout" => [
                "en" => "Logout",
                "yo" => "Jade",
                "ig" => "Pụọ",
                "ha" => "Fita"
            ],
            "First name" => [
                "en" => "First name",
                "yo" => "Orukọ Àkọ́kọ́",
                "ig" => "Aha Mbụ",
                "ha" => "Sunan Farko"
            ],
            "Last name" => [
                "en" => "Last name",
                "yo" => "Orukọ Ikẹyìn",
                "ig" => "Aha Ikpeazụ",
                "ha" => "Sunan Ƙarshe"
            ],
            "Other names" => [
                "en" => "Other names",
                "yo" => "Àwọn Orúkọ Míì",
                "ig" => "Aha ndị ọzọ",
                "ha" => "Sauran Sunaye"
            ],
            "Current password" => [
                "en" => "Current password",
                "yo" => "Ọrọ aṣínà Lọwọlọwọ",
                "ig" => "Okwuntughe dị ugbu a",
                "ha" => "Kalmar sirri ta yanzu"
            ],
            "New password" => [
                "en" => "New password",
                "yo" => "Ọrọ aṣínà Tuntun",
                "ig" => "Okwuntughe ọhụrụ",
                "ha" => "Sabuwar kalmar sirri"
            ],
            "Confirm new password" => [
                "en" => "Confirm new password",
                "yo" => "Jẹrisi Ọrọ aṣínà Tuntun",
                "ig" => "Kwenye okwuntughe ọhụrụ",
                "ha" => "Tabbatar da sabuwar kalmar sirri"
            ],
            "Confirm action with your PIN" => [
                "en" => "Confirm action with your PIN",
                "yo" => "Jẹ́risi ìṣe pẹ̀lú PIN rẹ",
                "ig" => "Kwenye omume a site na PIN gị",
                "ha" => "Tabbatar da aiki tare da PIN ɗinka"
            ],
            "Available Bulk Data Plans" => [
                "en" => "Available Bulk Data Plans",
                "yo" => "Àwọn Ètò Data Àpọ̀pọ̀ Tó Wà",
                "ig" => "Nhazi data obosara dị",
                "ha" => "Tsare-tsaren bayanai na jumla da ake da su"
            ],
            "Personal Information" => [
                "en" => "Personal Information",
                "yo" => "Alaye Ti Ara ẹni",
                "ig" => "Ozi nke Onwe",
                "ha" => "Bayanan Kai"
            ],
            "View Details" => [
                "en" => "View Details",
                "yo" => "Wo Alaye",
                "ig" => "Lee Nkọwa",
                "ha" => "Duba cikakkun bayanai"
            ],
            "Username" => [
                "en" => "Username",
                "yo" => "Orukọ Olumulo",
                "ig" => "Aha Ojiji",
                "ha" => "Sunan mai amfani"
            ],
            "Main" => [
                "en" => "Main",
                "yo" => "Àkọ́kọ́",
                "ig" => "Isi",
                "ha" => "Babba"
            ],
            "Modules" => [
                "en" => "Modules",
                "yo" => "Àwọn Module",
                "ig" => "Ngwugwu ọrụ",
                "ha" => "Modyuloli"
            ],
            "Home" => [
                "en" => "Home",
                "yo" => "Ilé",
                "ig" => "Ụlọ",
                "ha" => "Gida"
            ],
            "About" => [
                "en" => "About",
                "yo" => "Nipa",
                "ig" => "Banyere",
                "ha" => "Game da"
            ],
            "Services" => [
                "en" => "Services",
                "yo" => "Àwọn iṣẹ́",
                "ig" => "Ọrụ",
                "ha" => "Ayyuka"
            ],
            "Testimonials" => [
                "en" => "Testimonials",
                "yo" => "Ẹlẹ́ri",
                "ig" => "Akwukwo nkwupụta",
                "ha" => "Shaidun Bayani"
            ],
            "Signup" => [
                "en" => "Signup",
                "yo" => "Forukọsilẹ",
                "ig" => "Debanye",
                "ha" => "Yi rajista"
            ],
            "Login" => [
                "en" => "Login",
                "yo" => "Wọlé",
                "ig" => "Banye",
                "ha" => "Shiga"
            ],
            "Get Started" => [
                "en" => "Get Started",
                "yo" => "Bẹrẹ",
                "ig" => "Malite",
                "ha" => "Fara"
            ],
            "About Us" => [
                "en" => "About Us",
                "yo" => "Nipa Wa",
                "ig" => "Banyere Anyị",
                "ha" => "Game da Mu"
            ],
            "Who we are" => [
                "en" => "Who we are",
                "yo" => "Tani awa jẹ́",
                "ig" => "Onye anyị bụ",
                "ha" => "Mu su waye"
            ],
            "Get to know us more" => [
                "en" => "Get to know us more",
                "yo" => "Mọ̀ sí i nípa wa",
                "ig" => "Mụta ihe gbasara anyị karịa",
                "ha" => "Kara fahimtar mu"
              ],
              "Our Features and Services" => [
                "en" => "Our Features and Services",
                "yo" => "Àwọn Àmúyẹ àti Ìṣẹ́ Wa",
                "ig" => "Njirimara na Ọrụ anyị",
                "ha" => "Abubuwan da muke da su da ayyuka"
              ],
              "Our Partners" => [
                "en" => "Our Partners",
                "yo" => "Àwọn Alábàápàdé Wa",
                "ig" => "Ụmụ mmekọrịta anyị",
                "ha" => "Abokan hulɗarmu"
              ],
              "Product plans and prices" => [
                "en" => "Product plans and prices",
                "yo" => "Ètò àti Ọ̀nà Ọ̀jà",
                "ig" => "Nhazi ngwaahịa na ọnụahịa",
                "ha" => "Shirye-shiryen samfur da farashi"
              ],
              "Here’s a list of our pricing" => [
                "en" => "Here’s a list of our pricing",
                "yo" => "Ìyẹn ni àkójọ owó wa",
                "ig" => "Nke a bụ ndepụta ọnụahịa anyị",
                "ha" => "Ga jerin farashinmu"
              ],
              "Plans & Prices" => [
                  "en" => "Plans & Prices",
                  "yo" => "Ètò àti Ọ̀ya",
                  "ig" => "Atụmatụ na ọnụahịa",
                  "ha" => "Tsare-tsare da farashi"
              ],
              "Product name" => [
                "en" => "Product name",
                "yo" => "Orukọ Ọja",
                "ig" => "Aha Ngwaahịa",
                "ha" => "Sunan samfur"
              ],
              "Network" => [
                "en" => "Network",
                "yo" => "Nẹ́tíwọ́ọ̀kì",
                "ig" => "Netwọk",
                "ha" => "Sadarwa"
              ],
              "Plan name" => [
                "en" => "Plan name",
                "yo" => "Orukọ Ètò",
                "ig" => "Aha Mmemme",
                "ha" => "Sunan shiri"
              ],
              "Plan Category" => [
                "en" => "Plan Category",
                "yo" => "Ẹ̀ka Ètò",
                "ig" => "Udi Mmemme",
                "ha" => "Rukunin shiri"
              ],
              "Data Size" => [
                "en" => "Data Size",
                "yo" => "Iwọn Data",
                "ig" => "Nha Data",
                "ha" => "Girman bayanai"
              ],
              "Selling Price" => [
                "en" => "Selling Price",
                "yo" => "Iye Tita",
                "ig" => "Ọnụahịa",
                "ha" => "Farashin sayarwa"
              ],
              "Validity (Days)" => [
                "en" => "Validity (Days)",
                "yo" => "Ìfaramọ́ (Ọjọ́)",
                "ig" => "Oge ọrụ (Ụbọchị)",
                "ha" => "Inganci (Kwana)"
              ],
              "Developed with ❤️ by Subutility © 2025" => [
                "en" => "Developed with ❤️ by Subutility © 2025",
                "yo" => "Ti dá sílẹ̀ pẹ̀lú ❤️ nipasẹ Subutility © 2025",
                "ig" => "E mepụtara ya na ❤️ site na Subutility © 2025",
                "ha" => "An kirkireshi da ❤️ daga Subutility © 2025"
              ],
              "Owned by" => [
                "en" => "Owned by",
                "yo" => "Ti ẹni",
                "ig" => "Nke onye nwe ya",
                "ha" => "Mallakar"
              ],
              "We’re here to help! Reach out to our customer support team through the following channels" => [
                "en" => "We’re here to help! Reach out to our customer support team through the following channels",
                "yo" => "A wà níbí láti ràn ẹ́ lọ́wọ́! Kan sí ẹgbẹ́ ìtẹ́wọ́gbà oníbàárà wa nípasẹ̀ àwọn ọ̀nà yìí",
                "ig" => "Anyị nọ ebe a iji nyere gị aka! Kpọtụrụ ndị nkwado ahịa anyị site na ụzọ ndị a",
                "ha" => "Muna nan don taimako! Tuntubi ƙungiyar tallafin abokin cinikinmu ta hanyoyin da ke ƙasa"
              ],
              "Reach us on whatsapp by clicking this link" => [
                "en" => "Reach us on whatsapp by clicking this link",
                "yo" => "Kan sí wa lórí WhatsApp nípasẹ̀ líńkì yìí",
                "ig" => "Kpọtụrụ anyị na WhatsApp site na pịa njikọ a",
                "ha" => "Tuntube mu ta WhatsApp ta danna wannan mahada"
              ],
              "All rights Reserved" => [
                "en" => "All rights Reserved",
                "yo" => "Gbogbo ẹtọ wa ni idaabobo",
                "ig" => "Ejiri ikike niile chebe",
                "ha" => "Dukkan haƙƙi an tanada"
              ],
              "Prev" => [
                "en" => "Prev",
                "yo" => "Tẹlẹ̀",
                "ig" => "Gara aga",
                "ha" => "Na baya"
              ],
              "Next" => [
                "en" => "Next",
                "yo" => "Tó Kàn",
                "ig" => "Osote",
                "ha" => "Na gaba"
              ],
              "Already have an account" => [
                "en" => "Already have an account",
                "yo" => "Tí o bá ti ní àkọọlẹ̀",
                "ig" => "Ị nwere akaụntụ?",
                "ha" => "Kana da asusu tuni?"
              ],
              "First name and Surname" => [
                  "en" => "First name and Surname",
                  "yo" => "Orúkọ àkọ́kọ́ àti orúkọ ìdílé",
                  "ig" => "Aha mbụ na aha ezinụlọ",
                  "ha" => "Sunan farko da sunan mahaifi"
              ],
              "Fullname" => [
                "en" => "Fullname",
                "yo" => "Orukọ Kikun",
                "ig" => "Aha zuru ezu",
                "ha" => "Cikakken suna"
              ],
              "Email Address" => [
                "en" => "Email Address",
                "yo" => "Àdírẹ́sì Ímẹ́lì",
                "ig" => "Adreesị Email",
                "ha" => "Adireshin Imel"
              ],
              "Referral phone number (optional)" => [
                "en" => "Referral phone number (optional)",
                "yo" => "Nọ́mbà foonu ìtọ́kasí (aṣayan)",
                "ig" => "Ekwentị ntụaka (nhọrọ)",
                "ha" => "Lambar wayar da ta turo ka (zabi)"
              ],
              "Password" => [
                "en" => "Password",
                "yo" => "Ọrọ aṣínà",
                "ig" => "Okwuntughe",
                "ha" => "Kalmar sirri"
              ],
              "Confirm Password" => [
                "en" => "Confirm Password",
                "yo" => "Jẹ́risi Ọrọ aṣínà",
                "ig" => "Kwenye okwuntughe",
                "ha" => "Tabbatar da kalmar sirri"
              ],
              "Show password" => [
                "en" => "Show password",
                "yo" => "Fi Ọrọ aṣínà hàn",
                "ig" => "Gosi okwuntughe",
                "ha" => "Nuna kalmar sirri"
              ],
              "Welcome back" => [
                "en" => "Welcome back",
                "yo" => "Kaabọ padà",
                "ig" => "Nnọọ ọzọ",
                "ha" => "Barka da dawowa"
              ],
            "Email or Username or Phone" => [
            "en" => "Email or Username or Phone",
            "yo" => "Ímẹ́lì tàbí Orúkọ Olùgbani tàbí Foonu",
            "ig" => "Email ma ọ bụ Aha ojii ma ọ bụ Ekwentị",
            "ha" => "Imel ko Sunan mai amfani ko Lambar waya"
            ],
            "Don't have an account yet" => [
            "en" => "Don't have an account yet",
            "yo" => "Ṣé o kò tíì ní àkọọlẹ̀?",
            "ig" => "Ị nweghị akaụntụ?",
            "ha" => "Bakada asusu tukuna?"
            ],
            "Signup here" => [
            "en" => "Signup here",
            "yo" => "Forukọsilẹ níbí",
            "ig" => "Debanye aha ebe a",
            "ha" => "Yi rijista anan"
            ],
            "Signin here" => [
            "en" => "Signin here",
            "yo" => "Wọlé níbí",
            "ig" => "Banye ebe a",
            "ha" => "Shiga anan"
            ],
            "Forgot password" => [
            "en" => "Forgot password",
            "yo" => "Gbagbe Ọrọ aṣínà",
            "ig" => "Chefuo okwuntughe",
            "ha" => "Manta kalmar sirri"
            ],
            "Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one." => [
            "en" => "Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.",
            "yo" => "Gbagbe ọrọ aṣínà rẹ? Kò sí ìṣòro. Kan sọ àdírẹ́sì imeeli rẹ fún wa, a ó sì rán ọ ní líńkì ìtunṣètò.",
            "ig" => "Chefuru okwuntughe gị? Ọ dịghị nsogbu. Nye anyị adreesị email gị, anyị ga-eziga gị njikọ ịtọgharịa ya.",
            "ha" => "Ka manta kalmar sirrinka? Babu matsala. Kawai ba mu adireshin imel ɗinka, za mu aiko maka da hanyar sake saitawa."
            ],
      
            "Email Password Reset Link" => [
            "en" => "Email Password Reset Link",
            "yo" => "Rán Líńkì Ìtunṣètò Ọrọ aṣínà",
            "ig" => "Zipu njikọ mgbanwe okwuntughe",
            "ha" => "Aika hanyar sake saita kalmar sirri"
            ],
            "Return to login" => [
            "en" => "Return to login",
            "yo" => "Pada sí ìwọlé",
            "ig" => "Laghachi na nbanye",
            "ha" => "Koma zuwa shiga"
            ],
            "Complete Password Reset" => [
            "en" => "Complete Password Reset",
            "yo" => "Pari Ìtunṣètò Ọrọ aṣínà",
            "ig" => "Mechie mgbanwe okwuntughe",
            "ha" => "Kammala sake saita kalmar sirri"
            ],
            "New PIN" => [
            "en" => "New PIN",
            "yo" => "PIN Tuntun",
            "ig" => "PIN ọhụrụ",
            "ha" => "Sabon PIN"
            ],
            "Current PIN" => [
                "en" => "Current PIN",
                "yo" => "PIN Tó Wà Lọ́wọ́",
                "ig" => "PIN dị ugbu a",
                "ha" => "PIN ɗin yanzu"
            ],
            "Confirm New PIN" => [
            "en" => "Confirm New PIN",
            "yo" => "Jẹ́risi PIN Tuntun",
            "ig" => "Kwenye PIN ọhụrụ",
            "ha" => "Tabbatar da sabon PIN"
            ],
            "Confirm Password" => [
            "en" => "Confirm Password",
            "yo" => "Jẹ́risi Ọrọ aṣínà",
            "ig" => "Kwenye okwuntughe",
            "ha" => "Tabbatar da kalmar sirri"
            ],
            "Show Confirm Password" => [
                "en" => "Show Confirm Password",
                "yo" => "Fi Ọrọ aṣínà ìmúlòlùfẹ́ hàn",
                "ig" => "Gosi okwuntughe nkwenye",
                "ha" => "Nuna kalmar sirri ta tabbatarwa"
            ],
            "Update Password" => [
                "en" => "Update Password",
                "yo" => "Ṣàtúnṣe Ọrọ aṣínà",
                "ig" => "Melite okwuntughe",
                "ha" => "Sabunta kalmar sirri"
            ],
            "Update Profile" => [
                "en" => "Update Profile",
                "yo" => "Ṣàtúnṣe Profaili",
                "ig" => "Melite profaịlụ",
                "ha" => "Sabunta bayanin martaba"
            ],

            "Update PIN" => [
                "en" => "Update PIN",
                "yo" => "Ṣàtúnṣe PIN",
                "ig" => "Melite PIN",
                "ha" => "Sabunta PIN"
            ],

            "Note that 2FA feature is currently disabled. It will be enabled as soon as possible." => [
                "en" => "Note that 2FA feature is currently disabled. It will be enabled as soon as possible.",
                "yo" => "Akíyèsí pé ààmú 2FA ti wa nípò àìṣiṣẹ̀ lọ́wọ́lọwọ́. A ó ṣiṣẹ́ rẹ̀ lẹ́sẹ̀kẹsẹ̀ bí ó ti ṣeé ṣe tó.",
                "ig" => "Rịba ama na atụmatụ 2FA adịghị arụ ọrụ ugbu a. A ga-eme ka ọ rụọ ọrụ ozugbo o kwere mee.",
                "ha" => "Lura cewa fasalin 2FA yana a kashe yanzu. Za a kunna shi da wuri da zaran ya yiwu."
            ],

            "Need something else" => [
            "en" => "Need something else",
            "yo" => "Nilo nkankan mìíràn?",
            "ig" => "Chọrọ ihe ọzọ?",
            "ha" => "Kana buƙatar wani abu dabam?"
            ],

            'A new verification link has been sent to the email address you provided during registration.' => [
              'en' => 'A new verification link has been sent to the email address you provided during registration.',
              'yo' => 'A ti fi ọna asopọ ìmúdájú tuntun ranṣẹ si adirẹsi imeeli tí o fi silẹ nígbà ìforúkọsílẹ.',
              'ha' => 'An tura sabon hanyar tabbatarwa zuwa adireshin imel da ka bayar lokacin rijista.',
              'ig' => 'Ejikọ ọhụụ maka nkwenye ezipụtala na email i nyere mgbe i debanyere aha.',
            ],

            "Reset Password" => [
              "en" => "Reset Password",
              "yo" => "Tun Ọrọ aṣínà ṣe",
              "ig" => "Tọọgharị Okwuntughe",
              "ha" => "Sake saita kalmar sirri"
            ],

            "Coupon Codes" => [
              "en" => "Coupon Codes",
              "yo" => "Kóòdù Kópọ́n",
              "ig" => "Koodu Kouponụ",
              "ha" => "Lambobin ragi"
              ],


            "Enjoy data at the best rate" => [
              "en" => "Enjoy data at the best rate",
              "yo" => "Gbadun data ní oṣuwọn tó dára jùlọ",
              "ig" => "Nụrịrị data na ọnụahịa kacha mma",
              "ha" => "Ji daɗin data a farashi mafi kyau"
            ],



            "Category" => [
                "en" => "Category",
                "yo" => "Ẹ̀ka",
                "ig" => "Ngalaba",
                "ha" => "Rukuni"
            ],
            "Incorrect PIN" => [
              "en" => "Incorrect PIN",
              "yo" => "PIN tí kò tọ́",
              "ig" => "PIN ezighi ezi",
              "ha" => "PIN mara kyau"
            ],
           'You need to buy at least N50 worth of airtime.' =>  [
                  'en' => 'You need to buy at least N50 worth of airtime.',
                  'yo' => 'O ní láti rà tẹ́lẹ̀ N50 tó kéré jùlọ ti airtime.',
                  'ig' => 'Ị ga-azụta opekata mpe N50 nke airtime.',
                  'ha' => 'Dole ne ka saya aƙalla N50 na layin waya.'
           ],
           "Show PIN" => [
                "en" => "Show PIN",
                "yo" => "Fi PIN hàn",
                "ig" => "Gosi PIN",
                "ha" => "Nuna Lambar PIN"
            ],

            "Phone Number to recharge" => [
                "en" => "Phone Number to recharge",
                "yo" => "Nọ́mbà fónù láti tun ra ẹ̀rọ",
                "ig" => "Ekwentị a ga-eji akwụ ụgwọ ọzọ",
                "ha" => "Lambar wayar da za a caji"
            ],

            "Product Plan List" => [
                "en" => "Product Plan List",
                "yo" => "Àtòkọ Ètò Ọja",
                "ig" => "Ndepụta atụmatụ ngwaahịa",
                "ha" => "Jerin Tsarin Samfura"
            ],

            "Hot sales" => [
                  "en" => "Hot sales",
                  "yo" => "Tita Lẹsẹkẹsẹ",
                  "ig" => "Ahịa mberede",
                  "ha" => "Tallace-tallacen gaggawa"
              ],

              "Enjoy at discounted prices" => [
                  "en" => "Enjoy at discounted prices",
                  "yo" => "Gbadun rẹ ní owó díẹ̀",
                  "ig" => "Nwee ọṅụ na ọnụ ahịa dị ala",
                  "ha" => "Ji daɗi da rangwamen farashi"
              ],

              "No hot sales at the moment" => [
                  "en" => "No hot sales at the moment",
                  "yo" => "Ko si tita lẹsẹkẹsẹ ní báyìí",
                  "ig" => "Enweghị ahịa mberede ugbu a",
                  "ha" => "Babu tallace-tallacen gaggawa a yanzu"
              ],
              "Buy Now" => [
                  "en" => "Buy Now",
                  "yo" => "Ra Ní Bayìí",
                  "ig" => "Zụta Ugbu a",
                  "ha" => "Sayi Yanzu"
              ],

              "Metre No" => [
                  "en" => "Metre No",
                  "yo" => "Nọ́ńbà Mítà",
                  "ig" => "Nọmba mita",
                  "ha" => "Lambar mita"
              ],
              "Announcement" => [
                "en" => "Announcement",
                "yo" => "Ìkìlò",
                "ig" => "Nkọwa ozi",
                "ha" => "Sanarwa"
              ],
              "Announcements" => [
                "en" => "Announcements",
                "yo" => "Àwọn Ìkéde",
                "ig" => "Mgbasa ozi",
                "ha" => "Sanarwa"
              ],
              "KYC (Know Your Customer) helps us verify your identity so you can unlock all the features available on" => [
                  "en" => "KYC (Know Your Customer) helps us verify your identity so you can unlock all the features available on",
                  "yo" => "KYC (Mọ Onibara Rẹ) n ran wa lọwọ lati jẹrisi idanimọ rẹ ki o le ni iraye si gbogbo awọn ẹya inu pẹpẹ wa",
                  "ig" => "KYC (Mata Onye Ahịa Gị) na-enyere anyị aka ịchọpụta onye ị bụ ka i wee nwee ike iji atụmatụ niile dị na nyiwe a",
                  "ha" => "KYC (Sanin Abokin Ciniki) yana taimaka mana wajen tantance kai don ka sami cikakken amfani da duk fasalulluka"
              ],

              "You can enjoy lower charges on wallet funding — as low as 1% charge." => [
                  "en" => "You can enjoy lower charges on wallet funding — as low as 1% charge.",
                  "yo" => "O le gbadun idiyele kekere lori fifi owo si apamọwọ — bii 1% nikan.",
                  "ig" => "Ị nwere ike inweta ụgwọ dị ala mgbe ị na-etinye ego — dịka 1% naanị.",
                  "ha" => "Za ka iya more cajin ƙasa yayin da kake cike walat — kamar 1% kacal."
              ],

              "Why Do We Need KYC?" => [
                  "en" => "Why Do We Need KYC?",
                  "yo" => "Kí Lọ Dé Tí A Fi Nílò KYC?",
                  "ig" => "Gịnị mere anyị ji achọ KYC?",
                  "ha" => "Me ya sa muke buƙatar KYC?"
              ],

              "Why Do We Request for Your BVN?" => [
                  "en" => "Why Do We Request for Your BVN?",
                  "yo" => "Kí Lọ Dé Tí A Fi N Béèrè BVN Rẹ?",
                  "ig" => "Gịnị mere anyị ji achọ BVN gị?",
                  "ha" => "Me ya sa muke tambayar BVN ɗinka?"
              ],

              "Match your BVN with the details you used during registration" => [
                  "en" => "Match your BVN with the details you used during registration",
                  "yo" => "Ba BVN rẹ mu pẹlu awọn alaye ti o lo nigba iforukọsilẹ",
                  "ig" => "Tụnyere BVN gị na nkọwa ị nyere mgbe ị na-edebanye aha",
                  "ha" => "Daidaita BVN ɗinka da bayanan da ka bayar lokacin rijista"
              ],

              "Confirm that the account number you link truly belongs to you" => [
                  "en" => "Confirm that the account number you link truly belongs to you",
                  "yo" => "Jẹrisi pé nọmba àkọọlẹ tí o sọ pọ̀ mọ́ jẹ́ tiẹ gangan",
                  "ig" => "Gosi na akaụntụ ị jikọtara bụ nke gị n’ezie",
                  "ha" => "Tabbatar da cewa lambar asusun da ka ɗaura da gaske naka ne"
              ],

              "Create a unique and secure identity for each user" => [
                  "en" => "Create a unique and secure identity for each user",
                  "yo" => "Ṣẹda idanimọ alailẹgbẹ ati ailewu fun gbogbo olumulo",
                  "ig" => "Mepụta njirimara pụrụ iche na echekwara maka onye ọrụ ọ bụla",
                  "ha" => "Ƙirƙiri ingantacciyar shaidar musamman ga kowanne mai amfani"
              ],

              "Help protect both you and us from fraud" => [
                  "en" => "Help protect both you and us from fraud",
                  "yo" => "Ràn wá lọ́wọ́ láti dáàbò bò ọ́ ati wa lórí jibiti",
                  "ig" => "Na-enyere aka chebe gị na anyị pụọ na izu ohi",
                  "ha" => "Taimaka wajen kare kai da mu daga damfara"
              ],

              "Is It Safe to Provide My BVN?" => [
                  "en" => "Is It Safe to Provide My BVN?",
                  "yo" => "Ṣé ó dájú pé ó dáa láti fi BVN mi han?",
                  "ig" => "Ọ dị mma inye BVN m?",
                  "ha" => "Shin lafiya ne in bayar da BVN ɗina?"
              ],

              "Yes, your BVN is safe with us. We use it strictly to confirm your identity. Once your BVN is linked, no other user can use it on this platform." => [
                  "en" => "Yes, your BVN is safe with us. We use it strictly to confirm your identity. Once your BVN is linked, no other user can use it on this platform.",
                  "yo" => "Bẹẹni, BVN rẹ wa ní ààbò pẹ̀lú wa. A máa lò ó fún ìmúdájú idanimọ rẹ nìkan. Lẹ́yìn tí a bá dá a pọ̀ mọ́ akọọlẹ rẹ, ẹlòmíràn kò le lò ó lórí pẹpẹ yìí.",
                  "ig" => "Ee, BVN gị dị nchebe n’aka anyị. Anyị na-eji ya kpọrọ ihe iji chọpụta onye ị bụ. Ozugbo ejikọtala ya, onye ọzọ agaghị eji ya na nyiwe a.",
                  "ha" => "Eh, BVN ɗinka yana cikin aminci tare da mu. Muna amfani da shi ne kawai don tantance kai. Da zarar an ɗaura shi da asusunka, babu wani mai amfani da zai iya amfani da shi."
              ],

              "Note: Please use your own personal BVN for this verification. A fee of ₦50 applies per attempt." => [
                  "en" => "Note: Please use your own personal BVN for this verification. A fee of ₦50 applies per attempt.",
                  "yo" => "Akíyèsí: Jọ̀wọ́ lo BVN tirẹ fún ìmúdájú yìí. Ìsanwó ₦50 ni kọọkan ìgbìyànjú.",
                  "ig" => "Rịba ama: Biko jiri BVN gị onwe gị maka nkwenye a. Ego ₦50 na-akwụ ụgwọ kwa mgbalị.",
                  "ha" => "Lura: Da fatan za a yi amfani da BVN ɗinka na kanka don wannan tantancewa. Ana cajin ₦50 a kowane ƙoƙari."
              ],
              "Verify your Account for better opportunities" => [
                  "en" => "Verify your Account for better opportunities",
                  "yo" => "Jẹ́risi Àkọọlẹ Rẹ fún Àǹfààní Tó Dáa Jùlọ",
                  "ig" => "Chọpụta Akaụntụ Gị Maka Oge Ka Mma",
                  "ha" => "Tabbatar da Asusunka don Dama Mafi Alheri"
              ],

              "Return to Dashboard" => [
                  "en" => "Return to Dashboard",
                  "yo" => "Pada sí Dasibọdu",
                  "ig" => "Laghachi na Dashboard",
                  "ha" => "Koma zuwa Dashboard"
              ],

              "Please use your own personal BVN for this verification." => [
                  "en" => "Please use your own personal BVN for this verification.",
                  "yo" => "Jọ̀wọ́ lo BVN tirẹ fún ìmúdájú yìí.",
                  "ig" => "Biko jiri BVN gị onwe gị maka nkwenye a.",
                  "ha" => "Da fatan za a yi amfani da BVN ɗinka na kanka don wannan tantancewa."
              ],

              "Cost is" => [
                  "en" => "Cost is",
                  "yo" => "Ìye owó náà jẹ́",
                  "ig" => "Ego ya bụ",
                  "ha" => "Farashin shine"
              ],

              "IF YOU ENCOUNTER ANY ISSUES, REACH OUT TO SUPPORT ON WHATSAPP BY USING THE WHATSAPP ICON BELOW" => [
                  "en" => "IF YOU ENCOUNTER ANY ISSUES, REACH OUT TO SUPPORT ON WHATSAPP BY USING THE WHATSAPP ICON BELOW",
                  "yo" => "TÍ O BÁ NÍ ÌṢÒRÒ KÁNKAN, BÁ Ẹ̀YÀN ÌTÓJU SỌ̀RÒ LÓRÍ WHATSAPP PẸ̀LÚ ÀMÌ WHATSAPP TÓ WÀ NÍSÀLẸ̀",
                  "ig" => "Ọ BỤRỤ NA Ị NWEGHI NSỌPỤ, KỌRỌTA NA SUPPORT SITE NA WHATSAPP SITE KWỤỌ N’AKA ICON WHATSAPP DI N’ALA",
                  "ha" => "IDAN KA GAMU DA WANI MATSALA, TUNTUBI GOYON BAYANMU TA WHATSAPP TA HANNUN GURBIN WHATSAPP A KASA"
              ],
              "Verify your Account with better opportunities" => [
                  "en" => "Verify your Account with better opportunities",
                  "yo" => "Jẹ́risi Àkọọlẹ Rẹ Pẹ̀lú Àǹfààní Tó Dáa Jùlọ",
                  "ig" => "Chọpụta Akaụntụ Gị Maka Oge Ka Mma",
                  "ha" => "Tabbatar da Asusunka tare da Dama Mafi Alheri"
              ],

              "Generate Virtual Accounts" => [
                  "en" => "Generate Virtual Accounts",
                  "yo" => "Ṣẹda Àkọọlẹ Foju",
                  "ig" => "Mepụta Akaụntụ Ntan",
                  "ha" => "Ƙirƙiri Asusun Na Ƙarya"
              ],

              "Account Verification" => [
                  "en" => "Account Verification",
                  "yo" => "Ìmúdájú Àkọọlẹ",
                  "ig" => "Nkwenye Akaụntụ",
                  "ha" => "Tantance Asusun"
              ],

              "Congratulations! Your account has been verified." => [
                "en" => "Congratulations! Your account has been verified.",
                "yo" => "Ẹ ku orire! Àkọọlẹ rẹ ti jẹ́risi.",
                "ig" => "Ekele diri gị! A kwadoro akaụntụ gị.",
                "ha" => "Murna! An tabbatar da asusunka."
              ],

              "Generate More Virtual Accounts" => [
                  "en" => "Generate More Virtual Accounts",
                  "yo" => "Ṣẹda Àwọn Àkọọlẹ Foju Síi",
                  "ig" => "Mepụta Akaụntụ Ntan Ọzọ",
                  "ha" => "Ƙirƙiri Ƙarin Asusun Na Ƙarya"
              ]






          ];

          $merged = array_merge($arr1, $arr2);

          foreach($merged as $key=>$each){
            $text_to_transform =  json_encode($each, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            LanguageLine::updateOrCreate([
                'key' => $key,
                'group' => 'messages'
            ],[
                'text' => $each
            ]);
          }

        
          $validations = [

            [
              'group' => 'validation',
              'key' => 'min.string',
              'text' => [
                  'en' => 'The :attribute must be at least :min characters.',
                  'yo' => ':attribute gbọ́dọ̀ jẹ́ o kere tán :min lẹ́tà.',
                  'ig' => ':attribute ga-enwerịrị opekata mpe mkpụrụedemede :min.',
                  'ha' => ':attribute dole ne ya kasance aƙalla haruffa :min.'
              ]
            ],          

            // Required
            [
                'group' => 'validation',
                'key' => 'required',
                'text' => [
                    'en' => 'The :attribute field is required.',
                    'yo' => 'Pápá :attribute jẹ́ dandan.',
                    'ig' => 'Ụbọchị :attribute dị mkpa.',
                    'ha' => 'Filin :attribute yana da matuƙar muhimmanci.'
                ]
            ],
        
            // Regex
            [
                'group' => 'validation',
                'key' => 'regex',
                'text' => [
                    'en' => 'The :attribute format is invalid.',
                    'yo' => 'Àkóónú :attribute kò bójú mu.',
                    'ig' => 'Ụdị :attribute ezughị ezu.',
                    'ha' => 'Tsarin :attribute ba daidai ba ne.'
                ]
            ],
        
            // Unique
            [
                'group' => 'validation',
                'key' => 'unique',
                'text' => [
                    'en' => 'The :attribute has already been taken.',
                    'yo' => ':attribute ti gba tẹlẹ.',
                    'ig' => ':attribute ewereela ya.',
                    'ha' => ':attribute an riga an ɗauka.'
                ]
            ],
        
            // Exists
            [
                'group' => 'validation',
                'key' => 'exists',
                'text' => [
                    'en' => 'The selected :attribute is invalid.',
                    'yo' => ':attribute tí o yàn kò bójú mu.',
                    'ig' => ':attribute ahọpụtara ezughị ezu.',
                    'ha' => ':attribute da ka zaɓa ba daidai ba ne.'
                ]
            ],
        
            // Numeric
            [
                'group' => 'validation',
                'key' => 'numeric',
                'text' => [
                    'en' => 'The :attribute must be a number.',
                    'yo' => ':attribute gbọ́dọ̀ jẹ́ nọ́mbà.',
                    'ig' => ':attribute ga-abụ ọnụọgụgụ.',
                    'ha' => ':attribute dole ne ya zama lamba.'
                ]
            ],
        
            // Email
            [
                'group' => 'validation',
                'key' => 'email',
                'text' => [
                    'en' => 'The :attribute must be a valid email address.',
                    'yo' => ':attribute gbọ́dọ̀ jẹ́ àdírẹ́sì e-mail to peye.',
                    'ig' => ':attribute ga-abụ adreesị ozi-e dị irè.',
                    'ha' => ':attribute dole ne ya zama sahihin adireshin imel.'
                ]
            ],
        
            // Confirmed
            [
                'group' => 'validation',
                'key' => 'confirmed',
                'text' => [
                    'en' => 'The :attribute confirmation does not match.',
                    'yo' => 'Ìmúdájú :attribute kò bá mu.',
                    'ig' => 'Nkwenye :attribute ezughi oke.',
                    'ha' => 'Tabbatar da :attribute bai dace ba.'
                ]
            ],
        
            // Digits
            [
                'group' => 'validation',
                'key' => 'digits',
                'text' => [
                    'en' => 'The :attribute must be :digits digits.',
                    'yo' => ':attribute gbọ́dọ̀ ní díjítì :digits.',
                    'ig' => ':attribute ga-abụ ọnụọgụgụ :digits.',
                    'ha' => ':attribute dole ne ya kasance da lambobi :digits.'
                ]
            ],
        
            // In
            [
                'group' => 'validation',
                'key' => 'in',
                'text' => [
                    'en' => 'The selected :attribute is invalid.',
                    'yo' => ':attribute tí o yàn kò wúlò.',
                    'ig' => ':attribute ahọpụtara ezughị ezu.',
                    'ha' => ':attribute da aka zaɓa ba daidai ba ne.'
                ]
            ],
        
            // Min (numeric)
            [
                'group' => 'validation',
                'key' => 'min.numeric',
                'text' => [
                    'en' => 'The :attribute must be at least :min.',
                    'yo' => ':attribute gbọ́dọ̀ jẹ́ o kere ju :min.',
                    'ig' => ':attribute ga-abụ opekata mpe :min.',
                    'ha' => ':attribute dole ne ya kasance aƙalla :min.'
                ]
            ],
        
            // Max (numeric)
            [
                'group' => 'validation',
                'key' => 'max.numeric',
                'text' => [
                    'en' => 'The :attribute may not be greater than :max.',
                    'yo' => ':attribute kò gbọdọ̀ ju :max lọ.',
                    'ig' => ':attribute ekwesịghị ịdị elu karịa :max.',
                    'ha' => ':attribute ba zai fi :max ba.'
                ]
            ],
        
            // Boolean
            [
                'group' => 'validation',
                'key' => 'boolean',
                'text' => [
                    'en' => 'The :attribute field must be true or false.',
                    'yo' => ':attribute gbọ́dọ̀ jẹ́ òótọ́ tàbí irọ.',
                    'ig' => ':attribute ga-abụ eziokwu ma ọ bụ ụgha.',
                    'ha' => ':attribute dole ne ya kasance gaskiya ko ƙarya.'
                ]
            ],
        
            // Date
            [
                'group' => 'validation',
                'key' => 'date',
                'text' => [
                    'en' => 'The :attribute is not a valid date.',
                    'yo' => ':attribute kì í ṣe ọjọ́ tó wúlò.',
                    'ig' => ':attribute abụghị ụbọchị ziri ezi.',
                    'ha' => ':attribute ba shine kwanan wata mai inganci ba.'
                ]
            ],

            [
              'group' => 'passwords',
              'key' => 'reset',
              'text' => [
                  'en' => 'Your password has been reset!',
                  'yo' => 'A ti tun ọrọ aṣínà rẹ ṣe!',
                  'ig' => 'Emechago mgbake okwuntughe gị!',
                  'ha' => 'An sake saita kalmar sirrinka!',
              ]
          ],
          [
              'group' => 'passwords',
              'key' => 'sent',
              'text' => [
                  'en' => 'We have emailed your password reset link!',
                  'yo' => 'A ti fi ọna asopọ atunṣe ọrọ aṣínà ranṣẹ si imeeli rẹ!',
                  'ig' => 'Anyị ezipụla njikọ mgbake okwuntughe na email gị!',
                  'ha' => 'Mun tura hanyar canza kalmar sirri zuwa imel ɗinka!',
              ]
          ],
          [
              'group' => 'passwords',
              'key' => 'throttled',
              'text' => [
                  'en' => 'Please wait before retrying.',
                  'yo' => 'Jọwọ dúró díẹ̀ kí o tó tún gbìyànjú lẹ́ẹ̀kàn síi.',
                  'ig' => 'Biko chere ntakịrị tupu i nwalee ọzọ.',
                  'ha' => 'Da fatan za ka jira kafin sake gwadawa.',
              ]
          ],
          [
              'group' => 'passwords',
              'key' => 'token',
              'text' => [
                  'en' => 'This password reset token is invalid.',
                  'yo' => 'Ami atunṣe ọrọ aṣínà yii kò bófin mu.',
                  'ig' => 'Token mgbake okwuntughe a adịghị irè.',
                  'ha' => 'Wannan alamar sake saita kalmar sirri ba daidai ba ce.',
              ]
          ],
          [
              'group' => 'passwords',
              'key' => 'user',
              'text' => [
                  'en' => 'We can\'t find a user with that email address.',
                  'yo' => 'A kò rí olumulo kankan pẹ̀lú adirẹsi imeeli yìí.',
                  'ig' => 'Anyị enweghị ike ịchọta onye ọrụ nwere email a.',
                  'ha' => 'Ba za mu iya samun mai amfani da wannan imel ba.',
              ]
          ],
        ];

        foreach($validations as $key=>$each){
          $text_to_transform =  json_encode($each, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
          LanguageLine::updateOrCreate([
              'key' => $each['key'],
              'group' => $each['group']
          ],[
              'text' => $each['text']
          ]);
        }

       
    
    }  

}