<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plantd extends Model
{
    use HasFactory;

    protected $table = 'plantd';

    protected $fillable = [
        'Pemilik_lahan',
        'Luas_lahan',
    ];
}
