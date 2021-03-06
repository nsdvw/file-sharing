<?php
namespace FileSharing\Mapper;

use FileSharing\Model\User;

class UserMapper extends AbstractMapper
{
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
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\FileSharing\Model\User');
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
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\FileSharing\Model\User');
        return $sth->fetch();
    }

    public function findByLogin($login)
    {
        $sql = "SELECT COUNT(id) FROM user WHERE login=:login";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':login', $login, \PDO::PARAM_STR);
        $sth->execute();
        return $sth->fetchColumn();
    }

    public function findAllByID(array $ids)
    {
        $ids = array_filter(array_unique($ids));
        if (!$ids) {
            return $ids;
        }
        $ids = $this->quote($ids, \PDO::PARAM_INT);
        $sql = "SELECT id, login, email FROM user WHERE id IN ($ids)";
        $sth = $this->connection->prepare($sql);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\FileSharing\Model\User');
        return $sth->fetchAll();
    }

    public function findAllByIDindexed(array $ids)
    {
        $users = $this->findAllByID($ids);
        $indexedArray = [];
        foreach ($users as $user) {
            $indexedArray[$user->id] = $user;
        }
        return $indexedArray;
    }

    private function quote($value, $type = \PDO::PARAM_STR)
    {
        if (! is_array($value)) {
            return $this->connection->quote($value, $type);
        }
        foreach ($value as $k => $v) {
            $value[$k] = $this->connection->quote($v, $type);
        }
        return implode(', ', $value);
    }
}
