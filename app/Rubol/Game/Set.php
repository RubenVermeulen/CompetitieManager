<?php

namespace Rubol\Game;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Set extends Eloquent
{
    /**
     * Fillable fields for set.
     *
     * @var array
     */
    protected $fillable = [
        'result_one',
        'result_two'
    ];

    /**
     * A set belongs to a match.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function match() {
        return $this->belongsTo('Rubol\Game\Match');
    }

    /**
     * Who won the set.
     *
     * True: home
     * False: visitors
     *
     * @return bool
     */
    public function won() {
        if ($this->match->game->home) {
            if ($this->result_one > $this->result_two) {
                return true;
            }
        }
        else {
            if ($this->result_one < $this->result_two) {
                return true;
            }
        }

        return false;
    }
}