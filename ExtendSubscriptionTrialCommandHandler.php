<?php

namespace Shoperti\Stores\Bus\Handlers\Commands\Store\Billing;

use Carbon\Carbon;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Support\MessageBag;
use Shoperti\Platform\Core\Validation\ValidationException;
use Shoperti\Platform\Store\Billing\SubscriptionRepository;
use Shoperti\Platform\Store\Billing\SubscriptionStatusIdentity;
use Shoperti\Platform\Store\StoreRepository;
use Shoperti\Stores\Bus\Commands\Store\Billing\ExtendSubscriptionTrialCommand;
use Shoperti\Stores\Bus\Commands\Store\ReopenStoreCommand;
use Shoperti\Stores\Bus\Events\Store\StoreTrialWasExtendedEvent;

/**
 * The extend store subscription trial command handler.
 *
 * @author Carlos Vazquez <carlos@shoperti.com>
 * @author Arturo Rodríguez <arturo@shoperti.com>
 */
class ExtendSubscriptionTrialCommandHandler
{
    /**
     * The store repository instance.
     *
     * @var \Shoperti\Platform\Store\StoreRepository
     */
    private $storeRepository;

    /**
     * The store subscription repository instance.
     *
     * @var \Shoperti\Platform\Store\Billing\SubscriptionRepository
     */
    private $subscriptionRepository;

    /**
     * The Laravel bus dispatcher.
     *
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    private $busDispatcher;

    /**
     * The Laravel events dispatcher.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher
     */
    private $eventDispatcher;

    /**
     * Creates an extends store trial handler instance.
     *
     * @param \Shoperti\Platform\Store\StoreRepository                $storeRepository
     * @param \Shoperti\Platform\Store\Billing\SubscriptionRepository $subscriptionRepository
     * @param \Illuminate\Contracts\Bus\Dispatcher                    $busDispatcher
     * @param \Illuminate\Contracts\Events\Dispatcher                 $eventDispatcher
     *
     * @return void
     */
    public function __construct(
        StoreRepository $storeRepository,
        SubscriptionRepository $subscriptionRepository,
        BusDispatcher $busDispatcher,
        EventDispatcher $eventDispatcher
    ) {
        $this->storeRepository = $storeRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->busDispatcher = $busDispatcher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Handles the extend store trial command.
     *
     * @param \Shoperti\Stores\Bus\Commands\Store\Billing\ExtendSubscriptionTrialCommand $command
     *
     * @throws \InvalidArgumentException if unable to update store model
     *
     * @return \Shoperti\Platform\Store\Billing\Subscription
     */
    public function handle(ExtendSubscriptionTrialCommand $command)
    {
        /** @var \Shoperti\Platform\Store\Store $store */
        $store = $command->getStore();

        /** @var \Shoperti\Platform\Store\Billing\Subscription $subscription */
        $subscription = $store->subscription;

        if (!$subscription->app_plan->isTrial()) {
            throw new ValidationException(new MessageBag(['error' => 'El plan de tu tienda no admite la extension de días de prueba.']));
        }

        if ($subscription->trial_ends_at->gt(Carbon::now()->endOfDay())) {
            throw new ValidationException(new MessageBag(['error' => 'Tu tienda todavía tienes dias de prueba.']));
        }

        $usedTrialDays = $subscription->trial_starts_at->diffInDays($subscription->trial_ends_at);

        $daysToExtend = $command->getDaysToExtend();

        if (!$daysToExtend) {
            $maxTrialDays = 22;
            $daysToExtend = $usedTrialDays < 15 ? 7 : max(($maxTrialDays - $usedTrialDays), 0);
        }

        if ($daysToExtend < 1) {
            throw new ValidationException(new MessageBag(['error' => 'Tu periodo de prueba ya no es extendible.']));
        }

        $now = Carbon::now();

        $trialStartsAt = $now->copy()->subDays($usedTrialDays)->startOfDay();
        $trialEndsAt = $now->copy()->addDays($daysToExtend)->endOfDay();

        $subscription->status = SubscriptionStatusIdentity::ACTIVE;
        $subscription->trial_starts_at = $trialStartsAt->copy();
        $subscription->current_period_starts_at = $trialStartsAt->copy();
        $subscription->trial_ends_at = $trialEndsAt->copy();
        $subscription->current_period_ends_at = $trialEndsAt->copy();
        $subscription->ended_at = null;
        $subscription = $this->subscriptionRepository->save($subscription);

        $store = $this->busDispatcher->dispatch(new ReopenStoreCommand($store));

        $store->next_billing_at = $trialEndsAt->copy()->setTime(12, 0);
        $store = $this->storeRepository->save($store);

        $this->eventDispatcher->dispatch(new StoreTrialWasExtendedEvent($store));

        return $subscription;
    }
}
