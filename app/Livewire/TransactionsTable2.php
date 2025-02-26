<?php
namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TransactionsTable2 extends Component
{
    use WithPagination;

    
    public string $search = '';

    public int $perPage = 10;
    public string $sortField = 'id';
    public string $sortDirection = 'asc';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?string $role = null;
    public ?string $phone = null;

    // Reset pagination when filters are changed
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedRole()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Transaction::query();

        // Apply search filter
        if (!empty($this->search)) {
            $query->where('phone_number', 'like', "%{$this->search}%");
            
            // ->orWhere('email', 'like', "%{$this->search}%")
        }

        // Apply role filter
        if (!empty($this->phone)) {
            $query->where('phone_number', $this->phone);
        }

        // Apply date range filter
        if (!empty($this->startDate)) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        // Apply sorting
        $transactions = $query->orderBy($this->sortField, $this->sortDirection)
            ->with(['user','product_plan'])
            ->where('wallet_category','!=','data_wallet')
            // ->where('user_id',auth()->id())
            ->latest()
            ->paginate($this->perPage);

        // $transactions = Transaction::paginate();

        return view('livewire.transactions-table', compact('transactions'));
    }
}
