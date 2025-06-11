<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\TranslationLoader\LanguageLine;

class MultilanguageController extends Controller
{
    public function translation(){
        // dd('sssdfawerw');
        $arr1 = [
            'Dashboard' => [
              'en' => 'Dashboard',
              'yo' => 'Dasibodu',
              'ig' => 'Ntanụgharị',
              'ha' => 'Allon Kulawa',
            ],
            'Welcome' => [
              'en' => 'Welcome',
              'yo' => 'Kaabọ',
              'ig' => 'Nnọọ',
              'ha' => 'Barka da zuwa',
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
            'Account Number' => [
              'en' => 'Account Number',
              'yo' => 'Nọmba Iroyin',
              'ig' => 'Nọmba Akaụntụ',
              'ha' => 'Lambar Asusun',
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
              'yo' => 'Foonu ti gba agbara',
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

        

          
          


    }
}
