<?php namespace Holamanola45\Www\Entity\Post;

use Holamanola45\Www\Lib\Http\Request;
use Holamanola45\Www\Lib\Http\Response;

class PostController {
    private PostService $postService;

    function __construct() {
        $this->postService = new PostService();
    }

    public function getAllPosts(Request $req, Response $res) {
        $limit = $req->query_params['count'] ? $req->query_params['count'] : $_ENV['DEFAULT_ITEMS_PER_PAGE'];
        $page = $req->query_params['page'] ? $req->query_params['page'] : 1;

        $posts = $this->postService->findAll(array(
            'attributes' => ['post.id as post_id', 'user.id as user_id', 'user.username as user_username'],
            'limit' => $limit,
            'offset' => $limit * ($page - 1),
            'join' => array(
                array(
                    'table' => 'user',
                    'required' => false,
                    'as' => 'user',
                    'on' => array(
                        'post.user_id' => 'user.id'
                    )
                )
            )
        ));

        return array(
            'posts' => $posts
        );
    }
}