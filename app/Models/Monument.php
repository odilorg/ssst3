<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monument extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ticket_price',
        'foreigner_adult_price',
        'foreigner_child_price',
        'local_adult_price',
        'local_child_price',
        'description',
        'city_id',
        'images',
        'company_id',
        'voucher',
    ];

    protected $casts = [
        'images' => 'array',
        'voucher' => 'boolean',
        'ticket_price' => 'decimal:2',
        'foreigner_adult_price' => 'decimal:2',
        'foreigner_child_price' => 'decimal:2',
        'local_adult_price' => 'decimal:2',
        'local_child_price' => 'decimal:2',
    ];

    // Relationships
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function contractServices()
    {
        return $this->morphMany(ContractService::class, 'serviceable');
    }
}
