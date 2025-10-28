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
         * @var bool
         */
        private $update_switch = false;


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

            if (isset($columns)) {
                $this->selected_columns .= implode(',', $columns);
                if (isset($aggregate_function))
                    $this->built_query .= 'SELECT' . "\t" . $this->selected_columns . ",\t" . $aggregate_function . '(' . $aggregate_parameter . ')' . "\t FROM \t" . $this->table_name;
                else
                    $this->built_query .= 'SELECT' . "\t" . $this->selected_columns . "\t FROM \t" . $this->table_name;
            } else {
                if (isset($aggregate_function))
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
            return $result = $this->all()->where([['id', '=', $key]])->get();
        }

        /**
         * Delete records from table
         * @return $this
         */
        public function delete()
        {
            $this->built_query = "DELETE  FROM\t" . $this->table_name;
            return $this;
        }

        /**
         * Update records in table
         * @return $this
         */
        public function update()
        {
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

    }
