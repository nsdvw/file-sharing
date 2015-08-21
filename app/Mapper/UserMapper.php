<?php namespace Storage\Mapper;

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
        $sth->bindValue(':login', $user->login, \PDO::PARAM_STR);
        $sth->bindValue(':email', $user->email, \PDO::PARAM_STR);
        $sth->bindValue(':salt', $user->salt, \PDO::PARAM_STR);
        $sth->bindValue(':hash', $user->hash, \PDO::PARAM_STR);
        $sth->execute();
        $user->id = $this->connection->lastInsertId();
    }

    public function findByEmail($email)
    {
        $sql = "SELECT id, login, email, hash, salt
                FROM user
                WHERE email=:email";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':email', $email, \PDO::PARAM_STR);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Storage\Model\User');
        return $sth->fetch();
    }

    public function findById($id)
    {
        $sql = "SELECT id, login, email, hash, salt
                FROM user
                WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Storage\Model\User');
        return $sth->fetch();
    }
}
