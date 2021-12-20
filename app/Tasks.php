<?php

namespace sonaro;

use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Tasks
{
    protected $pdo;
    private $id;
    private $name;
    private $lastName;
    private $email;
    private $password;
    private $passwordNew;
    private $passwordNewRepeat;

    private $info;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
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

    public function registerExec()
    {
        try {
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM sonaro.users WHERE name = :name OR email = :email");
            $check->bindParam(':name', $this->name, PDO::PARAM_STR);
            $check->bindParam(':email', $this->email, PDO::PARAM_STR);
            $check->execute();

            if ($check->fetchColumn()) {
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

    public function loginExec()
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

    public function users()
    {
        try {
            $query = "SELECT id, name, last_name, email, pokes FROM sonaro.users";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    //------------------------------------------------------------- PROFILE ---------------------------------------------------------

    public function fetchProfile($id)
    {
        $this->id = $id;
        return $this->execFetchProfile();
    }

    public function execFetchProfile()
    {
        $data = [];

        try {
            $query = "SELECT id, name, last_name, email FROM sonaro.users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $data['user'] = $stmt->fetch(PDO::FETCH_ASSOC);

            $query = "SELECT from_user_name, time_sent FROM sonaro.pokes WHERE to_user = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
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
        $this->passwordNewRepeat = htmlspecialchars(strip_tags($post['updatePasswordNewRepeat']));
        $this->updateUserValidate();
        return $this->info;
    }

    public function updateUserValidate()
    {
        try {
            $query = "SELECT * FROM sonaro.users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($this->password, $user['password'])) {
                if ($this->passwordNew == $this->passwordNewRepeat) {
                    $this->passwordNew = password_hash($this->passwordNew, PASSWORD_DEFAULT);
                    $this->updateUserExec();
                } else {
                    $this->info = 'Passwords do not match!';
                }
            } else {
                $this->info = 'Incorrect password!';
            }
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    public function updateUserExec()
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

    //------------------------------------------------------------- POKES ---------------------------------------------------------

    public function poke($id, $post)
    {
        $sender = $this->sender($id);
        $this->id = htmlspecialchars(strip_tags($post['id']));
        $this->name = htmlspecialchars(strip_tags($post['name']));
        $this->email = htmlspecialchars(strip_tags($post['email']));
        $this->pokeExec($sender);
        $this->pokeEmail($sender);
        return $this->info;
    }

    public function sender($id) {
        try {
            $query = "SELECT id, name FROM sonaro.users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    public function pokeExec($sender)
    {
        $query = "UPDATE sonaro.users SET pokes = pokes+1 WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $query = "INSERT INTO sonaro.pokes (from_user, from_user_name, to_user) VALUES (:sender, :senderName, :user)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':sender', $sender['id'], PDO::PARAM_INT);
        $stmt->bindParam(':senderName', $sender['name'], PDO::PARAM_STR);
        $stmt->bindParam(':user', $this->id, PDO::PARAM_INT);
        $stmt->execute();

        $this->info = 'User ' . $this->name . ' has been poked!';
    }

    public function pokeEmail($sender)
    {
        $config = false; //file_exists('config.ini');
        if ($config) {
            $credentials = parse_ini_file('config.ini', true);
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $credentials['email']['username'];
            $mail->Password = $credentials['email']['password'];
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('sonaropoker@gmail.com');
            $mail->addAddress($this->email);
            $mail->Subject = 'You have been poked!';
            $mail->Body    = $sender['name'] . ' has poked you.';

            if (!$mail->send()) {
                $this->info .= ' Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            } else {
                $this->info .= ' Email has been sent.';
            }
        } else {
            $this->info .= ' Could not load email credentials, email not sent.';
        }

        // $to = $this->email;
        // $subject = '=?UTF-8?B?' . base64_encode('You have been poked!') . '?=';
        // $message = base64_encode($sender['name'] . ' has poked you.');
        // $headers = 'Content-Type: text/plain; charset=utf-8' . "\r\n";
        // $headers .= 'Content-Transfer-Encoding: base64' . "\r\n";
        // $headers .= 'From: ' . $sender['email'] . "\r\n";

        // mail($to, $subject, $message, $headers);
    }
}
