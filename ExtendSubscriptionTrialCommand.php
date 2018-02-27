<?php

namespace Shoperti\Stores\Bus\Commands\Store\Billing;

use Shoperti\Platform\Store\Store;
use Shoperti\Stores\Bus\Commands\BaseCommand;

/**
 * This is the extend subscription trial command class.
 *
 * @author Carlos Vazquez <carlos@shoperti.com>
 */
class ExtendSubscriptionTrialCommand extends BaseCommand
{
    /**
     * The store object.
     *
     * @var \Shoperti\Platform\Store\Store
     */
    protected $store;

    /**
     * The number of days to extend.
     *
     * @param int
     */
    public $daysToExtend;

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'daysToExtend' => 'nullable|int',
    ];

    /**
     * Creates a new extend subscription trial command.
     *
     * @param \Shoperti\Platform\Store\Store $store
     * @param int|null                       $daysToExtend
     *
     * @return void
     */
    public function __construct(Store $store, $daysToExtend = null)
    {
        $this->store = $store;
        $this->daysToExtend = $daysToExtend;
    }

    /**
     * Gets the store object.
     *
     * @return \Shoperti\Platform\Store\Store
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Gets the days to extend.
     *
     * @return int|null
     */
    public function getDaysToExtend()
    {
        return $this->daysToExtend;
    }
}
