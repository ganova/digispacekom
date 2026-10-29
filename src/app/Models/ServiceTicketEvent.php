<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTicketEvent extends Model
{
    protected $fillable = ['title', 'note', 'occurred_at', 'state', 'sort'];

    protected function casts(): array
    {
        return ['occurred_at' => 'datetime'];
    }
}
