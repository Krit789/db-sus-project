<?php
require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

class Database
{
    public $mysqli = null;
    private $result = array();
    private $conn = false;

    //connect database using consturcted method
    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
        $dotenv->safeload();
        if (!$this->conn) {

            $this->mysqli = mysqli_connect($_SERVER['DB_HOSTNAME'], $_SERVER['DB_USERNAME'], $_SERVER['DB_PASSWORD'], $_SERVER['DB_DATABASE'], $_SERVER['DB_PORT']);
            $this->mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE);
            $this->conn = true;

            if ($this->mysqli->connect_error) {
                error_log("Database connection failed: " . $this->mysqli->connect_error);
                array_push($this->result, $this);
                die("Database connection failed");
                return false;
            }
        } else {
            $this->mysqli->set_charset("utf8");
            return true;
        }
    }

    // insert data
    public function insert($table, $params = array())
    {
        if ($this->tableExist($table)) {
            $table_column = implode(', ', array_keys($params));
            $table_value = implode("', '", array_values($params));

            $sql = "INSERT INTO $table ($table_column) VALUES ('$table_value')";
            try {

                if ($this->mysqli->query($sql)) {
                    array_push($this->result, true);
                    return true;
                } else {
                    array_push($this->result, false);
                    return false;
                }
            } catch (Exception $e) {
                error_log("Error Occurred with the following query\n-- Query -----------------\n" . $sql . "\n-- Exception -------------\n" . $e . "\n-------------------------");
                $this->result = "{$e}";
                return false;
            }
        } else {
            return false;
        }
    }

    private function tableExist($table)
    {
        $sql = "SHOW TABLES FROM " . $_SERVER['DB_DATABASE'] . " LIKE '{$table}'";
        // error_log($sql);
        $tableInDb = $this->mysqli->query($sql);
        if ($tableInDb) {
            if ($tableInDb->num_rows == 1) {
                return true;
            } else {
                array_push($this->result, $table . " Does not Exist");
            }
        } else {
            return false;
        }
    }

    // get data

    public function insertlegacy($table, $table_column, $table_value)
    {
        if ($this->tableExist($table)) {
            $sql = "INSERT INTO $table ($table_column) VALUES $table_value";
            // echo $sql;
            if ($this->mysqli->query($sql)) {
                array_push($this->result, true);
                return true;
            } else {
                array_push($this->result, false);
                return false;
            }
        } else {
            return false;
        }
    }

    // update data

    public function select($table, $row = "*", $join = null, $where = null, $order = null, $limit = null, $free = null)
    {
        if ($this->tableExist($table)) {
            $sql = "SELECT $row FROM $table";
            if ($free != null)
                $sql .= " $free";
            if ($join != null)
                $sql .= " JOIN $join";
            if ($where != null)
                $sql .= " WHERE $where";
            if ($order != null)
                $sql .= " ORDER BY $order";
            if ($limit != null)
                $sql .= " LIMIT $limit";

            // error_log($sql); #ดูคำสั่ง sql ปิดๆ
            // echo $sql; #ดูคำสั่ง sql ปิดๆ
            try {
                $query = $this->mysqli->query($sql);
                if ($query) {
                    $this->result = $query->fetch_all(MYSQLI_ASSOC);
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                error_log("Error Occurred with the following query\n-- Query -----------------\n" . $sql . "\n-- Exception -------------\n" . $e . "\n-------------------------");
                $this->result = "{$e}";
                return true;
            }
        } else {
            return false;
        }
    }


    // update data

    public function selectAndJoin($table, $row = "*", $left_join = null, $right_join = null, $where = null, $order = null, $limit = null, $free = null)
    {
        if ($this->tableExist($table)) {
            $sql = "SELECT $row FROM $table";
            if ($free != null)
                $sql .= " $free";
            if ($left_join != null)
                $sql .= " LEFT OUTER JOIN $left_join";
            if ($right_join != null)
                $sql .= " RIGHT OUTER JOIN $right_join";
            if ($where != null)
                $sql .= " WHERE $where";
            if ($order != null)
                $sql .= " ORDER BY $order";
            if ($limit != null)
                $sql .= " LIMIT $limit";

            // error_log($sql); #ดูคำสั่ง sql ปิดๆ
            // echo $sql; #ดูคำสั่ง sql ปิดๆ
            try {
                $query = $this->mysqli->query($sql);
                if ($query) {
                    $this->result = $query->fetch_all(MYSQLI_ASSOC);
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                error_log("Error Occurred with the following query\n-- Query -----------------\n" . $sql . "\n-- Exception -------------\n" . $e . "\n-------------------------");
                $this->result = "{$e}";
                return true;
            }
        } else {
            return false;
        }
    }

    // delete data

    public function update($table, $params = array(), $where = null)
    {
        if ($this->tableExist($table)) {
            $arg = array();
            foreach ($params as $key => $val) {
                if ($val == NULL) {
                    $arg[] = "$key = NULL";
                } else {
                    $arg[] = "$key = '{$val}'";
                }
            }
            $sql = "UPDATE $table SET " . implode(', ', $arg);
            if ($where != null) {
                $sql .= " WHERE $where";
            }
            try {
                if ($this->mysqli->query($sql)) {
                    array_push($this->result, true);
                    return true;
                } else {
                    array_push($this->result, false);
                    return false;
                }
            } catch (Exception $e) {
                error_log("Error Occurred with the following query\n-- Query -----------------\n" . $sql . "\n-- Exception -------------\n" . $e . "\n-------------------------");
                array_push($this->result, false);
                return false;
            }
        } else {
            return false;
        }
    }

    // table exist

    public function delete($table, $where = null)
    {
        if ($this->tableExist($table)) {
            $sql = "DELETE FROM $table";
            if ($where != null) {
                $sql .= " WHERE $where";
            }
            if ($this->mysqli->query($sql)) {
                array_push($this->result, true);
                return true;
            } else {
                array_push($this->result, false);
                return false;
            }
        } else {
            return false;
        }
    }

    // get result

    public function getResult()
    {
        $val = $this->result;
        $this->result = array();
        return $val;
    }

    // close the connection
    public function __destruct()
    {
        if ($this->conn) {
            if ($this->mysqli->close()) {
                $this->conn = false;
                return true;
            }
        } else {
            return false;
        }
    }
}
