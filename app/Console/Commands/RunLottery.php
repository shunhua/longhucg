<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\OrderRepository;

class RunLottery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:lottery';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'run a lottery';

    /**
     * @var OrderRepository
     */
    protected $order;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(OrderRepository $order)
    {
        parent::__construct();
        $this->order = $order;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->order->handle();
    }
}
