<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Comment
 *
 * @property int id
 * @property int alert_id
 * @property string comment
 * @property int created_at
 * @property int updated_at
 *
 * @property Alert alert
 *
 * @package App
 */
class Comment extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo
     */
    public function alert()
    {
        return $this->belongsTo(Alert::class, 'alert_id', 'id');
    }
}
