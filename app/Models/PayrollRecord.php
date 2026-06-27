<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayrollRecord extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'paid_at' => 'date',
        ];
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
