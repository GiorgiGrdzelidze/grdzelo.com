<?php

declare(strict_types=1);

namespace App\Support;

use Tiptap\Editor;

class Tiptap
{
    /**
     * Convert a content value to safe HTML for rendering with v-html.
     *
     * Why: legacy rows store TipTap JSON documents in plain text columns
     * (the admin form was switched from RichEditor to Textarea, but historical
     * payloads remain). Pre-existing HTML rows must pass through untouched.
     *
     * - null / empty → null
     * - String starting with `{` and parsing to a TipTap doc → rendered HTML
     * - Anything else → returned verbatim (assumed already HTML)
     */
    public static function toHtml(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        if ($trimmed[0] !== '{') {
            return $value;
        }

        $decoded = json_decode($trimmed, true);
        if (! is_array($decoded) || ($decoded['type'] ?? null) !== 'doc') {
            return $value;
        }

        return (new Editor)->setContent($decoded)->getHTML();
    }
}
