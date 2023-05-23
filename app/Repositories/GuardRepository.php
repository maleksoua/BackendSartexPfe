<?php

namespace App\Repositories;

use App\Exceptions\DeletionException;
use App\Helpers\Helpers;
use App\Models\EquipmentHistory;
use App\Models\Guard;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GuardRepository
{
    /**
     * @var Guard
     */
    private $guard;

    /**
     * GuardRepository constructor.
     *
     * @param Guard $guard
     */
    public function __construct(Guard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Paginates guards
     *
     * @param int|null $page
     * @param int|null $perPage
     * @param string|null $search
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @return LengthAwarePaginator
     */
    public static function paginate($page = 1, $perPage = 20, $search = null, $orderBy = null, $orderDirection = 'desc', $chef = null)
    {
        $guards = Guard::with(['chef']);

        if (isset($chef)) {
            $guards->where('chef_id', '=', $chef);
        }

        if (isset($search)) {
            $guards->where(Helpers::fieldsLike($search, 'first_name', 'last_name', 'register_number', 'phone'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $guards->orderBy($orderBy, $orderDirection);
        }


        return $guards->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Creates a guard
     *
     * @param string $profileImage
     * @param string $firstName
     * @param string $lastName
     * @param string $register
     * @param string|null $phone
     *
     * @return Guard
     */
    public static function create($profileImage, $firstName, $lastName, $register, $phone, $tag, $chef = null)
    {
        $guard = new Guard();

        $guard->profile_image = $profileImage;
        $guard->first_name = $firstName;
        $guard->last_name = $lastName;
        $guard->register_number = $register;
        $guard->phone = $phone;
        $guard->tag = $tag;

        if ($chef) {
            $guard->chef_id = $chef;
        }

        $guard->save();

        return $guard;
    }

    /**
     * Updates a guard
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $profileImage
     * @param string $phone
     * @param string $register
     * @param string $tag
     * @param int|null $zone
     *
     * @return bool
     */
    public function update($firstName, $lastName, $profileImage, $phone, $register, $tag, $chef = null)
    {

        if (isset($firstName)) {
            $this->guard->first_name = $firstName;
        }

        if (isset($lastName)) {
            $this->guard->last_name = $lastName;
        }

        if (isset($register)) {
            $this->guard->register_number = $register;
        }

        if (isset($profileImage)) {
            $this->guard->profile_image = $profileImage;
        }

        if (isset($tag)) {
            $this->guard->tag = $tag;
        }

        if (isset($phone)) {
            $this->guard->phone = $phone;
        }

        $this->guard->chef_id = $chef;

        return $this->guard->save();
    }

    /**
     * Deletes a guard
     *
     * @throws DeletionException
     */
    public function delete()
    {
        try {
            $this->guard->delete();
        } catch (\Exception $e) {
            throw new DeletionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Paginate the list of equipment history of one guard
     *
     */
    public function paginateEquipmentHistory($page = 1, $perPage = 20, $search = null, $orderBy = null, $orderDirection = 'desc')
    {
        $equipmentHistory = EquipmentHistory::where('last_read_id_user', $this->guard->id);

        if (isset($search)) {
            $equipmentHistory->where(Helpers::fieldsLike($search, 'name', 'last_read_date', 'last_read_id_user'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $equipmentHistory->orderBy($orderBy, $orderDirection);
        }

        return $equipmentHistory->paginate($perPage, ['*'], 'page', $page);
    }
}
