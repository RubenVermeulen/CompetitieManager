<?php

namespace Rubol\Season;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Season extends Eloquent
{
    /**
     * Fillable fields for season.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Games belonging to the season.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function games() {
        return $this->hasMany('Rubol\Game\Game');
    }
}