<?php

namespace Rubol\Game;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Game extends Eloquent
{
    /**
     * Fillable fields for match.
     *
     * @var array
     */
    protected $fillable = [
        'competitor',
        'city',
        'zip_code',
        'street_name',
        'street_number',
        'home',
        'played_at'
    ];

    /**
     * Columns which should be Carbon instances.
     *
     * @var array
     */
    protected $dates = ['played_at'];

    /**
     * Get date for played at.
     *
     * @return mixed
     */
    public function getDatePlayedAt() {
        return $this->played_at->format('d/m/Y');
    }

    /**
     * Get time for played at.
     *
     * @return mixed
     */
    public function getTimePlayedAt() {
        return $this->played_at->format('H:i');
    }

    /**
     * Get uniform date for played at.
     *
     * @return mixed
     */
    public function getStandardDatePlayedAt() {
        return $this->played_at->format('Y-m-d');
    }

    public function getAddress() {
        return ($this->home) ? 'Thuis' : "{$this->street_name} {$this->street_number}, {$this->zip_code} {$this->city}";
    }

    /**
     * Get full textual representation of the day of the week.
     *
     * @return mixed
     */
    public function getDayPlayedAt() {
        return strftime('%A', $this->played_at->timestamp);
    }

    /**
     * Belongs to a season.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function season() {
        return $this->belongsTo('Rubol\Season\Season');
    }

    /**
     * Has many matches.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function matches() {
        return $this->hasMany('Rubol\Game\Match');
    }
}