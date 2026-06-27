<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WasteWithdrawal extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'withdrawal_date' => 'date',
            'amount' => 'decimal:2',
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
}
