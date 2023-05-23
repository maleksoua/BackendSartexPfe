<?php

namespace App\Repositories;

use App\Exceptions\EmailServiceNotAvailableException;
use App\Exceptions\DeletionException;
use App\Exceptions\PlanningExistsException;
use App\Helpers\Helpers;
use App\Models\Planning;
use App\Models\Site;
use App\Models\User;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserRepository
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserRepository constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Paginates users
     *
     * @param int|null $page
     * @param int|null $perPage
     * @param string|null $search
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param int|null $role
     * @param int|null $superChef
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($page = 1, $perPage = 20, $search = null, $orderBy = null, $orderDirection = 'desc', $role = null, $superChef = null)
    {
        $users = User::where('id', '!=', auth()->id())->with(['superChef', 'site', 'zones', 'chefs']);

        if (isset($search)) {
            $users->where(Helpers::fieldsLike($search, 'first_name', 'last_name', 'register_number', 'phone', 'email'));
        }

        if ($role) {
            $users->where('role', $role);
        }

        if ($superChef) {
            $users->where('super_chef_id', $superChef);
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $users->orderBy($orderBy, $orderDirection);
        }

        return $users->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Creates a user
     *
     * @param string $profileImage
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param int $role
     * @param string $password
     * @param string $register
     * @param string|null $phone
     * @param string|null $superChef
     * @param string|null $site
     *
     * @return User
     */
    public static function create($profileImage, $firstName, $lastName, $email, $role, $password, $register, $phone, $superChef = null, $site = null, $zoneIds = [], $chefIds = [])
    {
        $user = new User();

        $user->profile_image = $profileImage;
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->email = $email;
        $user->role = $role;
        $user->register_number = $register;
        $user->password = bcrypt($password);
        $user->phone = $phone;

        if (isset($superChef)) {
            $user->super_chef_id = $superChef;
        }

        $user->save();

        if (isset($site)) {
            $foundSite = Site::find($site);
            $foundSite->super_chef_id = $user->id;
            $foundSite->save();
        }

        if ($zoneIds) {
            $zones = Zone::whereIn('id', $zoneIds)->get();
            foreach ($zones as $zone) {
                $zone->chef()->associate($user);
                $zone->save();
            }
        }

        if ($chefIds) {
            $chefs = User::whereIn('id', $chefIds)->get();
            foreach ($chefs as $chef) {
                $chef->superChef()->associate($user);
                $chef->save();
            }
        }


        return $user;
    }

    /**
     * Updates a user
     *
     * @param string $firstName
     * @param string $lastName
     * @param string $email
     * @param string|null $profileImage
     * @param string|null $phone
     * @param string|null $password
     * @param string|null $register
     * @param string|null $superChef
     * @param string|null $site
     *
     * @return bool
     */
    public function update($firstName, $lastName, $email = null, $profileImage = null, $phone = null, $password = null, $register = null, $superChef = null, $site = null, $zoneIds = [], $chefIds = [])
    {
        $this->user->first_name = $firstName;
        $this->user->last_name = $lastName;

        if (isset($register)) {
            $this->user->register_number = $register;
        }


        if (isset($email)) {
            $this->user->email = $email;
        }

        if (isset($profileImage)) {
            $this->user->profile_image = $profileImage;
        }

        if (isset($password)) {
            $this->user->password = bcrypt($password);
        }

        if (isset($phone)) {
            $this->user->phone = $phone;
        }

        $this->user->super_chef_id = $superChef;

        $this->user->zones()->update(['chef_id' => null]);
        if ($zoneIds) {
            $zones = Zone::whereIn('id', $zoneIds)->get();
            foreach ($zones as $zone) {
                $zone->chef()->associate($this->user);
                $zone->save();
            }
        }

        $this->user->chefs()->update(['super_chef_id' => null]);
        if ($chefIds) {
            $chefs = User::whereIn('id', $chefIds)->get();
            foreach ($chefs as $chef) {
                $chef->superChef()->associate($this->user);
                $chef->save();
            }
        }

        if (isset($site)) {
            $foundSite = Site::find($site);
            $foundSite->super_chef_id = $this->user->id;
            $foundSite->save();
        } else {
            $foundSite = $this->user->site;
            if ($foundSite) {
                $foundSite->super_chef_id = null;
                $foundSite->save();
            }
        }


        return $this->user->save();
    }

    /**
     * Deletes a user
     *
     * @throws DeletionException
     */
    public function delete()
    {
        try {
            $this->user->delete();
        } catch (\Exception $e) {
            throw new DeletionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Updates a user
     *
     * @param string $firstName
     * @param string $lastName
     * @param null $email
     * @param string|null $profileImage
     * @param string|null $phone
     * @param string|null $register
     * @return bool
     */
    public function updateProfile($firstName = null, $lastName = null, $email = null, $profileImage = null, $phone = null, $register = null)
    {

        if (isset($firstName)) {
            $this->user->first_name = $firstName;
        }

        if (isset($lastName)) {
            $this->user->last_name = $lastName;
        }

        if (isset($register)) {
            $this->user->register_number = $register;
        }

        if (isset($email)) {
            $this->user->email = $email;
        }

        if (isset($profileImage)) {
            $this->user->profile_image = $profileImage;
        }

        if (isset($phone)) {
            $this->user->phone = $phone;
        }

        return $this->user->save();
    }

    /**
     * @param string $password
     * @param bool $sendEmail
     *
     * @return bool
     * @throws EmailServiceNotAvailableException
     */
    public function updatePassword($password, $sendEmail = true)
    {
        $this->user->password = bcrypt($password);
        if ($sendEmail) {
            MailRepository::sendPasswordUpdateMail($this->user);
        }
        return $this->user->save();
    }


    /**
     * Paginates zones
     *
     * @param int|null $page
     * @param int|null $perPage
     * @param string|null $search
     * @param string|null $orderBy
     * @param string|null $orderDirection
     *
     * @return LengthAwarePaginator
     */
    public function paginateZones($page = 1, $perPage = 20, $search = null, $orderBy = null, $orderDirection = 'desc')
    {
        $zones = Zone::where('chef_id', $this->user->id);

        if (isset($search)) {
            $zones->where(Helpers::fieldsLike($search, 'name'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $zones->orderBy($orderBy, $orderDirection);
        }

        return $zones->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get planning
     *
     *
     * @return Collection
     */
    public function getplanning()
    {
        return Planning::where('chef_id', $this->user->id)
            ->with(['planningGuard', 'chef', 'zone'])
            ->orderBy('start_at')
            ->get();
    }

    /**
     * Add planning
     *
     * @param Carbon $startAt
     * @param Carbon $endAt
     * @param int $guard
     * @param int $chefId
     *
     * @return Planning
     *
     * @throws PlanningExistsException
     */
    public function addPlanning($startAt, $endAt, $guard, $chefId, $zoneId)
    {
        $existingPlanning = Planning::where('guard_id', $guard)
            ->where('chef_id', $chefId)
            ->whereDate('start_at', $startAt)
            ->where(function ($query) use ($startAt, $endAt) {
                $query->whereTime('start_at', '>=', $startAt->toTimeString())
                    ->whereTime('end_at', '<=', $endAt->toTimeString())
                    ->orWhere(function ($query) use ($startAt, $endAt) {
                        $query->whereTime('start_at', '<', $startAt->toTimeString())
                            ->whereTime('end_at', '>=', $endAt->toTimeString());
                    });
            })
            ->first();


        if ($existingPlanning && $existingPlanning->id) {
            throw new PlanningExistsException();
        }

        $planning = new Planning();

        $planning->start_at = $startAt;
        $planning->end_at = $endAt;
        $planning->guard_id = $guard;
        $planning->chef_id = $chefId;
        $planning->zone_id = $zoneId;

        $planning->save();

        return $planning;
    }

    /**
     * Duplicate planning
     *
     * @param int $monthToDuplicate
     * @param int $monthCount
     *
     * @return bool|null
     */
    public function duplicatePlanning($monthToDuplicate, $monthCount): ?bool
    {
        $dateMonthArray = explode('/', $monthToDuplicate);
        $year = $dateMonthArray[0];
        $month = $dateMonthArray[1];
        $plannings = Planning::where('chef_id', $this->user->id)
            ->whereMonth('start_at', $month)
            ->whereYear('start_at', $year)
            ->get()
            ->toArray();
        $duplicatedPlannings = [];
        try {
            Planning::whereMonth('start_at', '>', $month)
                ->whereMonth('start_at', '<=', $month + $monthCount)
                ->each(function ($item) {
                    $item->delete();
                });

            foreach ($plannings as $planning) {
                for ($i = 1; $i <= $monthCount; $i++) {
                    $planning['start_at'] = Carbon::parse($planning['start_at'], 'Africa/Tunis')
                        ->month(Carbon::parse($planning['start_at'])->month + 1);
                    $planning['end_at'] = Carbon::parse($planning['end_at'], 'Africa/Tunis')
                        ->month(Carbon::parse($planning['start_at'])->month + 1);
                    $planning['created_at'] = Carbon::now();
                    $planning['updated_at'] = Carbon::now();
                    unset($planning['id']);
                    $duplicatedPlannings[] = $planning;
                }
            }

            Planning::query()->insert($duplicatedPlannings);

        } catch (\Exception $e) {
            return null;
        }
        return true;
    }
}
