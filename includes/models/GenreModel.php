<?php

class GenreModel extends BaseModel {

    private $table_name = "genre";

    /**
     * A model class for the `anime` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Get all genre records.
     */
    function getAll() {
        $sql = "SELECT * FROM $this->table_name";
        $result = $this->run($sql);
        return $result;
    }

    /**
     * Get a single genre record by its ID.
     */
    function getGenreById($genre_id) {
        $sql = "SELECT * FROM $this->table_name WHERE genre_id = ?";
        $result = $this->run($sql, [$genre_id])->fetch();
        return $result;
    }

    /**
     * Get all genre records whose name match the specified value.
     */
    function getGenreByName($name) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name";
        $result = $this->run($sql, [":name" => "%" . $name . "%"])->fetchAll();
        return $result;
    }
}
