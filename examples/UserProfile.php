<?php
    /**
     * UserProfile Model Example
     * Demonstrates 1:1 relationship
     */
    require_once __DIR__ . '/../SoftMapper.php';

    class UserProfile extends SoftMapper
    {
        public $table_name = "user_profiles";
        public $columns = [];

        protected $timestamps = true;

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * A profile belongs to a user (inverse of 1:1)
         */
        public function user()
        {
            return $this->belongsTo('User', 'user_id', 'id');
        }
    }
