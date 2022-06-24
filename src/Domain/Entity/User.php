<?php

namespace Domain\Entity;

use PDO;

class User
{
    private const TABLE = "user";
    private string $login;
    private string $password;
    private PDO $connection;

    /**
     * @param PDO $connection
     * @param string $login
     * @param string $password
     */
    public function __construct(\PDO $connection, string $login = "", string $password = "")
    {
        $this->connection = $connection;
        $this->login = $login;
        $this->password = $password;
    }

    // Создание новой записи
    public function createNewUser(string $login, string $password): User
    {
        return new User($this->connection, $login, $password);
    }

    // Получение всех записей
    public function getAll(): array
    {
        $sql = "SELECT * FROM " . self::TABLE;
        $query = $this->connection->prepare($sql);
        try {
            $query->execute();
        } catch (\Throwable $exception) {

        }

        return $query->fetchAll();
    }

    // Получение записи по ID
    public function getByID(int $id): object
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE user_id = :id";
        $query = $this->connection->prepare($sql);
        $query->bindParam("id", $id);
        try {
            $query->execute();
        } catch (\Throwable $exception) {

        }
        $result = $query->fetch();

        return self::createNewUser($result["login"], $result["password"]);
    }

    // Получение записей по значению поля из таблицы (фильтрация по полю)
    public function getByFieldValue(string $fieldName, $fieldValue): array
    {
        $sql = "SELECT * FROM " . self::TABLE . " WHERE " . $fieldName . " = ?";
        $query = $this->connection->prepare($sql);
        try {
            $query->execute([$fieldValue]);
        } catch (\Throwable $exception) {

        }

        return $query->fetchAll();
    }

    // Сохранение записи
    public function save(): bool
    {
        $sql = "INSERT INTO " . self::TABLE . "(login, password) VALUE (?, ?)";
        $query = $this->connection->prepare($sql);
        try {
            $query->execute([$this->login, $this->password]);
        } catch (\Throwable $exception) {

        }

        return $query->rowCount() > 0;
    }

    // Удаление записи
    public function remove(): bool
    {
        $sql = "DELETE FROM " . self::TABLE . " WHERE user_id = ?";
        $query = $this->connection->prepare($sql);
        try {
            $query->execute([$this->findID()]);
        } catch (\Throwable $exception) {

        }

        return $query->rowCount() > 0;
    }

    // Поиск ID записи
    public function findID(): int
    {
        $sql = "SELECT user_id FROM " . self::TABLE . " WHERE login = ?";
        $query = $this->connection->prepare($sql);
        try {
            $query->execute([$this->login]);
        } catch (\Throwable $exception) {

        }

        return $query->fetchColumn();
    }

    /**
     * @param string $login
     */
    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setUserInfo(string $login, string $password): void
    {
        $this->setLogin($login);
        $this->setPassword($password);
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }
}