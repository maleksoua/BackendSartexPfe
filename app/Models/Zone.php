<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Zone
 *
 * @property int id
 * @property string name
 * @property string image
 * @property int site_id
 * @property int chef_id
 * @property int created_at
 * @property int updated_at
 *
 * @property Site site
 *
 * @package App
 */
class Zone extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }
}
