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
					(name, author_id, description, size, mime_type, properties)
				VALUES
					(:name, :author_id, :description, :size, :mime_type, :properties)";
		$sth = $this->connection->prepare($sql);
		$sth->bindParam(':name', $file->name, \PDO::PARAM_STR);
		$sth->bindParam(':author_id', $file->author_id, \PDO::PARAM_INT);
		$sth->bindParam(':description', $file->description, \PDO::PARAM_STR);
		$sth->bindParam(':size', $file->size, \PDO::PARAM_INT);
		$sth->bindParam(':mime_type', $file->mime_type, \PDO::PARAM_STR);
		$sth->bindParam(':properties', $file->properties, \PDO::PARAM_STR);
		$sth->execute();
		return $this->connection->lastInsertId();
	}

	public function find($id = null, $limit = 100)
	{
		if(!$id){
			$sql = "SELECT id, name, upload_time, description,
					author_id, size, mime_type 
					FROM file ORDER BY upload_time DESC LIMIT :limit";
			$sth = $this->connection->prepare($sql);
			$sth->bindParam(':limit', $limit, \PDO::PARAM_INT);
		}else{
			$sql = "SELECT id, name, upload_time, description,
					author_id, size, mime_type
					FROM file WHERE id=:id";
			$sth = $this->connection->prepare($sql);
			$sth->bindParam(':id', $id, \PDO::PARAM_INT);
		}
		$sth->execute();
		return $sth->fetchAll();
	}
}