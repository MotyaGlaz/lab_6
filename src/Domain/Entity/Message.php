<?php

namespace Domain\Entity;

class Message
{
    private int $author_id;
    private string $message_text;
    private string $message_time;

    /**
     * @param int $author_id
     * @param string $message_text
     */
    public function __construct(int $author_id, string $message_text)
    {
        $this->author_id = $author_id;
        $this->message_text = $message_text;
        $this->message_time = date('Y-m-d H:i:s', time());
    }

    public static function withTime(int $author_id, string $message_text, string $message_time): Message
    {
        $instance = new self($author_id, $message_text);
        $instance->message_time = $message_time;
        return $instance;
    }

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->author_id;
    }

    /**
     * @return string
     */
    public function getMessageText(): string
    {
        return $this->message_text;
    }

    /**
     * @return string
     */
    public function getMessageTime(): string
    {
        return $this->message_time;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return [
            "author_id" => $this->getAuthorId(),
            "message_text" => $this->getMessageText(),
            "message_time" => $this->getMessageTime(),
        ];
    }
}
