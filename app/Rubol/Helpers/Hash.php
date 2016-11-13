<?php

namespace Rubol\Helpers;

class Hash 
{
    /**
     * Global config.
     *
     * @var
     */
    protected $config;

    /**
     * Instantiate config attribute.
     *
     * @param $config
     */
    public function __construct($config) {
        $this->config = $config;
    }

    /**
     * Hash a password.
     *
     * @param $password
     * @return string
     */
    public function password($password) {
        return password_hash(
            $password,
            $this->config->get('app.hash.algo'),
            ['cost' => $this->config->get('app.hash.cost')]
        );
    }

    /**
     * Check if a password matches the hash.
     *
     * @param $password
     * @param $hash
     * @return bool
     */
    public function passwordCheck($password, $hash) {
        return password_verify($password, $hash);
    }

    /**
     * Hash the input.
     *
     * @param $input
     * @return string
     */
    public function hash($input) {
        return hash('sha256', $input);
    }

    /**
     * Check if a hash matches.
     *
     * @param $known
     * @param $user
     * @return bool
     */
    public function hashCheck($known, $user) {
        if (function_exists('hash_equals')) {
            return hash_equals($known, $user);
        }
        else {
            return $known === $user;
        }

    }
}