<?php

namespace TomatoPHP\FilamentIssues\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Livewire\Wireable;

class Reaction extends Model
{
    protected $table = 'git_reactions';

    protected $fillable = [
        'content',
        'count',
        'emoji',
    ];

    public function toLivewire()
    {
        return [
            'content' => $this->content,
            'count' => $this->count,
            'emoji' => $this->emoji,
        ];
    }

    public static function fromLivewire($value)
    {
        return self::fromArray([
            'content' => $value['content'],
            'count' => $value['count'],
            'emoji' => $value['emoji'],
        ]);
    }

    public static function fromArray(array $reaction): self
    {
        return new self(...$reaction);
    }

    public static function multipleFromArray(array $labels): array
    {
        return Arr::map(
            $labels,
            static fn (array $label): Reaction => self::fromArray($label)
        );
    }
}
