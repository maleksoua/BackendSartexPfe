<?php

namespace App\Repositories;

use App\Exceptions\DeletionException;
use App\Models\Planning;
use Carbon\Carbon;

class PlanningRepository
{
    /**
     * @var Planning
     */
    private $planning;

    /**
     * PlanningRepository constructor.
     *
     * @param Planning $planning
     */
    public function __construct(Planning $planning)
    {
        $this->planning = $planning;
    }

    /**
     * Add planning
     *
     * @param Carbon $startAt
     * @param Carbon $endAt
     * @param int $guard
     *
     * @return Planning
     */
    public function update($startAt, $endAt, $guard, $zoneId)
    {
        $this->planning->start_at = $startAt;
        $this->planning->end_at = $endAt;
        $this->planning->guard_id = $guard;
        $this->planning->zone_id = $zoneId;

        $this->planning->save();

        return $this->planning;
    }

    /**
     * Deletes a planning
     *
     * @throws DeletionException
     */
    public function delete()
    {
        try {
            $this->planning->delete();
        } catch (\Exception $e) {
            throw new DeletionException($e->getMessage(), $e->getCode(), $e);
        }
    }


}
