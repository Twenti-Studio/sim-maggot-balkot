<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteType extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'price_per_unit' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function deposits()
    {
        return $this->hasMany(WasteDeposit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
