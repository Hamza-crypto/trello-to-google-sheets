<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookTask extends Model
{
    use HasFactory;
    protected $fillable = [
        'webhook_card_id',
        'webhook_card_name',
        'status',
    ];
}
