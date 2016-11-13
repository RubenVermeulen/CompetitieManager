<?php

namespace Rubol\Game;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Match extends Eloquent
{
    /**
     * Fillable fields for match.
     *
     * @var array
     */
    protected $fillable = [
        'type'
    ];

    /**
     * Belongs to a game.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game() {
        return $this->belongsto('Rubol\Game\Game');
    }

    /**
     * Has many match and player.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function players() {
        return $this->belongsToMany('Rubol\Player\Player')->withPivot('id');
    }

    /**
     * A match has many sets.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sets() {
        return $this->hasMany('Rubol\Game\Set');
    }

    /**
     * Count all sets where there is a result.
     * Doing it this way will result in a significantly drop in queries.
     *
     * @return mixed
     */
    public function setsCount() {
        return $this->sets()
            ->whereNotNull('result_one')
            ->selectRaw('match_id, COUNT(*) as aggregate')
            ->groupBy('match_id');
    }

    /**
     * Did the team won the match.
     *
     * @return bool
     */
    public function won() {
        $count = 0;

        /*
         * Count how many times result one is bigger than result two.
         */
        foreach ($this->sets as $set) {
            /*
             * First check if the set has results.
             */
            if ($set->result_one) {
                if ($set->result_one > $set->result_two) {
                    $count++;
                }
            }
        }

        if ($this->game->home) { // Home
            if ($count === 2) {
                return true;
            }
        }
        else { // Not home
            if ($count <= 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Count the total points for result one column.
     *
     * @return mixed
     */
    public function totalPointsResultOne() {
        return $this->sets->sum('result_one');
    }

    /**
     * Count the total points for result two column.
     *
     * @return mixed
     */
    public function totalPointsResultTwo() {
        return $this->sets->sum('result_two');
    }

    /**
     * Return all points made in the match.
     *
     * @return mixed
     */
    public function totalPoints() {
        return $this->totalPointsResultOne() + $this->totalPointsResultTwo();
    }

    /**
     * Has the match been played.
     *
     * @return bool
     */
    public function played() {
        return (count($this->sets) > 0) ? true : false;
    }

    /**
     * Return total sets.
     *
     * @return mixed
     */
    public function totalSets() {
        return $this->setsCount->first()->aggregate;
    }

    /**
     * Won sets.
     *
     * @return int
     */
    public function setsWon() {
        $count = 0;

        foreach ($this->sets as $set) {
            /*
             * If the set is won increment count.
             */
            if ($set->won()) {
                $count++;
            }
        }

        return $count;
    }
}