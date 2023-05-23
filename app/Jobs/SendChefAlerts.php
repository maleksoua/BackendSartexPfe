<?php

namespace App\Jobs;

use App\Models\Alert;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendChefAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Alert
     */
    private $alert;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $alert = new Alert();
        $alert->chef_id = $this->alert->chef_id;
        $alert->type = Alert::CHEF_ALERT;
        $alert->alert_date = $this->alert->created_at;
        $alert->alert_id = $this->alert->id;

        $alert->save();
    }
}
