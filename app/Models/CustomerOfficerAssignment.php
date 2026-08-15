<?php
namespace App\Models;
use App\Models\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
class CustomerOfficerAssignment extends Model {
    use HasUuids;
    protected $guarded = [];
    protected function casts(): array { return ['started_at' => 'datetime', 'ended_at' => 'datetime']; }
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function officer() { return $this->belongsTo(User::class, 'officer_id'); }
    public function batch() { return $this->belongsTo(OfficerAssignmentBatch::class, 'batch_id'); }
}
