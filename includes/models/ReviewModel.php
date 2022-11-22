<?php

class ReviewModel extends BaseModel {

    private $table_name = "review";

    /**
     * A model class for the `reviews` database table.
     * It exposes operations that can be performed on artists records.
     */
    function __construct() {
        // Call the parent class and initialize the database connection settings.
        parent::__construct();
    }

    
    /**
     * Get all review records.
     */
    function getAll() {
        $sql = "SELECT * FROM $this->table_name";
        $result = $this->run($sql)->fetchAll();
        return $result;
    }
    
    /**
     * Get all Reviews records of a specific anime.
     */
    function getAnimeReviews($anime_id){
        $sql = "SELECT * FROM $this->table_name WHERE anime_id = ?";
        $result = $this->run($sql, [$anime_id])->fetchAll();
        return $result;
    }

    /**
     * Get all Reviews records of a specific anime.
     */
    function getUserReviews($user_id){
        $sql = "SELECT * FROM $this->table_name WHERE user_id = ?";
        $result = $this->run($sql, [$user_id])->fetchAll();
        return $result;
    }

    /**
     * Get all Reviews records of a specific manga.
     */
    function getMangaReviews($manga_id){
        $sql = "SELECT * FROM $this->table_name WHERE manga_id = ?";
        $result = $this->run($sql, [$manga_id])->fetchAll();
        return $result;
    }

    /**
     * Get all Reviews records of a specific manga.
     */
    function getMangaReviewsByRate($manga_id, $rate){
        $sql = "SELECT * FROM $this->table_name 
                WHERE manga_id = :manga_id && star_rating = :star_rating";
        $result = $this->run($sql, 
        [   "manga_id" => $manga_id, 
            "star_rating" => $rate
        ])->fetchAll();
        return $result;
    }
}
