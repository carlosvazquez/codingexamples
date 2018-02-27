<?php

namespace Shoperti\Shipping\Bus\Handlers\Commands\Customer;

use Shoperti\Shipping\Bus\Commands\Customer\ChangeCustomerInformationCommand;
use Shoperti\Shipping\Bus\Events\Customer\CustomerWasChangedEvent;
use Shoperti\Shipping\Models\Customer;

/**
 * This is the update customer information command handler class.
 *
 * @author Carlos Vazquez <carlos@shoperti.com>
 */
class ChangeCustomerInformationCommandHandler
{
    /**
     * Handles the update Customer Information Command.
     *
     * @param \Shoperti\Shipping\Bus\Commands\Customer\ChangeCustomerInformationCommand $command
     *
     * @throws \Exception if unable to update any of the models
     *
     * @return \Shoperti\Shipping\Models\Customer
     */
    public function handle(ChangeCustomerInformationCommand $command)
    {
        $customer = $command->customer;
        $customer->id = $command->customer->id;
        $customer->account_id = $command->customer->account_id;
        $customer->live = $command->customer->live;
        $customer->name = $command->name;
        $customer->email = $command->email;

        if ($customer->addresses->contains($command->default_address)) {
            $customer->default_address = $command->default_address;
        }

        $customer->save();

        event(new CustomerWasChangedEvent($customer));

        return $customer->fresh(['addresses']);
    }
}
