<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pledge;
use App\Models\User;
use App\Models\Fund;
use Illuminate\Support\Carbon;
use Stripe\Stripe;
use Stripe\Transfer;

use Log;

class TransferToBusiness extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:business';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer amount to business';

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
     * @return int
     */
    public function handle()
    {
        // $funds = Fund::where('is_transfer',1)->where('pledge_amount','>=','rise')->where('created_at', '<', Carbon::now()->subDays(1)->toDateTimeString())->get();
        // foreach($funds as $fund){
        //     $user = User::find($fund->user_id);
        //     // payout code for stripe
        //     Stripe::setApiKey(env('STRIPE_SECRET'));
        //     $transfer  = Transfer::create([
        //         "amount" => $fund->pledge_amount * 100,
        //         "currency" => "usd",
        //         "destination" => "{{CONNECTED_STRIPE_ACCOUNT_ID}}",
        //         ]);
            
        //     if($transfer->id != null){

        //         $fund->is_transfer = 2;
        //         $fund->save();
        //     }

        //     // if success then change status is_transfer of fund.

        // }
        Log::info("Transfer amount to business Execute");

    }
}
