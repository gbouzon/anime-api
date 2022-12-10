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
        $result = $this->paginate($sql);
        return $result;
    }

    /**
     * Get a single user record by its ID.
     */
    function getUserById($user_id) {
        $sql = "SELECT * FROM $this->table_name WHERE user_id = ?";
        $result = $this->paginate($sql, [$user_id]);
        return $result;
    }

    /**
     * Get all user records whose username match the specified value.
     */
    function getUserByUsername($username) {
        $sql = "SELECT * FROM $this->table_name WHERE username = ?";
        $result = $this->paginate($sql, [$username]);
        return $result;
    }

    /**
     * 
     */
    function getUserByEmail($email) {
        $sql = "SELECT * FROM $this->table_name WHERE email = ?";
        $result = $this->paginate($sql, [$email]);
        return $result;
    }

    /**
     * Get the user who made the specified review.
     */
    function getUserByReviewID($review_id) {
        $sql = "SELECT * FROM $this->table_name WHERE user_id = (SELECT user_id FROM review WHERE review_id = ?)";
        $result = $this->paginate($sql, [$review_id]);
        return $result;
    }

    /**
     * Get all manga on user read list
     */
    function getUserMangaWatched($user_id) {
        $sql = "SELECT manga.* FROM list 
                JOIN manga ON list.manga_id = manga.manga_id 
                WHERE list.user_id = ? && list.Type = 'watched'";
        $result = $this->paginate($sql, [$user_id]);
        return $result;
    }


    /**
     * Get all manga on user to-read list
     */
    function getUserMangaToWatch($user_id) {
        $sql = "SELECT manga.* FROM list 
        JOIN manga ON list.manga_id = manga.manga_id 
        WHERE list.user_id = ? && list.Type = 'to-watch'";
        $result = $this->paginate($sql, [$user_id]);
        return $result;
    }


    /**
     * Get all anime on user to watched list
     */
    function getUserAnimeWatched($user_id) {
        $sql = "SELECT anime.* FROM list 
                JOIN anime ON list.anime_id = anime.anime_id 
                WHERE list.user_id = ? && list.Type = 'watched'";
        $result = $this->paginate($sql, [$user_id]);
        return $result;
    }
    
    /**
     * Get all anime on user to-watch list
     */
    function getUserAnimeToWatch($user_id) {
        $sql = "SELECT anime.* FROM list 
        JOIN anime ON list.anime_id = anime.anime_id 
        WHERE list.user_id = ? && list.Type = 'to-watch'";
        $result = $this->paginate($sql, [$user_id]);
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
     * Update a User record.
     */
    public function updateUser($user, $user_id) {
        $user = $this->update('user', $user, array('user_id' => $user_id));
        return $user;
    }

    /**
     * Delete one or more User
     */
    function deleteUsers($user_id){
        $data = $this->deleteByIds($this->table_name, "user_id", $user_id);
        return $data;
    }
}
