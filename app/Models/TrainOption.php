<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'train_id',
        'train_seats_count_x',
        'train_seats_count_y',
        'avalilable_class'
    ];

}
