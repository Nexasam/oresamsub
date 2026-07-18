<?php

namespace App\Services\Whatsapp;

use App\Enums\WhatsappState;
use App\Http\Services\DataPlansService;
use App\Models\OreWhatsappConversation;
use App\Models\OreWhatsappFavorite;
use App\Models\Network;
use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\ProductPlanCategory;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Whatsapp\OreWhatsappService;
use App\Services\Whatsapp\OreWhatsappUserResolverService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OreWhatsappConversationService
{
    private const FAVORITE_SELECT = 'ore_favorite_select';
    private const FAVORITE_SAVE_CHOICE = 'ore_favorite_save_choice';
    private const FAVORITE_NAME = 'ore_favorite_name';

    public function __construct(
        protected OreWhatsappService $whatsapp,
        protected OreWhatsappUserResolverService $userResolver,
        protected WhatsappIntentResolver $intentResolver
    ) {
    }

    public function hasFavoriteShortcut(string $phone, string $shortcut): bool
    {
        $user = $this->userResolver->resolve($phone);

        if (! $user) {
            return false;
        }

        return OreWhatsappFavorite::query()
            ->where('user_id', $user->id)
            ->where('shortcut', strtolower(trim($shortcut)))
            ->exists();
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
        | Exit Ore Bot
        |--------------------------------------------------------------------------
        */
        if ($message === 'start') {

            Cache::forget(
                "ore_session:{$phone}"
            );

            OreWhatsappConversation::where(
                'phone',
                $phone
            )->delete();

            return $this->whatsapp->sendText(
                $phone,
                'Ore session ended. Returning to the main menu...'
            );
        }

    
        $user = $this->userResolver->resolve(
            $phone
        );
        
        if (! $user) {

            return $this->whatsapp->sendText(
                $phone,
                'You do not have a Ore account. Please register first.'
            );
        }

        $conversation =
            OreWhatsappConversation::firstOrCreate([
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
    
        if ($message === 'ore') {
    
            return $this->showMainMenu(
                $conversation
            );
        }

        if (in_array($message, ['wallet', 'account', 'fund'], true)) {
            return $this->showWallet($conversation);
        }

        if (in_array($message, ['transactions', 'transaction', 'recent'], true)) {
            return $this->showRecentTransactions($conversation);
        }

        if (in_array($message, ['help', 'support'], true)) {
            return $this->showHelp($conversation);
        }

        if (in_array($message, ['favorites', 'favourites', 'favorite', 'favourite'], true)) {
            return $this->showFavorites($conversation);
        }

        if ($conversation->current_state !== self::FAVORITE_NAME) {
            $favorite = OreWhatsappFavorite::query()
                ->where('user_id', $user->id)
                ->where('shortcut', $message)
                ->first();

            if ($favorite) {
                return $this->activateFavorite($conversation, $favorite);
            }
        }
    
        return $this->handleState(
            $conversation,
            $message
        );
    }


    private function showMainMenu(
        OreWhatsappConversation $conversation
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
            "👋 Welcome to Ore!\n\nWhat would you like to do today?",
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
                    'title' => 'Account & Fund'
                ],
                [
                    'id' => 'help',
                    'title' => 'Get Help'
                ],
                [
                    'id' => 'transactions',
                    'title' => 'Transactions'
                ],
                [
                    'id' => 'favorites',
                    'title' => 'Favorites'
                ]
            ],
            'View Menu'
        );
    }



    private function handleState(
        OreWhatsappConversation $conversation,
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

            // RECENT TRANSACTIONS FLOW
            WhatsappState::TRANSACTION_SELECT =>
                $this->processTransactionSelection(
                    $conversation,
                    $message
                ),

            WhatsappState::TRANSACTION_AMOUNT =>
                $this->processTransactionAmount(
                    $conversation,
                    $message
                ),

            WhatsappState::TRANSACTION_PHONE =>
                $this->processTransactionPhone(
                    $conversation,
                    $message
                ),

            WhatsappState::TRANSACTION_CONFIRM =>
                $this->processTransactionConfirmation(
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

            self::FAVORITE_SELECT =>
                $this->processFavoriteSelection($conversation, $message),

            self::FAVORITE_SAVE_CHOICE =>
                $this->processFavoriteSaveChoice($conversation, $message),

            self::FAVORITE_NAME =>
                $this->processFavoriteName($conversation, $message),
    
            default =>
                $this->showMainMenu(
                    $conversation
                )
        };
    }

    private function updateConversation(
        OreWhatsappConversation $conversation,
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
        OreWhatsappConversation $conversation,
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
    
            'wallet', 'account', 'fund' =>
                $this->showWallet(
                    $conversation
                ),
    
            'help' =>
                $this->showHelp(
                    $conversation
                ),

            'transactions', 'transaction', 'recent' =>
                $this->showRecentTransactions(
                    $conversation
                ),

            'favorites', 'favourites', 'favorite', 'favourite' =>
                $this->showFavorites($conversation),
    
            default =>
                $this->showMainMenu(
                    $conversation
                )
        };
    }



    //DATA
    private function showDataNetworks(
        OreWhatsappConversation $conversation,
        bool $showMore = false
    )
    {
        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_NETWORK,
            $conversation->payload ?? []
        );
    
        $buttons = $showMore
            ? [
                ['id' => 'glo', 'title' => 'Glo'],
                ['id' => '9mobile', 'title' => '9mobile'],
                ['id' => 'back_data_networks', 'title' => 'Back'],
            ]
            : [
                ['id' => 'mtn', 'title' => 'MTN'],
                ['id' => 'airtel', 'title' => 'Airtel'],
                ['id' => 'more_data_networks', 'title' => 'More Networks'],
            ];

        return $this->whatsapp->sendButtons(
            $conversation->phone,
            $showMore
                ? "📶 Select another network."
                : "📶 Great choice!\n\nPlease select your preferred network.",
            $buttons
        );
    }
    

    
    private function processDataNetwork(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        if ($message === 'more_data_networks') {
            return $this->showDataNetworks($conversation, true);
        }

        if ($message === 'back_data_networks') {
            return $this->showDataNetworks($conversation);
        }

        $network = Network::query()
            ->whereRaw(
                'UPPER(network_name) = ?',
                [strtoupper($message)]
            )
            ->first();

        if (! $network) {
            return $this->showDataNetworks($conversation);
        }

        $dataProduct = Product::query()
            ->whereRaw('LOWER(slug) = ?', ['data'])
            ->first();

        if (! $dataProduct) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Data product configuration is missing.'
            );
        }

        $payload = $conversation->payload ?? [];
        $payload['network_id'] = $network->id;
        $payload['network_name'] = $network->network_name;
        $payload['product_id'] = $dataProduct->id;
        $payload['data_size_page'] = 0;

        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_TYPE,
            $payload
        );

        return $this->showDataSizes($conversation, 0);
    }

    private function showDataSizes(
        OreWhatsappConversation $conversation,
        int $page
    ) {
        $payload = $conversation->payload ?? [];
        $networkId = $payload['network_id'] ?? null;
        $productId = $payload['product_id'] ?? null;

        $sizes = ProductPlan::query()
            ->where('visibility', 1)
            ->whereHas('product_plan_category', fn ($query) => $query
                ->where('network_id', $networkId)
                ->where('product_id', $productId)
            )
            ->pluck('data_size_in_mb')
            ->filter(fn ($size) => is_numeric($size) && (float) $size > 0)
            ->unique(fn ($size) => (string) (float) $size)
            ->sortBy(fn ($size) => (float) $size)
            ->values();

        if ($sizes->isEmpty()) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                "😔 No data plans are currently available for {$payload['network_name']}."
            );
        }

        $page = max(0, $page);
        $offset = $page * 9;

        if ($offset >= $sizes->count()) {
            $page = 0;
            $offset = 0;
        }

        $remaining = $sizes->count() - $offset;
        $take = $remaining <= 10 ? $remaining : 9;
        $rows = $sizes
            ->slice($offset, $take)
            ->map(fn ($size) => [
                'id' => 'data_size_' . (string) (float) $size,
                'title' => $this->formatDataSize((float) $size),
                'description' => 'View available plans',
            ])
            ->values()
            ->toArray();

        if ($remaining > $take) {
            $rows[] = [
                'id' => 'more_data_sizes',
                'title' => 'More Sizes',
                'description' => 'View the next sizes',
            ];
        }

        $payload['data_size_page'] = $page;
        $this->updateConversation($conversation, WhatsappState::DATA_TYPE, $payload);

        return $this->whatsapp->sendList(
            $conversation->phone,
            "📦 Select your preferred {$payload['network_name']} data size.",
            $rows,
            'View Sizes'
        );
    }

    private function processDataType(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        $payload = $conversation->payload ?? [];

        if ($message === 'more_data_sizes') {
            return $this->showDataSizes(
                $conversation,
                ((int) ($payload['data_size_page'] ?? 0)) + 1
            );
        }

        if (! str_starts_with($message, 'data_size_')) {
            return $this->showDataSizes($conversation, (int) ($payload['data_size_page'] ?? 0));
        }

        $size = (float) substr($message, strlen('data_size_'));

        if ($size <= 0) {
            return $this->showDataSizes($conversation, 0);
        }

        $payload['data_size_in_mb'] = $size;
        $payload['data_plan_page'] = 0;

        $this->updateConversation(
            $conversation,
            WhatsappState::DATA_PLAN,
            $payload
        );

        return $this->showMatchingDataPlans($conversation, 0);
    }

    private function showMatchingDataPlans(
        OreWhatsappConversation $conversation,
        int $page
    ) {
        $payload = $conversation->payload ?? [];
        $plans = $this->matchingDataPlansQuery($payload)
            ->orderByDesc('validity_in_days')
            ->orderBy('product_plan_name')
            ->get();

        if ($plans->isEmpty()) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '😔 No plans are currently available for this data size.'
            );
        }

        $page = max(0, $page);
        $offset = $page * 9;

        if ($offset >= $plans->count()) {
            $page = 0;
            $offset = 0;
        }

        $remaining = $plans->count() - $offset;
        $take = $remaining <= 10 ? $remaining : 9;
        $rows = $plans
            ->slice($offset, $take)
            ->map(fn ($plan) => [
                'id' => $plan->id,
                'title' => Str::limit(
                    ($plan->product_plan_category?->product_plan_category_name ?? 'Data')
                    . " · {$plan->validity_in_days} Days",
                    24,
                    ''
                ),
                'description' => Str::limit(
                    '₦' . number_format($this->dataPriceForUser($conversation, $plan), 2)
                    . ' · ' . $plan->product_plan_name,
                    72,
                    ''
                ),
            ])
            ->values()
            ->toArray();

        if ($remaining > $take) {
            $rows[] = [
                'id' => 'more_data_plans',
                'title' => 'More Plans',
                'description' => 'View the next available plans',
            ];
        }

        $payload['data_plan_page'] = $page;
        $this->updateConversation($conversation, WhatsappState::DATA_PLAN, $payload);

        return $this->whatsapp->sendList(
            $conversation->phone,
            "🎯 {$this->formatDataSize($payload['data_size_in_mb'])} selected.\n\nChoose a plan. Price is based on your account level.",
            $rows,
            'View Plans'
        );
    }

    private function matchingDataPlansQuery(array $payload)
    {
        return ProductPlan::query()
            ->with('product_plan_category')
            ->where('visibility', 1)
            ->where('data_size_in_mb', $payload['data_size_in_mb'] ?? null)
            ->whereHas('product_plan_category', fn ($query) => $query
                ->where('network_id', $payload['network_id'] ?? null)
                ->where('product_id', $payload['product_id'] ?? null)
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

    private function dataPriceForUser(
        OreWhatsappConversation $conversation,
        ProductPlan $plan
    ): float {
        $user = $conversation->user()->first();
        $category = $plan->product_plan_category;

        if (! $user) {
            return (float) $plan->user_level_1_selling_price;
        }

        $fallbackPrice = $this->fallbackDataPriceForUser($user, $plan);

        if (! $category) {
            return $fallbackPrice;
        }

        try {
            $pricing = app(DataPlansService::class)->get_customer_price_per_plan([
                'product_id' => $category->product_id,
                'user' => $user,
                'plan_details' => $plan,
                'network_id' => $category->network_id,
            ]);

            return (float) ($pricing['message'] ?? $fallbackPrice);
        } catch (\Throwable $exception) {
            logger()->warning('Ore data price resolution failed', [
                'user_id' => $user->id,
                'product_plan_id' => $plan->id,
                'error' => $exception->getMessage(),
            ]);

            return $fallbackPrice;
        }
    }

    private function fallbackDataPriceForUser(
        User $user,
        ProductPlan $plan
    ): float {
        $planLevel = $user->user_plan()->value('plan_level') ?? 1;
        $priceColumn = "user_level_{$planLevel}_selling_price";

        return (float) ($plan->{$priceColumn} ?? $plan->user_level_1_selling_price);
    }
    
    private function processDataPlan(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        $payload = $conversation->payload ?? [];

        if ($message === 'more_data_plans') {
            return $this->showMatchingDataPlans(
                $conversation,
                ((int) ($payload['data_plan_page'] ?? 0)) + 1
            );
        }

        $plan = $this->matchingDataPlansQuery($payload)
            ->where('id', $message)
            ->first();
    
        if (! $plan) {
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Invalid plan selected. Please try again.'
            );
        }
    
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
                $this->dataPriceForUser($conversation, $plan),
                2
            ) .
            "\n\n📱 Type the recipient's phone number or share a WhatsApp contact."
        );
    }


    private function processDataPhone(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        $phone = $this->userResolver->normalize($message);
    
        if (! preg_match('/^0[789][01]\d{8}$/', $phone)) {
    
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
                $this->dataPriceForUser($conversation, $plan),
                2
            ),
            [
                [
                    'id' => 'confirm_data_purchase',
                    'title' => 'Confirm'
                ],
                [
                    'id' => 'cancel_data_purchase',
                    'title' => 'Cancel'
                ]
            ]
        );
    }
    

    
    private function processDataConfirmationold(
        OreWhatsappConversation $conversation,
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
            "Thank you for choosing Ore 🚀"
        );
    }


    private function processDataConfirmation(
        OreWhatsappConversation $conversation,
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
    
            if ($status == 1) {

                $successMessage =
                    "✅ Data Purchase Successful\n\n" .
                    "📶 {$plan->product_plan_name}\n" .
                    "📱 {$payload['beneficiary_phone']}\n\n" .
                    "{$responseMessage}";

                if (! empty($payload['favorite_id'])) {
                    $this->updateConversation(
                        $conversation,
                        WhatsappState::MAIN_MENU,
                        []
                    );

                    return $this->whatsapp->sendButtons(
                        $conversation->phone,
                        $successMessage,
                        [['id' => 'ore_main_menu', 'title' => 'Main Menu']]
                    );
                }

                $this->updateConversation(
                    $conversation,
                    self::FAVORITE_SAVE_CHOICE,
                    $payload
                );

                return $this->whatsapp->sendButtons(
                    $conversation->phone,
                    $successMessage . "\n\nWould you like to save this purchase as a favorite?",
                    [
                        ['id' => 'save_favorite_plan', 'title' => 'Save Plan'],
                        ['id' => 'save_favorite_full', 'title' => 'Plan & Number'],
                        ['id' => 'skip_favorite', 'title' => 'Not Now'],
                    ]
                );
            }

            $this->updateConversation(
                $conversation,
                WhatsappState::MAIN_MENU,
                []
            );
    
            return $this->whatsapp->sendText(
                $conversation->phone,
                "❌ Purchase Failed\n\n" .
                $responseMessage
            );
    
        } catch (\Throwable $exception) {
    
            logger()->error(
                'Ore Data Purchase Error',
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



    private function showFavorites(OreWhatsappConversation $conversation, int $page = 0)
    {
        $favorites = OreWhatsappFavorite::query()
            ->where('user_id', $conversation->user_id)
            ->whereHas('productPlan', fn ($query) => $query->where('visibility', 1))
            ->with('productPlan.product_plan_category')
            ->latest()
            ->get();

        if ($favorites->isEmpty()) {
            return $this->whatsapp->sendButtons(
                $conversation->phone,
                "⭐ You have no saved favorites yet.\n\nAfter a successful data purchase, choose Save Plan or Plan & Number.",
                [['id' => 'ore_main_menu', 'title' => 'Main Menu']]
            );
        }

        $page = max(0, $page);
        $offset = $page * 9;

        if ($offset >= $favorites->count()) {
            $page = 0;
            $offset = 0;
        }

        $remaining = $favorites->count() - $offset;
        $take = $remaining <= 10 ? $remaining : 9;
        $rows = $favorites->slice($offset, $take)
            ->map(function (OreWhatsappFavorite $favorite) {
                $plan = $favorite->productPlan;
                $mode = $favorite->beneficiary_phone ?: 'Complete details when buying';
                $type = strtoupper($favorite->product_type ?? 'data');

                return [
                    'id' => 'ore_favorite_' . $favorite->id,
                    'title' => Str::limit($favorite->shortcut, 24, ''),
                    'description' => Str::limit("{$type} · {$plan->product_plan_name} · {$mode}", 72, ''),
                ];
            })->values()->toArray();

        if ($remaining > $take) {
            $rows[] = [
                'id' => 'more_ore_favorites',
                'title' => 'More Favorites',
                'description' => 'View the next saved options',
            ];
        }

        $this->updateConversation(
            $conversation,
            self::FAVORITE_SELECT,
            ['favorite_page' => $page]
        );

        return $this->whatsapp->sendList(
            $conversation->phone,
            "⭐ Saved Favorites\n\nSelect one, or type its shortcut anytime while Ore is active.",
            $rows,
            'View Favorites'
        );
    }

    private function processFavoriteSelection(
        OreWhatsappConversation $conversation,
        string $message
    ) {
        $payload = $conversation->payload ?? [];

        if ($message === 'more_ore_favorites') {
            return $this->showFavorites(
                $conversation,
                ((int) ($payload['favorite_page'] ?? 0)) + 1
            );
        }

        if ($message === 'ore_main_menu') {
            return $this->showMainMenu($conversation);
        }

        if (! str_starts_with($message, 'ore_favorite_')) {
            return $this->showFavorites($conversation, (int) ($payload['favorite_page'] ?? 0));
        }

        $favorite = OreWhatsappFavorite::query()
            ->where('id', substr($message, strlen('ore_favorite_')))
            ->where('user_id', $conversation->user_id)
            ->first();

        return $favorite
            ? $this->activateFavorite($conversation, $favorite)
            : $this->showFavorites($conversation);
    }

    private function activateFavorite(
        OreWhatsappConversation $conversation,
        OreWhatsappFavorite $favorite
    ) {
        $plan = ProductPlan::query()
            ->with('product_plan_category')
            ->where('id', $favorite->product_plan_id)
            ->where('visibility', 1)
            ->first();
        $category = $plan?->product_plan_category;

        if (! $plan || ! $category) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ The plan saved in this favorite is no longer available.'
            );
        }

        $payload = [
            'favorite_id' => $favorite->id,
            'favorite_type' => $favorite->product_type ?? 'data',
            'product_plan_id' => $plan->id,
            'network_id' => $category->network_id,
            'product_id' => $category->product_id,
            'data_size_in_mb' => $plan->data_size_in_mb,
        ];

        if (($favorite->product_type ?? 'data') === 'airtime') {
            $payload['network_name'] = $category->network?->network_name
                ?? Network::find($category->network_id)?->network_name;

            if ($favorite->amount !== null && $favorite->beneficiary_phone) {
                $payload['amount'] = (float) $favorite->amount;
                $this->updateConversation($conversation, WhatsappState::AIRTIME_PHONE, $payload);

                return $this->processAirtimePhone($conversation, $favorite->beneficiary_phone);
            }

            $this->updateConversation($conversation, WhatsappState::AIRTIME_AMOUNT, $payload);

            return $this->whatsapp->sendText(
                $conversation->phone,
                "⭐ {$favorite->shortcut}\n\n📞 {$payload['network_name']} airtime\n\n💰 Enter the airtime amount (minimum ₦50)."
            );
        }

        $this->updateConversation($conversation, WhatsappState::DATA_PHONE, $payload);

        if ($favorite->beneficiary_phone) {
            return $this->processDataPhone($conversation, $favorite->beneficiary_phone);
        }

        return $this->whatsapp->sendText(
            $conversation->phone,
            "⭐ {$favorite->shortcut}\n\n📦 {$plan->product_plan_name}\n💰 ₦" .
            number_format($this->dataPriceForUser($conversation, $plan), 2) .
            "\n\n📱 Type the recipient's number or share a WhatsApp contact."
        );
    }

    private function processFavoriteSaveChoice(
        OreWhatsappConversation $conversation,
        string $message
    ) {
        $payload = $conversation->payload ?? [];
        $isAirtime = ($payload['favorite_type'] ?? 'data') === 'airtime';

        if ($message === 'skip_favorite') {
            return $this->showMainMenu($conversation);
        }

        if (! in_array($message, ['save_favorite_plan', 'save_favorite_full'], true)) {
            return $this->whatsapp->sendButtons(
                $conversation->phone,
                'Choose how you would like to save this purchase.',
                $isAirtime
                    ? [
                        ['id' => 'save_favorite_plan', 'title' => 'Save Network'],
                        ['id' => 'save_favorite_full', 'title' => 'Save Full Setup'],
                        ['id' => 'skip_favorite', 'title' => 'Not Now'],
                    ]
                    : [
                        ['id' => 'save_favorite_plan', 'title' => 'Save Plan'],
                        ['id' => 'save_favorite_full', 'title' => 'Plan & Number'],
                        ['id' => 'skip_favorite', 'title' => 'Not Now'],
                    ]
            );
        }

        $payload['favorite_mode'] = $message === 'save_favorite_full'
            ? 'plan_and_number'
            : 'plan_only';

        $this->updateConversation($conversation, self::FAVORITE_NAME, $payload);

        return $this->whatsapp->sendText(
            $conversation->phone,
            "✏️ Enter a unique shortcut.\n\nExamples: 1gbplan or 1gb4mom\nUse 3–30 letters, numbers, underscores, or hyphens."
        );
    }

    private function processFavoriteName(
        OreWhatsappConversation $conversation,
        string $message
    ) {
        $shortcut = strtolower(trim($message));
        $reserved = [
            'ore', 'start', 'data', 'airtime', 'wallet', 'account', 'fund',
            'transactions', 'transaction', 'recent', 'favorites', 'favourites',
            'favorite', 'favourite', 'help', 'support',
        ];

        if (! preg_match('/^[a-z0-9_-]{3,30}$/', $shortcut)
            || in_array($shortcut, $reserved, true)) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ That shortcut is unavailable. Use 3–30 letters, numbers, underscores, or hyphens.'
            );
        }

        if (OreWhatsappFavorite::query()
            ->where('user_id', $conversation->user_id)
            ->where('shortcut', $shortcut)
            ->exists()) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                "⚠️ You already use *{$shortcut}*. Please enter another shortcut."
            );
        }

        $payload = $conversation->payload ?? [];
        $favorite = OreWhatsappFavorite::create([
            'user_id' => $conversation->user_id,
            'shortcut' => $shortcut,
            'product_type' => $payload['favorite_type'] ?? 'data',
            'product_plan_id' => $payload['product_plan_id'],
            'beneficiary_phone' => ($payload['favorite_mode'] ?? null) === 'plan_and_number'
                ? ($payload['beneficiary_phone'] ?? null)
                : null,
            'amount' => ($payload['favorite_mode'] ?? null) === 'plan_and_number'
                && ($payload['favorite_type'] ?? 'data') === 'airtime'
                    ? ($payload['amount'] ?? null)
                    : null,
        ]);

        $this->updateConversation($conversation, WhatsappState::MAIN_MENU, []);

        if (($favorite->product_type ?? 'data') === 'airtime') {
            $description = $favorite->beneficiary_phone
                ? 'The network, amount, and recipient number were saved.'
                : 'The airtime network was saved. You will enter an amount and number when buying.';
        } else {
            $description = $favorite->beneficiary_phone
                ? 'The plan and recipient number were saved.'
                : 'The plan was saved. You will choose a number when buying.';
        }

        return $this->whatsapp->sendButtons(
            $conversation->phone,
            "✅ Favorite saved as *{$shortcut}*.\n\n{$description}\n\nType *{$shortcut}* anytime in Ore to use it.",
            [['id' => 'ore_main_menu', 'title' => 'Main Menu']]
        );
    }

    ///AIRTIME
    private function showAirtimeNetworks(
        OreWhatsappConversation $conversation
    )
    {
        $this->updateConversation(
            $conversation,
            WhatsappState::AIRTIME_NETWORK,
            []
        );

        return $this->whatsapp->sendList(
            $conversation->phone,
            "📞 Which network would you like to recharge?",
            [
                ['id' => 'mtn', 'title' => 'MTN'],
                ['id' => 'airtel', 'title' => 'Airtel'],
                ['id' => 'glo', 'title' => 'Glo'],
                ['id' => '9mobile', 'title' => '9mobile'],
            ],
            'Select Network'
        );
    }
    
    private function processAirtimeNetwork(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        $network = Network::query()
            ->whereRaw('LOWER(network_name) = ?', [strtolower($message)])
            ->first();

        if (! $network) {
            return $this->showAirtimeNetworks($conversation);
        }

        $product = Product::query()
            ->whereRaw('LOWER(slug) = ?', ['airtime'])
            ->first();

        if (! $product) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Airtime product configuration is missing.'
            );
        }

        $categoryIds = ProductPlanCategory::query()
            ->where('product_id', $product->id)
            ->where('network_id', $network->id)
            ->pluck('id');

        $plan = ProductPlan::query()
            ->whereIn('product_plan_category_id', $categoryIds)
            ->where('visibility', 1)
            ->first();

        if (! $plan) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                "😔 Airtime is currently unavailable for {$network->network_name}."
            );
        }

        $this->updateConversation(
            $conversation,
            WhatsappState::AIRTIME_AMOUNT,
            [
                'network_id' => $network->id,
                'network_name' => $network->network_name,
                'product_plan_id' => $plan->id,
            ]
        );

        return $this->whatsapp->sendText(
            $conversation->phone,
            "✅ {$network->network_name} selected.\n\n💰 Enter the airtime amount (minimum ₦50)."
        );
    }
    
    private function processAirtimeAmount(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        $amount = str_replace(',', '', trim($message));
        $amount = preg_replace('/^(?:₦|n|ngn)\s*/i', '', $amount);

        if (! is_numeric($amount) || (float) $amount < 50) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                "⚠️ Enter a valid airtime amount of at least ₦50.\n\nExample: 500"
            );
        }

        $payload = $conversation->payload ?? [];
        $payload['amount'] = abs((float) $amount);

        $this->updateConversation(
            $conversation,
            WhatsappState::AIRTIME_PHONE,
            $payload
        );

        return $this->whatsapp->sendText(
            $conversation->phone,
            "📱 Type the recipient's phone number or share a WhatsApp contact."
        );
    }
    
    private function processAirtimePhone(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        $phone = $this->userResolver->normalize($message);

        if (! preg_match('/^0[789][01]\d{8}$/', $phone)) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Please enter a valid Nigerian phone number.'
            );
        }

        $payload = $conversation->payload ?? [];
        $payload['beneficiary_phone'] = $phone;

        if (
            empty($payload['network_id']) ||
            empty($payload['product_plan_id']) ||
            empty($payload['amount'])
        ) {
            return $this->showMainMenu($conversation);
        }

        $this->updateConversation(
            $conversation,
            WhatsappState::AIRTIME_CONFIRM,
            $payload
        );

        return $this->whatsapp->sendButtons(
            $conversation->phone,
            "📋 Please confirm your airtime purchase\n\n" .
            "📞 Network: {$payload['network_name']}\n" .
            "📱 Number: {$phone}\n" .
            '💰 Amount: ₦' . number_format($payload['amount'], 2),
            [
                ['id' => 'confirm_airtime_purchase', 'title' => 'Confirm'],
                ['id' => 'cancel_airtime_purchase', 'title' => 'Cancel'],
            ]
        );
    }
    
    private function processAirtimeConfirmation(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        if ($message === 'cancel_airtime_purchase') {
            return $this->showMainMenu($conversation);
        }

        if ($message !== 'confirm_airtime_purchase') {
            return $this->whatsapp->sendText(
                $conversation->phone,
                'Please click the confirm or cancel button.'
            );
        }

        $payload = $conversation->payload ?? [];
        $plan = ProductPlan::find($payload['product_plan_id'] ?? null);
        $user = $conversation->user;

        if (! $plan || ! $user || empty($payload['beneficiary_phone'])) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Unable to process request. Please start again.'
            );
        }

        try {
            $this->whatsapp->sendText(
                $conversation->phone,
                "⏳ Processing your airtime purchase...\n\nPlease wait."
            );

            $request = new \Illuminate\Http\Request([
                'network_id' => $payload['network_id'],
                'phone_number' => $payload['beneficiary_phone'],
                'product_plan_id' => $plan->id,
                'amount' => $payload['amount'],
                'wallet_category' => 'main_wallet',
                'validatephonenetwork' => 0,
                'pin' => $user->pin,
                'user' => $user,
            ]);

            $result = app(
                \App\Http\Controllers\AirtimeController::class
            )->buy_airtime_action_1($request);

            $data = $result->getData(true);
            $status = $data['status'] ?? 0;
            $responseMessage = $data['message'] ?? 'Transaction completed';

            if ($status == 1) {
                $successMessage =
                    "✅ Airtime Purchase Successful\n\n" .
                    "📞 {$payload['network_name']}\n" .
                    "📱 {$payload['beneficiary_phone']}\n" .
                    '💰 ₦' . number_format($payload['amount'], 2) .
                    "\n\n{$responseMessage}";

                if (! empty($payload['favorite_id'])) {
                    $this->updateConversation(
                        $conversation,
                        WhatsappState::MAIN_MENU,
                        []
                    );

                    return $this->whatsapp->sendButtons(
                        $conversation->phone,
                        $successMessage,
                        [['id' => 'ore_main_menu', 'title' => 'Main Menu']]
                    );
                }

                $payload['favorite_type'] = 'airtime';
                $this->updateConversation(
                    $conversation,
                    self::FAVORITE_SAVE_CHOICE,
                    $payload
                );

                return $this->whatsapp->sendButtons(
                    $conversation->phone,
                    $successMessage . "\n\nWould you like to save this airtime purchase as a favorite?",
                    [
                        ['id' => 'save_favorite_plan', 'title' => 'Save Network'],
                        ['id' => 'save_favorite_full', 'title' => 'Save Full Setup'],
                        ['id' => 'skip_favorite', 'title' => 'Not Now'],
                    ]
                );
            }

            $this->updateConversation(
                $conversation,
                WhatsappState::MAIN_MENU,
                []
            );

            return $this->whatsapp->sendText(
                $conversation->phone,
                "❌ Airtime Purchase Failed\n\n{$responseMessage}"
            );
        } catch (\Throwable $exception) {
            logger()->error('Ore Airtime Purchase Error', [
                'error' => $exception->getMessage(),
                'payload' => $payload,
            ]);

            return $this->whatsapp->sendText(
                $conversation->phone,
                "❌ An error occurred while processing your airtime purchase.\n\nPlease try again later."
            );
        }
    }

    private function showRecentTransactions(
        OreWhatsappConversation $conversation,
        int $page = 0
    ) {
        $transactions = Transaction::query()
            ->where('user_id', $conversation->user_id)
            ->whereIn('transaction_category', ['data', 'airtime'])
            ->whereNotNull('product_plan_id')
            ->whereHas('product_plan', fn ($query) =>
                $query->where('visibility', 1)
            )
            ->with([
                'product_plan.product_plan_category.product',
                'product_plan.product_plan_category.network',
            ])
            ->latest()
            ->take(20)
            ->get();

        if ($transactions->isEmpty()) {
            $this->updateConversation(
                $conversation,
                WhatsappState::MAIN_MENU,
                []
            );

            return $this->whatsapp->sendButtons(
                $conversation->phone,
                "📋 You don't have any DATA or AIRTIME transactions to show yet.",
                [
                    ['id' => 'ore_main_menu', 'title' => 'Main Menu'],
                ]
            );
        }

        $options = $transactions
            ->values()
            ->mapWithKeys(fn ($transaction, $index) => [
                (string) ($index + 1) => $transaction->id,
            ])
            ->all();

        $pageSize = 5;
        $lastPage = (int) ceil($transactions->count() / $pageSize) - 1;
        $page = max(0, min($page, $lastPage));
        $visibleTransactions = $transactions
            ->slice($page * $pageSize, $pageSize)
            ->values();

        $message = "📋 Recent Transactions\n\nYou can rebuy any of these.\n\n";

        foreach ($visibleTransactions as $index => $transaction) {
            $number = ($page * $pageSize) + $index + 1;
            $plan = $transaction->product_plan;
            $network = $plan?->product_plan_category?->network?->network_name;
            $category = strtoupper($transaction->transaction_category);
            $status = $this->transactionStatusLabel($transaction->status);

            $message .= "{$number}. {$category}";
            $message .= $network ? " · {$network}" : '';
            $message .= "\n{$plan->product_plan_name}\nStatus: {$status}";

            if ($transaction->transaction_category === 'airtime') {
                $message .= " · ₦" . number_format((float) $transaction->amount, 2);
            }

            $message .= "\n\n";
        }

        $message .= 'Reply with a number to buy it again.';

        $this->updateConversation(
            $conversation,
            WhatsappState::TRANSACTION_SELECT,
            [
                'transaction_options' => $options,
                'transaction_page' => $page,
            ]
        );

        $buttons = [];

        if ($page < $lastPage) {
            $buttons[] = ['id' => 'more_transactions', 'title' => 'Show More'];
        }

        $buttons[] = ['id' => 'ore_main_menu', 'title' => 'Main Menu'];

        return $this->whatsapp->sendButtons(
            $conversation->phone,
            $message,
            $buttons
        );
    }

    private function transactionStatusLabel(string|int|null $status): string
    {
        return match ((string) $status) {
            '1' => 'Successful ✅',
            '0' => 'Pending ⏳',
            '-1' => 'Failed ❌',
            '2' => 'Refunded ↩️',
            '3' => 'Processing ⏳',
            default => 'Unknown',
        };
    }

    private function processTransactionSelection(
        OreWhatsappConversation $conversation,
        string $message
    ) {
        $payload = $conversation->payload ?? [];

        if ($message === 'more_transactions') {
            return $this->showRecentTransactions(
                $conversation,
                ((int) ($payload['transaction_page'] ?? 0)) + 1
            );
        }

        if ($message === 'ore_main_menu') {
            return $this->showMainMenu($conversation);
        }

        $transactionId = data_get(
            $payload,
            "transaction_options.{$message}"
        );

        if (! $transactionId) {
            return $this->whatsapp->sendButtons(
                $conversation->phone,
                '⚠️ Reply with one of the transaction numbers shown above.',
                [
                    ['id' => 'ore_main_menu', 'title' => 'Main Menu'],
                ]
            );
        }

        $transaction = Transaction::query()
            ->where('id', $transactionId)
            ->where('user_id', $conversation->user_id)
            ->whereIn('transaction_category', ['data', 'airtime'])
            ->with('product_plan.product_plan_category.network')
            ->first();

        $plan = $transaction?->product_plan;
        $network = $plan?->product_plan_category?->network;

        if (! $transaction || ! $plan || ! $network || ! $plan->visibility) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ That plan is no longer available. Please select another transaction.'
            );
        }

        $payload = [
            'transaction_id' => $transaction->id,
            'transaction_category' => $transaction->transaction_category,
            'product_plan_id' => $plan->id,
            'network_id' => $network->id,
            'network_name' => $network->network_name,
        ];

        if ($transaction->transaction_category === 'airtime') {
            $this->updateConversation(
                $conversation,
                WhatsappState::TRANSACTION_AMOUNT,
                $payload
            );

            return $this->whatsapp->sendText(
                $conversation->phone,
                "✅ {$network->network_name} airtime selected.\n\n💰 Enter the new airtime amount (minimum ₦50)."
            );
        }

        $this->updateConversation(
            $conversation,
            WhatsappState::TRANSACTION_PHONE,
            $payload
        );

        return $this->whatsapp->sendText(
            $conversation->phone,
            "✅ {$plan->product_plan_name} selected.\n\n📱 Enter the phone number that should receive this purchase."
        );
    }

    private function processTransactionAmount(
        OreWhatsappConversation $conversation,
        string $message
    ) {
        $amount = str_replace(',', '', trim($message));
        $amount = preg_replace('/^(?:₦|n|ngn)\s*/i', '', $amount);

        if (! is_numeric($amount) || (float) $amount < 50) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                "⚠️ Enter a valid airtime amount of at least ₦50.\n\nExample: 200"
            );
        }

        $payload = $conversation->payload ?? [];

        if (($payload['transaction_category'] ?? null) !== 'airtime') {
            return $this->showRecentTransactions($conversation);
        }

        $payload['amount'] = abs((float) $amount);

        $this->updateConversation(
            $conversation,
            WhatsappState::TRANSACTION_PHONE,
            $payload
        );

        return $this->whatsapp->sendText(
            $conversation->phone,
            "📱 Enter the phone number that should receive the airtime."
        );
    }

    private function processTransactionPhone(
        OreWhatsappConversation $conversation,
        string $message
    ) {
        $phone = $this->userResolver->normalize($message);

        if (! preg_match('/^0[789][01]\d{8}$/', $phone)) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Please enter a valid Nigerian phone number.'
            );
        }

        $payload = $conversation->payload ?? [];
        $plan = ProductPlan::find($payload['product_plan_id'] ?? null);

        if (! $plan || empty($payload['transaction_category'])) {
            return $this->showRecentTransactions($conversation);
        }

        $payload['beneficiary_phone'] = $phone;

        $this->updateConversation(
            $conversation,
            WhatsappState::TRANSACTION_CONFIRM,
            $payload
        );

        $amount = $payload['transaction_category'] === 'airtime'
            ? "\n💰 Amount: ₦" . number_format($payload['amount'], 2)
            : "\n💰 Amount: ₦" . number_format(
                $this->dataPriceForUser($conversation, $plan),
                2
            );

        return $this->whatsapp->sendButtons(
            $conversation->phone,
            "📋 Confirm Repeat Purchase\n\n" .
            "📦 {$plan->product_plan_name}\n" .
            "📶 {$payload['network_name']}\n" .
            "📱 {$phone}{$amount}",
            [
                ['id' => 'confirm_transaction_purchase', 'title' => 'Confirm'],
                ['id' => 'cancel_transaction_purchase', 'title' => 'Cancel'],
            ]
        );
    }

    private function processTransactionConfirmation(
        OreWhatsappConversation $conversation,
        string $message
    ) {
        if ($message === 'cancel_transaction_purchase') {
            return $this->showMainMenu($conversation);
        }

        if ($message !== 'confirm_transaction_purchase') {
            return $this->whatsapp->sendText(
                $conversation->phone,
                'Please click the confirm or cancel button.'
            );
        }

        $payload = $conversation->payload ?? [];
        $plan = ProductPlan::query()
            ->where('id', $payload['product_plan_id'] ?? null)
            ->where('visibility', 1)
            ->first();
        $user = $conversation->user()->first();

        if (! $plan || ! $user || empty($payload['beneficiary_phone'])) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Unable to process this repeat purchase. Please start again.'
            );
        }

        try {
            $this->whatsapp->sendText(
                $conversation->phone,
                "⏳ Processing your repeat purchase...\n\nPlease wait."
            );

            $requestData = [
                'network_id' => $payload['network_id'],
                'phone_number' => $payload['beneficiary_phone'],
                'product_plan_id' => $plan->id,
                'wallet_category' => 'main_wallet',
                'validatephonenetwork' => 0,
                'pin' => $user->pin,
                'user' => $user,
            ];

            if ($payload['transaction_category'] === 'airtime') {
                $requestData['amount'] = $payload['amount'];
                $result = app(
                    \App\Http\Controllers\AirtimeController::class
                )->buy_airtime_action_1(new \Illuminate\Http\Request($requestData));
            } else {
                $result = app(
                    \App\Http\Controllers\DataController::class
                )->buy_again_data_action(new \Illuminate\Http\Request($requestData));
            }

            $data = $result->getData(true);
            $status = $data['status'] ?? 0;
            $responseMessage = $data['message'] ?? 'Transaction completed';

            $this->updateConversation(
                $conversation,
                WhatsappState::MAIN_MENU,
                []
            );

            if ($status == 1) {
                return $this->whatsapp->sendButtons(
                    $conversation->phone,
                    "✅ Repeat Purchase Successful\n\n" .
                    "📦 {$plan->product_plan_name}\n" .
                    "📱 {$payload['beneficiary_phone']}\n\n" .
                    $responseMessage,
                    [
                        ['id' => 'ore_main_menu', 'title' => 'Main Menu'],
                    ]
                );
            }

            return $this->whatsapp->sendButtons(
                $conversation->phone,
                "❌ Repeat Purchase Failed\n\n{$responseMessage}",
                [
                    ['id' => 'ore_main_menu', 'title' => 'Main Menu'],
                ]
            );
        } catch (\Throwable $exception) {
            logger()->error('Ore Repeat Purchase Error', [
                'error' => $exception->getMessage(),
                'payload' => $payload,
            ]);

            return $this->whatsapp->sendText(
                $conversation->phone,
                "❌ An error occurred while processing your repeat purchase.\n\nPlease try again later."
            );
        }
    }

    private function showWallet(
        OreWhatsappConversation $conversation
    )
    {
        $user = $conversation->user()->first();

        if (! $user) {
            return $this->whatsapp->sendText(
                $conversation->phone,
                '⚠️ Unable to load your account. Please start again.'
            );
        }

        $this->updateConversation(
            $conversation,
            WhatsappState::WALLET,
            []
        );

        $result = $this->intentResolver->resolveAccount(
            $user,
            $conversation->phone
        );

        return $this->whatsapp->sendButtons(
            $conversation->phone,
            $result['message'],
            [
                ['id' => 'ore_refresh_balance', 'title' => 'Refresh Balance'],
                ['id' => 'ore_main_menu', 'title' => 'Main Menu'],
            ]
        );
    }
    
    private function processWallet(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        return match ($message) {
            'ore_refresh_balance' => $this->showWallet($conversation),
            'ore_main_menu' => $this->showMainMenu($conversation),
            default => $this->showWallet($conversation),
        };
    }

    private function showHelp(
        OreWhatsappConversation $conversation
    )
    {
        $this->updateConversation(
            $conversation,
            WhatsappState::MAIN_MENU,
            []
        );

        return $this->whatsapp->sendButtons(
            $conversation->phone,
            "🆘 Ore Help Center\n\n" .
            "🛒 HOW TO BUY\n" .
            "Select DATA or AIRTIME from the main menu, choose the network and package or amount, enter the beneficiary number, then confirm the purchase.\n\n" .
            "🔐 ACCOUNT ACCESS\n" .
            "Login: https://oresamsub.com/login\n" .
            "Forgot password: https://oresamsub.com/forgot-password\n" .
            "For a forgotten transaction PIN, contact support.\n\n" .
            "💬 WHATSAPP SUPPORT\n" .
            "https://wa.me/2349011988807\n" .
            "https://wa.me/2348168509044\n\n" .
            "📧 EMAIL\n" .
            "info@ore.com",
            [
                ['id' => 'ore_main_menu', 'title' => 'Main Menu'],
            ]
        );
    }
    
    private function processHelp(
        OreWhatsappConversation $conversation,
        string $message
    )
    {
        return $this->showHelp($conversation);
    }



}
