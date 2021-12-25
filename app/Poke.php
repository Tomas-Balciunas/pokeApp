<?php

namespace sonaro;

use PDO;
use PDOException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Poke extends Tasks
{
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

    private function sender($id)
    {
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

    private function pokeExec($sender)
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

    private function pokeEmail($sender)
    {
        $config = file_exists('config.ini');
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
