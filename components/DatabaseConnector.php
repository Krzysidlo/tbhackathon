<?php
namespace components;

use functions\DCInterface;

use models\OfferImg;
use models\Offers;
use mysqli;
use Exception;

/**
 * Class DatabaseConnector
 * Abstract class for connecting to database
 * Every model extends this class
 * @package components
 */
abstract class DatabaseConnector implements DCInterface {

    public static $tableName = null;
    public static $query;

    protected static $mysqli;

    private $getNames = [];
    private $result;

    private static $db;

    /**
     * Function for checking fields in form.
     * In every row first value must be the attribute of field and last name of validator.
     * All available validators are in \backend\components\Validator class
     * Only default validator is not in \backend\components\Validator class but in DatabaseConnector in save function
     * Validator must return true or false
     * @return array rules
     */
    abstract function rules();

    public function defaultValues() {
        return [];
    }

    abstract function attributeLabels();

    abstract static function tableName();

    public static function dbName() {
        return self::$db;
    }

    function __construct() {
        if (static::tableName() === null) {
            throw new Exception("Table name not set");
        }
        $data = include(CONF_DIR . "/db.php");
        self::$db = $data['database'];
        self::$mysqli = new mysqli($data['host'], $data['username'], $data['password'], $data['database']);
        self::$mysqli->set_charset("utf8");

        if ($query = self::$mysqli->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '" . static::tableName() . "' AND TABLE_SCHEMA = '" . static::dbName() . "'")) {
            while($result = $query->fetch_assoc()) {
                $key = reset($result);
                $this->createProperty($key, "");
            }
        }
        foreach (get_class_methods(self::className()) as $methodName) {
            if (substr($methodName, 0, 3) === "get") {
                $name = lcfirst(substr($methodName, 3));
                $this->createProperty($name, []);
                $query = self::$mysqli->query("SHOW COLUMNS FROM `" . $this->tableName() . "` LIKE '" . $name . "'");
                if ($query->num_rows !== 1) {
                    if (!in_array($name, $this->getNames)) {
                        $this->getNames[] = $name;
                    }
                }
            }
        }
    }

    public static function find() {
        self::setQuery("SELECT * FROM `" . static::tableName() . "`");
        $model = get_called_class();
        return new $model;
    }

    public static function findOne(int $primaryKey = null) {
        if ($primaryKey === null) {
            throw new Exception("Primary key not set");
        } else {
            $model = get_called_class();
            $model = new $model;
            if ($query = $model::$mysqli->query("SHOW KEYS FROM `" . static::tableName() . "` WHERE Key_name = 'PRIMARY'")) {
                $result = $query->fetch_object();
                $primaryKeyName = $result->Column_name;
                $model->createProperty("primaryKey", (int) $primaryKey);
                return self::find()->where([$primaryKeyName => $primaryKey])->one();
            } else {
                throw new Exception("Error occurred");
            }
        }
    }

    public static function findAll() {
        return self::find()->all();
    }

    public function all(bool $array = false) {
        $columnsArr = [];
        try {
            if ($query = self::$mysqli->query(self::$query)) {

                $result = $query->fetch_fields();

                foreach ($result as $val) {
                    $columnsArr[] = $val->name;
                }

                $this->result = $query->fetch_all(MYSQLI_ASSOC);

                if ($primaryKeyName = $this->PrimaryKey()) {
                    foreach ($this->result as $key => $result) {
                        $this->result[$key]['primaryKey'] = $result[$primaryKeyName];
                        if (is_numeric($result[$primaryKeyName])) {
                            $this->result[$key]['primaryKey'] = (int) $result[$primaryKeyName];
                        }
                    }
                }

                foreach ($this->result as &$row) {
                    foreach ($row as $attribute => &$value) {
                        if (is_numeric($value)) {
                            if ((float) $value == (int) $value) {
                                $value = (int) $value;
                            }
                        }
                        if ($value === null) {
                            $value = "null";
                        }
                    }
                }

                if ($array) {
                    $result = $this->asArray();
                } else {
                    $result = $this->asObject();
                }

                foreach ($result as &$model) {
                    foreach (get_class_methods(self::className()) as $methodName) {
                        if (substr($methodName, 0, 3) === "get") {
                            if ($array) {
                                $model[lcfirst(substr($methodName, 3))] = $model->{$methodName}();
                            } else {
                                $model->createProperty(lcfirst(substr($methodName, 3)), $model->{$methodName}());
                            }
                        }
                    }
                }
            } else {
                $result = false;
            }

            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function one() {
        $this->appendQuery(" LIMIT 1");
        $return = $this->all();
        if (is_array($return) && !empty($return)) {
            $return = $return[0];
        } else if (empty($return)) {
            $return = false;
        }
        return $return;
    }

    public function last(int $limit = 1, int $offset = 0) {
        $columnsArr = [];

        $primaryKeyName = $this->PrimaryKey();
        $this->appendQuery(" ORDER BY " . $primaryKeyName . " DESC LIMIT " . $limit . " OFFSET " . $offset);

        try {
            if ($query = self::$mysqli->query(self::$query)) {

                $result = $query->fetch_fields();

                foreach ($result as $val) {
                    $columnsArr[] = $val->name;
                }

                $this->result = $query->fetch_all(MYSQLI_ASSOC);

                if ($primaryKeyName) {
                    foreach ($this->result as $key => $result) {
                        $this->result[$key]['primaryKey'] = $result[$primaryKeyName];
                        if (is_numeric($result[$primaryKeyName])) {
                            $this->result[$key]['primaryKey'] = (int) $result[$primaryKeyName];
                        }
                    }
                }

                foreach ($this->result as &$row) {
                    foreach ($row as $attribute => &$value) {
                        if (is_numeric($value)) {
                            if ((float) $value == (int) $value) {
                                $value = (int) $value;
                            }
                        }
                        if ($value === null) {
                            $value = "null";
                        }
                    }
                }

                $result = $this->asObject();

                foreach ($result as &$model) {
                    foreach (get_class_methods(self::className()) as $methodName) {
                        if (substr($methodName, 0, 3) === "get") {
                            $model->createProperty(lcfirst(substr($methodName, 3)), $model->{$methodName}());
                        }
                    }
                }
            } else {
                $result = false;
            }

            if (is_array($result)) {
                $result = array_reverse($result);
            }

            if (count($result) === 1) {
                $result = array_shift($result);
            }

            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function lastOne(int $offset = 0) {
        $model = get_called_class();
        $model = new $model;
        if ($query = $model::$mysqli->query("SHOW KEYS FROM `" . static::tableName() . "` WHERE Key_name = 'PRIMARY'")) {
            $result = $query->fetch_object();
            $primaryKeyName = $result->Column_name;
            $this->orderBy($primaryKeyName . " DESC");
            $this->appendQuery(" LIMIT " . $offset . ",1");
            $return = $this->all();
        } else {
            throw new Exception("Error occurred");
        }
        if (is_array($return) && !empty($return)) {
            $return = $return[0];
        } else if (empty($return)) {
            $return = false;
        }
        return $return;
    }

    private function andOrWhere(string $andOrWhere, ...$conditions) {
        $first = true;
        $andOr = null;
        foreach ($conditions as $key => $condition) {
            if (is_string($condition)) {
                $andOr = $condition;
                unset($conditions[$key]);
            }
        }
        if ($andOr === null) {
            $andOr = "AND";
        }
        foreach ($conditions as $condition) {
            if ($first) {
                $AndWhere = " " . $andOrWhere . " (";
            } else {
                $AndWhere = " " . $andOr . " ";
            }
            if (!isAssoc($condition)) {
                if (count($condition) === 3) {
                    $sign = " " . $condition[1] . " ";
                    $colName = $condition[0];
                    $value = $condition[2];
                    if (is_string($condition[2])) {
                        $value = "'" . $condition[2] . "'";
                    }
                    $this->appendQuery($AndWhere . $colName . " " . $sign . " " . $value);
                }
            } else {
                $this->appendQuery($AndWhere . "(");
                $firstInArray = true;
                foreach ($condition as $colName => $value) {
                    $and = "";
                    if (!$firstInArray) {
                        $and = " AND ";
                    }
                    $sign = " = ";
                    if (is_string($value)) {
                        $sign = " LIKE ";
                        $value = "'" . $value . "'";
                    }
                    if (is_numeric($colName)) {
                        $this->appendQuery($and . $this->PrimaryKey() . $sign . $value);
                    } else {
                        $this->appendQuery($and . $colName . $sign . $value);
                    }
                    $firstInArray = false;
                }
                $this->appendQuery(")");
            }
            $first = false;
        }
        $this->appendQuery(")");

        return $this;
    }

    public function where(...$conditions) {
        return $this->andOrWhere("WHERE", ...$conditions);
    }

    public function andWhere(...$conditions) {
        return $this->andOrWhere("AND", ...$conditions);
    }

    public function orWhere(...$conditions) {
        return $this->andOrWhere("OR", ...$conditions);
    }

    public function orderBy(string $order) {
        if (substr($order, -3) === "ASC") {
            $order = substr($order, 0, -4);
            $asc = "ASC";
        } elseif (substr($order, -4) === "DESC") {
            $order = substr($order, 0, -5);
            $asc = "DESC";
        }
        $this->appendQuery(" ORDER BY `" . $order . "` " . $asc);
        return $this;
    }

    public function load(array $attrVal) {
        $return = (empty($attrVal) ? false : true);
        foreach ($attrVal as $attribute => $value) {
            if (isset($this->{$attribute})) {
                $this->{$attribute} = $value;
            } else {
                throw new Exception("Invalid attribute" . ": " . $attribute);
            }
        }
        return $return;
    }

    public function limit(int $num) {
        $this->appendQuery(" LIMIT " . $num);
        return $this;
    }

    public function offset(int $num) {
        $this->appendQuery(" OFFSET " . $num);
    }

    private function asObject() {
        $counter = 0;
        $resultArr = [];
        foreach ($this->result as $row) {
            $resultArr[$counter] = get_called_class();
            $resultArr[$counter] = new $resultArr[$counter];
            foreach ($row as $key => $value) {
                $resultArr[$counter]->createProperty($key, $value);
            }
            $counter++;
        }
        return $resultArr;
    }

    private function asArray() {
        return $this->result;
    }

    public function createProperty($name, $value) {
        $this->{$name} = $value;
    }

    public function save($debug = false) {
        $return = false;

        $attributes = get_object_vars($this);
        if (isset($this->primaryKey)) {
            $oldAttributes = get_object_vars(self::className()::findOne($this->primaryKey));
        } else {
            $oldAttributes = [];
        }
        foreach (get_class_methods(self::className()) as $method) {
            if (substr($method, 0, 3) === "get") {
                $query = self::$mysqli->query("SHOW COLUMNS FROM `" . $this->tableName() . "` LIKE '" . lcfirst(substr($method, 3)) . "'");
                if ($query->num_rows !== 1) {
                    unset($oldAttributes[lcfirst(substr($method, 3))], $attributes[lcfirst(substr($method, 3))]);
                }
            }
        }

        if (method_exists($this, 'beforeSave')) {
            $this->beforeSave($oldAttributes, $attributes);
        }

        $exists = isset($this->primaryKey);

        $rules = $this->rules();

        $rulesArr = [
            'default',
        ];
        foreach ($rules as $rule) {
            if (in_array(end($rule), $rulesArr)) {
                reset($rule);
                $val = current($rule);
                switch (end($rule)) {
                    case "default":
                        if ($this->{$val} === null || $this->{$val} === "" || $this->{$val} === " ") {
                            $this->{$val} = $rule['value'];
                        }
                        break;
                }
            } else {
                continue;
            }
        }

        $keyArr = $this->ObjectKeys();

        if ($exists) {
            $query = "UPDATE `" . static::tableName() . "` SET ";

            foreach ($keyArr as $key => $value) {
                if (!in_array($key, $this->getNames)) {
                    if (is_string($value)) {
                        if ($value !== "null") {
                            $value = "'" . $value . "'";
                        }
                    }
                    $query .= "`" . $key . "`" . " = " . $value . ", ";
                }
            }
            $query = substr($query, 0, -2);

            $primaryKeyName = $this->PrimaryKey();
            $query .= " WHERE " . $primaryKeyName . " = " . $this->primaryKey;
        } else {
            $query = "INSERT INTO `" . static::tableName() . "` (";
            foreach ($keyArr as $key => $value) {
                if (!in_array($key, $this->getNames)) {
                    $query .= "`" . $key . "`, ";
                }
            }
            $query = substr($query, 0, -2);
            $query .= ") VALUES (";

            $default = $this->defaultValues();
            foreach ($keyArr as $key => $value) {
                if (!in_array($key, $this->getNames)) {
                    if ($value === "") {
                        if (array_key_exists($key, $default)) {
                            $value = $default[$key];
                            $this->{$key} = $default[$key];
                        }
                    }
                    if (is_string($value)) {
                        if ($value !== "null") {
                            $value = "'" . $value . "'";
                        }
                    }
                    $query .= $value . ", ";
                }
            }
            $query = substr($query, 0, -2);
            $query .= ")";
        }
        self::setQuery($query);

        if ($query = self::$mysqli->query(self::$query)) {
            if (!$exists) {
                $newId = self::$mysqli->insert_id;
                $pkName = $this->PrimaryKey();
                $this->{$pkName} = $this->primaryKey = $newId;
            }
            $return = true;
        }

        if ($return) {
            if (method_exists($this, 'afterSave')) {
                $this->afterSave($oldAttributes, $attributes);
            }
        }
        return $return;
    }

    public function delete() {
        $return = false;

        $attributes = get_object_vars($this);

        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete($attributes);
        }

        if (isset($this->primaryKey)) {
            $primaryKeyName = $this->PrimaryKey();
            $query = "DELETE FROM `" . static::tableName() . "` WHERE " . $primaryKeyName . " = " . $this->primaryKey;
            self::setQuery($query);

            if (self::$mysqli->query(self::$query)) {
                $return = true;
            }
        }

        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete($attributes);
        }

        return $return;
    }

    public function ObjectKeys() {
        $keys = get_object_vars($this);
        unset($keys['result']);
        unset($keys['primaryKey']);
        unset($keys['getNames']);
        unset($keys[$primaryKeyName = $this->PrimaryKey()]);
        return $keys;
    }

    public function PrimaryKey() {
        $primaryKeyName = null;
        if ($query = self::$mysqli->query("SHOW KEYS FROM `" . static::tableName() . "` WHERE Key_name = 'PRIMARY'")) {
            $primaryKey = $query->fetch_array();
            $primaryKeyName = $primaryKey['Column_name'];
        }
        return $primaryKeyName;
    }

    public static function hashPassword(string $password, $salt) {
        return password_hash($password, PASSWORD_DEFAULT, ['salt' => $salt]);
    }

    private static function setQuery($query) {
        self::$query = $query;
        return true;
    }

    private function appendQuery($query) {
        self::$query .= $query;
        return true;
    }

    public static function className() {
        return get_called_class();
    }

    public function Values() {
        return json_encode(get_object_vars($this));
    }

    public function beforeSave($oldAttributes, $attributes) {

    }

    public function afterSave($oldAttributes, $attributes) {

    }

    public function beforeDelete($attributes) {

    }

    public function afterDelete($attributes) {

    }
}