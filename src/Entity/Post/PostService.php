<?php namespace Holamanola45\Www\Entity\Post;
      
use Holamanola45\Www\Lib\Db\DbConnection;
use Holamanola45\Www\Lib\Db\DbService;

class PostService extends DbService {
    function __construct() {
        $conn = new DbConnection();

        parent::__construct('post', $conn, PostModel::class);
    }
}