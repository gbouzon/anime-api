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

    function getAll() {
        $sql = "SELECT * FROM $this->table_name";
        $result = $this->run($sql)->fetchAll();
        return $result;
    }

    function getStudioById($studio_id) {
        $sql = "SELECT * FROM $this->table_name WHERE studio_id = ?";
        $result = $this->run($sql, [$studio_id])->fetch();
        return $result;
    }

    function getStudioByName($name) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name";
        $result = $this->run($sql, [":name" => "%" . $name . "%"])->fetchAll();
        return $result;
    }

    function getStudiobyAnimeTitle($animeTitle) {
        $sql = "SELECT studio.*, anime.name FROM $this->table_name
        INNER JOIN production 
            ON production.studio_id = $this->table_name.studio_id
        INNER JOIN anime
            ON anime.anime_id =  production.anime_id  
         WHERE anime.name LIKE :animeTitle";
        $result = $this->run($sql, [":animeTitle" => "%" . $animeTitle . "%"])->fetchAll();
        return $result;
    }

    function getStudiobyAnimeId($anime_id) {
        $sql = "SELECT studio.*, anime.name FROM $this->table_name
        INNER JOIN production 
            ON production.studio_id = $this->table_name.studio_id
        INNER JOIN anime
            ON anime.anime_id =  production.anime_id  
        WHERE anime.anime_id = ?";
        $result = $this->run($sql, [$anime_id])->fetchAll();
        return $result;
    }
}
