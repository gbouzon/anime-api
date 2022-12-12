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
        $result = $this->paginate($sql);
        return $result;
    }

    /**
     * Get all genre records matching the provided name and that contain a keyword in description column
     */
    function getByNameDescription($name, $description) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND description LIKE :description";
        $result = $this->paginate($sql, ["name" => "%" . $name . "%", "description" => "%" . $description . "%"]);
        return $result;
    }

    /**
     * Get all genre records that contain a keyword in description column
     */
    function getByDescription($description) {
        $sql = "SELECT * FROM $this->table_name WHERE description LIKE :description";
        $result = $this->paginate($sql, ["description" => "%" . $description . "%"]);
        return $result;
    }

    /**
     * Get a single genre record by its ID.
     */
    function getGenreById($genre_id) {
        $sql = "SELECT * FROM $this->table_name WHERE genre_id = ?";
        $result = $this->paginate($sql, [$genre_id]);
        return $result;
    }

    /**
     * Get all genre records whose name match the specified value.
     */
    function getGenreByName($name) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name";
        $result = $this->paginate($sql, [":name" => "%" . $name . "%"]);
        return $result;
    }

    /**
     * Get a list of anime that belong to the specified genre.
     * @param string $genre_id 
     * @return array An array containing the matches found.
     */
    public function getAllAnimeFromGenre($genre_id) {
        $sql = "SELECT * FROM anime WHERE anime_id IN (SELECT anime_id FROM genre_list WHERE genre_id = ?)";
        $data = $this->paginate($sql, [$genre_id]);
        return $data;
    }

    /**
     * Get a list of manga that belong to the specified genre.
     * @param string $genre_id 
     * @return array An array containing the matches found.
     */
    public function getAllMangaFromGenre($genre_id) {
        $sql = "SELECT * FROM manga WHERE manga_id IN (SELECT manga_id FROM genre_list WHERE genre_id = ?)";
        $data = $this->paginate($sql, [$genre_id]);
        return $data;
    }

    /**
     * Adds a new genre record into the database
     */
    function insertGenre($name, $description) {
        $sql = "INSERT INTO $this->table_name (name, description) VALUES (?, ?)";
        $result = $this->run($sql, [$name, $description]);
        return $result;
    }

    /**
     * Retrieves the last genre added into the database
     */
    function lastIdInsert() {
        return $this->lastInsertId();
    }

    /**
     * Retrieves the last genre_list added into the database
     */
    function insertGenreList($genre_id, $anime_id) {
        $sql = "INSERT INTO genre_list (genre_id, anime_id) VALUES (?, ?)";
        $result = $this->run($sql, [$genre_id, $anime_id]);
        return $result;
    }

    /**
     * Retrieves the genre name by its ID
     */
    function getGenreNameById($genre_id) {
        $sql = "SELECT name FROM $this->table_name WHERE genre_id = :genre_id";
        $result = $this->run($sql, [$genre_id])->fetch();
        return $result;
    }
}
