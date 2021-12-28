<?php

namespace sonaro;

use PDO;
use PDOException;
use sonaro\Validation;

class Import extends Tasks
{

    public function import()
    {
        $fileName = $_FILES['csv']['tmp_name'];
        $ext = pathinfo($_FILES['csv']['name'], PATHINFO_EXTENSION);
        if ($ext == 'csv') {
            if ($_FILES['csv']['size'] > 0) {
                $file = fopen($fileName, 'r');
                $count = 0;
                $skipped = 0;
                $query = "INSERT INTO sonaro.users (name, last_name, email, password) VALUES (:name, :lastname, :email, :password)";

                while (($line = fgetcsv($file)) !== false) {
                    $exists = $this->check($line[0], $line[2]);
                    $validation = Validation::importValidation($line[0], $line[1], $line[2]);
                    if (!$exists && empty(implode('', $validation))) {
                        $generate = $this->generatePw();
                        $pw = password_hash($generate, PASSWORD_DEFAULT);
                        try {
                            $prepare = $this->pdo->prepare($query);
                            $prepare->bindParam(':name', $line[0], PDO::PARAM_STR);
                            $prepare->bindParam(':lastname', $line[1], PDO::PARAM_STR);
                            $prepare->bindParam(':email', $line[2], PDO::PARAM_STR);
                            $prepare->bindParam(':password', $pw, PDO::PARAM_STR);
                            $prepare->execute();
                        } catch (PDOException $msg) {
                            throw $msg;
                        }

                        $count++;
                    } else {
                        $skipped++;
                    }
                }

                fclose($file);
                $this->info = $count . ' user(s) imported! ' . $skipped . ' user(s) skipped due to failing validation or already existing names and emails';
                return $this->info;
            } else {
                $this->info = 'File is empty!';
                return $this->info;
            }
        } else {
            $this->info = 'Only .csv file type is accepted';
            return $this->info;
        }
    }

    public function importPokes()
    {
        if (!empty($_FILES['json']['tmp_name'])) {
            $file = file_get_contents($_FILES['json']['tmp_name']);
        } else {
            $this->info = 'No file selected!';
            return $this->info;
        }
        $ext = pathinfo($_FILES['json']['name'], PATHINFO_EXTENSION);
        if ($ext == 'json') {
            if ($_FILES['json']['size'] > 0) {
                $pokes = json_decode($file, true);
                $count = 0;
                $skipped = 0;

                foreach ($pokes as $poke) {
                    if (!empty($this->findPokes($poke))) {
                        $exists = [];
                    } else {
                        $exists = $this->findUsers($poke);
                    }

                    if (!empty($exists['sender']) && !empty($exists['receiver'])) {
                        try {
                            $query = "INSERT INTO sonaro.pokes (from_user, from_user_name, to_user, time_sent) VALUES (:sender, :name, :receiver, :time)";
                            $query = $this->pdo->prepare($query);
                            $query->bindParam(':sender', $exists['sender']['id'], PDO::PARAM_INT);
                            $query->bindParam(':name', $poke['name_from'], PDO::PARAM_STR);
                            $query->bindParam(':receiver', $exists['receiver']['id'], PDO::PARAM_INT);
                            $query->bindParam(':time', $poke['time_sent'], PDO::PARAM_STR);
                            $query->execute();
                        } catch (PDOException $msg) {
                            throw $msg;
                        }

                        $count++;
                    } else {
                        $skipped++;
                    }
                }

                $this->updatePokes();
                $this->info = $count . ' poke(s) imported! ' . $skipped . ' poke(s) skipped due to no matching senders and receivers or already existing pokes';
                return $this->info;
            } else {
                $this->info = 'File is empty!';
                return $this->info;
            }
        } else {
            $this->info = 'Only .json file type is accepted!';
            return $this->info;
        }
    }

    public function generatePokes()
    {
        try {
            $query = "SELECT name FROM sonaro.users";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }

        $pokes = [];
        $receivers = [];

        for ($i = 0; $i < count($users); $i++) {
            array_push($receivers, $users[$i]['name']);
        }

        foreach ($users as $user) {
            $pokeNumber = mt_rand(1, 7);

            for ($i = 0; $i < $pokeNumber; $i++) {
                shuffle($receivers);
                $receiver = array_rand($receivers, 1);
                if ($receivers[$receiver] != $user['name']) {
                    $poke = [
                        'name_from' => $user['name'],
                        'name_to' => $receivers[$receiver],
                        'time_sent' => $this->generateDate()
                    ];

                    array_push($pokes, $poke);
                }
            }
        }

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename=generated_pokes.json');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Content-Length: ' . strlen(json_encode($pokes)));
        file_put_contents('php://output', json_encode($pokes));
    }

    private function generateDate()
    {
        $min = strtotime('2020-01-01 00:00:00');
        $max = strtotime('now');

        $rand = mt_rand($min, $max);
        return date('Y-m-d H:i:s', $rand);
    }

    private function generatePw()
    {
        $pool = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pw = '';
        $max = strlen($pool) - 1;
        for ($i = 0; $i < 10; ++$i) {
            $pw .= $pool[random_int(0, $max)];
        }

        return $pw;
    }

    private function findUsers($poke)
    {
        try {
            $data = [];

            $query = "SELECT * from sonaro.users WHERE name = :namefrom";
            $query = $this->pdo->prepare($query);
            $query->bindParam(':namefrom', $poke['name_from'], PDO::PARAM_STR);
            $query->execute();

            $data['sender'] = $query->fetch(PDO::FETCH_ASSOC);

            $query2 = "SELECT * from sonaro.users WHERE name = :nameto";
            $query2 = $this->pdo->prepare($query2);
            $query2->bindParam(':nameto', $poke['name_to'], PDO::PARAM_STR);
            $query2->execute();

            $data['receiver'] = $query2->fetch(PDO::FETCH_ASSOC);

            return $data;
        } catch (PDOException $msg) {
            throw $msg;
        }
    }

    private function updatePokes()
    {
        try {
            $query = "SELECT id FROM sonaro.users";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }

        foreach ($users as $user) {
            try {
                $query = "SELECT COUNT(*) FROM sonaro.pokes WHERE to_user = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                $stmt->execute();
                $count = $stmt->fetch(PDO::FETCH_ASSOC);

                $query2 = "UPDATE sonaro.users SET pokes = :pokes WHERE id = :id";
                $stmt2 = $this->pdo->prepare($query2);
                $stmt2->bindParam(':pokes', $count['COUNT(*)'], PDO::PARAM_INT);
                $stmt2->bindParam(':id', $user['id'], PDO::PARAM_INT);
                $stmt2->execute();
            } catch (PDOException $msg) {
                throw $msg;
            }
        }
    }

    private function findPokes($poke)
    {
        try {
            $query = "SELECT * FROM sonaro.pokes WHERE from_user_name = :namefrom AND time_sent = :timesent";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':namefrom', $poke['name_from'], PDO::PARAM_STR);
            $stmt->bindParam(':timesent', $poke['time_sent'], PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $msg) {
            throw $msg;
        }
    }
}
