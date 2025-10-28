<?php
    /**
     * Created by PhpStorm.
     * User: Mohammed Elamin
     * Date: 12/12/2018
     * Time: 06:33
     */

    require_once 'env.php';

    class SoftMapper
    {

        /**
         * PDO instance for database operations
         * @var PDO
         */
        private $pdo;

        /**
         * Initial query for complex queries
         * @var string
         */
        private $built_query = 'SELECT * FROM ';

        /**
         * Selected columns specified by select function
         * @var string
         */
        private $selected_columns = '';

        /**
         * Values for columns used by prepared statement
         * @var array
         */
        private $query_columns_place_holder_array;

        /**
         * Table name for the model
         * @var string
         */
        public $table_name;

        /**
         * Entity columns
         * @var array
         */
        public $columns;

        /**
         * Primary key column name
         * @var string
         */
        protected $primary_key = 'id';

        /**
         * Enable automatic timestamp management
         * @var bool
         */
        protected $timestamps = true;

        /**
         * Enable soft delete functionality
         * @var bool
         */
        protected $soft_deletes = false;

        /**
         * Custom query scopes
         * @var array
         */
        protected $scopes = [];

        /**
         * @var bool
         */
        private $update_switch = false;

        /**
         * @var bool
         */
        private $with_trashed = false;


        /**
         * SoftMapper constructor.
         * Initializes database connection using environment configuration
         */
        public function __construct()
        {
            $dsn = 'mysql:host=' . host . ';dbname=' . dbname;
            $this->pdo = new PDO($dsn, user, password);
        }

        /**
         * Get all rows from table
         * @return $this
         */
        public function all()
        {
            $this->built_query .= $this->table_name;
            return $this;
        }

        /**
         * Apply soft delete filter if needed
         * @return void
         */
        private function applySoftDeleteFilter()
        {
            if ($this->soft_deletes && !$this->with_trashed) {
                // Add soft delete filter to existing WHERE clause
                if (strpos($this->built_query, 'where') !== false || strpos($this->built_query, 'WHERE') !== false) {
                    $this->built_query .= "\t AND \t deleted_at IS NULL";
                } else {
                    $this->built_query .= "\t WHERE \t deleted_at IS NULL";
                }
            }
        }

        /**
         * Select rows from table with optional aggregate functions
         * @param array $columns Columns to select
         * @param string $aggregate_function Aggregate function (e.g., COUNT, SUM, AVG)
         * @param string $aggregate_parameter Parameter for aggregate function
         * @return $this
         */
        public function select($columns = array(), $aggregate_function = '', $aggregate_parameter = null)
        {
            // Reset old query for selection
            $this->built_query = '';

            if (!empty($columns)) {
                $this->selected_columns .= implode(',', $columns);
                if (!empty($aggregate_function))
                    $this->built_query .= 'SELECT' . "\t" . $this->selected_columns . ",\t" . $aggregate_function . '(' . $aggregate_parameter . ')' . "\t FROM \t" . $this->table_name;
                else
                    $this->built_query .= 'SELECT' . "\t" . $this->selected_columns . "\t FROM \t" . $this->table_name;
            } else {
                if (!empty($aggregate_function))
                    $this->built_query .= 'SELECT' . "\t" . $aggregate_function . '(' . $aggregate_parameter . ')' . "\t FROM \t" . $this->table_name;
            }

            return $this;
        }

        /**
         * Execute query and fetch all results
         * This method follows most methods included here to execute generated query
         * @return array
         */
        public function getAll()
        {
            $this->applySoftDeleteFilter();
            $stmt = $this->pdo->prepare($this->built_query);
            $stmt->execute($this->query_columns_place_holder_array);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $result;
        }

        /**
         * Execute query and fetch single result
         * @return mixed
         */
        public function get()
        {
            $this->applySoftDeleteFilter();
            $stmt = $this->pdo->prepare($this->built_query);
            $stmt->execute($this->query_columns_place_holder_array);
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result;
        }

        /**
         * Execute the built query
         * @return bool
         */
        public function execute()
        {
            $stmt = $this->pdo->prepare($this->built_query);
            $result = $stmt->execute($this->query_columns_place_holder_array);
            return $result;
        }


        /**
         * Add conditions to the built query
         * @param array $conditions Array of conditions [[column, operator, value, logical_operator], ...]
         * @return $this
         */
        public function where($conditions = array())
        {
            $this->query_columns_place_holder_array = array();

            $where_query = "\t" . 'where' . "\t";

            for ($i = 0; $i < sizeof($conditions); $i++) {
                $values = $conditions[$i];
                $index = $values[0];
                $this->query_columns_place_holder_array[$index] = $values[2];
                if (isset($values[3]))
                    $where_query .= "\t" . $values[0] . $values[1] . ' :' . $values[0] . "\t" . $values[3];
                else
                    $where_query .= "\t" . $values[0] . $values[1] . ' :' . $values[0];
            }
            $this->built_query .= $where_query;

            if ($this->update_switch) {
                $keys = array_keys($this->columns);
                $values = array_values($this->columns);
                // Add update data to be added to prepared statement
                for ($i = 0; $i < sizeof($this->columns); $i++)
                    $this->query_columns_place_holder_array['UP_' . $keys[$i]] = $values[$i];
            }

            return $this;
        }

        /**
         * Add ORDER BY clause to query
         * @param string $column_name Column to order by
         * @param string $ordering_type Order type (ASC or DESC)
         * @return $this
         */
        public function orderBy($column_name, $ordering_type)
        {
            $this->built_query .= "\t" . 'order by' . "\t" . $column_name . "\t" . $ordering_type;
            return $this;
        }

        /**
         * Limit number of selected rows
         * @param int $limitation_number Number of rows to limit
         * @return $this
         */
        public function limit($limitation_number)
        {
            $this->built_query .= "\t" . 'LIMIT' . "\t" . $limitation_number;
            return $this;
        }

        /**
         * Offset for pagination (use with limit)
         * @param int $offset_number Number of rows to skip
         * @return $this
         */
        public function offset($offset_number)
        {
            $this->built_query .= "\t" . 'OFFSET' . "\t" . $offset_number;
            return $this;
        }

        /**
         * Group results by column (used with aggregate functions)
         * @param string $grouper Column to group by
         * @return $this
         */
        public function groupBy($grouper)
        {
            $this->built_query .= "\t group by \t" . $grouper;
            return $this;
        }

        /**
         * Add HAVING clause (used as condition for aggregate functions)
         * @param array $conditions Array of conditions [[column, operator, value, logical_operator], ...]
         * @return $this
         */
        public function having($conditions = array())
        {
            $this->query_columns_place_holder_array = array();
            $having_query = "\t" . 'HAVING' . "\t";
            for ($i = 0; $i < sizeof($conditions); $i++) {
                $values = $conditions[$i];
                $index = $values[0];
                $this->query_columns_place_holder_array[$index] = $values[2];
                if (isset($values[3]))
                    $having_query .= "\t" . $values[0] . $values[1] . ' :' . $values[0] . "\t" . $values[3];
                else
                    $having_query .= "\t" . $values[0] . $values[1] . ' :' . $values[0];
            }
            $this->built_query .= $having_query;

            return $this;
        }

        /**
         * Insert data into table
         * @return bool
         */
        public function insert()
        {
            // Add timestamps if enabled
            if ($this->timestamps) {
                if (!isset($this->columns['created_at'])) {
                    $this->columns['created_at'] = date('Y-m-d H:i:s');
                }
                if (!isset($this->columns['updated_at'])) {
                    $this->columns['updated_at'] = date('Y-m-d H:i:s');
                }
            }

            $columns = array_keys($this->columns);
            $values_indicator = array();
            for ($i = 0; $i < sizeof($columns); $i++) {
                array_push($values_indicator, ':' . $columns[$i]);
            }
            $values_pointers = implode(',', $values_indicator);
            $columns_name = implode(',', $columns);
            $query = 'INSERT INTO ' . $this->table_name . "\t" . '(' . $columns_name . ') ' . "values (" . $values_pointers . ')';
            $stmt_insert = $this->pdo->prepare($query);
            $insert_a_row = $stmt_insert->execute($this->columns);
            return $insert_a_row;
        }

        /**
         * Find a record by its primary key (id)
         * @param mixed $key Primary key value
         * @return mixed
         */
        public function find($key)
        {
            return $result = $this->all()->where([[$this->primary_key, '=', $key]])->get();
        }

        /**
         * Delete records from table
         * @param bool $force Force delete (ignore soft deletes)
         * @return $this
         */
        public function delete($force = false)
        {
            // Use soft delete if enabled and not forced
            if ($this->soft_deletes && !$force) {
                $this->columns['deleted_at'] = date('Y-m-d H:i:s');
                if ($this->timestamps) {
                    $this->columns['updated_at'] = date('Y-m-d H:i:s');
                }
                return $this->update();
            }
            
            $this->built_query = "DELETE  FROM\t" . $this->table_name;
            return $this;
        }

        /**
         * Update records in table
         * @return $this
         */
        public function update()
        {
            // Add updated_at timestamp if enabled
            if ($this->timestamps && !isset($this->columns['updated_at'])) {
                $this->columns['updated_at'] = date('Y-m-d H:i:s');
            }

            $keys = array_keys($this->columns);
            $values = array_values($this->columns);
            $keys_for_prepare = array();
            for ($i = 0; $i < sizeof($keys); $i++) {
                array_push($keys_for_prepare, $keys[$i] . "=:" . 'UP_' . $keys[$i] . "\t");
            }
            $query = 'UPDATE  ' . $this->table_name . "\t" . 'SET ' . implode(',', $keys_for_prepare);
            $this->built_query = $query;
            $this->update_switch = true;
            return $this;
        }

        /**
         * Include soft deleted records in query results
         * @return $this
         */
        public function withTrashed()
        {
            $this->with_trashed = true;
            return $this;
        }

        /**
         * Get only soft deleted records
         * @return $this
         */
        public function onlyTrashed()
        {
            if ($this->soft_deletes) {
                $this->with_trashed = true; // Don't filter in applySoftDeleteFilter
                if (strpos($this->built_query, $this->table_name) === false) {
                    $this->built_query .= $this->table_name;
                }
                $this->built_query .= "\t WHERE \t deleted_at IS NOT NULL";
            }
            return $this;
        }

        /**
         * Restore soft deleted records
         * @return $this
         */
        public function restore()
        {
            if ($this->soft_deletes) {
                $this->columns['deleted_at'] = NULL;
                if ($this->timestamps) {
                    $this->columns['updated_at'] = date('Y-m-d H:i:s');
                }
                return $this->update();
            }
            return $this;
        }

        /**
         * Insert multiple records at once
         * @param array $records Array of associative arrays with column => value
         * @return bool
         */
        public function insertMany($records)
        {
            if (empty($records)) {
                return false;
            }

            $pdo = $this->pdo;
            $pdo->beginTransaction();

            try {
                foreach ($records as $record) {
                    $this->columns = $record;
                    $this->insert();
                }
                $pdo->commit();
                return true;
            } catch (Exception $e) {
                $pdo->rollBack();
                return false;
            }
        }

        /**
         * WHERE IN clause
         * @param string $column Column name
         * @param array $values Array of values
         * @return $this
         */
        public function whereIn($column, $values)
        {
            if (empty($values)) {
                return $this;
            }

            $placeholders = [];
            $this->query_columns_place_holder_array = $this->query_columns_place_holder_array ?: [];

            foreach ($values as $index => $value) {
                $placeholder = $column . '_' . $index;
                $placeholders[] = ':' . $placeholder;
                $this->query_columns_place_holder_array[$placeholder] = $value;
            }

            $this->built_query .= "\t WHERE \t" . $column . ' IN (' . implode(',', $placeholders) . ')';
            return $this;
        }

        /**
         * WHERE NOT IN clause
         * @param string $column Column name
         * @param array $values Array of values
         * @return $this
         */
        public function whereNotIn($column, $values)
        {
            if (empty($values)) {
                return $this;
            }

            $placeholders = [];
            $this->query_columns_place_holder_array = $this->query_columns_place_holder_array ?: [];

            foreach ($values as $index => $value) {
                $placeholder = $column . '_' . $index;
                $placeholders[] = ':' . $placeholder;
                $this->query_columns_place_holder_array[$placeholder] = $value;
            }

            $this->built_query .= "\t WHERE \t" . $column . ' NOT IN (' . implode(',', $placeholders) . ')';
            return $this;
        }

        /**
         * WHERE BETWEEN clause
         * @param string $column Column name
         * @param mixed $start Start value
         * @param mixed $end End value
         * @return $this
         */
        public function whereBetween($column, $start, $end)
        {
            $this->query_columns_place_holder_array = $this->query_columns_place_holder_array ?: [];
            $this->query_columns_place_holder_array[$column . '_start'] = $start;
            $this->query_columns_place_holder_array[$column . '_end'] = $end;

            $this->built_query .= "\t WHERE \t" . $column . ' BETWEEN :' . $column . '_start AND :' . $column . '_end';
            return $this;
        }

        /**
         * WHERE NULL clause
         * @param string $column Column name
         * @return $this
         */
        public function whereNull($column)
        {
            $this->built_query .= "\t WHERE \t" . $column . ' IS NULL';
            return $this;
        }

        /**
         * WHERE NOT NULL clause
         * @param string $column Column name
         * @return $this
         */
        public function whereNotNull($column)
        {
            $this->built_query .= "\t WHERE \t" . $column . ' IS NOT NULL';
            return $this;
        }

        /**
         * Get the first record
         * @return mixed
         */
        public function first()
        {
            return $this->limit(1)->get();
        }

        /**
         * Count records
         * @return int
         */
        public function count()
        {
            $result = $this->select([], 'COUNT', '*')->get();
            return $result ? (int)$result->{'COUNT(*)'} : 0;
        }

        /**
         * Check if records exist
         * @return bool
         */
        public function exists()
        {
            return $this->count() > 0;
        }

        /**
         * Pluck a single column's values
         * @param string $column Column name
         * @return array
         */
        public function pluck($column)
        {
            $results = $this->select([$column])->getAll();
            return array_map(function($item) use ($column) {
                return $item->$column;
            }, $results);
        }

        /**
         * Define a query scope
         * @param string $name Scope name
         * @param callable $callback Callback function that modifies the query
         * @return void
         */
        public function scope($name, $callback)
        {
            $this->scopes[$name] = $callback;
        }

        /**
         * Apply a query scope
         * @param string $name Scope name
         * @param mixed ...$args Additional arguments for the scope
         * @return $this
         */
        public function applyScope($name, ...$args)
        {
            if (isset($this->scopes[$name])) {
                call_user_func_array($this->scopes[$name], array_merge([$this], $args));
            }
            return $this;
        }

        /**
         * Start a database transaction
         * @return bool
         */
        public function beginTransaction()
        {
            return $this->pdo->beginTransaction();
        }

        /**
         * Commit a database transaction
         * @return bool
         */
        public function commit()
        {
            return $this->pdo->commit();
        }

        /**
         * Rollback a database transaction
         * @return bool
         */
        public function rollback()
        {
            return $this->pdo->rollBack();
        }

        /**
         * Execute a raw SQL query
         * @param string $query SQL query
         * @param array $params Parameters for prepared statement
         * @return mixed
         */
        public function raw($query, $params = [])
        {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }

        /**
         * Get the last inserted ID
         * @return string
         */
        public function lastInsertId()
        {
            return $this->pdo->lastInsertId();
        }

        /**
         * Chunk results for memory-efficient processing
         * @param int $size Chunk size
         * @param callable $callback Callback to process each chunk
         * @return void
         */
        public function chunk($size, $callback)
        {
            $page = 0;
            do {
                $results = $this->limit($size)->offset($page * $size)->getAll();
                if (empty($results)) {
                    break;
                }
                call_user_func($callback, $results);
                $page++;
            } while (count($results) === $size);
        }

        /**
         * Simple JOIN support
         * @param string $table Table to join
         * @param string $first First column
         * @param string $operator Operator
         * @param string $second Second column
         * @param string $type Join type (INNER, LEFT, RIGHT)
         * @return $this
         */
        public function join($table, $first, $operator, $second, $type = 'INNER')
        {
            $this->built_query .= "\t" . $type . ' JOIN ' . $table . ' ON ' . $first . $operator . $second;
            return $this;
        }

        /**
         * LEFT JOIN
         * @param string $table Table to join
         * @param string $first First column
         * @param string $operator Operator
         * @param string $second Second column
         * @return $this
         */
        public function leftJoin($table, $first, $operator, $second)
        {
            return $this->join($table, $first, $operator, $second, 'LEFT');
        }

        /**
         * RIGHT JOIN
         * @param string $table Table to join
         * @param string $first First column
         * @param string $operator Operator
         * @param string $second Second column
         * @return $this
         */
        public function rightJoin($table, $first, $operator, $second)
        {
            return $this->join($table, $first, $operator, $second, 'RIGHT');
        }

        /**
         * Get distinct records
         * @return $this
         */
        public function distinct()
        {
            $this->built_query = str_replace('SELECT', 'SELECT DISTINCT', $this->built_query);
            return $this;
        }

        /**
         * Create or update a record
         * @param array $attributes Attributes to search for
         * @param array $values Values to update or insert
         * @return bool
         */
        public function updateOrCreate($attributes, $values = [])
        {
            $conditions = [];
            foreach ($attributes as $key => $value) {
                $conditions[] = [$key, '=', $value];
            }

            $existing = $this->all()->where($conditions)->get();

            if ($existing) {
                $this->columns = array_merge($attributes, $values);
                return $this->update()->where($conditions)->execute();
            } else {
                $this->columns = array_merge($attributes, $values);
                return $this->insert();
            }
        }

    }
