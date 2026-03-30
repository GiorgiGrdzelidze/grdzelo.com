<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasPublishState
{
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            });
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && ($this->publish_at === null || $this->publish_at->lte(now()));
    }
}
