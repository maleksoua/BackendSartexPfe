<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Planning
 *
 * @property int id
 * @property int zone_id
 * @property int start_at
 * @property int end_at
 * @property int chef_id
 * @property int guard_id
 * @property int created_at
 * @property int updated_at
 *
 * @property Guard planningGuard
 * @property User chef
 *
 * @package App
 */
class Planning extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function planningGuard()
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
     * @return BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }
}
