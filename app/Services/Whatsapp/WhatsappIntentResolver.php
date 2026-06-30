<?php
namespace App\Services\Whatsapp;

use App\Http\Services\DataPlansService;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\UserContact;
use App\Models\UserPlan;
use App\Models\UserVirtualAccount;

class WhatsappIntentResolver
{
    public function resolve(array $intent, $user, string $phone): array
    {
        return match ($intent['type']) {

            'data' => $this->resolveData($intent, $user, $phone),

            'airtime' => $this->resolveAirtime($intent,$user,$phone),

            'favorites' => $this->resolveFavorites($user, $phone),

            'account'
            => $this->resolveAccount($user, $phone),

            'help' => $this->resolveHelp(),


            'navigation_app',
            'navigation_telegram',
            'navigation_support'
                => $this->resolveNavigation($intent['type']),
        
                default => [

                    'status' => 'unsupported',
                
                    'message' =>
                        "🤔 I couldn't quite understand that request.\n\n"
                
                        . "I can help you with the following (it's not case-sensitive):\n\n"
                
                        . "📶 Buy Data\n"
                        . "• MTN 1GB Weekly\n"
                        . "• Airtel 2GB Monthly\n"
                        . "• Glo 500MB\n"
                        . "• MTN 1GB Weekly 09034556677\n"
                        . "• Airtel 2GB Monthly 08123456789\n\n"
                
                        . "📞 Buy Airtime\n"
                        . "• Airtime 1000 MTN\n"
                        . "• Airtel Airtime 500\n"
                        . "• MTN Airtime 300 09011223344\n"
                        . "• Glo Airtime 1000 08123456789\n\n"
                
                        . "📋 Repeat Purchases\n"
                        . "• Recent\n"
                        . "• Buy Again\n"
                        . "• Favourites\n\n"
                
                        . "💰 Account Information\n"
                        . "• Balance\n"
                        . "• Wallet\n\n"
                
                        . "🆘 Support\n"
                        . "• Support\n\n"
                
                        . "Examples can be typed in any format:\n"
                        . "• mtn 1gb weekly\n"
                        . "• MTN 1GB WEEKLY\n"
                        . "• Mtn Airtime 500\n\n"
                
                        . "If you're not sure where to start, simply type:\n"
                        . "👉 START"
                ]

        
        };
    }


    public function resolveHelp(): array
    {
        return [

            'status' => 'help',

            'message' =>
                "🆘 OresamSub Help Center\n\n"

                . "📱 Web Version\n"
                . "Login:"
                . "https://oresamsub.com/login\n\n"

                . "Forgot Password?\n"
                . "https://oresamsub.com/forgot-password\n\n"

                . "Forgot PIN?\n"
                . "Please contact support.\n\n"

                . "💬 WhatsApp Support\n"
                . "• 09011988807\n"
                . "• 08168509044\n\n"

                . "📞 Call Support\n"
                . "• 09011988807\n"
                . "• 08168509044\n\n"

                . "📶 Data Examples\n"
                . "• MTN 1GB Weekly\n"
                . "• Airtel 2GB Monthly\n"
                . "• MTN 1GB Weekly to Mom\n\n"

                . "📞 Airtime Examples\n"
                . "• MTN Airtime 500\n"
                . "• Airtime 1000 MTN\n"
                . "• Airtime 1000 to 09011223344\n\n"

                . "💰 Account Commands\n"
                . "• Account\n"
                . "• Balance\n"
                . "• Wallet\n\n"

                . "🔄 To start a new transaction anytime, type:\n"
                . "👉 START"
        ];
    }


    public function resolveAccount(
        $user,
        string $phone
    ): array
    {
        if (! $user) {
    
            return [
                'status' => 'unlinked_user',
                'message' =>
                    "⚠️ Your WhatsApp number is not linked to an OresamSub account."
            ];
        }
    
        $virtualAccounts = UserVirtualAccount::query()
            ->where('funding_slug','!=','crystal_pay')
            ->where('user_id', $user->id)
            ->get();
    
        $message =
            "💰 ACCOUNT INFORMATION\n"
    
            . "Wallet Balance: ₦"
            . number_format($user->main_wallet, 2)
            . "\n\n";
    
        if ($virtualAccounts->count()) {
    
            $message .= "🏦 FUND YOUR WALLET\n";
    
            foreach ($virtualAccounts as $account) {
    
                $message .=
                    "Bank: {$account->bank_name}\n"
                    . "Account No: {$account->account_number}\n"
                    . "Account Name: {$account->account_name}\n\n";
            }
        }
    
        $message .=
            "After making a transfer, wait about 10 seconds and tap *Refresh Balance* below to check if your funds have reflected.\n";
        $message .= "🔄 To return to the main menu and start fresh, type *START* anytime.\n\n";
    
        return [
            'status' => 'account_view',
            'whatsapp_phone' => $phone,
            'message' => $message,
        ];
    }

    public function resolveFavorites($user, string $phone): array
    {
        if (!$user) {
    
            return [
                'status' => 'unlinked_user',
                'message' =>
                    "Your WhatsApp number is not linked to an account.\n\nPlease contact support."
            ];
        }
    
        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->whereNotNull('product_plan_id')
            ->whereHas('product_plan', function ($query) {
                $query->where('visibility', 1);
            })
            ->with([
                'product_plan.product_plan_category.product',
                'product_plan.product_plan_category.network'
            ])
            ->where('transaction_category','data')
            ->latest()
            ->take(20)
            ->get();
    
        if ($transactions->isEmpty()) {
    
            return [
                'status' => 'favorites_empty',
                'message' =>
                    "No recent purchases found.\n\nTry something like:\nMTN 1GB Weekly"
            ];
        }
    
        $transactions = $transactions
            ->unique('product_plan_id')
            ->take(5)
            ->values();
    
        return $this->buildFavoriteOptionsResponse(
            $transactions,
            $user
        );
    }

    protected function buildFavoriteOptionsResponse(
        $transactions,
        $user
    ): array {
    
        $message = "📌 Recent / Favourite Plans\n\n";
    
        $options = [];
    
        foreach ($transactions as $index => $txn) {
    
            if (!$txn->product_plan) {
                continue;
            }
    
            $number = $index + 1;
    
            $price = $this->getCustomerPlanPrice(
                $txn->product_plan,
                $user
            );
    
            $options[$number] = [
                'product_plan_id' => $txn->product_plan_id,
                // 'phone' => $txn->phone_number,
            ];
    
            $message .=
                "{$number}. {$txn->product_plan->product_plan_name}\n"
                . "💰 ₦" . number_format($price)
                . "\n\n";
        }
    
        $message .= "Reply with a number to continue.";
    
        return [
            'status' => 'favorites_selection',
            'message' => $message,
            'options' => $options,
        ];
    }



    private function resolveNavigation(string $type): array
    {
        return match ($type) {

            'navigation_app' => [
                'status' => 'navigation',
                'message' => "Download our app here:\nhttps://yourapp.link/android"
            ],

            'navigation_telegram' => [
                'status' => 'navigation',
                'message' => "Join our Telegram channel:\nhttps://t.me/oresamsub"
            ],

            // 'navigation_support' => [
            //     'status' => 'navigation',
            //     'message' => "Chat support here:\nhttps://wa.me/234xxxxxxxxx"
            // ],

            default => [
                'status' => 'unknown',
                'message' => "I didn't understand that...."
            ]
        };
    }


  

    public function resolveData($intent, $user, $phone): array
    {

        /*
        |--------------------------------------------------------------------------
        | Resolve saved contact names
        |--------------------------------------------------------------------------
        */
         /*
    |--------------------------------------------------------------------------
    | Resolve saved contact names
    |--------------------------------------------------------------------------
    */
    if (
        empty($intent['phone'])
        && !empty($intent['raw_message'])
    ) {

        $contactPhone = $this->resolvePhoneFromSavedContacts(
            $intent['raw_message'],
            $user
        );

        if ($contactPhone) {
            $intent['phone'] = $contactPhone;
        }
    }

        if (empty($intent['network'])) {

            return [
                'status' => 'data_network_required',
                'field' => 'network',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "📶 Which network would you like?\n\n"
                    . "• MTN\n"
                    . "• Airtel\n"
                    . "• Glo\n"
                    . "• 9mobile"
            ];
        }

        if (empty($intent['data_size_in_mb'])) {

            return [
                'status' => 'data_size_required',
                'field' => 'data_size',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "📦 What data size are you looking for?\n\n"
                    . "Examples:\n"
                    . "• 1GB\n"
                    . "• 2GB\n"
                    . "• 5GB"
            ];
        }


        $plans = $this->findMatchingPlans($intent);
        logger('is it here: '.$plans);

        if ($plans->isEmpty()) {

            $alternatives = ProductPlan::query()
                ->where('visibility', 1)
                ->where('network', strtolower($intent['network']))
                ->where('data_size_in_mb', $intent['data_size_in_mb'])
                ->with([
                    'product_plan_category.product',
                    'product_plan_category.network'
                ])
                ->get();

            if ($alternatives->isNotEmpty()) {

                return $this->buildPlanOptionsResponse(
                    $alternatives,
                    $user,
                    $phone,
                    $intent,
                    "🤔 I couldn't find that exact validity.\n\nHere are the available options:\n\n"
                );
            }

            return [
                'status' => 'data_plan_not_found',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "😔 I couldn't find a matching "
                    . strtoupper($intent['network'])
                    . " "
                    . ($intent['data_size_in_mb'] / 1000)
                    . "GB plan.\n\n"
                    . "Try another variation or reply START."
            ];
        }

        if ($plans->count() > 1) {

            return $this->buildPlanOptionsResponse(
                $plans,
                $user,
                $phone,
                $intent,
                "📦 Multiple plans found:\n\n"
            );
        }

        $plan = $plans->first();

        if ( empty($intent['phone']) ) {

            // $intent['product_plan_id'] = $plan->id;

            return [
                'status' => 'data_phone_required',
                'field' => 'phone',
                'product_plan_id' => $plan->id,
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "📱 Which number should receive this data?\n\n"
                    . "You can:\n"
                    . "• Type the phone number\n"
                    . "• Share a contact"
            ];
        }

        $beneficiaryUser = app(WhatsappUserResolver::class)
            ->resolve($intent['phone']);

        if (!$beneficiaryUser) {

            return [
                'status' => 'unlinked_user',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "⚠️ This phone number is not linked to an account yet."
            ];
        }

        $price = $this->getCustomerPlanPrice(
            $plan,
            $beneficiaryUser
        );

        return [
            'status' => 'data_awaiting_confirmation',
            'network_id' => $plan->product_plan_category->network->id,
            'user_id' => $beneficiaryUser->id,
            'product_plan_id' => $plan->id,
            'phone' => $intent['phone'],
            'price' => $price,
            'whatsapp_phone' => $phone,
            'intent' => $intent,

            'message' =>
                "🛒 Almost done!\n\n"
                . "📦 Plan: {$plan->product_plan_name}\n"
                . "📱 Number: {$intent['phone']}\n"
                . "💰 Amount: ₦" . number_format($price) . "\n\n"
                . "Please confirm to continue."
        ];
    }


    private function resolvePhoneFromSavedContacts(
        string $message,
        $user
    ): ?string
    {
        if (!$user) {
            return null;
        }
    
        $message = strtolower(trim($message));
    
        $contacts = UserContact::where(
            'user_id',
            $user->id
        )->get();
    
        foreach ($contacts as $contact) {
    
            $name = strtolower(trim($contact->name));
    
            if (empty($name)) {
                continue;
            }
    
            if (
                preg_match(
                    '/\b' . preg_quote($name, '/') . '\b/i',
                    $message
                )
            ) {
                logger('loads:   '.$contact->phone_number);
                return $contact->phone_number;
            }
        }
    
        return null;
    }

    public function findMatchingPlans(array $intent)
    {
        logger('find matching plans: '.json_encode($intent));
        $query = ProductPlan::query()
            ->where('visibility', 1)
            ->where('network', strtolower($intent['network']))
            ->where('data_size_in_mb', $intent['data_size_in_mb']);

        if (!empty($intent['validity_in_days'])) {
            $query->where(
                'validity_in_days',
                $intent['validity_in_days']
            );
        }

        return $query
            ->with([
                'product_plan_category.product',
                'product_plan_category.network'
            ])
            ->get();
    }

    protected function buildPlanOptionsResponse(
        $plans,
        $user,
        string $phone,
        array $intent,
        string $header
    ): array {
    
        $message = $header;
    
        $options = [];
    
        foreach ($plans as $index => $plan) {
    
            $number = $index + 1;
    
            $price = $this->getCustomerPlanPrice(
                $plan,
                $user
            );
    
            $options[$number] = $plan->id;
    
            $message .=
                "{$number}. {$plan->product_plan_name}\n"
                . "💰 ₦" . number_format($price)
                . "\n\n";
        }
    
        $message .= "Reply with a number to continue.";
    
        return [
            'status' => 'data_multiple_options',
            'whatsapp_phone' => $phone,
            'intent' => $intent,
            'options' => $options,
            'plans' => $plans,
            'message' => $message,
        ];
    }

    public function getCustomerPlanPrice(
        ProductPlan $plan,
        $user
    ): float {
    
        $response = app(DataPlansService::class)
            ->get_customer_price_per_plan([
                'product_id' =>
                    $plan->product_plan_category->product->id,
    
                'network_id' =>
                    $plan->product_plan_category->network->id,
    
                'user' => $user,
    
                'plan_details' => $plan,
            ]);
    
        return (float) ($response['message'] ?? 0);
    }

    private function resolveAirtimeooo(array $intent, $user, $phone): array
    {
        if (empty($intent['amount'])) {

            return [
                'status' => 'airtime_amount_required',
                'field' => 'amount',
                'message' =>
                    "How much airtime do you want?"
            ];
        }

        if (empty($intent['phone'])) {

            return [
                'status' => 'airtime_phone_required',
                'field' => 'phone',
                'message' =>
                    "Which phone number should receive the airtime?"
            ];
        }

        return [
            'status' => 'airtime_awaiting_confirmation',

            'amount' => $intent['amount'],

            'network' => $intent['network'],

            'phone' => $intent['phone'],

            'message' =>
                "Confirm Airtime Purchase\n\n"
                . "Amount: ₦" . number_format($intent['amount']) . "\n"
                . "Phone: {$intent['phone']}\n\n"
                . "Reply YES to continue."
        ];
    }

    public function resolveAirtime(
        array $intent,
        $user,
        string $phone
    ): array
    {
        /*
        |--------------------------------------------------------------------------
        | Network
        |--------------------------------------------------------------------------
        */
        if (empty($intent['network'])) {
    
            return [
                'status' => 'airtime_network_required',
                'field' => 'network',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "📞 Which network would you like to recharge?\n\n"
                    . "1. MTN\n"
                    . "2. Airtel\n"
                    . "3. Glo\n"
                    . "4. 9mobile"
            ];
        }
    
        /*
        |--------------------------------------------------------------------------
        | Amount
        |--------------------------------------------------------------------------
        */
        if (empty($intent['amount'])) {
    
            return [
                'status' => 'airtime_amount_required',
                'field' => 'amount',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "💰 How much airtime would you like to buy?\n\n"
                    . "Examples:\n"
                    . "• 500\n"
                    . "• 1000\n"
                    . "• 2000"
            ];
        }
    
        /*
        |--------------------------------------------------------------------------
        | Phone
        |--------------------------------------------------------------------------
        */
        if (empty($intent['phone'])) {
    
            return [
                'status' => 'airtime_phone_required',
                'field' => 'phone',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "📱 Which number should receive the airtime?\n\n"
                    . "You can:\n"
                    . "• Type a phone number\n"
                    . "• Use a saved contact name\n"
                    . "• Share a WhatsApp contact"
            ];
        }

        if (empty($intent['product_plan_id'])) {

            $product = Product::where('slug','airtime')->first();
            $product_plan_categoriesarr = ProductPlanCategory::where('product_id',$product->id)
            ->pluck('id')
            ->toArray();
            $plan = ProductPlan::query()
                ->where('visibility', 1)
                ->whereIn('product_plan_category_id',$product_plan_categoriesarr)
                ->where('network',$intent['network'])
                // ->where('product_plan_name','like','%virtual topup%')
                // ->whereHas(
                //     'product_plan_category.network',
                //     fn ($q) => $q->where(
                //         'network_name',
                //         strtoupper($intent['network'])
                //     )
                // )
                ->first();
        
            if (!$plan) {
        
                return [
                    'status' => 'airtime_plan_not_found',
                    'message' =>
                        "⚠️ Airtime is currently unavailable for "
                        . strtoupper($intent['network'])
                ];
            }
        
            $intent['product_plan_id'] = $plan->id;
            $intent['network_id'] = Network::where('network_name',strtoupper($intent['network']))->first()->id;
        }
    
            /*
        |--------------------------------------------------------------------------
        | Airtime pricing (apply user discount)
        |--------------------------------------------------------------------------
        */
        $userPlan = UserPlan::find($user->user_plan_id);

        $planLevel = $userPlan?->plan_level ?? 1;

        $userLevelSelling =
            "user_level_{$planLevel}_selling_price";

        $purchaseDiscount =
            (float) ($plan->$userLevelSelling ?? 0);

        $actualAmount = abs($intent['amount']);

        $discountValue = ceil(
            ($purchaseDiscount / 100) * $actualAmount
        );

        $finalAmount =
            $discountValue < 0 ||
            $discountValue > $actualAmount
                ? $actualAmount
                : ($actualAmount - $discountValue);

        /*
        |--------------------------------------------------------------------------
        | Confirmation
        |--------------------------------------------------------------------------
        */
        return [
            'status' => 'airtime_awaiting_confirmation',

            'amount' => $actualAmount,

            'payable_amount' => $finalAmount,

            'network' => strtoupper($intent['network']),

            'product_plan_id' => $plan->id,

            'network_id' => $intent['network_id'],

            'phone' => $intent['phone'],

            'whatsapp_phone' => $phone,

            'intent' => $intent,

            'message' =>
                "🛒 Almost done!\n\n"
                . "📞 Network: " . strtoupper($intent['network']) . "\n"
                . "💰 Airtime Value: ₦" . number_format($actualAmount) . "\n"
                . "💳 Amount Charged: ₦" . number_format($finalAmount) . "\n"
                . "📱 Number: {$intent['phone']}\n\n"
                . "Please review the details above.\n\n"
                . "Tap a button below to continue."
        ];
    }
}