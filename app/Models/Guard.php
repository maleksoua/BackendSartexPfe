<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Guard
 *
 * @property int id
 * @property string profile_image
 * @property string first_name
 * @property string last_name
 * @property string phone
 * @property string register_number
 * @property string tag
 * @property int chef_id
 * @property int created_at
 * @property int updated_at
 *
 * @property User chef
 * @property Collection plannings
 *
 * @package App
 */
class Guard extends Model
{
    use HasFactory;

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
    public function plannings()
    {
        return $this->hasMany(Planning::Class);
    }
}
