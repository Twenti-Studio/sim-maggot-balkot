<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['purchased_at' => 'date'];
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function maintenances()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}
