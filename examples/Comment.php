<?php
    /**
     * Comment Model Example
     * Demonstrates relationship definitions
     */
    require_once __DIR__ . '/../SoftMapper.php';

    class Comment extends SoftMapper
    {
        public $table_name = "comments";
        public $columns = [];

        protected $timestamps = true;

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * A comment belongs to a post (inverse of 1:N)
         */
        public function post()
        {
            return $this->belongsTo('Post', 'post_id', 'id');
        }

        /**
         * A comment belongs to a user (inverse of 1:N)
         */
        public function user()
        {
            return $this->belongsTo('User', 'user_id', 'id');
        }
    }
