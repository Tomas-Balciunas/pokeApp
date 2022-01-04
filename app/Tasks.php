<?php

namespace sonaro;

use PDO;
use PDOException;

class Tasks
{
    protected $pdo;
    protected $search;
    protected $id;
    protected $name;
    protected $lastName;
    protected $email;
    protected $password;
    protected $passwordNew;
    protected $info;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    protected function checkUserExists($name, $email)
    {
        try {
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM sonaro.users WHERE name = :name OR email = :email");
            $check->bindParam(':name', $name, PDO::PARAM_STR);
            $check->bindParam(':email', $email, PDO::PARAM_STR);
            $check->execute();
            return $check->fetchColumn();
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- REGISTER ---------------------------------------------------------

    public function register($post)
    {
        $this->name = htmlspecialchars(strip_tags($post['registerName']));
        $this->lastName = htmlspecialchars(strip_tags($post['registerLastname']));
        $this->email = htmlspecialchars(strip_tags($post['registerEmail']));
        $this->password = password_hash(htmlspecialchars(strip_tags($post['registerPassword'])), PASSWORD_DEFAULT);
        $this->registerExec();
        return $this->info;
    }

    private function registerExec()
    {
        try {
            if ($this->checkUserExists($this->name, $this->email)) {
                $this->info = 'User with this name or email already exists';
            } else {
                $query = "INSERT INTO sonaro.users (name, last_name, email, password) VALUES (:name, :last_name, :email, :password)";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
                $stmt->bindParam(':last_name', $this->lastName, PDO::PARAM_STR);
                $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $this->password, PDO::PARAM_STR);
                $stmt->execute();

                $this->info = "User " . $this->name . " has been created, you may now log in";
            }
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- LOGIN ---------------------------------------------------------

    public function login($post)
    {
        $this->name = htmlspecialchars(strip_tags($post['loginName']));
        $this->password = htmlspecialchars(strip_tags($post['loginPassword']));

        if (empty($this->name) or empty($this->password)) {
            $this->info = 'Log in name and password fields cannot be empty';
            return $this->info;
        } else {
            $this->loginExec();
            return $this->info;
        }
    }

    private function loginExec()
    {
        try {
            $query = "SELECT * FROM sonaro.users WHERE name = :name";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($user)) {
                $this->info = 'User with this name does not exist';
            } else if (password_verify($this->password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location:/sonaro');
            } else {
                $this->info = 'Incorrect password!';
            }
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- USER LIST ---------------------------------------------------------

    public function users($post, $offset, $itemsPerPage)
    {
        if (isset($post['search'])) {
            $this->search = htmlspecialchars(strip_tags($post['search'] . '%'));
            $query = "SELECT id, name, last_name, email, pokes FROM sonaro.users WHERE name LIKE :search LIMIT :offset, :perPage";
        } else {
            $query = "SELECT id, name, last_name, email, pokes FROM sonaro.users LIMIT :offset, :perPage";
        }
        try {
            
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':search', $this->search, PDO::PARAM_STR);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':perPage', $itemsPerPage, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- NOTIFICATIONS ---------------------------------------------------------

    public function notifs($id)
    {
        try {
            $query = "SELECT pokes.time_sent, users.name FROM sonaro.users INNER JOIN sonaro.pokes ON pokes.from_user = users.id AND to_user = :id ORDER BY time_sent DESC LIMIT 5";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- PROFILE ---------------------------------------------------------

    public function fetchProfile($post, $id, $offset, $itemsPerPage)
    {
        $this->id = $id;
        if (!empty($_POST['search'])) {
            $this->search = htmlspecialchars(strip_tags($post['search'] . '%'));
        }
        $data = [];

        try {
            $query = "SELECT id, name, last_name, email FROM sonaro.users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $data['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($post['search'])) {
                $query2 = "SELECT pokes.time_sent, users.name FROM sonaro.users INNER JOIN sonaro.pokes ON pokes.from_user = users.id AND to_user = :id WHERE users.name LIKE :search LIMIT :offset, :perPage";
                $stmt = $this->pdo->prepare($query2);
                $stmt->bindParam(':search', $this->search, PDO::PARAM_STR);
            } else {
                $query2 = "SELECT pokes.time_sent, users.name FROM sonaro.users INNER JOIN sonaro.pokes ON pokes.from_user = users.id AND to_user = :id LIMIT :offset, :perPage";
                $stmt = $this->pdo->prepare($query2);
                
            }
            
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':perPage', $itemsPerPage, PDO::PARAM_INT);
            $stmt->execute();
            $data['pokes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    public function updateUser($post, $id)
    {
        $this->id = $id;
        $this->lastName = htmlspecialchars(strip_tags($post['updateLastname']));
        $this->email = htmlspecialchars(strip_tags($post['updateEmail']));
        $this->password = htmlspecialchars(strip_tags($post['updatePasswordOld']));
        $this->passwordNew = htmlspecialchars(strip_tags($post['updatePasswordNew']));
        $this->updateUserValidate();
        return $this->info;
    }

    private function updateUserValidate()
    {
        try {
            $check = $this->pdo->prepare("SELECT * FROM sonaro.users WHERE email = :email");
            $check->bindParam(':email', $this->email, PDO::PARAM_STR);
            $check->execute();
            $res = $check->fetch(PDO::FETCH_ASSOC);

            if (!empty($res) && $res['id'] != $this->id) {
                $this->info = 'User with this email already exists!';
            } else {
                $query = "SELECT * FROM sonaro.users WHERE id = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (password_verify($this->password, $user['password'])) {
                    $this->passwordNew = password_hash($this->passwordNew, PASSWORD_DEFAULT);
                    $this->updateUserExec();
                } else {
                    $this->info = 'Incorrect password!';
                }
            }
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    private function updateUserExec()
    {
        try {
            $query = "UPDATE sonaro.users SET last_name = :last_name, email = :email, password = :password WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':last_name', $this->lastName, PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $this->passwordNew, PDO::PARAM_STR);
            $stmt->execute();
            $this->info = 'Info updated!';
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- SEARCH ---------------------------------------------------------

    public function search($post, $offset, $itemsPerPage, $id)
    {
        $this->search = htmlspecialchars(strip_tags($post['search'] . '%'));

        try {
            switch ($post['type']) {
                case 'users':
                    $query = "SELECT id, name, last_name, email, pokes FROM sonaro.users WHERE name LIKE :search LIMIT :offset, :perPage";
                    break;
                case 'pokes':
                    $query = "SELECT pokes.time_sent, users.name FROM sonaro.users INNER JOIN sonaro.pokes ON pokes.from_user = users.id AND to_user = $id WHERE from_user_name LIKE :search LIMIT :offset, :perPage";
                    break;
            };

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':search', $this->search, PDO::PARAM_STR);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':perPage', $itemsPerPage, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- PAGINATION ---------------------------------------------------------

    public function rowCount()
    {
        try {
            $query = "SELECT COUNT(*) FROM sonaro.users";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    public function rowCountPokes($id)
    {
        try {
            $query = "SELECT COUNT(*) FROM sonaro.pokes WHERE to_user = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    public function rowCountSearch($post, $id)
    {
        $this->search = htmlspecialchars(strip_tags($post['search'] . '%'));

        try {
            switch ($post['type']) {
                case 'users':
                    $query = "SELECT COUNT(*) FROM sonaro.users WHERE name LIKE :search";
                    break;
                case 'pokes':
                    $query = "SELECT COUNT(*) FROM sonaro.users INNER JOIN sonaro.pokes ON pokes.from_user = users.id AND to_user = $id WHERE users.name LIKE :search";
                    break;
            };

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':search', $this->search, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }
}
