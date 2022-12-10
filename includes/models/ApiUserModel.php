<?php

class ApiUserModel extends BaseModel {

    private $table_name = "api_user";

    function __construct() {
        parent::__construct();
    }

    public function verifyEmail($email) {
        $sql = "SELECT * FROM $this->table_name WHERE email= :email";
        return $this->run($sql, [":email" => $email])->fetch();
    }

    public function verifyPassword($email, $input_password) {
        $sql = "SELECT * FROM $this->table_name WHERE email= :email";
        $row = $this->run($sql, [":email" => $email])->fetchAll();
        if ($row && is_array($row)) {
            if (password_verify($input_password, $row[0]['password'])) {
                return $row[0];
            }
        }
        return null;
    }

    public function createUser($new_user) {
        $new_user["password"] = $this->getHashedPassword($new_user["password"]);
        return $this->insert($this->table_name, $new_user);
    }

    private function getHashedPassword($password_to_hash) {
        $options = ['cost' => 15];
        $hash = password_hash($password_to_hash, PASSWORD_DEFAULT, $options);
        return $hash;
    }
}
