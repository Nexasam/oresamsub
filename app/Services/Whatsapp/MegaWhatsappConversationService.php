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
    
        $plans = ProductPlan::where(
                'product_plan_category_id',
                $category->id
            )
            // ->where(
            //     'visibility',
            //     1
            // )
            ->take(10)
            ->orderBy('user_level_1_selling_price')
            ->get();
    
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
                'title' => $plan->product_plan_name,
                'description' =>
                    'N' .
                    number_format(
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
    
    private function processDataPlan(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
    }
    
    private function processDataPhone(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
    }
    
    private function processDataConfirmation(
        MegaWhatsappConversation $conversation,
        string $message
    )
    {
        //
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