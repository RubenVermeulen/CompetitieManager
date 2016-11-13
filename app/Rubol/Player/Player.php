<?php

namespace Rubol\Player;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Player extends Eloquent
{
    /**
     * Fillable fields for player.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'telephone',
        'membership_id',
        'level_single',
        'level_double',
        'profile_url',
        'sm',
        'dm',
        'dx',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Return an avatar URL.
     *
     * @param array $options
     * @return string
     */
    public function getAvatarUrl($options = []) {
        $size = isset($options['size']) ? $options['size'] : 45;

        return 'http://www.gravatar.com/avatar/' . md5($this->email) . '?s=' . $size . '&d=identicon';
    }

    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Has many match and player.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function matches() {
        return $this->belongsToMany('Rubol\Game\Match');
    }

    public function totalMatches() {
        return $this->matches->count();
    }

    public function totalMatchesWon() {
        $count = 0;

        foreach ($this->matches as $match) {
            if ($match->won()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Count total points for the given type of match.
     *
     * @param $type
     * @return int
     */
    public function totalPoints($type) {
        $count = 0;

        foreach ($this->matches as $match) {
            /*
             * Is seasonId set or does the match belongs to the season.
             * And is a match played.
             */
            if ($match->played()) {
                /*
                 * Is the match of the type single
                 */
                if ($match->type === $type) {
                    /*
                     * Is the match played home, if so the player scores is result one, otherwise it's result two.
                     */
                    if ($match->game->home) {
                        $count += $match->totalPointsResultOne();
                    }
                    else {
                        $count += $match->totalPointsResultTwo();
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Count total points for single matches.
     *
     * @return int
     */
    public function totalPointsSingle() {
        return $this->totalPoints(1);
    }

    /**
     * Count total points for double matches.
     *
     * @return int
     */
    public function totalPointsDouble() {
        return $this->totalPoints(2);
    }

    /**
     * Count won matches for single matches.
     *
     * @return int
     */
    public function matchesWonSingle() {
        $count = 0;

        foreach ($this->matches as $match) {
            if ($match->type === 1 && $match->played() && $match->won()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Count played matches for single matches.
     *
     * @return int
     */
    public function matchesPlayedSingle() {
        $count = 0;

        foreach ($this->matches as $match) {
            if ($match->type === 1 && $match->played()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Calculate points per set for single matches.
     *
     * @return float|int
     */
    public function pointsPerMatchSingle() {
        $matches = $this->matchesPlayedSingle();

        if ($matches !== 0) {
            return round($this->totalPointsSingle() / $matches);
        }

        return 0;
    }

    /**
     * Count all sets for single matches.
     *
     * @return int
     */
    public function totalSetsSingle() {
        $count = 0;

        foreach ($this->matches as $match) {
            if ($match->type === 1 && $match->played()) {
                $count += $match->totalSets();
            }
        }

        return $count;
    }

    /**
     * Calculate the average points per set for single matches.
     *
     * @return float|null
     */
    public function pointsPerSetSingle() {
        $sets = $this->totalSetsSingle();

        if ($sets !== 0) {
            return round($this->totalPointsSingle() / $sets);
        }

        return 0;
    }

    /**
     * Calculate the win percentage for single matches.
     *
     * @return float|null
     */
    public function winPercentageSingle() {
        $played = $this->matchesPlayedSingle();

        if ($played !== 0) {
            return round($this->matchesWonSingle() / $this->matchesPlayedSingle() * 100);
        }

        return 0;
    }

    /**
     * Calculate how much points the two players have gathered together.
     *
     * @param $otherPlayer
     * @return int
     */
    public function totalPointsDoubleWithOtherPlayer($otherPlayer) {
        $count = 0;

        foreach ($this->matches as $match) {
            /*
             * Check if match is of type 2 and match is played.
             */
            if ($match->type === 2 && $match->played()) {
                if ($match->players[0]->id === $otherPlayer->id || $match->players[1]->id === $otherPlayer->id) {
                    /*
                     * Is the match played home, if so the player scores is result one, otherwise it's result two.
                     */
                    if ($match->game->home) {
                        $count += $match->totalPointsResultOne();
                    }
                    else {
                        $count += $match->totalPointsResultTwo();
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Calculate how many matches two players played together.
     *
     * @param $otherPlayer
     * @return int
     */
    public function totalMatchesDoubleWithOtherPlayer($otherPlayer) {
        $count = 0;

        foreach ($this->matches as $match) {
            /*
             * Check if match is of type 2 and match is played.
             */
            if ($match->type === 2 && $match->played()) {
                if ($match->players[0]->id === $otherPlayer->id || $match->players[1]->id === $otherPlayer->id) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Calculate how many points per match the two players have together.
     *
     * @param $otherPlayer
     * @return float|int
     */
    public function pointsPerMatchDoubleWithOtherPlayer($otherPlayer) {
        $matches = $this->totalMatchesDoubleWithOtherPlayer($otherPlayer);

        if ($matches !== 0) {
            return round($this->totalPointsDoubleWithOtherPlayer($otherPlayer) / $matches);
        }

        return 0;
    }

    /**
     * Calculate how many sets the two players have together.
     *
     * @param $otherPlayer
     * @return int
     */
    public function setsWithOtherPlayer($otherPlayer) {
        $count = 0;

        foreach ($this->matches as $match) {
            /*
             * Check if match is of type 2 and match is played.
             */
            if ($match->type === 2 && $match->played()) {
                if ($match->players[0]->id === $otherPlayer->id || $match->players[1]->id === $otherPlayer->id) {
                    $count += $match->totalSets();
                }
            }
        }

        return $count;
    }

    /**
     * Calculate how many points per set the two players have together.
     *
     * @param $otherPlayer
     * @return float|int
     */
    public function pointsPerSetWithOtherPlayer($otherPlayer) {
        $sets = $this->setsWithOtherPlayer($otherPlayer);

        if ($sets !== 0) {
            return round($this->totalPointsDoubleWithOtherPlayer($otherPlayer) / $sets);
        }

        return 0;
    }

    /**
     * Calculate how many matches the two players have won together.
     *
     * @param $otherPlayer
     * @return int
     */
    public function wonWithOtherPlayer($otherPlayer) {
        $count = 0;

        foreach ($this->matches as $match) {
            /*
             * Check if match is of type 2 and match is played.
             */
            if ($match->type === 2 && $match->played() && $match->won()) {
                if ($match->players[0]->id === $otherPlayer->id || $match->players[1]->id === $otherPlayer->id) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Calculate win percentage for the two players.
     *
     * @param $otherPlayer
     * @return float|int
     */
    public function winPercentageWithOtherPlayer($otherPlayer) {
        $matches = $this->totalMatchesDoubleWithOtherPlayer($otherPlayer);

        if ($matches !== 0) {
            return round($this->wonWithOtherPlayer($otherPlayer) / $matches * 100);
        }

        return 0;
    }

    /**
     * Won sets single.
     *
     * @return int
     */
    public function setsWonSingle() {
        $count = 0;

        foreach ($this->matches as $match) {
            /*
             * Has the match been played and is it from the type single.
             */
            if ($match->played() && $match->type === 1) {
                $count += $match->setsWon();
            }
        }

        return $count;
    }

    /**
     * Won sets double.
     *
     * @param $otherPlayer
     * @return int
     */
    public function setsWonWithOtherPlayer($otherPlayer) {
        $count = 0;

        foreach ($this->matches as $match) {
            /*
             * Has the match been played and is it from the type double.
             */
            if ($match->played() && $match->type === 2) {
                /*
                 * Filter for the correct player.
                 */
                if ($match->players[0]->id === $otherPlayer->id || $match->players[1]->id === $otherPlayer->id) {
                    $count += $match->setsWon();
                }
            }
        }

        return $count;
    }

    /**
     * Calculate win percentage single.
     *
     * @return float|int
     */
    public function setsWinPercentageSingle() {
        $sets = $this->totalSetsSingle();

        if ($sets !== 0) {
            return round($this->setsWonSingle() / $sets * 100);
        }

        return 0;
    }

    /**
     * Calculate win percentage for two players.
     *
     * @return float|int
     */
    public function setsWinPercentageWithOtherPlayer($otherPlayer) {
        $sets = $this->setsWithOtherPlayer($otherPlayer);

        if ($sets !== 0) {
            return round($this->setsWonWithOtherPlayer($otherPlayer) / $sets * 100);
        }

        return 0;
    }

    /**
     * Get ranking determined by type parameter.
     *
     * @param $type
     * @return mixed|string
     */
    public function getRanking($type) {
        return (! is_null($this->{$type}) && ! empty($this->{$type})) ? $this->{$type} : 'Niet gekend';
    }

    /**
     * Get ranking single men.
     *
     * @return mixed|string
     */
    public function getSm() {
        return $this->getRanking('sm');
    }

    /**
     * Get ranking double men.
     *
     * @return mixed|string
     */
    public function getDm() {
        return $this->getRanking('dm');
    }

    /**
     * Get ranking double mix.
     *
     * @return mixed|string
     */
    public function getDx() {
        return $this->getRanking('dx');
    }
}