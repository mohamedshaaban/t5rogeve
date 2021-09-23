<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\DeviceInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class sendBirthNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendBirthNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Birthday ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
          $customers = Customer::where('date_of_birth','like','%'.date('d').'/'.date('m').'%')->get();
         foreach($customers as $customer)
         {
             (sendNotification($customer->id,'','Happy Biethday',''));
         }
    }
}
