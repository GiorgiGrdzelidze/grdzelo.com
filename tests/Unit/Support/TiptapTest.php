<?php

declare(strict_types=1);

use App\Support\Tiptap;

it('returns null for null input', function () {
    expect(Tiptap::toHtml(null))->toBeNull();
});

it('returns null for empty / whitespace-only input', function () {
    expect(Tiptap::toHtml(''))->toBeNull();
    expect(Tiptap::toHtml('   '))->toBeNull();
});

it('passes plain text through unchanged', function () {
    expect(Tiptap::toHtml('Just a sentence.'))->toBe('Just a sentence.');
});

it('passes pre-existing HTML through unchanged', function () {
    $html = '<p>Already <strong>HTML</strong>.</p>';
    expect(Tiptap::toHtml($html))->toBe($html);
});

it('converts a TipTap doc with a single paragraph to HTML', function () {
    $json = json_encode([
        'type' => 'doc',
        'content' => [[
            'type' => 'paragraph',
            'content' => [['type' => 'text', 'text' => 'Hello world']],
        ]],
    ]);

    expect(Tiptap::toHtml($json))->toBe('<p>Hello world</p>');
});

it('converts a TipTap doc with headings and paragraphs', function () {
    $json = json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'heading',
                'attrs' => ['level' => 3],
                'content' => [['type' => 'text', 'text' => 'Discovery']],
            ],
            [
                'type' => 'paragraph',
                'content' => [['type' => 'text', 'text' => 'Mapped every endpoint.']],
            ],
        ],
    ]);

    expect(Tiptap::toHtml($json))->toBe('<h3>Discovery</h3><p>Mapped every endpoint.</p>');
});

it('passes JSON-looking but non-doc payloads through unchanged', function () {
    $notADoc = '{"type":"image","src":"x.png"}';
    expect(Tiptap::toHtml($notADoc))->toBe($notADoc);
});

it('passes malformed JSON through unchanged', function () {
    $malformed = '{not really json';
    expect(Tiptap::toHtml($malformed))->toBe($malformed);
});
