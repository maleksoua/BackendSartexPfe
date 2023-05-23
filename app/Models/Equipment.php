<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Equipment
 *
 * @property int id
 * @property string name
 * @property int zone_id
 * @property int created_at
 * @property int updated_at
 *
 * @property Zone zone
 *
 * @package App
 */
class Equipment extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class, 'zone_id', 'id');
    }
}
