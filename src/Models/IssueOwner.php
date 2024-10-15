<?php

declare(strict_types=1);

namespace TomatoPHP\FilamentIssues\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Wireable;

class IssueOwner extends Model
{
    protected $table = 'git_issue_owners';

    protected $fillable = [
        'name',
        'url',
        'profilePictureUrl',
    ];

    /**
     * @return HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function toLivewire()
    {
        return [
            'name' => $this->name,
            'url' => $this->url,
            'profilePictureUrl' => $this->profilePictureUrl,
        ];
    }

    public static function fromLivewire($value)
    {
        return self::fromArray([
            'name' => $value['name'],
            'url' => $value['url'],
            'profilePictureUrl' => $value['profilePictureUrl'],
        ]);
    }

    public static function fromArray(array $ownerDetails): self
    {
        return new self(...$ownerDetails);
    }
}
