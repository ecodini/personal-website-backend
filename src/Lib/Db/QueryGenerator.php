<?php namespace Holamanola45\Www\Lib\Db;

class QueryGenerator {
    public static function parseAttributes(array $query_params) {
        $attributes = NULL;

        if (isset($query_params['attributes'])) {
            $attributes = implode(', ', $query_params['attributes']);
        } else {
            $attributes = '*';
        }

        return $attributes;
    }

    public static function generateWhere(array $query_params) {
        $where = NULL;

        if (isset($query_params['where'])) {
            $where = 'WHERE ';

            $conds = array();

            foreach ($query_params['where'] as $col => $val) {
                $conds[] = $col . " = :" . $col;
            }

            $where = $where . implode(' AND ', $conds);
        }

        return $where;
    }

    public static function generateJoins(array $query_params, string $table_name): array {
        $join_statement = NULL;

        $vals = array();

        if (isset($query_params['join'])) {
            $count = 0;

            foreach ($query_params['join'] as $row) {
                $row_statement = '';

                if ($row['required']) {
                    $row_statement = 'INNER JOIN ';
                } else {
                    $row_statement = 'LEFT OUTER JOIN ';
                }

                $conds = array();

                foreach ($row['on'] as $col => $val) {
                    if (str_starts_with($col, $table_name . '.')) {
                        $conds[] = $col . " = " . $val;
                    } else {
                        $conds[] = $col . " = :" . $row['as'] . $count;

                        $vals[$row['as']. $count] = $val;

                        $count = $count + 1;
                    }
                }

                $row_statement = $row_statement . $row['table'] . ' ' . $row['as'] . ' ON ' . implode(' AND ', $conds) . ' ';

                $join_statement = $join_statement . $row_statement;
            }
        }

        return array(
            'join_statement' => $join_statement,
            'values' => $vals
        );
    }

    public static function generateSet(array $query_params) {
        $set = NULL;

        if (isset($query_params['set'])) {
            $set = 'SET ';

            $conds = array();

            foreach ($query_params['set'] as $col => $val) {
                $conds[] = $col . " = :" . $col;
            }

            $set = $set . implode(', ', $conds);
        }

        return $set;
    }

    public static function generateOrderClause(array $query_params) {
        $order = NULL;

        if (isset($query_params['order'])) {
            if ($query_params['order'][1] != 'ASC' && $query_params['order'][1] != 'DESC') {
                return $order;
            }

            if (!preg_match('/^[a-z]+$/', $query_params['order'][0])) {
                return $order;
            }

            $order = 'ORDER BY ' . $query_params['order'][0] . ' ' . $query_params['order'][1];
        }

        return $order;
    }
}