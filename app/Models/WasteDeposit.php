<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteDeposit extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'deposit_date' => 'date',
            'weight' => 'decimal:2',
            'price_per_unit' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function wasteType()
    {
        return $this->belongsTo(WasteType::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
