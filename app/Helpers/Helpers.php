<?php
namespace App\Helpers;

use Closure;

class Helpers
{
    /**
     * Query builder helper for the Like operator
     *
     * Return value can be used as follow :
     *     \Illuminate\Database\Query\Builder::where(fieldsLike(...))
     *     \Illuminate\Database\Eloquent\Model::with(['relationName' => fieldsLike(...)])
     *
     * @param string $term
     * @param array $fields
     *
     * @return Closure
     */
    static function fieldsLike($term, ...$fields)
    {
        return function ($query) use ($term, $fields) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $term . '%');
            }
        };
    }

    /**
     * A function that calculates the percentage of a given number.
     *
     * @param int $number
     * @param int $total
     * @return int The final result.
     */
    static function getPercentOfNumber($total, $number)
    {
        if ( $total > 0 ) {
            return round(($number * 100) / $total, 2);
        } else {
            return 0;
        }
    }
}
?>
