<?php namespace Holamanola45\Www\Entity\Post;

use Holamanola45\Www\Entity\User\UserModel;

class PostModel {
    function __set($key, $value) {
        $keys_arr = explode('-', $key);

        $col = $keys_arr[0];
        $val = $keys_arr[1];

        if ($col === 'post') {
            $this->$val = $value;
        } else if ($col === 'user') {
            if (!isset($this->user)) {
                $this->user = new UserModel();
            }

            $this->user->$val = $value;
        } else {
            $this->$key = $value;
        }
    }

    public int $id;

    public string $title;
    
    public string $content;

    public string $created_at;

    public string $poster_ip;

    public string | null $img_link;

    public int $user_id;

    public UserModel $user;
}