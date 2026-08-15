<?php
namespace App\Http\Controllers;
use App\Models\AccountOfficerProfile;
use App\Models\CustomerOfficerAssignment;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Notifications\CustomersAssignedNotification;
use App\Services\AccountOfficers\WeightedCustomerAllocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class AccountOfficerController extends Controller {
    public function index() {
        $profiles = AccountOfficerProfile::with(['user' => fn ($query) => $query->withCount('assignedCustomers')])->get();
        $eligibleUsers = User::whereDoesntHave('accountOfficerProfile')->whereHas('role', fn ($query) => $query->where('role_name', '!=', 'User'))->where('email', '!=', 'adebsholey4real@gmail.com')->orderBy('first_name')->get();
        $unassignedCount = User::whereHas('role', fn ($q) => $q->where('role_name', 'User'))
            ->whereDoesntHave('officerAssignments', fn ($q) => $q->whereNull('ended_at'))->count();
        return view('admin.account_officers.index', compact('profiles', 'eligibleUsers', 'unassignedCount'));
    }

    public function store(Request $request) {
        $data = $request->validate(['user_id' => ['required', 'exists:users,id', 'unique:account_officer_profiles,user_id'], 'allocation_weight' => ['required', 'numeric', 'min:0', 'max:100']]);
        abort_if(User::findOrFail($data['user_id'])->role?->role_name === 'User', 422, 'A customer account cannot be made an account officer.');
        $role = Role::firstOrCreate(['role_name' => 'Account Officer']);
        $defaultPermissions = collect(['followups.view_assigned', 'followups.log_call', 'followups.view_performance'])->map(function ($key) {
            return Permission::firstOrCreate(['key' => $key], ['name' => str($key)->after('.')->replace('_', ' ')->title(), 'group' => 'followups'])->id;
        });
        $role->accessPermissions()->syncWithoutDetaching($defaultPermissions);
        User::findOrFail($data['user_id'])->roles()->syncWithoutDetaching([$role->id]);
        AccountOfficerProfile::create($data + ['is_active' => true, 'updated_by' => $request->user()->id]);
        return back()->with('success', 'Account officer added.');
    }

    public function update(Request $request) {
        $data = $request->validate(['officers' => ['required', 'array'], 'officers.*.weight' => ['required', 'numeric', 'min:0', 'max:100'], 'officers.*.active' => ['nullable', 'boolean']]);
        DB::transaction(function () use ($data, $request) {
            foreach ($data['officers'] as $id => $values) AccountOfficerProfile::findOrFail($id)->update(['allocation_weight' => $values['weight'], 'is_active' => (bool) ($values['active'] ?? false), 'updated_by' => $request->user()->id]);
        });
        return back()->with('success', 'Officer settings updated.');
    }

    public function allocate(Request $request, WeightedCustomerAllocator $allocator) {
        try { $batch = $allocator->allocate($request->user()); }
        catch (\InvalidArgumentException $e) { throw ValidationException::withMessages(['allocation' => $e->getMessage()]); }
        $this->notifyBatch($batch);
        return back()->with('success', "{$batch->total_customers} unassigned customers allocated.");
    }

    public function redistribute(Request $request, AccountOfficerProfile $profile, WeightedCustomerAllocator $allocator) {
        abort_if($profile->is_active, 422, 'Deactivate the officer before redistribution.');
        try { $batch = DB::transaction(function () use ($profile, $allocator, $request) {
            CustomerOfficerAssignment::where('officer_id', $profile->user_id)->whereNull('ended_at')->update(['ended_at' => now(), 'updated_at' => now()]);
            return $allocator->allocate($request->user(), 'redistribution');
        }); }
        catch (\InvalidArgumentException $e) { throw ValidationException::withMessages(['allocation' => $e->getMessage()]); }
        $this->notifyBatch($batch);
        return back()->with('success', "{$batch->total_customers} customers redistributed.");
    }

    private function notifyBatch($batch): void {
        $batch->assignments()->selectRaw('officer_id, COUNT(*) as aggregate')->groupBy('officer_id')->get()->each(function ($row) {
            Notification::send(User::find($row->officer_id), new CustomersAssignedNotification((int) $row->aggregate));
        });
    }
}
