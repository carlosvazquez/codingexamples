<?php

namespace Shoperti\Stores\Bus\Commands\User;

use Shoperti\Platform\Store\Store;
use Shoperti\Stores\Bus\Commands\BaseCommand;

/**
 * This is the add user command class.
 *
 * @author Carlos Vazquez <carlos@shoperti.com>
 * @author Joseph Cohen <joe@shoperti.com>
 */
class AddUserCommand extends BaseCommand
{
    /**
     * The store instance.
     *
     * @var \Shoperti\Platform\Store\Store
     */
    private $store;

    /**
     * The user first name.
     *
     * @var string
     */
    private $firstName;

    /**
     * The user last name.
     *
     * @var string
     */
    private $lastName;

    /**
     * The user email.
     *
     * @var string
     */
    private $email;

    /**
     * The user permissions.
     *
     * @var array
     */
    private $permissions;

    /**
     * The command rules.
     *
     * @var array
     */
    public $rules = [
        'firstName'    => 'nullable|string|max:45',
        'lastName'     => 'nullable|string|max:45',
        'email'        => 'required|email',
        'permissions'  => 'nullable|array',
    ];

    /**
     * Creates a new add user command instance.
     *
     * @param \Shoperti\Platform\Store\Store $store
     * @param string                         $firstName
     * @param string                         $lastName
     * @param string                         $email
     * @param bool                           $isFullAccess
     * @param array                          $permissions
     *
     * @return void
     */
    public function __construct(
        Store $store,
        $firstName,
        $lastName,
        $email,
        array $permissions
    ) {
        $this->store = $store;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->permissions = $permissions;
    }

    /**
     * Gets the store instance.
     *
     * @return \Shoperti\Platform\Store\Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Gets the user first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Gets the user last name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Gets the user email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Gets the user permissions.
     *
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}
