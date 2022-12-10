<?php

class StudioModel extends BaseModel {

    private $table_name = "studio";

    /**
     * A model class for the `studio` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Retrieve all studio records
     */
    function getAll() {
        $sql = "SELECT * FROM $this->table_name";
        $result = $this->paginate($sql);
        return $result;
    }

    /**
     * Get a single studio record by its ID.
     */
    function getStudioById($studio_id) {
        $sql = "SELECT * FROM $this->table_name WHERE studio_id = ?";
        $result = $this->paginate($sql, [$studio_id]);
        return $result;
    }

    /**
     * Get all studio records whose name match the specified value.
     */
    function getStudioByName($name) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name";
        $result = $this->paginate($sql, [":name" => "%" . $name . "%"]);
        return $result;
    }

    /**
     * Get all studio records whose anime title match the specified value
     */
    function getStudiobyAnimeTitle($animeTitle) {
        $sql = "SELECT studio.*, anime.name FROM $this->table_name
        INNER JOIN production 
            ON production.studio_id = $this->table_name.studio_id
        INNER JOIN anime
            ON anime.anime_id =  production.anime_id  
         WHERE anime.name LIKE :animeTitle";
        $result = $this->paginate($sql, [":animeTitle" => "%" . $animeTitle . "%"]);
        return $result;
    }

    /**
     * Get all studio records whose anime id match the specified value
     */
    function getStudiobyAnimeId($anime_id) {
        $sql = "SELECT studio.*, anime.name FROM $this->table_name
        INNER JOIN production 
            ON production.studio_id = $this->table_name.studio_id
        INNER JOIN anime
            ON anime.anime_id =  production.anime_id  
        WHERE anime.anime_id = ?";
        $result = $this->paginate($sql, [$anime_id]);
        return $result;
    }
}
