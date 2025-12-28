<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function prices()
    {
        return $this->hasMany(ServicePrice::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'service_category_id');
    }
}
