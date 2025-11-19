<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'citys'; // Pastikan sesuai dengan nama tabel di database
    protected $fillable = ['name'];

    public function locations()
    {
        return $this->hasMany(Location::class, 'city_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'city_id');
    }
}
