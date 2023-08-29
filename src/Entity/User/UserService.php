<?php namespace Holamanola45\Www\Entity\User;

use Holamanola45\Www\Domain\User;
use Holamanola45\Www\Lib\Db\DbConnection;
use Holamanola45\Www\Lib\Db\DbService;

class UserService extends DbService {
    function __construct() {
        $conn = new DbConnection();

        parent::__construct('user', $conn);
    }

    public function findByUsername(string $username, array $attributes): User {
        $row = $this->query('
            SELECT ' . implode(', ', $attributes) . ' FROM user
            WHERE username = :name LIMIT 1;
        ', array(
            'name' => $username
        ));

        return new User($row[0]);
    }

    public function createUser(array $user_data) {
        $this->query('
            INSERT INTO user (username, password, created_at, created_by_ip)
            VALUES (:username, :password, :created_at, :created_by_ip)
        ', $user_data);
    }
}