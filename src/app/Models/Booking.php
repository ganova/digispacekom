<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const STATUSES = [
        'baru' => 'Baru',
        'dihubungi' => 'Sudah dihubungi',
        'selesai' => 'Selesai',
        'batal' => 'Batal',
    ];

    protected $fillable = ['type', 'status', 'source', 'name', 'organization', 'device', 'complaint', 'location', 'units', 'admin_note'];
}
