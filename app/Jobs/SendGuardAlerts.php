<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\Planning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendGuardAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Planning
     */
    private $planning;

    /**
     * @var integer
     */
    private $percentage;

    /**
     * @var string
     */
    private $alertDate;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($planning, $alertDate, $percentage)
    {
        $this->planning = $planning;
        $this->alertDate = $alertDate;
        $this->percentage = $percentage;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $alert = new Alert();
        $alert->zone_id = $this->planning->zone_id;
        $alert->guard_id = $this->planning->guard_id;
        $alert->chef_id = $this->planning->chef_id;
        $alert->type = Alert::GUARD_ALERT;
        $alert->alert_date = $this->alertDate;
        $alert->percentage = $this->percentage;

        $alert->save();
    }
}
