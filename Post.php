<?php
    /**
     * Created by PhpStorm.
     * User: Mohammed Elamin
     * Date: 14/12/2018
     * Time: 22:05
     */
require_once 'SoftMapper.php';
    class Post extends SoftMapper
    {
        public $table_name="post";

        public $columns=[];


        public function __construct()
        {
           Parent::__construct();
        }


    }

    $post=new Post();
    $post->columns['title']='this is the title xx';
    $post->columns['body']='xx';
  echo  $post->insert();

