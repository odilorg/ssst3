<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monument extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'ticket_price',
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
}
