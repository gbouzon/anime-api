<?php

class UserModel extends BaseModel {

    private $table_name = "user";

    /**
     * A model class for the `user` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Get all user records.
     */
    function getAll() {
        $sql = "SELECT * FROM $this->table_name";
        $result = $this->run($sql);
        return $result;
    }

    /**
     * Get a single user record by its ID.
     */
    function getUserById($user_id) {
        $sql = "SELECT * FROM $this->table_name WHERE user_id = ?";
        $result = $this->run($sql, [$user_id])->fetch();
        return $result;
    }
}
