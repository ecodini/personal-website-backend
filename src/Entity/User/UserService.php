<?php namespace Holamanola45\Www\Entity\User;

use Holamanola45\Www\Lib\Db\DbConnection;
use Holamanola45\Www\Lib\Db\DbService;

class UserService extends DbService {
    function __construct() {
        $conn = new DbConnection();

        parent::__construct('user', $conn, UserModel::class);
    }

    public function findByUsername(string $username, array $attributes): UserModel | null {
        $row = $this->query('
            SELECT ' . implode(', ', $attributes) . ' FROM user
            WHERE username = :name LIMIT 1;
        ', array(
            'name' => $username
        ), $this->class);

        return $row[0];
    }

    public function findByUsernameOrEmail(string $username, string $email, array $attributes): UserModel | null {
        $row = $this->query('
            SELECT ' . implode(', ', $attributes) . ' FROM user
            WHERE username = :name OR email = :email LIMIT 1;
        ', array(
            'name' => $username,
            'email' => $email
        ), $this->class);

        return $row[0];
    }

    public function createUser(array $user_data) {
        $this->query('
            INSERT INTO user (username, password, created_at, created_by_ip, email, token, mail_sent_at)
            VALUES (:username, :password, :created_at, :created_by_ip, :email, :token, mail_sent_at)
        ', $user_data);
    }
}