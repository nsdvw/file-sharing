<?php
class FileModel
{
	public $id; // lastInsertId
	protected $db_config;

	public function __construct()
	{
		$this->db_config = parse_ini_file('config.txt');
	}

	public function save($name, $author_id = null, $description = null)
	{
		$dbh = new PDO($this->db_config['conn'], $this->db_config['user'],
					   $this->db_config['pass']);
		$sql = "INSERT INTO file (name, author_id, description)
						VALUES (:name, :author_id, :description)";
		$sth = $dbh->prepare($sql);
		$sth->bindParam(':name', $name, PDO::PARAM_STR);
		$sth->bindParam(':author_id', $author_id, PDO::PARAM_INT);
		$sth->bindParam(':description', $description, PDO::PARAM_STR);
		$sth->execute();
		$this->id = $dbh->lastInsertId();
	}

	public function find($id = null, $limit = 100)
	{
		$dbh = new PDO($this->db_config['conn'], $this->db_config['user'],
					   $this->db_config['pass']);
		if(!$id){
			$sql = "SELECT id, name, upload_time, description, author_id
					FROM file ORDER BY upload_time DESC LIMIT :limit";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':limit', $limit, PDO::PARAM_INT);
		}else{
			$sql = "SELECT id, name, upload_time, description, author_id
					FROM file WHERE id=:id";
			$sth = $dbh->prepare($sql);
			$sth->bindParam(':id', $id, PDO::PARAM_INT);
		}
		$sth->execute();
		return $sth->fetchAll();
	}
}