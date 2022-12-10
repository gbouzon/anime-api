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
        $result = $this->paginate($sql);
        return $result;
    }

    /**
     * Get a single anime record by its ID.
     */
    function getAnimeById($anime_id) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id = ?";
        $result = $this->paginate($sql, [$anime_id]);
        return $result;
    }

    /**
     * Get all anime records whose name match the specified value.
     */
    function getAnimeByName($name) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name";
        $result = $this->paginate($sql, [":name" => "%" . $name . "%"]);
        return $result;
    }

    /**
     * Get all anime records whose studio id match the specified value
     */
    function getAnimeByStudio($studio_id) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id IN (SELECT anime_id FROM production WHERE studio_id = ?)";
        $result = $this->paginate($sql, [$studio_id]);
        return $result;
    }

    /**
     * Get all anime records whose studio name match the specified value
     */
    function getAnimeByStudioName($studio_name) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id IN (SELECT anime_id FROM production WHERE studio_id IN (SELECT studio_id FROM studio WHERE name LIKE :name))";
        $result = $this->paginate($sql, [":name" => "%" . $studio_name . "%"]);
        return $result;
    }

    /**
     * Get all anime records whose description match the specified value 
     * and whose name match the specified value 
     * and whose year match the specified value
     */
    function getByNameDescriptionYear($name, $description, $year) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND description LIKE :description AND year = :year";
        $result = $this->paginate($sql, [":name" => "%" . $name . "%", ":description" => "%" . $description . "%", ":year" => $year]);
        return $result;
    }

    /**
     * Get all anime records whose description match the specified value
     */
    function getByDescription($description) {
        $sql = "SELECT * FROM $this->table_name WHERE description LIKE :description";
        $result = $this->paginate($sql, [":description" => "%" . $description . "%"]);
        return $result;
    }

    /**
     * Get all anime records whose year match the specified value
     */
    function getByYear($year) {
        $sql = "SELECT * FROM $this->table_name WHERE year = :year";
        $result = $this->paginate($sql, [":year" => $year]);
        return $result;
    }

    /**
     * Get all anime records whose name and description match the specified value
     */
    function getByNameDescription($name, $description) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND description LIKE :description";
        $result = $this->paginate($sql, [":name" => "%" . $name . "%", ":description" => "%" . $description . "%"]);
        return $result;
    } 

    /**
     * Get all anime records whose name and year match the specified value
     */
    function getByNameYear($name, $year) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND year = :year";
        $result = $this->paginate($sql, [":name" => "%" . $name . "%", ":year" => $year]);
        return $result;
    }

    /**
     * Get all anime records whose description and year match the specified value
     */
    function getByDescriptionYear($description, $year) {
        $sql = "SELECT * FROM $this->table_name WHERE description LIKE :description AND year = :year";
        $result = $this->paginate($sql, [":description" => "%" . $description . "%", ":year" => $year]);
        return $result;
    }

    /**
     * Inserts anime record into database
     */
    function createAnime($anime) {
        $data = $this->insert($this->table_name, $anime) ;
        return $data;
    }

    /**
     * Update an anime record.
     */
    public function updateAnime($anime, $anime_id) {
        $anime = $this->update('anime', $anime, array('anime_id' => $anime_id));
        return $anime;
    }

    /**
     * Checks if anime id is in anime table
     */
    function doesAnimeIdExist($anime_id) {
        $sql = "SELECT * FROM anime WHERE anime_id = ?";
        $data = $this->run($sql, [$anime_id])->fetch();
        if ($data)
            return true;
        return false;
    }

    /**
     * Inserts an anime record into the database based on its properties 
     */
    function insertAnime($name, $otherName, $description, $year, $nb_releases, $cover_picture) {
        $sql = "INSERT INTO $this->table_name (name, other_name, description, year, nb_releases, cover_picture) VALUES (?,?, ?, ?, ?, ?)";
        $result = $this->run($sql, [$name, $otherName, $description, $year, $nb_releases, $cover_picture]);
        return $result;
    }

    /**
     * Checks if the specified anime record contains a definition for its cover picture attribute.
     */
    function hasAnimePicture($animeId) {
        $sql = "SELECT * FROM $this->table_name WHERE anime_id = :anime_id AND cover_picture IS NOT 'blank.png'";
        $result = $this->run($sql, [$animeId])->fetch();
        return $result;
    }

    /**
     * Retrieves the last anime record inserted into the database
     */
    function lastIdInsert() {
        return $this->lastInsertId();
    }

    /**
     * Adds an alternate title to the specified anime record
     */
    function addOtherTitle($title, $anime_id) {
        $sql = "UPDATE anime SET other_name = :name WHERE anime_id = :anime_id";
        $result = $this->run($sql, [$title, $anime_id]);
        return $result;
    }

    /**
     * Retrieves all genre of an anime
     */
    function getGenres($anime_id) {
        $sql = "SELECT genre_list.genre_id from anime JOIN genre_list on anime.anime_id = genre_list.anime_id WHERE genre_list.anime_id = :anime_id";
        $result = $this->run($sql, [$anime_id])->fetchAll();
        return $result;
    }

    /**
     * Retrieves all genre of a manga
     */
    function getGenresManga($manga_id) {
        $sql = "SELECT genre_list.genre_id from manga JOIN genre_list on manga.manga_id = genre_list.manga_id WHERE genre_list.manga_id = :manga_id";
        $result = $this->run($sql, [$manga_id])->fetchAll();
        return $result;
    }
}