<?php namespace Model\File;

class Mapper
{
	public $id; // lastInsertId
	protected $connection;

	public function __construct(\PDO $connection)
	{
		$this->connection = $connection;
	}

	public function save($name, $author_id = null, $description = null)
	{
		$sql = "INSERT INTO file (name, author_id, description)
					   VALUES (:name, :author_id, :description)";
		$sth = $this->connection->prepare($sql);
		$sth->bindParam(':name', $name, \PDO::PARAM_STR);
		$sth->bindParam(':author_id', $author_id, \PDO::PARAM_INT);
		$sth->bindParam(':description', $description, \PDO::PARAM_STR);
		$sth->execute();
		$this->id = $this->connection->lastInsertId();
	}

	public function find($id = null, $limit = 100)
	{
		if(!$id){
			$sql = "SELECT id, name, upload_time, description, author_id
					FROM file ORDER BY upload_time DESC LIMIT :limit";
			$sth = $this->connection->prepare($sql);
			$sth->bindParam(':limit', $limit, \PDO::PARAM_INT);
		}else{
			$sql = "SELECT id, name, upload_time, description, author_id
					FROM file WHERE id=:id";
			$sth = $this->connection->prepare($sql);
			$sth->bindParam(':id', $id, \PDO::PARAM_INT);
		}
		$sth->execute();
		return $sth->fetchAll();
	}
}