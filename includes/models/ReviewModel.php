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
}
