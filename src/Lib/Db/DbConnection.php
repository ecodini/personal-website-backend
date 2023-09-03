<?php namespace Holamanola45\Www\Lib\Db;

use PDO;
use Throwable;

class DbConnection {
    private string $dsn;

    private PDO $conn;

    function __construct() {
        $this->dsn = "mysql:host=" . $_ENV["DB_HOST"]. ";dbname=". $_ENV["DB_NAME"]. ";charset=UTF8";

        $this->conn = new PDO($this->dsn, $_ENV["DB_USER"], $_ENV["DB_PASS"], array(
            PDO::ATTR_PERSISTENT =>  true
        ));
    }

    public function close() {
        // garbage collector should kill the PDO connection automatically once there are no references to it.
        unset($conn);
    }

    function __destruct() {
        $this->close();
    }

    public function query(string $sql, array $vars = [], $class = NULL): array {
        $statement = $this->conn->prepare($sql);

        $statement->execute($vars);

        if (isset($class)) {
            return $statement->fetchAll(PDO::FETCH_CLASS, $class);
        } else {
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function beginTransaction(): bool {
        return $this->conn->beginTransaction();
    }

    public function commit(): bool {
        return $this->conn->commit();
    }

    public function rollback(): bool {
        return $this->conn->rollBack();
    }
}