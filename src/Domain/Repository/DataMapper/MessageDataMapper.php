<?php

namespace Domain\Repository\DataMapper;

use Domain\Entity\Message;

class MessageDataMapper
{
    private const MAP = [
        "author_id" => [
            "type" => "integer",
            "nullable" => false,
        ],
        "text" => [
            "type" => "string",
            "nullable" => false,
        ],
        "mtime" => [
            "type" => "string",
            "nullable" => false,
        ],
    ];

    public function map(array $row): ?Message
    {
        $result = null;

        if (!(isset($row["author_id"]) && empty($row["author_id"])
                || isset($row["text"]) && empty($row["text"]))
            || isset($row["mtime"]) && empty($row["mtime"]))
        {
            $result = Message::withTime($row["author_id"], $row["text"], $row["mtime"]);
        }

        return $result;
    }
}