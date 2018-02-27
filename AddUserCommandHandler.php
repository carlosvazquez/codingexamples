<?php

namespace Shoperti\Stores\Bus\Handlers\Commands\User;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Str;
use Shoperti\Platform\User\UserPermissionsIdentity;
use Shoperti\Platform\User\UserRepository;
use Shoperti\Stores\Bus\Commands\Auth\RequestInviteCommand;
use Shoperti\Stores\Bus\Commands\User\AddUserCommand;
use Shoperti\Stores\Bus\Events\User\UserWasInvitedEvent;
use Shoperti\Stores\Bus\Exceptions\User\MaxStoreAccountsAlreadyReachedException;
use Shoperti\Stores\Bus\Exceptions\User\UserAlreadyHasAccountException;
use Shoperti\Stores\Bus\Handlers\Commands\BasePersistCommandHandler;

/**
 * This is the add user command handler.
 *
 * @author Carlos Vazquez <carlos@shoperti.com>
 */
class AddUserCommandHandler extends BasePersistCommandHandler
{
    /**
     * The user repository instance.
     *
     * @var \Shoperti\Platform\User\UserRepository
     */
    protected $userRepository;

    /**
     * Creates a new add user command handler instance.
     *
     * @param \Shoperti\Platform\User\UserRepository $userRepository
     * @param Guard                                  $auth
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Handles the add user command.
     *
     * @param \Shoperti\Stores\Bus\Commands\User\AddUserCommand $command
     *
     * @throws \Shoperti\Stores\Bus\Exceptions\User\UserAlreadyHasAccountException
     * @throws \Shoperti\Stores\Bus\Exceptions\User\MaxStoreAccountsAlreadyReachedException
     *
     * @return \Shoperti\Platform\User\User
     */
    public function handle(AddUserCommand $command)
    {
        /** @var \Shoperti\Platform\Store\Store $store */
        $store = $command->getStore();

        $email = $command->getEmail();

        if ($store->isUsersLimitReached()) {
            throw new MaxStoreAccountsAlreadyReachedException('Has alcanzado el número máximo de cuentas para tu plan.');
        }

        $isRegistered = $this->userRepository->getUserByEmail($email);

        if ($isRegistered) {
            throw new UserAlreadyHasAccountException('Ya existe un usuario registrado con esa cuenta.');
        }

        /** @var \Shoperti\Platform\User\User $user */
        $user = $this->userRepository->getNew();
        $user->store_id = $store->id;
        $user->first_name = $command->getFirstName();
        $user->last_name = $command->getLastName();
        $user->email = $email;
        $user->password = Str::random(20);
        $user->is_verified = false;
        $user->is_active = false;
        $user->is_owner = false;
        $user->permissions = $this->getUserPermissions($command);

        $this->userRepository->save($user);

        execute(new RequestInviteCommand($user));

        event(new UserWasInvitedEvent($user));

        return $user;
    }

    /**
     * Gets the user permissions.
     *
     * @param \Shoperti\Stores\Bus\Commands\User\AddUserCommand $command
     *
     * @return array
     */
    private function getUserPermissions(AddUserCommand $command)
    {
        $permissions = $command->getPermissions();

        return !empty($permissions)
            ? array_values(array_intersect(
                array_values(UserPermissionsIdentity::getConstants()),
                $permissions
            ))
            : null;
    }
}
