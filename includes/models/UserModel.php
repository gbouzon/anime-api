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
        $result = $this->run($sql)->fetchAll();
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

    function getUserByUsername($username) {
        $sql = "SELECT * FROM $this->table_name WHERE username = ?";
        $result = $this->run($sql, [$username])->fetch();
        return $result;
    }

    function getUserByEmail($email) {
        $sql = "SELECT * FROM $this->table_name WHERE email = ?";
        $result = $this->run($sql, [$email])->fetch();
        return $result;
    }

    function getUserByReviewID($review_id) {
        $sql = "SELECT * FROM $this->table_name WHERE user_id = (SELECT user_id FROM review WHERE review_id = ?)";
        $result = $this->run($sql, [$review_id])->fetch();
        return $result;
    }

    /**
     * Get all manga on user read list
     */
    function getUserMangaWatched($user_id) {
        $sql = "SELECT manga.* FROM list 
                JOIN manga ON list.manga_id = manga.manga_id 
                WHERE list.user_id = ? && list.Type = 'watched'";
        $result = $this->run($sql, [$user_id])->fetchAll();
        return $result;
    }


    /**
     * Get all manga on user to-read list
     */
    function getUserMangaToWatch($user_id) {
        $sql = "SELECT manga.* FROM list 
        JOIN manga ON list.manga_id = manga.manga_id 
        WHERE list.user_id = ? && list.Type = 'to-watch'";
        $result = $this->run($sql, [$user_id])->fetchAll();
        return $result;
    }


    /**
     * Get all anime on user to watched list
     */
    function getUserAnimeWatched($user_id) {
        $sql = "SELECT anime.* FROM list 
                JOIN anime ON list.anime_id = anime.anime_id 
                WHERE list.user_id = ? && list.Type = 'watched'";
        $result = $this->run($sql, [$user_id])->fetchAll();
        return $result;
    }
    
    /**
     * Get all anime on user to-watch list
     */
    function getUserAnimeToWatch($user_id) {
        $sql = "SELECT anime.* FROM list 
        JOIN anime ON list.anime_id = anime.anime_id 
        WHERE list.user_id = ? && list.Type = 'to-watch'";
        $result = $this->run($sql, [$user_id])->fetchAll();
        return $result;
    }

    /**
     * Create one or more User 
     */
    function createUsers($user) {
        $data = $this->insert($this->table_name, $user) ;
        return $data;
    }

    /**
     * Delete one or more User
     */
    function deleteUsers($user_id){
        $data = $this->deleteByIds($this->table_name, "user_id", $user_id);
        return $data;
    }
}
