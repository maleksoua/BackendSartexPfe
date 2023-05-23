<?php

namespace App\Console\Commands;

use App\Jobs\SendChefAlerts;
use App\Models\Alert;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendChefAlertsCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chef_alert:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending alerts to the s.chefs about the chefs';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->sendChefsAlerts();
    }

    /**
     * Send the chefs alerts to the super chefs
     */
    public function sendChefsAlerts()
    {
       echo('***** Starting to send alerts about chefs at ' . Carbon::now()->toTimeString() . ' *****');
        $alerts = Alert::whereDate('created_at', Carbon::today())
            ->where('type', Alert::GUARD_ALERT)
            ->get();
        foreach ($alerts as $alert) {
            $commentTime = $alert->created_at->copy()->addMinute(30)->toTimeString();
            $comment = $alert->comment;

            if (!$comment ||
                !($comment->created_at->toTimeString() >= $alert->created_at->toTimeString()
                    && $comment->created_at->toTimeString() <= $commentTime)) {

                $alertAboutThis = Alert::where('type', Alert::CHEF_ALERT)
                    ->where('chef_id', $alert->chef_id)
                    ->whereDate('alert_date', $alert->created_at)
                    ->whereTime('alert_date', $alert->created_at->toTimeString())
                    ->first();

                if (!$alertAboutThis) {
                    Log::info('** Sending alert for guard ' . $alert->guard_id . ' for time ' . $alert->created_at->toTimeString());

                    SendChefAlerts::dispatch($alert);
                }

            }
        }
        Log::info('***** End of sending chefs alerts *****');
    }
}
