<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Equipment
 *
 * @property int id
 * @property string name
 * @property string last_read_date
 * @property string last_read_id_user
 * @property int created_at
 * @property int updated_at
 *
 * @package App
 */
class EquipmentHistory extends Model
{
    use HasFactory;
}
