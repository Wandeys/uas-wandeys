<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'nip', 'nidn', 'gelar'];

    protected $with = ['user'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the teacher's email from the associated user.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }
}
