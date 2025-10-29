<?php
    /**
     * User Model Example
     * Demonstrates relationship definitions
     */
    require_once __DIR__ . '/../SoftMapper.php';

    class User extends SoftMapper
    {
        public $table_name = "users";
        public $columns = [];

        protected $timestamps = true;

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * A user has many posts (1:N relationship)
         */
        public function posts()
        {
            return $this->hasMany('Post', 'user_id', 'id');
        }

        /**
         * A user has many comments (1:N relationship)
         */
        public function comments()
        {
            return $this->hasMany('Comment', 'user_id', 'id');
        }

        /**
         * A user has one profile (1:1 relationship)
         */
        public function profile()
        {
            return $this->hasOne('UserProfile', 'user_id', 'id');
        }
    }
