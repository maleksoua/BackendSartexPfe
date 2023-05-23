<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Alert
 *
 * @property int id
 * @property int zone_id
 * @property int guard_id
 * @property int alert_id
 * @property int chef_id
 * @property int alert_date
 * @property int percentage
 * @property int type
 * @property int created_at
 * @property int updated_at
 *
 * @property Zone zone
 * @property User chef
 * @property Guard alertGuard
 * @property Comment comment
 *
 * @package App
 */
class Alert extends Model
{
    use HasFactory;

    const GUARD_ALERT = 1;
    const CHEF_ALERT = 2;

    /**
     * @return BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function alertGuard()
    {
        return $this->belongsTo(Guard::class, 'guard_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function comment()
    {
        return $this->hasOne(Comment::class);
    }
}
