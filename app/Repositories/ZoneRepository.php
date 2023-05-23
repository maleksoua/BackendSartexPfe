<?php

namespace App\Repositories;

use App\Exceptions\DeletionException;
use App\Helpers\Helpers;
use App\Models\Equipment;
use App\Models\Zone;
use Illuminate\Pagination\LengthAwarePaginator;

class ZoneRepository
{
    /**
     * @var Zone
     */
    private $zone;

    /**
     * UserRepository constructor.
     *
     * @param Zone $zone
     */
    public function __construct(Zone $zone)
    {
        $this->zone = $zone;
    }

    /**
     * Update a zone
     *
     * @param string $name
     * @param int|null $chef
     *
     * @return Zone
     */
    public function update($name, $chef = null, $filename = null)
    {

        $this->zone->name = $name;
        $this->zone->chef_id = $chef;
        $this->zone->image = $filename;

        $this->zone->save();

        return $this->zone;
    }

    /**
     * Deletes a zone
     *
     * @throws DeletionException
     */
    public function delete()
    {
        try {
            $this->zone->delete();
        } catch (\Exception $e) {
            throw new DeletionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Add equipment to the zone
     *
     * @param string $name
     *
     * @return Equipment
     *
     */
    public function addEquipment($name)
    {
        $equipment = new Equipment();
        $equipment->name = $name;

        $equipment->zone_id = $this->zone->id;

        $equipment->save();

        return $equipment;
    }

    /**
     * Paginates equipments
     *
     * @param int|null $page
     * @param int|null $perPage
     * @param string|null $search
     * @param string|null $orderBy
     * @param string|null $orderDirection
     *
     * @return LengthAwarePaginator
     */
    public function paginateEquipments($page = 1, $perPage = 20, $search = null, $orderBy = null, $orderDirection = 'desc')
    {
        $equipments = $this->zone->equipments()->with(['zone', 'zone.site']);

        if (isset($search)) {
            $equipments->where(Helpers::fieldsLike($search, 'name'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $equipments->orderBy($orderBy, $orderDirection);
        }

        return $equipments->paginate($perPage, ['*'], 'page', $page);
    }
}
