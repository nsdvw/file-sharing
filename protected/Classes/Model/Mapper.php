<?php namespace Model\File;

class Mapper
{
    protected $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(\Model\File\File $file)
    {
        $sql = "INSERT INTO file
                    (name, author_id, description, properties)
                VALUES
                    (:name, :author_id, :description, :properties)";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':name', $file->name, \PDO::PARAM_STR);
        $sth->bindParam(':author_id', $file->author_id, \PDO::PARAM_INT);
        $sth->bindParam(':description', $file->description, \PDO::PARAM_STR);
        $sth->bindParam(':properties', $file->properties, \PDO::PARAM_STR);
        $sth->execute();
        return $this->connection->lastInsertId();
    }

    public function findAll($limit = 100, $offset = 0)
    {
        $sql = "SELECT id, name, upload_time, description,
                author_id, properties
                FROM file ORDER BY upload_time DESC LIMIT :offset, :limit";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $sth->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "SELECT id, name, upload_time, description,
                author_id, properties
                FROM file WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetch(\PDO::FETCH_ASSOC);
    }
}