<?php
/**
 * @author Həmid Musəvi <w1w@yahoo.com>
 * 12/13/21
 */

namespace MirHamit\MiriSearch;

use Illuminate\Support\Facades\Schema;

trait MiriSearch
{
    /**
     * @param $query
     * @param $searchFields
     * @param  boolean  $searchByLike
     * @param  bool  $orWhere
     * @return mixed
     */
    public static function scopeMiriSearch($query, $searchFields, bool $searchByLike = false, bool $orWhere = false): mixed
    {
        $searchKeywords = [];
        $tableFields = static::getSearchableFields();
        if (is_string($searchFields)) {
            $searchKeywords[] = $searchFields;
        } else {
            foreach ($searchFields as $key => $value) {
                $searchKeywords[$key] = $value;
            }
        }
        if (empty($searchKeywords)) {
            return $query;
        }

        self::searchForEach($query, $searchKeywords, $tableFields, $searchByLike, $orWhere);
        return $query;
    }

    private static function searchForEach($query, $searchKeywords, $tableFields, $searchByLike, $orWhere)
    {
        $forEachRunned = false;
        foreach ($searchKeywords as $key => $value) {
            if (is_string($key)) {
                self::isKeyStringSearchLoop($query, $key, $value, $tableFields, $forEachRunned, $searchByLike,
                    $orWhere);
            } else {
                self::isKeyNotStringSearchLoop($query, $value, $forEachRunned, $searchByLike);
            }
            $forEachRunned = true;
        }
    }

    private static function isKeyNotStringSearchLoop($query, $value, $forEachRunned, $searchByLike)
    {
        foreach (self::getSearchableFields() as $searchableField) {
            if (!$forEachRunned) {
                if ($searchByLike) {
                    $query->where($searchableField, "LIKE", "%$value%");
                } else {
                    $query->where($searchableField, $value);
                }
                $forEachRunned = true;
            } else {
                if ($searchByLike) {
                    $query->orWhere($searchableField, 'LIKE', "%$value%");
                } else {
                    $query->orWhere($searchableField, $value);
                }
            }
        }

    }

    private static function isKeyStringSearchLoop(
        $query,
        $key,
        $value,
        $tableFields,
        $forEachRunned,
        $searchByLike,
        $orWhere
    ) {
        if (in_array($key, $tableFields)) {
            if ($searchByLike) {
                if (!$forEachRunned) {
                    $query->where($key, "LIKE", "%$value%");
                } else {
                    if ($orWhere) {
                        $query->orWhere($key, 'LIKE', "%$value%");
                    } else {
                        $query->where($key, 'LIKE', "%$value%");
                    }
                }
            } else {
                if (!$forEachRunned) {
                    $query->where($key, $value);
                } else {
                    if ($orWhere) {
                        $query->orWhere($key, $value);
                    } else {
                        $query->where($key, $value);
                    }
                }

            }
        }

    }

    /**
     * Get all searchable fields
     *
     * @return array
     */
    private static function getSearchableFields(): array
    {
        $model = new static;

        $fields = $model->searcher;

        if (empty($fields)) {
            $fields = Schema::getColumnListing($model->getTable());

            $ignoredColumns = [
                $model->getKeyName(),
                //                $model->getUpdatedAtColumn(),
                //                $model->getCreatedAtColumn(),
            ];

            if (method_exists($model, 'getDeletedAtColumn')) {
                $ignoredColumns[] = $model->getDeletedAtColumn();
            }

            $fields = array_diff($fields, $model->getHidden(), $ignoredColumns);
        }

        return $fields;
    }
}
