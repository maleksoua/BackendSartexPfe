<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Site
 *
 * @property int id
 * @property string name
 * @property int super_chef_id
 * @property int created_at
 * @property int updated_at
 *
 * @property User superChef
 * @property Collection zones
 *
 * @package App
 */
class Site extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function superChef()
    {
        return $this->belongsTo(User::class, 'super_chef_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function zones()
    {
        return $this->hasMany(Zone::Class);
    }
}
