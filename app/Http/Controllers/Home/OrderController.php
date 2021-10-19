<?php

namespace App\Http\Controllers\Home;

use Request;
use App\Models\Order\Order;
use App\Repositories\OrderRepository;

class OrderController extends Controller
{

     /**
     * @var AddressRepository
     */
    protected $order;
    
    /**
     * orderController constructor.
     *
     * @param orderRepository $order
     */
    public function __construct(OrderRepository $order)
    {
        $this->order = $order;
    }

    /**
     * 投注下单
     *
     * @param orderRepository $order
     */

    public function add()
    { 
        $this->order->create(Request::input());return responseJson('', true);
    }

}
