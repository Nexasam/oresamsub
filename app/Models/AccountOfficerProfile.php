<?php
namespace App\Models;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
class AccountOfficerProfile extends Model {
    use HasUuids;
    protected $guarded = [];
    protected function casts(): array { return ['is_active' => 'boolean', 'allocation_weight' => 'decimal:2']; }
    public function user() { return $this->belongsTo(User::class); }
}
