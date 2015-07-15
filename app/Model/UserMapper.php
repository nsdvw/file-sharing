<?php namespace Storage\Model;

class UserMapper
{
    protected $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function register(User $user)
    {
        $sql = "INSERT INTO user (login, email, salt, hash)
                VALUES (:login, :email, :salt, :hash)";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':login', $user->login, \PDO::PARAM_STR);
        $sth->bindParam(':email', $user->email, \PDO::PARAM_STR);
        $sth->bindParam(':salt', $user->salt, \PDO::PARAM_STR);
        $sth->bindParam(':hash', $user->hash, \PDO::PARAM_STR);
        $sth->execute();
        $user->id = $this->connection->lastInsertId();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT id, login, email, hash, salt
                FROM user
                WHERE email=:email";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':email', $email, \PDO::PARAM_STR);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Storage\Model\User');
        return $sth->fetch();
    }
}
