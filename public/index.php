<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";
// По не понятной причине не подтягивается класс User
require_once "C:\Users\glazy\PhpstormProjects\lab_6\src\Domain\Entity\User.php";

use Domain\Entity\User;
use Domain\Chat\ChatHandler;
use Domain\Repository\DataMapper\MessageDataMapper;
use Domain\Repository\MessageRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$host = "localhost";
$dbname = "chat";
$user_name = "admin";
$pass = "12345672";
$DBH = new PDO("mysql:host=$host;dbname=$dbname", $user_name, $pass);
$DBH->exec("USE chat");

$loader = new FilesystemLoader(dirname(__DIR__) . "/templates/");
$twig = new Environment($loader);

$log = new Logger('login');
$user_handler = new StreamHandler('chat.log', Logger::INFO);
$log->pushHandler($user_handler);

$user = new User($DBH);
$messageDataMapper = new MessageDataMapper();
$messageRepository = new MessageRepository($DBH, $messageDataMapper);
$chat = new ChatHandler($twig, $user, $messageRepository);

$twig->display("web/chat.html.twig");

$login = empty($_GET["login"]) ? "" : $_GET["login"];
$password = empty($_GET["password"]) ? "" : $_GET["password"];
if (isset($login) && $login != "") {
    if (isset($password) && $password != "") {
        // adding user
        if (!$chat->is_user_exists($login)) {
            try {
                $user->setUserInfo($login, $password);
                if ($user->save()) {
                    echo "<p><i>Создан пользователь <b>$login</b></i></p>";
                    $log->info("Adding a new user", ["username" => $login]);

                    $chat->add_message($login);
                    $chat->print_messages($login);
                } else {
                    echo "<p><i>Ошибка ввода. Поменяйте логин или пароль</i></p>";
                }
            } catch (PDOException $e) {
                echo "!Error!: " . $e->getMessage() . "<br/>";
            }

        } else { // checking password
            $proper_password = $chat->get_password($login);

            if ($password == $proper_password) {
                $log->info("User signed in", ["username" => $login]);
                $chat->add_message($login);
                $chat->print_messages($login);
            } else {
                $log->error("Wrong password", ["username" => $login]);
                echo "<p style='color: red'><i>Неверный пароль</i></p>";
            }
        }
    } else {
        echo "<p style='color: red'><i>Введите пароль</i></p>";
    }
}