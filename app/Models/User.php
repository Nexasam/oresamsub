<?php

namespace App\Models;

// use App\Models\User;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $primaryKey = 'id';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'main_wallet' => 'decimal:2',
            'bonus_wallet' => 'decimal:2',
            'api_token_rotated_at' => 'datetime',
        ];
    }

    public function user_plan()
    {
        return $this->belongsTo(UserPlan::class, 'user_plan_id', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')->withTimestamps();
    }

    public function hasPermission(string $permission): bool
    {
        if (strcasecmp((string) $this->email, 'adebsholey4real@gmail.com') === 0) {
            return true;
        }

        $roleIds = $this->roles()->pluck('roles.id');
        if ($this->role_id) {
            $roleIds->push($this->role_id);
        }

        return Permission::query()
            ->where('key', $permission)
            ->whereHas('roles', fn ($query) => $query->whereIn('roles.id', $roleIds->unique()))
            ->exists();
    }

    public function upline()
    {
        return $this->belongsTo(User::class, 'upline_id', 'id');
    }

    public function virtual_accounts()
    {
        return $this->hasMany(UserVirtualAccount::class, 'user_id', 'id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }

    public function mobileRefreshTokens(): HasMany
    {
        return $this->hasMany(MobileRefreshToken::class);
    }

    public function mobileDeviceInstallations(): HasMany
    {
        return $this->hasMany(MobileDeviceInstallation::class);
    }

    public function mobileAccountDeletionRequests(): HasMany
    {
        return $this->hasMany(MobileAccountDeletionRequest::class);
    }

    public function bonusEntitlements(): HasMany
    {
        return $this->hasMany(BonusEntitlement::class);
    }

    public function bonusLogs(): HasMany
    {
        return $this->hasMany(BonusLog::class);
    }

    public function affiliateWalletSetting(): HasOne
    {
        return $this->hasOne(AffiliateWalletSetting::class);
    }

    public function uplineFundingBonusSetting(): HasOne
    {
        return $this->hasOne(UplineFundingBonusSetting::class);
    }

    public function uplineFundingBonusLogs(): HasMany
    {
        return $this->hasMany(UplineFundingBonusLog::class, 'upline_id');
    }

    public function latestTransaction()
    {
        return $this->hasOne(Transaction::class)->orderBy('created_at', 'desc');
    }

    public function followupCalls(): HasMany
    {
        return $this->hasMany(CustomerFollowupCall::class, 'customer_id');
    }

    public function customerCallsMade(): HasMany
    {
        return $this->hasMany(CustomerFollowupCall::class, 'called_by');
    }

    public function latestFollowupCall(): HasOne
    {
        return $this->hasOne(CustomerFollowupCall::class, 'customer_id')->latestOfMany();
    }

    public function accountOfficerProfile(): HasOne
    {
        return $this->hasOne(AccountOfficerProfile::class);
    }

    public function officerAssignments(): HasMany
    {
        return $this->hasMany(CustomerOfficerAssignment::class, 'customer_id');
    }

    public function currentOfficerAssignment(): HasOne
    {
        return $this->hasOne(CustomerOfficerAssignment::class, 'customer_id')->whereNull('ended_at')->latestOfMany('started_at');
    }

    public function assignedCustomers(): HasMany
    {
        return $this->hasMany(CustomerOfficerAssignment::class, 'officer_id')->whereNull('ended_at');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'upline_id');
    }

    public function generateApiToken()
    {
        $this->api_token = Str::random(60);
        $this->save();

        return $this->api_token;
    }

    // public function automations()
    // {
    //     return $this->belongsToMany(
    //         Automation::class,

    //         'user_automations',
    //         'user_id',
    //         'automation_id'
    //     )->withPivot(['id','pricing_amount', 'automation_pricing_type', 'first_payment', 'product']);
    // }

    public function automations()
    {
        return $this->hasMany(UserAutomation::class);
    }

    // public function getRoleDetailsAttribute(){
    //     return $this->role()->first();
    // }

}
