<?php

class AnimeModel extends BaseModel {

    private $table_name = "anime";

    /**
     * A model class for the `anime` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    /**
     * Get all anime records.
     */
    function getAll() {
        $sql = "SELECT * FROM $this->table_name";
        $result = $this->run($sql)->fetchAll();
        return $result;
    }

    /**
     * Get a single anime record by its ID.
     */
    function getAnimeById($anime_id) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id = ?";
        $result = $this->run($sql, [$anime_id])->fetch();
        return $result;
    }

    /**
     * Get all anime records whose name match the specified value.
     */
    function getAnimeByName($name) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name";
        $result = $this->run($sql, [":name" => "%" . $name . "%"])->fetchAll();
        return $result;
    }

    function getAnimeByStudio($studio_id) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id IN (SELECT anime_id FROM production WHERE studio_id = ?)";
        $result = $this->run($sql, [$studio_id])->fetchAll();
        return $result;
    }

    function getAnimeByStudioName($studio_name) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id IN (SELECT anime_id FROM production WHERE studio_id IN (SELECT studio_id FROM studio WHERE name LIKE :name))";
        $result = $this->run($sql, [":name" => "%" . $studio_name . "%"])->fetchAll();
        return $result;
    }

    /**
     * Get all anime records whose description match the specified value 
     * and whose name match the specified value 
     * and whose year match the specified value
     */
    function getByNameDescriptionYear($name, $description, $year) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND description LIKE :description AND year = :year";
        $result = $this->run($sql, [":name" => "%" . $name . "%", ":description" => "%" . $description . "%", ":year" => $year])->fetchAll();
        return $result;
    }

    /**
     * Get all anime records whose description match the specified value
     */
    function getByDescription($description) {
        $sql = "SELECT * FROM $this->table_name WHERE description LIKE :description";
        $result = $this->run($sql, [":description" => "%" . $description . "%"])->fetchAll();
        return $result;
    }

    /**
     * Get all anime records whose year match the specified value
     */
    function getByYear($year) {
        $sql = "SELECT * FROM $this->table_name WHERE year = :year";
        $result = $this->run($sql, [":year" => $year])->fetchAll();
        return $result;
    }

    function getByNameDescription($name, $description) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND description LIKE :description";
        $result = $this->run($sql, [":name" => "%" . $name . "%", ":description" => "%" . $description . "%"])->fetchAll();
        return $result;
    } 

    function getByNameYear($name, $year) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND year = :year";
        $result = $this->run($sql, [":name" => "%" . $name . "%", ":year" => $year])->fetchAll();
        return $result;
    }

    function getByDescriptionYear($description, $year) {
        $sql = "SELECT * FROM $this->table_name WHERE description LIKE :description AND year = :year";
        $result = $this->run($sql, [":description" => "%" . $description . "%", ":year" => $year])->fetchAll();
        return $result;
    }

    function createAnime($anime) {
        $data = $this->insert($this->table_name, $anime) ;
        return $data;
    }

    /**
     * Update an anime record.
     */
    public function updateAnime($anime, $anime_id) {
        $anime = $this->update('artist', $anime, array('ArtistId' => $anime_id));
        return $anime;
    }

    function doesAnimeIdExist($anime_id) {
        $sql = "SELECT * FROM anime WHERE anime_id = ?";
        $data = $this->run($sql, [$anime_id])->fetch();
        if ($data)
            return true;
        return false;
    }

    function insertAnime($name, $description, $year, $nb_releases, $cover_picture) {
        $sql = "INSERT INTO $this->table_name (name, description, year, nb_releases, cover_picture) VALUES (?, ?, ?, ?, ?)";
        $result = $this->run($sql, [$name, $description, $year, $nb_releases, $cover_picture]);
        return $result;
    }

    function hasAnimePicture($animeId) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id = :anime_id AND cover_picture IS NOT 'blank.png'";
        $result = $this->run($sql, [$animeId])->fetch();
        return $result;
    }
}