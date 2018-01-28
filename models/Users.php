<?php
namespace models;

use components\DatabaseConnector;

class Users extends DatabaseConnector {

    public static function tableName() {
        return 'users';
    }

    public function rules() {
        return [
            [['username', 'password'], 'required'],
            ['username', 'trim'],
            ['username', 'unique'],
        ];
    }

    public function attributeLabels() {
        return [
            'username' => "Username",
            'password' => "Password",
            'name' => "Name",
            'image' => "Add picture",
            'email' => "E-mail address",
        ];
    }

    public function defaultValues() {
        return [];
    }

    public function getName() {
        return $this->username;
    }
}