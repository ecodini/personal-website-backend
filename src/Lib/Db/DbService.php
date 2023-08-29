<?php namespace Holamanola45\Www\Lib\Db;

use Exception;

abstract class DbService {
    private DbConnection $db_conn;

    private string $table_name;

    function __construct(string $table_name, DbConnection $db_conn) {
        $this->table_name = $table_name;
        $this->db_conn = $db_conn;
    }

    function __destruct() {
        $this->db_conn->close();
    }

    public function query(string $sql, array $vars = []): array {
        return $this->db_conn->query($sql, $vars);
    }

    public function getTotalRows(): int {
        $rows = $this->query('
            SELECT COUNT(id) as count FROM ' . $this->table_name . '
        ');

        return $rows[0]['count'];
    }

    public function findById(int $id, array $query_params) {
        $attributes = QueryGenerator::parseAttributes($query_params);

        $joins = QueryGenerator::generateJoins($query_params, $this->table_name);

        $params = NULL;

        if (isset($joins['values'])) {
            $params = $joins['values'];
        }

        if (isset($params)) {
            $rows = $this->query('
                SELECT ' . $attributes .' FROM ' . $this->table_name . ' ' . $joins['join_statement'] . '
                WHERE id = :id LIMIT 1;
            ', array_merge(array('id' => $id), $joins['values']));
        } else {
            $rows = $this->query('
                SELECT ' . $attributes .' FROM ' . $this->table_name . '
                WHERE id = :id LIMIT 1;
            ', array('id' => $id));
        }

        if (count($rows) == 0) {
            return NULL;
        }

        return $rows[0];
    }

    public function findAll(array $query_params) {
        $attributes = QueryGenerator::parseAttributes($query_params);

        $where = QueryGenerator::generateWhere($query_params);
        $joins = QueryGenerator::generateJoins($query_params, $this->table_name);

        $rows = 'SELECT ' . $attributes .' FROM ' . $this->table_name . ' ' . $this->table_name . ' ' . $joins['join_statement'];

        $limit_offset = '
            LIMIT ' . $query_params['limit'] . ' OFFSET ' . $query_params['offset'] . ';
        ';

        $params = NULL;

        if (isset($joins['values']) && isset($query_params['where'])) {
            $params = array_merge($joins['values'], $query_params['where']);
        } else if (isset($joins['values'])) {
            $params = $joins['values'];
        } else if (isset($query_params['where'])) {
            $params = $query_params['where'];
        }

        if (isset($params)) {
            if (isset($where)) {
                $rows = $rows . ' ' . $where . ' ';
            }

            return $this->query($rows . $limit_offset, $params);
        } else {
            return $this->query($rows . $limit_offset);
        }
    }

    public function findOne(array $query_params) {
        $query_params['limit'] = 1;
        $query_params['offset'] = 0;

        $res = $this->findAll($query_params);

        return $res[0];
    }

    public function deleteById(int $id) {
        $rows = $this->query('
            DELETE FROM ' . $this->table_name . '
            WHERE id = :id;
        ', array('id' => $id));

        if (count($rows) == 0) {
            return NULL;
        }

        return $rows[0];
    }

    public function deleteAll(array $query_params, bool $force = false) {
        $where = QueryGenerator::generateWhere($query_params);

        $rows = NULL;

        if (isset($where)) {
            $rows = $this->query('DELETE FROM ' . $this->table_name . ' ' . $where, $query_params["where"]);
        } else if (!$force) {
            throw new Exception('Delete from without where clause detected! Use force flag to force delete. WARNING: This will delete ALL rows!');
        } else {
            $rows = $this->query('DELETE FROM ' . $this->table_name);
        }

        if (count($rows) == 0) {
            return NULL;
        }

        return $rows[0];
    }

    public function update(array $query_params) {
        $where = QueryGenerator::generateWhere($query_params);
        $set = QueryGenerator::generateSet($query_params);

        $this->query('UPDATE ' . $this->table_name . ' ' . $set . ' ' . $where, array_merge($query_params['where'], $query_params['set']));
    }

    public function beginTransaction(): bool {
        return $this->db_conn->beginTransaction();
    }

    public function commit(): bool {
        return $this->db_conn->commit();
    }

    public function rollback(): bool {
        return $this->db_conn->rollback();
    }
}