<?php

namespace App\Services\Whatsapp;

use App\Enums\WhatsappState;
use App\Models\MegaWhatsappConversation;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Services\Whatsapp\MegaWhatsappService;
use App\Services\Whatsapp\MegaWhatsappUserResolverService;
use Illuminate\Support\Facades\Cache;

class MegaWhatsappConversationService
{
    public function __construct(
        protected MegaWhatsappService $whatsapp,
        protected MegaWhatsappUserResolverService $userResolver
    ) {
    }

    public function handle(
        array $payload
    ) {
    
        $phone = data_get(
            $payload,
            'phone'
        );
    
        $message = strtolower(
            trim(
                data_get(
                    $payload,
                    'message'
                )
            )
        );



         /*
        |--------------------------------------------------------------------------
        | Exit Mega Bot
        |--------------------------------------------------------------------------
        */
        if ($message === 'start') {

            Cache::forget(
                "mega_session:{$phone}"
            );

            MegaWhatsappConversation::where(
                'phone',
                $phone
            )->delete();

            return $this->whatsapp->sendText(
                $phone,
                'Mega session ended. Returning to the main menu...'
            );
        }

    
        $user = $this->userResolver->resolve(
            $phone
        );
        
        if (! $user) {

            return $this->whatsapp->sendText(
                $phone,
                'You do not have a MegaSub account. Please register first.'
            );
        }

        $conversation =
            MegaWhatsappConversation::firstOrCreate([
                'phone' => $phone
            ]);
    
        if (
            $user &&
            $conversation->user_id !== $user->id
        ) {
    
            $conversation->update([
                'user_id' => $user->id
            ]);
        }
    
        if ($message === 'mega') {
    
            return $this->showMainMenu(
                $conversation
            );
        }
    
        return $this->handleState(
            $conversation,
            $message
        );
    }


    private function showMainMenu(
        MegaWhatsappConversation $conversation
    ) {
    
        // $conversation->update([
        //     'current_state' => WhatsappState::MAIN_MENU,
        //     'payload' => []
        // ]);

        $this->updateConversation(
            $conversation,
            WhatsappState::MAIN_MENU,
            []
        );
    
        return $this->whatsapp->sendList(
            $conversation->phone,
            "👋 Welcome to MegaSub!\n\nWhat would you like to do today?",
            [
                [
                    'id' => 'data',
                    'title' => 'Buy Data'
                ],
                [
                    'id' => 'airtime',
                    'title' => 'Buy Airtime'
                ],
                [
                    'id' => 'wallet',
                    'title' => 'My Wallet'
                ],
                [
                    'id' => 'help',
                    'title' => 'Get Help'
                ]
            ],
            'View Menu'
        );
    }



    private function handleState(
        MegaWhatsappConversation $conversation,
        string $message
    ) {
    
        return match ($conversation->current_state) {
    
            // MAIN MENU
            WhatsappState::MAIN_MENU =>
                $this->handleMainMenu(
                    $conversation,
                    $message
                ),
    
            // DATA FLOW
            WhatsappState::DATA_NETWORK =>
                $this->processDataNetwork(
                    $conversation,
                    $message
                ),
    
            WhatsappState::DATA_TYPE =>
                $this->processDataType(
                    $conversation,
                    $message
                ),
    
            WhatsappState::DATA_PLAN =>
                $this->processDataPlan(
                    $conversation,
                    $message
                ),
    
            WhatsappState::DATA_PHONE =>
                $this->processDataPhone(
                    $conversation,
                    $message
                ),
    
            WhatsappState::DATA_CONFIRM =>
                $this->processDataConfirmation(
                    $conversation,
                    $message
                ),
    
            // AIRTIME FLOW
            WhatsappState::AIRTIME_NETWORK =>
                $this->processAirtimeNetwork(
                    $conversation,
                    $message
                ),
    
            WhatsappState::AIRTIME_AMOUNT =>
                $this->processAirtimeAmount(
                    $conversation,
                    $message
                ),
    
            WhatsappState::AIRTIME_PHONE =>
                $this->processAirtimePhone(
                    $conversation,
                    $message
                ),
    
            WhatsappState::AIRTIME_CONFIRM =>
                $this->processAirtimeConfirmation(
                    $conversation,
                    $message
                ),
    
            // WALLET FLOW
            WhatsappState::WALLET =>
                $this->processWallet(
                    $conversation,
                    $message
                ),
    
            // HELP FLOW
            WhatsappState::HELP =>
                $this->processHelp(
                    $conversation,
                    $message
                ),
    
            default =>
                $this->showMainMenu(
                    $conversation
                )
        };
    }

    private function updateConversation(
        MegaWhatsappConversation $conversation,
        string $state,
        array $payload = []
    )
    {
        $conversation->update([
            'current_state' => $state,
            'payload' => $payload
        ]);
    }

    private function handleMainMenu(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        return match ($message) {
    
            'data' =>
                $this->showDataNetworks(
                    $conversation
                ),
    
            'airtime' =>
                $this->showAirtimeNetworks(
                    $conversation
                ),
    
            'wallet' =>
                $this->showWallet(
                    $conversation
                ),
    
            'help' =>
                $this->showHelp(
                    $conversation
                ),
    
            default =>
                $this->showMainMenu(
                    $conversation
                )
        };
    }



    //DATA
    private function showDataNetworks(
        MegaWhatsappConversation $conversation
    )
    {
        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_NETWORK,
            $conversation->payload ?? []
        );
    
        return $this->whatsapp->sendList(
            $conversation->phone,
            "📶 Great choice!\n\nPlease select your preferred network.",
            [
                [
                    'id' => 'mtn',
                    'title' => 'MTN'
                ],
                [
                    'id' => 'airtel',
                    'title' => 'Airtel'
                ],
                [
                    'id' => 'glo',
                    'title' => 'Glo'
                ],
                [
                    'id' => '9mobile',
                    'title' => '9mobile'
                ]
            ],
            'Select Network'
        );
    }
    

    
    private function processDataNetwork(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        
        $network = Network::query()
            ->whereRaw(
                'UPPER(network_name) = ?',
                [strtolower($message)]
            )
            ->first();
    
        if (! $network) {
    
            return $this->showDataNetworks(
                $conversation
            );
        }
    
        $payload = $conversation->payload ?? [];
    
        $payload['network_id'] = $network->id;
    
        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_TYPE,
            $payload
        );
    
        $dataProduct = Product::query()
            ->whereRaw(
                'LOWER(slug) = ?',
                ['data']
            )
            ->first();
    
        if (! $dataProduct) {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Data product configuration is missing.'
            );
        }
    
        $categories = ProductPlanCategory::query()
            ->where(
                'network_id',
                $network->id
            )
            ->where(
                'product_id',
                $dataProduct->id
            )
            // ->where('visibility',1)
            ->orderBy('product_plan_category_name')
            ->get();
    
        if ($categories->isEmpty()) {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                "😔 No data categories are currently available for {$network->network_name}."
            );
        }
    
        return $this->whatsapp->sendList(
            $conversation->phone,
            "✅ Great choice!\n\nYou're about to purchase *{$network->network_name}* data.\n\nPlease select the type of data bundle you'd like.",
            $categories
                ->map(fn ($category) => [
                    'id' => $category->id,
                    'title' => $category->product_plan_category_name,
                ])
                ->toArray(),
            'View Types'
        );
    }
    
   

    private function processDataType(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        $category = ProductPlanCategory::find(
            $message
        );

        logger('catid: '.$message);
    
        if (! $category) {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Invalid selection. Please try again.'
            );
        }
    
        $payload = $conversation->payload ?? [];
    
        $payload['product_plan_category_id']
            = $category->id;
    
        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_PLAN,
            $payload
        );
    
        // $plans = ProductPlan::where(
        //         'product_plan_category_id',
        //         $category->id
        //     )
        //     // ->where(
        //     //     'visibility',
        //     //     1
        //     // )
        //     ->take(10)
        //     ->orderBy('user_level_1_selling_price')
        //     ->get();

        // $plans = ProductPlan::query()
        // ->where(
        //     'product_plan_category_id',
        //     $category->id
        // )
        // ->where('visibility', 1)
        // ->orderByRaw("
        //     CASE
        //         WHEN data_size_in_mb = 500 THEN 1
        //         WHEN data_size_in_mb = 1000 THEN 2
        //         WHEN data_size_in_mb = 2000 THEN 3
        //         WHEN data_size_in_mb = 3000 THEN 4
        //         WHEN data_size_in_mb = 5000 THEN 5
        //         ELSE 6
        //     END
        // ")
        // ->orderByRaw('CAST(data_size_in_mb AS UNSIGNED) DESC')
        // ->take(20)
        // ->get();

        $plans = ProductPlan::query()
        ->where(
            'product_plan_category_id',
            $category->id
        )
        ->where('visibility', 1)
        ->get()
        ->sortBy(function ($plan) {

            $priority = [
                500 => 1,
                1000 => 2,
                2000 => 3,
                3000 => 4,
                5000 => 5,
            ];

            $size = (int) $plan->data_size_in_mb;

            return [
                $priority[$size] ?? 999,
                -$size,
            ];
        })
        ->take(10)
        ->values();
    
        if ($plans->isEmpty()) {
    
            logger('maybe na this onne');
            return $this->whatsapp->sendText(
                $conversation->phone,
                '😔 No plans are currently available for this category.'
            );
        }

        

        $planss = $plans->map(
            fn ($plan) => [
                'id' => $plan->id,
                'title' => sprintf(
                    '%s - %s Days',
                    $this->formatDataSize(
                        $plan->data_size_in_mb
                    ),
                    $plan->validity_in_days
                ),
                'description' => '₦' . number_format(
                    $plan->user_level_1_selling_price,
                    2
                ),
            ]
        )->toArray();
        

        logger('planssssss::'.json_encode($planss));
    
        return $this->whatsapp->sendList(
            $conversation->phone,
            "🎯 *{$category->product_plan_category_name}* selected.\n\nChoose your preferred data plan below.",
           $planss,
            'View Plans'
        );
    }

    private function formatDataSize(
        int|float $sizeInMb
    ): string {
    
        if ($sizeInMb >= 1000) {
    
            return rtrim(
                rtrim(
                    number_format(
                        $sizeInMb / 1000,
                        2
                    ),
                    '0'
                ),
                '.'
            ) . 'GB';
        }
    
        return $sizeInMb . 'MB';
    }
    
    private function processDataPlan(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        $plan = ProductPlan::find($message);
    
        if (! $plan) {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Invalid plan selected. Please try again.'
            );
        }
    
        $payload = $conversation->payload ?? [];
    
        $payload['product_plan_id'] = $plan->id;
    
        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_PHONE,
            $payload
        );
    
        // $size = $this->formatDataSize(
        //     $plan->data_size_in_mb
        // );

        $sizeee = $this->formatDataSize($plan->data_size_in_mb);
    
        return $this->whatsapp->sendText(
            $conversation->phone,
            "✅ Plan Selected\n\n" .
            "📦 {$sizeee}\n" .
            "⏳ {$plan->validity_in_days} Days\n" .
            "💰 N" . number_format(
                $plan->user_level_1_selling_price,
                2
            ) .
            "\n\n📱 Enter the phone number you want to receive this data."
        );
    }


    private function processDataPhone(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        $phone = preg_replace(
            '/[^0-9]/',
            '',
            $message
        );
    
        if (strlen($phone) < 11) {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Please enter a valid phone number.'
            );
        }
    
        $payload = $conversation->payload ?? [];
    
        $payload['beneficiary_phone'] = $phone;
    
        $plan = ProductPlan::find(
            $payload['product_plan_id']
        );
    
        if (! $plan) {
    
            return $this->showMainMenu(
                $conversation
            );
        }
    
        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_CONFIRM,
            $payload
        );
    
        return $this->whatsapp->sendButtons(
            $conversation->phone,
            "📋 Please confirm your purchase\n\n" .
            "📶 Plan: {$plan->product_plan_name}\n" .
            "📱 Number: {$phone}\n" .
            "💰 Amount: ₦" .
            number_format(
                $plan->user_level_1_selling_price,
                2
            ),
            [
                [
                    'id' => 'confirm_data_purchase',
                    'title' => 'CONFIRM'
                ],
                [
                    'id' => 'cancel_data_purchase',
                    'title' => 'CANCEL'
                ]
            ]
        );
    }
    

    
    private function processDataConfirmationold(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        if ($message === 'cancel_data_purchase') {
    
            return $this->showMainMenu(
                $conversation
            );
        }
    
        if ($message !== 'confirm_data_purchase') {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                'Please click the confirm button.'
            );
        }
    
        $payload = $conversation->payload ?? [];
    
        $plan = ProductPlan::find(
            $payload['product_plan_id']
        );
    
        if (! $plan) {
    
            return $this->showMainMenu(
                $conversation
            );
        }
    
        /*
        |--------------------------------------------------------------------------
        | Actual Purchase Happens Here
        |--------------------------------------------------------------------------
        |
        | Later:
        |
        | $this->dataService->purchase(...)
        |
        */
    
        
        $this->updateConversation(
            $conversation,
            WhatsappState::MAIN_MENU,
            []
        );
    
        return $this->whatsapp->sendText(
            $conversation->phone,
            "✅ Success!\n\n" .
            "{$plan->product_plan_name} has been queued for processing.\n\n" .
            "Thank you for choosing MegaSub 🚀"
        );
    }


    private function processDataConfirmation(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        if ($message === 'cancel_data_purchase') {
    
            return $this->showMainMenu(
                $conversation
            );
        }
    
        if ($message !== 'confirm_data_purchase') {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                'Please click the confirm button.'
            );
        }
    
        $payload = $conversation->payload ?? [];
    
        $plan = ProductPlan::find(
            $payload['product_plan_id'] ?? null
        );
    
        $user = $conversation->user;
    
        if (! $plan || ! $user) {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Unable to process request. Please start again.'
            );
        }
    
        try {
    
            /*
            Inform customer
            */
            $this->whatsapp->sendText(
                $conversation->phone,
                "⏳ Processing your request...\n\nPlease wait."
            );
    
            $request = new \Illuminate\Http\Request([
                'product_plan_id' => $plan->id,
                'phone_number' => $payload['beneficiary_phone'],
                'network_id' => $payload['network_id'] ?? null,
                'wallet_category' => 'main_wallet',
                'validatephonenetwork' => 0,
                'pin' => $user->pin,
                'user' => $user,
            ]);
    
            $result = app(
                \App\Http\Controllers\DataController::class
            )->buy_again_data_action(
                $request
            );
    
            $data = $result->getData(true);
    
            $status = $data['status'] ?? 0;
    
            $responseMessage =
                $data['message']
                ?? 'Transaction completed';
    
            /*
            Reset conversation
            */
            $this->updateConversation(
                $conversation,
                WhatsappState::MAIN_MENU,
                []
            );
    
            if ($status == 1) {
    
                return $this->whatsapp->sendText(
                    $conversation->phone,
                    "✅ Data Purchase Successful\n\n" .
                    "📶 {$plan->product_plan_name}\n" .
                    "📱 {$payload['beneficiary_phone']}\n\n" .
                    "{$responseMessage}"
                );
            }
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                "❌ Purchase Failed\n\n" .
                $responseMessage
            );
    
        } catch (\Throwable $exception) {
    
            logger()->error(
                'Mega Data Purchase Error',
                [
                    'error' => $exception->getMessage(),
                    'payload' => $payload,
                ]
            );
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                "❌ An error occurred while processing your request.\n\nPlease try again later."
            );
        }
    }



    ///AIRTIME
    private function showAirtimeNetworks(
        MegaWhatsappConversation $conversation
    )
    {
        return $this->whatsapp->sendText(
            $conversation->phone,
            'Airtime module coming next'
        );
    }
    
    private function processAirtimeNetwork(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
    }
    
    private function processAirtimeAmount(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
    }
    
    private function processAirtimePhone(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
    }
    
    private function processAirtimeConfirmation(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
    }

    private function showWallet(
        MegaWhatsappConversation $conversation
    )
    {
        return $this->whatsapp->sendText(
            $conversation->phone,
            'Wallet module coming next'
        );
    }
    
    private function processWallet(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
    }

    private function showHelp(
        MegaWhatsappConversation $conversation
    )
    {
        return $this->whatsapp->sendText(
            $conversation->phone,
            '
    Support WhatsApp:
    2348168509044
    
    Email:
    info@megasub.com
            '
        );
    }
    
    private function processHelp(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        return $this->showMainMenu(
            $conversation
        );
    }



}