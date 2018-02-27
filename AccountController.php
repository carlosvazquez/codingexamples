<?php

namespace Shoperti\Stores\Http\Controllers\Store\Front;

use Illuminate\Support\Facades\Auth;
use Shoperti\Platform\Core\Repository\SearchableTrait;
use Shoperti\Platform\Purchase\Order\OrderRepository;

/**
 * The Account controller class.
 *
 * @author Carlos Vazquez <carlos@shoperti.com>
 */
class AccountController extends BaseController
{
    use SearchableTrait;

    /**
     * Gets all customer orders.
     *
     * @method GET
     *
     * @return \Illuminate\Http\Response
     */
    public function account()
    {
        $this->globalData['page_title'] = 'Cuenta';

        /** @var \Shoperti\Platform\Store\Store $store */
        $store = $this->getContext('store')->model();

        /** @var \Shoperti\Platform\Customer\Customer $customer */
        $customer = Auth::guard('customers')->user();

        return $this->render('pages/customers/account', [
            'customer' => $customer,
        ]);
    }

    /**
     * Gets one customer order by id.
     *
     * @method GET
     *
     * @param \Shoperti\Platform\Purchase\Order\OrderRepository $orderRepository
     * @param string                                            $id
     *
     * @return \Illuminate\Http\Response
     */
    public function order(OrderRepository $orderRepository, $id)
    {
        $this->globalData['page_title'] = 'Cuenta';

        $customer = Auth::guard('customers')->user();

        /** @var \Shoperti\Platform\Purchase\Order\Order $order */
        $order = $orderRepository->searchOneBy([
            ['id', $id],
            ['customer_id', $customer->id],
        ], ['customer', 'items.product', 'items.sku']);

        if (!$order) {
            abort(404);
        }

        return $this->render('pages/customers/order', [
            'order'      => $order,
            'customer'   => $customer,
        ]);
    }
}
