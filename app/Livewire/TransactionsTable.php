<?php
namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\ProductPlan;
use App\Models\Transaction;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\AdminColorSetting;
use Illuminate\Support\Facades\DB;

class TransactionsTable extends Component
{
    use WithPagination;

    
    public string $search = '';

    // #[Url(history: true, keepScroll: true)]
    public int $perPage = 10;
    public string $sortField = 'id';
    public string $sortDirection = 'asc';
    public ?string $date_to = null;
    public ?string $date_from = null;
    public ?string $phone = null;
    public ?string $product_plan_category_filter = '';

    public $site_primary_color;

    public $site_secondary_color;



    // Reset pagination when filters are changed
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // public function mount($site_primary_color,$site_secondary_color): void
    // {
    //     $this->site_primary_color = $site_primary_color;
    //     $this->site_secondary_color = $site_secondary_color;
    // }


    // public function updatedEndDate()
    // {
    //     $this->resetPage();
    // }

    // public function updatedRole()
    // {
    //     $this->resetPage();
    // }

    // public function sortBy(string $field)
    // {
    //     if ($this->sortField === $field) {
    //         $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    //     } else {
    //         $this->sortField = $field;
    //         $this->sortDirection = 'asc';
    //     }
    // }

    public function render()
    {
        $query = Transaction::query();

        // // Apply search filter
        // if (!empty($this->search)) {
        //     $query->where('phone_number', 'like', "%{$this->search}%");
            
        //     // ->orWhere('email', 'like', "%{$this->search}%")
        // }

        // // Apply role filter
        if (!empty($this->search)) {
            $query->where('phone_number','like',"%{$this->search}%");
        }

        // // Apply date range filter
        if (!empty($this->date_from) && !empty($this->date_to)) {
            // $query->whereDate('created_at', '>=', $this->startDate);
            $date_too = date('Y-m-d', strtotime('+1 day', strtotime($this->date_to)));
            $query->where('created_at','>=',$this->date_from)->where('created_at','<=',$date_too);
        }

        if(!empty($this->product_plan_category_filter)){
            $product_plan_ids = ProductPlan::where('product_plan_category_id',$this->product_plan_category_filter)->pluck('id');
            $query->whereIn('product_plan_id',$product_plan_ids);
        }

        if(!empty($this->phone)){
            $query->where('phone_number',$this->phone);
        }



        // $data = Transaction::when(!empty($this->date_from) && !empty($this->date_to) , function ($query) use ($date_from,$date_to){
        //     $date_to = date('Y-m-d', strtotime('+1 day', strtotime($date_to)));
        //     $query->where('created_at','>=',$date_from)->where('created_at','<=',$date_to);
        // })->when(!empty($product_plan_category_filter) , function ($query) use ($product_plan_category_filter){
        //     $product_plan_ids = ProductPlan::where('product_plan_category_id',$product_plan_category_filter)->pluck('id');
        //     $query->whereIn('product_plan_id',$product_plan_ids);
        // })->when(!empty($phone) , function ($query) use ($phone){
        //   $query->where('phone_number',$phone);
        // })
        // ->with(['user','product_plan'])
        // ->where('wallet_category','!=','data_wallet')
        // ->where('user_id',auth()->id())
        // ->latest()->limit($limit)->get();

        $transactions = $query->with(['user','product_plan'])
        ->where('wallet_category','!=','data_wallet')
        ->where('user_id',auth()->id())
        ->latest()->paginate($this->perPage);

      
        // $site_primary_color =  AdminColorSetting::where('color_name','site_primary_color')->first();
        // $site_secondary_color = AdminColorSetting::where('color_name','site_secondary_color')->first();
        // $this->site_primary_colorr = $site_primary_color->color_value ?? (int) '90, 102, 241'; 
        // $this->site_secondary_colorr = $site_secondary_color->color_value ?? (int) '90, 102, 241'; 
       

        return view('livewire.transactions-table', [
            'transactions'=>$transactions,
            // 'site_primary_color' =>$this->site_primary_colorr,
            // 'site_secondary_color'=>$this->site_secondary_colorr
        ]);
    }
}
