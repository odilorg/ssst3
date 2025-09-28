<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'room_type_id',
        'cost_per_night',
        'hotel_id',
        'images',
        'image',
        'room_size',
    ];

    protected $casts = [
        'images' => 'array',
        'cost_per_night' => 'decimal:2',
        'room_size' => 'decimal:2',
    ];

    // Relationships
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenity');
    }

    public function hotelRooms()
    {
        return $this->hasMany(HotelRoom::class);
    }
}
