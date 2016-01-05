<?php
namespace FileSharing\Mapper;

use FileSharing\Helper\Pager;
use FileSharing\Helper\JsonEncoder;
use FileSharing\Model\MediaInfo;
use FileSharing\Model\File;

class FileMapper extends AbstractMapper
{
    public function save(File $file)
    {
        $sql =
        "INSERT INTO file
            (name, author_id, size, mime_type, mediaInfo)
        VALUES
            (:name, :author_id, :size, :mime_type, :mediaInfo)";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':name', $file->name, \PDO::PARAM_STR);
        $sth->bindValue(':author_id', $file->author_id, \PDO::PARAM_INT);
        $sth->bindValue(':size', $file->size, \PDO::PARAM_INT);
        $sth->bindValue(':mime_type', $file->mime_type, \PDO::PARAM_STR);
        $mediaInfo = JsonEncoder::encode($file->mediaInfo);
        $sth->bindValue(':mediaInfo', $mediaInfo, \PDO::PARAM_STR);
        $sth->execute();
        $file->id = $this->connection->lastInsertId();
    }

    public function updateCounter($id)
    {
        $sql = "UPDATE file
                SET download_counter = download_counter + 1
                WHERE id = :id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
    }

    public function findAll($offset = 0, $limit = 20)
    {
        $sql = "SELECT id, name, upload_time, size, mime_type,
                       download_counter, author_id, mediaInfo
                FROM file
                ORDER BY upload_time DESC LIMIT :offset, :limit";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $sth->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $sth->execute();
        $list = $sth->fetchAll(\PDO::FETCH_CLASS, '\FileSharing\Model\File');
        foreach ($list as $row) {
            $row->mediaInfo = MediaInfo::fromDataBase(
                JsonEncoder::decode($row->mediaInfo)
            );
        }
        return $list;
    }

    public function findById($id)
    {
        $sql = "SELECT id, name, upload_time,
                size, mime_type, download_counter,
                author_id, mediaInfo
                FROM file WHERE id=:id";
        $sth = $this->connection->prepare($sql);
        $sth->bindValue(':id', $id, \PDO::PARAM_INT);
        $sth->execute();
        $sth->setFetchMode(\PDO::FETCH_CLASS, '\FileSharing\Model\File');
        $row = $sth->fetch();
        if ($row == null) {
            return $row;
        }
        $row->mediaInfo = MediaInfo::fromDataBase(
            JsonEncoder::decode($row->mediaInfo)
        );
        return $row;
    }

    public function getFileCount()
    {
        $sql = "SELECT COUNT(*) as page_count FROM file";
        $sth = $this->connection->prepare($sql);
        $sth->execute();
        return $sth->fetchColumn();
    }
}
