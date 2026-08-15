<?php
namespace App\Models;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
class OfficerAssignmentBatch extends Model {
    use HasUuids;
    protected $guarded = [];
    protected function casts(): array { return ['configuration' => 'array', 'total_customers' => 'integer']; }
    public function assignments() { return $this->hasMany(CustomerOfficerAssignment::class, 'batch_id'); }
}
