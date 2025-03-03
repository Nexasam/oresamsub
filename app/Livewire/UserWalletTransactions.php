<?php

namespace App\Livewire;

use App\Models\FundingWebhookPayload;
use Livewire\Component;
use Livewire\WithPagination;

class UserWalletTransactions extends Component
{

    use WithPagination;
    
    public string $search = '';

    // #[Url(history: true, keepScroll: true)]
    public int $perPage = 5;
    public string $sortField = 'id';
    public string $sortDirection = 'asc';
    public ?string $date_to = null;
    public ?string $date_from = null;
    public ?string $phone = null;
    public ?string $product_plan_category_filter = '';

    public $site_primary_color;

    public $site_secondary_color;
  


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = FundingWebhookPayload::query();

        
        $date_from = $request->date_from ?? date('Y-m-d', strtotime('-2 days'));
        $date_to= $request->date_to ?? date('Y-m-d');

        if (!empty($this->search)) {
            $query->where('transaction_reference','like',"%{$this->search}%");
        }

        if (!empty($this->date_from) && !empty($this->date_to)) {
            // $query->whereDate('created_at', '>=', $this->startDate);
            $date_too = date('Y-m-d', strtotime('+1 day', strtotime($this->date_to)));
            $query->where('created_at','>=',$this->date_from)->where('created_at','<=',$date_too);
        }

        $wallet_transactions = $query->where('user_id',auth()->id())
        ->latest()
        ->paginate();

        return view('livewire.user-wallet-transactions', [
            'wallet_transactions'=>$wallet_transactions,
        ]);
    }
}
