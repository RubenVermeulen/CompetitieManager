<?php

namespace Rubol\User;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    /**
     * Fillable fields for user.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'username',
        'first_name',
        'last_name',
        'password',
        'active',
        'active_hash',
        'recover_hash',
        'remember_identifier',
        'remember_token'
    ];

    /**
     * Return the full name.
     *
     * @return null|string
     */
    public function getFullName() {
        if( ! $this->first_name || ! $this->last_name) {
            return null;
        }
        else {
            return "{$this->first_name} {$this->last_name}";
        }
    }

    /**
     * Return the full name or username.
     *
     * @return mixed
     */
    public function getFullNameOrUsername() {
        return $this->getFullName() ?: $this->username;
    }

    /**
     * Activate a user.
     */
    public function activateAccount($password) {
        $this->update([
            'password' => $password,
            'active' => true,
            'active_hash' => null
        ]);
    }

    /**
     * Return a avatart URL.
     *
     * @param array $options
     * @return string
     */
    public function getAvatarUrl($options = []) {
        $size = isset($options['size']) ? $options['size'] : 45;

        return 'https://secure.gravatar.com/avatar/' . md5($this->email) . '?s=' . $size . '&d=identicon';
    }

    /**
     * Update the remember credentials.
     *
     * @param $identifier
     * @param $token
     */
    public function updateRememberCredentials($identifier, $token) {
        $this->update([
            'remember_identifier' => $identifier,
            'remember_token' => $token
        ]);
    }

    /**
     * Remove the remember credentials.
     */
    public function removeRememberCredentials() {
        $this->updateRememberCredentials(null, null);
    }

    /**
     * Checks whether the identifier already exists or not.
     *
     * @param $identifier
     * @return bool
     */
    public function rememberIdentifierExists($identifier) {
        $users = new User();

        return $users->where('remember_identifier', $identifier)->count() > 0;
    }

    /**
     * Checks if the user has a certain permissions.
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission) {
        return (bool) $this->permissions->{$permission};
    }

    /**
     * Is a user an admin.
     *
     * @return bool
     */
    public function isAdmin() {
        return $this->hasPermission('is_admin');
    }

    /**
     * Link a permissions record to a user instance in the database.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function permissions() {
        return $this->hasOne('Rubol\User\UserPermission', 'user_id');
    }
}