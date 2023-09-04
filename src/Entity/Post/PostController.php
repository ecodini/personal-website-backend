<?php namespace Holamanola45\Www\Entity\Post;

use Holamanola45\Www\Lib\Error\BadRequestException;
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

        $query_options = array(
            'attributes' => [
                'post.id as `post-id`', 
                'user.id as `user-id`', 
                'user.username as `user-username`',
                'post.title as `post-title`',
                'post.img_link as `post-img_link`', 
            ],
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
        );

        if (isset($req->query_params['order']) && count($req->query_params['order']) > 0) {
            $query_options['order'] = $req->query_params['order'];
        }

        $posts = $this->postService->findAll($query_options);
        $total_count = $this->postService->getTotalRows();

        return array(
            'count' => $total_count,
            'posts' => $posts
        );
    }

    public function getPostById(Request $req, Response $res) {
        $post_id = $req->params[0];

        $post = $this->postService->findById($post_id, array(
            'attributes' => [
                'post.id as `post-id`', 
                'post.title as `post-title`', 
                'post.content as `post-content`', 
                'post.img_link as `post-img_link`', 
                'user.id as `user-id`',
                'user.username as `user-username`'
            ],
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

        if (!isset($post)) {
            throw new BadRequestException('The post does not exist.');
        }

        return array(
            'post' => $post
        );
    }

    public function createPost(Request $req, Response $res) {
        return array(
            'message' => 'todo Lole'
        );
    }
}