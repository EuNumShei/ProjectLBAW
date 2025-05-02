<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupUser extends Model
{
    use HasFactory;

    protected $table = 'group_users';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'group_id',
        'created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}