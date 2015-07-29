<?php namespace Storage\Model;

class FileMapper
{
    protected $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(\Storage\Model\File $file)
    {
        $sql =
        "INSERT INTO file
            (name, author_id, description, size, mime_type, mediaInfo)
        VALUES
            (:name, :author_id, :description, :size, :mime_type, :mediaInfo)";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':name', $file->name, \PDO::PARAM_STR);
        $sth->bindParam(':author_id', $file->author_id, \PDO::PARAM_INT);
        $sth->bindParam(':description', $file->description, \PDO::PARAM_STR);
        $sth->bindParam(':size', $file->size, \PDO::PARAM_INT);
        $sth->bindParam(':mime_type', $file->mime_type, \PDO::PARAM_STR);
        $sth->bindParam(
            ':mediaInfo',
            json_encode($file->mediaInfo),
            \PDO::PARAM_STR
        );
        $sth->execute();
        $file->id = $this->connection->lastInsertId();
    }

    public function updateCounter($id)
    {
        $sql = "UPDATE file SET download_counter = download_counter + 1
        WHERE id = :id";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
    }

    public function findAll($offset = 0, $limit = 5)
    {
        $sql = "SELECT id, name, upload_time, description,
                       size, mime_type, download_counter,
                       author_id, mediaInfo
                FROM file
                ORDER BY upload_time DESC LIMIT :offset, :limit";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $sth->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $sth->execute();
        $list = $sth->fetchAll(\PDO::FETCH_CLASS, '\Storage\Model\File');
        foreach ($list as $row) {
            $row->mediaInfo = \Storage\Model\MediaInfo::fromDataBase(
                json_decode($row->mediaInfo)
            );
        }
        return $list;
    }

    public function findById($id)
    {
        $sql = "SELECT id, name, upload_time, description,
                size, mime_type, download_counter,
                author_id, mediaInfo
                FROM file WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindParam(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\Storage\Model\File');
        $row = $sth->fetch();
        $row->mediaInfo = \Storage\Model\MediaInfo::fromDataBase(
            json_decode($row->mediaInfo)
        );
        return $row;
    }
}
