<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens, HasUuids;

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

    protected $primaryKey='id';

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
        ];
    }

    // public function generateTwoFactorCode(): void
    // {
    //     $this->timestamps = false;  // Prevent updating the 'updated_at' column
    //     $this->two_factor_code = rand(100000, 999999);  // Generate a random code
    //     $this->two_factor_expires_at = now()->addMinutes(10);  // Set expiration time
    //     $this->save();
    // }

    public function user_plan(){
        return $this->belongsTo(UserPlan::class,'user_plan_id','id');
    }

    public function role(){
        return $this->belongsTo(Role::class,'role_id','id');
    }

   

    

    // public function getRoleDetailsAttribute(){
    //     return $this->role()->first();
    // }

    
}
