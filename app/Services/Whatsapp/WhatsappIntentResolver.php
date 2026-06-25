<?php
namespace App\Services\Whatsapp;

use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;
use App\Models\Transaction;

class WhatsappIntentResolver
{
    public function resolve(array $intent, $user, string $phone): array
    {
        return match ($intent['type']) {

            'data' => $this->resolveData($intent, $user, $phone),

            'airtime' => $this->resolveAirtime($intent,$user,$phone),

            'favorites' => $this->resolveFavorites($user, $phone),

            'navigation_app',
            'navigation_telegram',
            'navigation_support'
                => $this->resolveNavigation($intent['type']),
        
            default => [
                'status' => 'unsupported',
                'message' => "I didn't understand that2..."
            ]

        
        };
    }

    protected function resolveFavorites($user, string $phone): array
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
        ->with('product_plan')
        ->latest()
        ->take(20)
        ->get();

        if ($transactions->isEmpty()) {

            return [
                'status' => 'favorites_empty',
                'message' =>
                    "No recent purchases found.\n\nTry:\nMTN 1GB Weekly"
            ];
        }

        /*
        Unique plans only
        */
        $transactions = $transactions
            ->unique('product_plan_id')
            ->take(5)
            ->values();

        $message = "📌 Recent / Favourite Plans\n\n";

        $options = [];

        foreach ($transactions as $index => $txn) {

            if (!$txn->product_plan) {
                continue;
            }

            $number = $index + 1;

            $message .=
                "{$number}. {$txn->product_plan->product_plan_name}\n";

            $options[$number] = [
                'product_plan_id' => $txn->product_plan_id,
                'phone' => $txn->phone_number,
            ];
        }

        $message .= "\nReply with a number.";

        return [
            'status' => 'favorites_selection',
            'message' => $message,
            'options' => $options,
        ];
    }

    protected function resolveFavoritesnew($user, string $phone): array
{
    if (!$user) {

        return [
            'status' => 'unlinked_user',
            'message' =>
                "🔒 Your WhatsApp number is not linked to an account yet.\n\nPlease contact support for assistance."
        ];
    }

    $transactions = Transaction::query()
        ->where('user_id', $user->id)
        ->where('status', 1)
        ->whereNotNull('product_plan_id')
        ->whereHas('product_plan', function ($query) {
            $query->where('visibility', 1);
        })
        ->with('product_plan')
        ->latest()
        ->take(20)
        ->get();

    if ($transactions->isEmpty()) {

        return [
            'status' => 'favorites_empty',
            'message' =>
                "📭 No recent purchases found.\n\n"
                . "Try something like:\n"
                . "• MTN 1GB Weekly\n"
                . "• Airtel 2GB Monthly"
        ];
    }

    /*
    Keep only unique plans
    */
    $transactions = $transactions
        ->unique('product_plan_id')
        ->take(5)
        ->values();

    $message =
        "📌 Your Recent / Favourite Plans\n\n"
        . "Choose a plan you'd like to buy again:\n\n";

    $options = [];

    foreach ($transactions as $index => $txn) {

        if (!$txn->product_plan) {
            continue;
        }

        $number = $index + 1;

        $message .=
            "{$number}. {$txn->product_plan->product_plan_name}\n"
            . "   📱 Last used: {$txn->phone_number}\n\n";

        $options[$number] = [
            'product_plan_id' => $txn->product_plan_id,
            'phone' => $txn->phone_number,
            'plan_name' => $txn->product_plan->product_plan_name,
        ];
    }

    $message .= "Reply with the number of your preferred plan.";

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

            'navigation_support' => [
                'status' => 'navigation',
                'message' => "Chat support here:\nhttps://wa.me/234xxxxxxxxx"
            ],

            default => [
                'status' => 'unknown',
                'message' => "I didn't understand that...."
            ]
        };
    }


    public function resolveDataold($intent, $user, $phone): array
    {
        if (!$intent['network']) {
    
            return [
                'status' => 'data_network_required',
                'field' => 'network',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "Which network?\n\nMTN\nAirtel\nGlo\n9mobile"
            ];
        }
    
        if (!$intent['data_size_in_mb']) {
    
            return [
                'status' => 'data_size_required',
                'field' => 'data_size',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "What data size?\n\n1GB\n2GB\n5GB"
            ];
        }
    
        $query = ProductPlan::query()
            ->where('visibility',1)
            ->where('network', strtolower($intent['network']))
            ->where('data_size_in_mb', $intent['data_size_in_mb']);
    
        /**
         * IMPORTANT:
         * Only apply validity if user explicitly provided it
         */
        if (!empty($intent['validity_in_days'])) {
            $query->where('validity_in_days', $intent['validity_in_days']);
        }
    
        $plans = $query->with([
            'product_plan_category.product',
            'product_plan_category.network'
        ])->get();
    
        /**
         * NO MATCH → DO NOT RELAX FILTERS
         * Instead, show what exists within same strict filters
         */
    
        if ($plans->isEmpty()) {
    
            $alternatives = ProductPlan::query()
                ->where('visibility',1)
                ->where('network', strtolower($intent['network']))
                ->where('data_size_in_mb', $intent['data_size_in_mb'])
                ->get();
    
            if ($alternatives->isNotEmpty()) {
    
                $message = "I couldn't find that exact plan.\n\nAvailable options:\n\n";
    
                foreach ($alternatives as $i => $plan) {
                    // $message .= ($i + 1) . ". {$plan->product_plan_name}\n";
                    $index = $i + 1;
                    $options[$index] = $plan->id;
                    $message .= "{$index}. {$plan->product_plan_name}\n";
                }
    
                return [
                    'status' => 'data_multiple_options',
                    'whatsapp_phone' => $phone,
                    'intent' => $intent,
                    'options' => $options,
                    'plans' => $alternatives,
                    'message' => $message,
                ];
            }
    
           

            return [
                'status' => 'data_plan_not_found',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "I couldn't find a matching {$intent['network']} "
                    . ($intent['data_size_in_mb'] / 1000)
                    . "GB plan.\n\n"
                    . "Reply START to try another search."
            ];
        }




    
        if ($plans->count() > 1) {
    
            $message = "Multiple plans found:\n\n";
    
            foreach ($plans as $i => $plan) {
                // $message .= ($i + 1) . ". {$plan->product_plan_name}\n";

                $index = $i + 1;
                $options[$index] = $plan->id;
                $message .= "{$index}. {$plan->product_plan_name}\n";
            }
    
            return [
                'status' => 'data_multiple_options',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'options' => $options,
                'plans' => $plans,
                'message' => $message,
            ];
        }
    
        $plan = $plans->first();
    
        if (!$intent['phone']) {
    
            return [
                'status' => 'data_phone_required',
                'field' => 'phone',
                'product_plan_id' => $plan->id,
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "Which phone number should receive this data?"
            ];
        }


        /*
        Resolve customer-specific price
        */
        $user = app(WhatsappUserResolver::class)
        ->resolve($intent['phone']);
        if (!$user) {
            return [
                'status' => 'unlinked_user',
                'whatsapp_phone' => $phone,
                'intent' => $intent,
                'message' =>
                    "Your number is not linked yet.\n\n"
                    . "Please register or fund your account here:\n"
                    . "https://your-link.com"
            ];
        }
        
        $dat = [
            'product_id' => $plan->product_plan_category->product->id,
            'network_id' => $plan->product_plan_category->network->id,
            'user' => $user,
            'plan_details' => $plan,
        ];

        $dataplanservice = app(DataPlansService::class);

        $priceResponse = $dataplanservice->get_customer_price_per_plan($dat);

        $price = $priceResponse['message'] ?? null;

        // cache()->put(
        //     "wa_session:$phone",
        //     [
        //         'type' => 'data',
        //         'product_plan_id' => $plan->id,
        //         'phone' => $intent['phone'],
        //         'price' => $price,
        //         'status' => 'data_awaiting_confirmation'
        //     ],
        //     now()->addMinutes(10)
        // );
        
    
        return [
            'status' => 'data_awaiting_confirmation',
            // 'whatsapp_phone' => $phone,
            // 'intent' => $intent,

            'network_id' => $plan->product_plan_category->network->id,

        
            'user_id' => $user?->id,
            'product_plan_id' => $plan->id,
            'phone' => $intent['phone'] ?? $phone,
        
            'price' => $price,
        
            'message' =>
                "Confirm Purchase\n\n"
                . "{$plan->product_planprod_name}\n"
                . "Phone: {$intent['phone']}\n"
                . "Price: ₦" . number_format($price)
                . "\n\nReply YES to continue or NO to cancel."
        ];
    }

    public function resolveData($intent, $user, $phone): array
    {
        if (!$intent['network']) {

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

        if (!$intent['data_size_in_mb']) {

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

        if (!$intent['phone']) {

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

    protected function findMatchingPlans(array $intent)
    {
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

    protected function getCustomerPlanPrice(
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

    private function resolveAirtime(array $intent, $user, $phone): array
    {
        if (!$intent['amount']) {

            return [
                'status' => 'need_more_info',
                'field' => 'amount',
                'message' =>
                    "How much airtime do you want?"
            ];
        }

        if (!$intent['phone']) {

            return [
                'status' => 'need_more_info',
                'field' => 'phone',
                'message' =>
                    "Which phone number should receive the airtime?"
            ];
        }

        return [
            'status' => 'confirmation',

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
}