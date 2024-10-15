<?php


namespace TomatoPHP\FilamentIssues\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Livewire\Wireable;

class Repository extends Model
{
    protected $table = 'git_repos';

    protected $fillable = [
        'owner_id',
        'name',
    ];

    protected $appends = [
        'repo'
    ];

    public function getRepoAttribute(): string
    {
        return $this->owner->name.'/'.$this->name;
    }

    /**
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Org::class, 'owner_id');
    }
}
