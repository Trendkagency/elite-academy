<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use SoftDeletes;

    protected $table = 'contact_messages';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];
}
