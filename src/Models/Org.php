<?php


namespace TomatoPHP\FilamentIssues\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Livewire\Wireable;

class Org extends Model
{
    protected $table = 'git_orgs';

    protected $fillable = [
        'name',
        'last_update',
    ];

    /**
     * @return HasMany
     */
    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class, 'owner_id');
    }
}
