<?php

namespace Rubol\Validation;

use Violin\Violin;
use Rubol\User\User;
use Rubol\Helpers\Hash;
use Rubol\Player\Player;

class Validator extends  Violin
{
    /**
     * User instance.
     *
     * @var User
     */
    protected $user;

    /**
     * Hash instance.
     *
     * @var Hash
     */
    protected $hash;

    /**
     * Possible user instance of the authenticated user.
     *
     * @var null
     */
    protected $auth;

    /**
     * Player instance.
     *
     * @var User
     */
    protected $player;

    /**
     * Initiate the Validator instance.
     *
     * @param User $user
     * @param Hash $hash
     * @param null $auth
     * @param Player $player
     */
    public function __construct(User $user, Hash $hash, $auth = null, Player $player) {
        $this->user = $user;
        $this->hash = $hash;
        $this->auth = $auth;
        $this->player = $player;

        $this->addFieldMessages([
            'email' => [
                'uniqueEmail' => 'Het e-mailadres is al gebruikt.'
            ],
            'username' => [
                'uniqueUsername' => 'De gebruikersnaam is al gebruikt.'
            ],
            'membership_id' => [
                'uniqueMembershipId' => 'Het lidnummer is al in gebruik.'
            ],
            'password_confirm' => [
                'matches' => '{field} moet overeenkomen met wachtwoord.'
            ]
        ]);

        $this->addRuleMessages([
            'required' => '{field} is vereist.',
            'int' => '{field} moet een nummer zijn.',
            'min' => '{field} moet minimum uit {$0} tekens bestaan.',
            'max' => '{field} mag maximum uit {$0} tekens bestaan.',
            'between' => '{field} moet tussen {$0} en {$1} liggen.',
            'alnumDash' => '{field} mag enkel bestaan uit letters, cijfers, koppelteken en liggend streepje.',
            'email' => '{field} moet een geldig e-mailadres bevatten.',
            'matchesCurrentPassword' => 'Dit komt niet overeen met je huidig wachtwoord.',
            'resultFormat' => '{field} moet een geldig formaat hebben.'
        ]);
    }

    /**
     * Checks if the email is unique.
     *
     * @param $value
     * @param $input
     * @param $args
     * @return bool
     */
    public function validate_uniqueEmail($value, $input, $args) {
        $user = $this->user->where('email', $value);

        /*
         * Exclude email address.
         */
        if ( ! empty($args)) {

            $user->where('email', '!=', $args[0]);
        }

        return ! (bool) $user->count();
    }

    /**
     * Checks if the username is unique.
     *
     * @param $value
     * @param $input
     * @param $args
     * @return bool
     */
    public function validate_uniqueUsername($value, $input, $args) {
        $user = $this->user->where('username', $value);

        return ! (bool) $user->count();
    }

    /**
     * Checks if the current password matches the given one.
     *
     * @param $value
     * @param $input
     * @param $args
     * @return bool
     */
    public function validate_matchesCurrentPassword($value, $input, $args) {
        if ($this->auth && $this->hash->passwordCheck($value, $this->auth->password)) {
            return true;
        }

        return false;
    }

    public function validate_resultFormat($value, $input, $args) {
        if (empty($value)) {
            return true;
        }

        $pattern = '/^\d{1,2}\s\d{1,2}$/';

        if (preg_match($pattern, $value)) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the membership id is unique.
     *
     * @param $value
     * @param $input
     * @param $args
     * @return bool
     */
    public function validate_uniqueMembershipId($value, $input, $args) {
        $player = $this->player->where('membership_id', $value);

        return ! (bool) $player->count();
    }
}