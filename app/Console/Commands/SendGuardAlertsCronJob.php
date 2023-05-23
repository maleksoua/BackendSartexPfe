<?php

namespace App\Console\Commands;

use App\Helpers\Helpers;
use App\Jobs\SendGuardAlerts;
use App\Models\Alert;
use App\Models\EquipmentHistory;
use App\Models\Planning;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendGuardAlertsCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alert:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending alerts to the chefs about the guards';

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->sendGuardsAlerts();
    }

    /**
     * Send the late guards alerts to the chefs
     */
    public function sendGuardsAlerts()
    {
        Log::info('***** Starting to send alerts about guards at ' . Carbon::now() . ' *****');
        $plannings = Planning::query()
            ->whereDate('start_at', Carbon::today())
            ->whereTime('start_at', '<=', Carbon::now()->toTimeString())
            ->get();

        $now = Carbon::now();

        Log::info('* found plannings: ' . count($plannings));

        foreach ($plannings as $planning) {
            $startDate = Carbon::create($planning->start_at);
            $history = [];
            $startDate->addHours(config('constants.loop_per_hour'));

            while ($startDate->lessThanOrEqualTo($now)) {
                if ($planning->planningGuard) {
                    $interval = $startDate->copy()->addHours(1)->toTimeString();
                    $history = EquipmentHistory::where('last_read_id_user', $planning->planningGuard->id)
                        ->whereDate('last_read_date', $startDate)
                        ->whereTime('last_read_date', '>=', $startDate->toTimeString())
                        ->whereTime('last_read_date', '<=', $interval)
                        ->get();
                }

                $equipments = [];
                try {
                    $equipments = $planning->zone->equipments;
                } catch (\Exception $e) {
                }

                $uniqueHistory = array_unique(($history->pluck('name')->toArray()));

                Log::info('* found equipments: ' . count($equipments));
                Log::info('* found history: ' . count($history));


                if (count($equipments) != count($uniqueHistory) || !$equipments->pluck('name')->diff($uniqueHistory)->isEmpty()) {
                    $alertAboutThis = Alert::where('guard_id', $planning->guard_id)
                        ->whereDate('alert_date', $startDate)
                        ->whereTime('alert_date', $startDate->toTimeString())
                        ->first();

                    $percentage = Helpers::getPercentOfNumber(count($equipments), count($uniqueHistory));

                    if (!$alertAboutThis) {
                        Log::info('** Sending alert for planning ' . $planning->id . ' for time ' . $startDate->toTimeString());

                        SendGuardAlerts::dispatch($planning, $startDate, $percentage);
                    } else {
                        Log::info('* alert already send: ' . $alertAboutThis->id);
                    }
                }

                $startDate->addHours(config('constants.loop_per_hour'));
            }
        }


        Log::info('***** End of sending guards alerts *****');
    }

}
