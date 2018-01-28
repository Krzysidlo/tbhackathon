<?php

namespace functions;

interface DCInterface {

    function rules();

    function defaultValues();

    function attributeLabels();

    static function tableName();

    static function dbName();

    static function find();

    static function findOne(int $primaryKey = null);

    static function findAll();

    function all(bool $array = false);

    function one();

    function last(int $num = 1, int $addOffset = 0);

    function lastOne(int $offset = 0);

    function where(...$conditions);

    function andWhere(...$conditions);

    function orWhere(...$conditions);

    public function orderBy(string $order);

    function load(array $attrVal);

    function limit(int $num);

    function offset(int $num);

    function createProperty($name, $value);

    function save();

    function ObjectKeys();

    function PrimaryKey();

    static function hashPassword(string $password, $salt);

    static function className();

    function Values();

    function beforeSave($oldAttributes, $attributes);

    function afterSave($oldAttributes, $attributes);

    function beforeDelete($attributes);

    function afterDelete($attributes);
}