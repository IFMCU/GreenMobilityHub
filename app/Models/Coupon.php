<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuid;
use Google\Service\DriveActivity\User;

class Coupon extends Model
{
    use HasFactory, Uuid;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'guid';
    // protected $table = 'merchant_locations';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'user_guid',
        'offer_guid', 
        'code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
        // 'status' => StatusEnum::class
    ];

    /**
     * USER OBJECT
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'guid');
    }

     /**
     * OFFER OBJECT
     */
    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_guid', 'guid');
    }
}
