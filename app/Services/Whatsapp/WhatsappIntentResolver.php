<?php
namespace App\Services\Whatsapp;

use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;

class WhatsappIntentResolver
{
    public function resolve(array $intent, $user, string $phone): array
    {
        return match ($intent['type']) {

            'data' => $this->resolveData($intent, $user, $phone),

            'airtime' => $this->resolveAirtime($intent),

            'navigation_app',
            'navigation_telegram',
            'navigation_support'
                => $this->resolveNavigation($intent['type']),
        
            default => [
                'status' => 'unsupported',
                'message' => "I didn't understand that."
            ]

        
        };
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
                'message' => "I didn't understand that."
            ]
        };
    }


    public function resolveData($intent, $user, $phone): array
    {
        if (!$intent['network']) {
    
            return [
                'status' => 'need_more_info',
                'field' => 'network',
                'message' =>
                    "Which network?\n\nMTN\nAirtel\nGlo\n9mobile"
            ];
        }
    
        if (!$intent['data_size_in_mb']) {
    
            return [
                'status' => 'need_more_info',
                'field' => 'data_size',
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
                    $message .= ($i + 1) . ". {$plan->product_plan_name}\n";
                }
    
                return [
                    'status' => 'multiple_options',
                    'plans' => $alternatives,
                    'message' => $message,
                ];
            }
    
            return [
                'status' => 'need_more_info',
                'field' => 'validity',
                'message' =>
                    "Choose validity:\n\n"
                    . "1. Daily\n"
                    . "2. Weekly\n"
                    . "3. Monthly\n\n"
                    . "Reply with 1, 2 or 3"
            ];
        }
    
        if ($plans->count() > 1) {
    
            $message = "Multiple plans found:\n\n";
    
            foreach ($plans as $i => $plan) {
                $message .= ($i + 1) . ". {$plan->product_plan_name}\n";
            }
    
            return [
                'status' => 'multiple_options',
                'plans' => $plans,
                'message' => $message,
            ];
        }
    
        $plan = $plans->first();
    
        if (!$intent['phone']) {
    
            return [
                'status' => 'need_more_info',
                'field' => 'phone',
                'product_plan_id' => $plan->id,
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

        cache()->put(
            "wa_session:$phone",
            [
                'type' => 'data',
                'product_plan_id' => $plan->id,
                'phone' => $intent['phone'],
                'price' => $price,
                'status' => 'awaiting_confirmation'
            ],
            now()->addMinutes(10)
        );
        
    
        return [
            'status' => 'awaiting_confirmation',

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

    private function resolveAirtime(array $intent): array
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