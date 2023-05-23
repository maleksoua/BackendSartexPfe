<?php

namespace App\Repositories;

use App\Exceptions\DeletionException;
use App\Helpers\Helpers;
use App\Models\Site;
use App\Models\Zone;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SiteRepository
{
    /**
     * @var Site
     */
    private $site;

    /**
     * SiteRepository constructor.
     *
     * @param Site $site
     */
    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Paginates sites
     *
     * @param int|null $page
     * @param int|null $perPage
     * @param string|null $search
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param int|null $superChef
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($page = 1, $perPage = 20, $search = null, $orderBy = null, $orderDirection = 'desc', $superChef = null)
    {
        $sites = Site::with('superChef');

        if ($superChef) {
            $sites->where('super_chef_id', $superChef);
        }

        if (isset($search)) {
            $sites->where(Helpers::fieldsLike($search, 'name'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $sites->orderBy($orderBy, $orderDirection);
        }

        return $sites->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Creates a site
     *
     * @param string $name
     *
     * @return Site
     */
    public static function create($name, $superChef)
    {
        $site = new Site();

        $site->name = $name;

        if ($superChef) {
            $site->super_chef_id = $superChef;
        }

        $site->save();

        return $site;
    }

    /**
     * Updates a site
     *
     * @param string $name
     * @param int|null $superChef
     *
     * @return Site
     */
    public function update($name, $superChef = null)
    {

        $this->site->name = $name;

        $this->site->super_chef_id = $superChef;

        $this->site->save();

        return $this->site;
    }

    /**
     * Deletes a site
     *
     * @throws DeletionException
     */
    public function delete()
    {
        try {
            $this->site->delete();
        } catch (\Exception $e) {
            throw new DeletionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Add a zone to the site
     *
     * @param string $name
     * @param int|null $chef
     *
     * @return Zone
     *
     */
    public function addZone($name, $chef = nul, $image = nulll)
    {
        $zone = new Zone();
        $zone->name = $name;

        if ($chef) {
            $zone->chef_id = $chef;
        }

        if ($image) {
            $zone->image = $image;
        }

        $zone->site_id = $this->site->id;

        $zone->save();

        return $zone;
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
        $zones = Zone::where('site_id', $this->site->id)->with(['site', 'chef']);

        if (isset($search)) {
            $zones->where(Helpers::fieldsLike($search, 'name'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $zones->orderBy($orderBy, $orderDirection);
        }

        return $zones->paginate($perPage, ['*'], 'page', $page);
    }
}
