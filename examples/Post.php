<?php
    /**
     * Post Model Example
     * Demonstrates relationship definitions
     */
    require_once __DIR__ . '/../SoftMapper.php';

    class Post extends SoftMapper
    {
        public $table_name = "posts";
        public $columns = [];

        protected $timestamps = true;
        protected $soft_deletes = true;

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * A post belongs to a user (inverse of 1:N)
         */
        public function user()
        {
            return $this->belongsTo('User', 'user_id', 'id');
        }

        /**
         * A post has many comments (1:N relationship)
         */
        public function comments()
        {
            return $this->hasMany('Comment', 'post_id', 'id');
        }

        /**
         * A post belongs to many tags (N:N relationship)
         */
        public function tags()
        {
            return $this->belongsToMany('Tag', 'post_tag', 'post_id', 'tag_id');
        }
    }
