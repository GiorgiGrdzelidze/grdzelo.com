<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'replied_at' => 'datetime',
        ];
    }

    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeNotSpam($query)
    {
        return $query->where('status', '!=', 'spam');
    }

    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }

    public function markAsReplied(): void
    {
        $this->update([
            'status' => 'replied',
            'replied_at' => now(),
        ]);
    }

    public function markAsSpam(): void
    {
        $this->update(['status' => 'spam']);
    }
}
