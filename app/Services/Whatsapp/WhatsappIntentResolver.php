<?php
namespace App\Services\Whatsapp;

use App\Http\Services\DataPlansService;
use App\Models\ProductPlan;
use App\Models\Transaction;
use App\Models\UserContact;

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
                'message' => "I didn't understand that message..."
            ]

        
        };
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