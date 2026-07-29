<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lokasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function User()
    {
        return $this->hasMany(User::class);
    }

    public function CreatedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function patrolis()
    {
        return $this->hasMany(Patroli::class, 'lokasi_id');
    }

    /**
     * Generate or retrieve QR token for this location
     */
    public function getOrCreateQrToken(): string
    {
        if (empty($this->qr_token)) {
            $token = Str::random(32);
            $this->update(['qr_token' => $token]);
        }
        return $this->qr_token;
    }
}
