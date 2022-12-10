<?php

class MangaModel extends BaseModel {

    private $table_name = "manga";

    /**
     * A model class for the `manga` database table.
     * It exposes operations that can be performed on manga records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

     /**
     * Get all manga records.
     */
    function getAll() {
        $sql = "SELECT * FROM $this->table_name";
        return $this->paginate($sql);
    }

    /**
     * Get a single manga record by its ID.
     */
    function getMangaById($manga_id) {
        $sql = "SELECT * FROM $this->table_name WHERE manga_id = ?";
        $result = $this->paginate($sql, [$manga_id]);
        return $result;
    }

    /**
     * Get all manga records whose name match the specified value.
     */
    function getMangaByName($name) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name";
        $result = $this->paginate($sql, [":name" => "%" . $name . "%"]);
        return $result;
    }

    function getByTitleMangaka($title, $mangaka) {
        $sql = "SELECT * FROM $this->table_name WHERE name LIKE :name AND mangaka LIKE :mangaka";
        $result = $this->paginate($sql, [":name" => "%" . $title . "%", ":mangaka" => "%" . $mangaka . "%"]);
        return $result;
    }

    function getByMangaka($mangaka) {
        $sql = "SELECT * FROM $this->table_name WHERE mangaka LIKE :mangaka";
        $result = $this->paginate($sql, [":mangaka" => "%" . $mangaka . "%"]);
        return $result;
    }

    function createManga($manga) {
        $data = $this->insert($this->table_name, $manga) ;
        return $data;
    }

    /**
     * Update a Manga record.
     */
    public function updateManga($manga, $manga_id) {
        $manga = $this->update('manga', $manga, array('manga_id' => $manga_id));
        return $manga;
    }

    function doesMangaIdExist($anime_id) {
        $sql = "SELECT * FROM manga WHERE manga_id = ?";
        $data = $this->run($sql, [$anime_id])->fetch();
        if ($data)
            return true;
        return false;
    }
}
