<?php

class User
{
    public $loginUser;
    public $firstname;
    public $lastname;
    public $birth;
    public $mailUser;

    private $password;

    public function __construct($loginUser, $firstname, $lastname, $birth, $mailUser, $password)
    {
        $this->loginUser = $loginUser;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->birth = $birth;
        $this->mailUser = $mailUser;
        $this->password = $password;
    }

    public static function from_array($array)
    {
        return new User($array['loginUser'], $array['firstname'], $array['lastname'], $array['birth'], $array['mailUser'], $array['password']);
    }


}

class Users_model extends Model
{


    public function m_create_user($loginUser, $firstname, $lastname, $birth, $mailUser, $password)
    {
        try {
            // On peut mettre des verifications sur toutes les attributs en ajoutant des fonctions ( check )

            $statement = $this->db->prepare("INSERT INTO Utilisateurs(loginUser, firstname, lastname, birth, mailUser, password) VALUES (:loginUser, :firstname, :lastname, :birth, :mailUser, :password)");
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $statement->execute(array(
                'loginUser' => $loginUser,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'birth' => $birth,
                'mailUser' => $mailUser,
                'password' => $hash));
            $new = new User($loginUser, $firstname, $lastname, $birth, $mailUser, $hash);
            //var_dump($new);
            return $new;
        } catch (PDOException $e) {
            throw new Exception('Impossible d\'inscrire l\'utilisateur.');
        }
    }


    public function user_from_loginUser($loginUser)
    {
        return $this->user_from_query('SELECT * FROM Utilisateurs WHERE loginUser = ?', [$loginUser]);
    }


    private function user_from_query($query, $array)
    {
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($array);
            $users = $statement->fetchAll();
            if (count($users) == 0) return null;
            return User::from_array($users[0]);
        } catch (PDOException $e) {
            throw new Exception('Impossible d\'effectuer la demande.');
        }
    }

    public function password_is_valid($password)
    {
        return password_verify($password, $this->password);
    }


    public function m_show_user($logged_user)
    {
        try {
            $statement = $this->db->prepare("SELECT * FROM Utilisateurs WHERE loginUser = :loginUser");
            $statement->execute(['loginUser' => $logged_user]);
            return $statement->fetchAll();
        } catch (PDOException $e) {
            throw new Exception(self::str_error_database);
        }


    }
}



