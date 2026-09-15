<?php

namespace TomatoPHP\FilamentIssues\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Org extends Model
{
    protected $table = 'git_orgs';

    protected $fillable = [
        'name',
        'last_update',
    ];

    public function repositories(): HasMany
    {
        return $this->hasMany(Repository::class, 'owner_id');
    }
}
