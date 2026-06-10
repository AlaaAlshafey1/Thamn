<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArbitratorDeclaration extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'full_name',
        'national_id',
        'phone',
        'email',
        'nationality',
        'city',
        'expertise',
        'signature',
        'pdf_path',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSigned(): bool
    {
        return !is_null($this->signed_at);
    }
}
