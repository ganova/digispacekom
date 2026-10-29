<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceTicket extends Model
{
    protected $fillable = ['code', 'customer_name', 'device', 'complaint'];

    public function events(): HasMany
    {
        return $this->hasMany(ServiceTicketEvent::class)->orderBy('sort');
    }

    /** Nama pelanggan disamarkan untuk tampilan publik: "Budi S****" */
    public function maskedName(): string
    {
        return collect(explode(' ', $this->customer_name))
            ->map(fn ($part, $i) => $i === 0 ? $part : mb_substr($part, 0, 1).str_repeat('*', max(mb_strlen($part) - 1, 2)))
            ->implode(' ');
    }
}
