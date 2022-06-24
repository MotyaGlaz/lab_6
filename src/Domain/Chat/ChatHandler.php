<?php

namespace Domain\Chat;

use Domain\Entity\Message;
use Domain\Entity\User;
use Domain\Repository\MessageRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PDO;
use PDOException;

class ChatHandler
{
    private $twig;
    private $log;
    private $chat_handler;
    private User $user;
    private MessageRepository $messageRepository;

    /**
     * @param $twig
     * @param $user
     * @param $messageRepository
     */
    public function __construct($twig, $user, $messageRepository)
    {
        $this->twig = $twig;
        $this->log = new Logger('chat');
        $this->chat_handler = new StreamHandler('chat.log', Logger::INFO);
        $this->user = $user;
        $this->messageRepository = $messageRepository;
    }

    public function print_messages($login)
    {
        $messages = $this->messageRepository->getAll();
        $author_id = 0;
        if ($login === "admin") {
            $users = $this->user->getAll();
            echo '<pre>';
            print_r($users);
            echo '</pre>';
        } else {
            $this->user->setLogin($login);
            $author_id = $this->user->findID();
        }

        foreach ($messages as $message) {
            if ($author_id == 0 || $author_id == $message->getAuthorId()) {
                $this->twig->display("web/message.html.twig", [
                    "message" => [
                        "user" => $this->user->getByID($message->getAuthorId())->getLogin(),
                        "message" => $message->getMessageText(),
                        "date" => $message->getMessageTime(),
                    ],
                ]);
            }
        }
    }

    public function add_message($login)
    {
        $messageText = empty($_GET["message"]) ? "" : $_GET["message"];

        // adding message
        if (isset($messageText) && $messageText !== "") {
            $this->user->setLogin($login);
            $this->messageRepository->save(new Message($this->user->findID(), $messageText));

            $this->log->pushHandler($this->chat_handler);
            $this->log->info("New message", ["username" => $login]);
        }
    }

    public function is_user_exists($login): bool
    {
        $result = false;

        try {
            $author_id = $this->user->getByFieldValue("login", $login)[0]["user_id"];
            $result = !empty($author_id);
        } catch (PDOException $e) {
            echo "Error!: " . $e->getMessage() . "<br/>";
        }

        return $result;
    }

    public function get_password($login): string
    {
        return $this->user->getByFieldValue("login", $login)[0]["password"];
    }
}