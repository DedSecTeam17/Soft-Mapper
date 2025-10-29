<?php
    /**
     * Tag Model Example
     * Demonstrates N:N relationship
     */
    require_once __DIR__ . '/../SoftMapper.php';

    class Tag extends SoftMapper
    {
        public $table_name = "tags";
        public $columns = [];

        protected $timestamps = true;

        public function __construct()
        {
            parent::__construct();
        }

        /**
         * A tag belongs to many posts (N:N relationship)
         */
        public function posts()
        {
            return $this->belongsToMany('Post', 'post_tag', 'tag_id', 'post_id');
        }
    }
