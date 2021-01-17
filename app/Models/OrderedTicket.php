<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderedTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'order_id'
    ];


    public function locations() {
    	return $this->hasMany(OrderedTicketLocation::class);
    }

}
