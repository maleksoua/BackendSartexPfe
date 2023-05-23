<?php

namespace App\Repositories;

use App\Models\Alert;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

class AlertRepository
{
    /**
     * @var Alert
     */
    private $alert;

    /**
     * AlertRepository constructor.
     *
     * @param Alert $alert
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get list of alerts
     *
     * @param int $chefId
     *
     */
    public static function getAlertsByGuards($chefId)
    {
        return Alert::with(['chef', 'zone', 'alertGuard', 'comment'])
            ->where('type', Alert::GUARD_ALERT)
            ->where('chef_id', $chefId)->get();
    }

    /**
     * Get list of alerts
     *
     * @param $superChefId
     *
     * @return Collection
     */
    public static function getAlertsByChefs($superChefId)
    {
        return Alert::with(['chef', 'zone', 'alertGuard', 'comment'])
            ->where('type', Alert::CHEF_ALERT)
            ->whereHas('chef', function ($chef) use ($superChefId) {
                $chef->where('super_chef_id', '=', $superChefId);
            })->get();
    }

    /**
     * Creates a comment
     *
     * @param string $message
     *
     * @return Comment
     */
    public function addComment($message)
    {
        $comment = new Comment();

        $comment->comment = $message;
        $comment->alert_id = $this->alert->id;

        $comment->save();

        return $comment;
    }
}
