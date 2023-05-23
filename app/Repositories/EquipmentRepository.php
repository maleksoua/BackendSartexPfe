<?php

namespace App\Repositories;

use App\Exceptions\DeletionException;
use App\Models\Equipment;

class EquipmentRepository
{
    /**
     * @var Equipment
     */
    private $equipment;

    /**
     * UserRepository constructor.
     *
     * @param Equipment $equipment
     */
    public function __construct(Equipment $equipment)
    {
        $this->equipment = $equipment;
    }

    /**
     * Update a equipment
     *
     * @param string $name
     *
     * @return Equipment
     */
    public function update($name)
    {

        $this->equipment->name = $name;

        $this->equipment->save();

        return $this->equipment;
    }

    /**
     * Deletes a equipment
     *
     * @throws DeletionException
     */
    public function delete()
    {
        try {
            $this->equipment->delete();
        } catch (\Exception $e) {
            throw new DeletionException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
