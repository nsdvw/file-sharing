<?php namespace Model\File;

class File
{
	public $name;
	public $description;
	public $author_id;
	public $size;
	public $mime_type;
	public $properties;

	public function __construct(
								$name,
								$tmp_name,
								$description = NULL,
								$author_id = NULL
								)
	{
		$this->name = $name;
		$this->description = $description;
		$this->author_id = $author_id;

		$info = self::getInfo("/$tmp_name");
		$this->size = $info['size'];
		$this->mime_type = $info['mime_type'];
		$this->properties = $info['properties'];
	}

	protected static function getInfo($file)
	{
		$info = array();
		$info['size'] = filesize($file);
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$info['mime_type'] = $finfo->file($file);
		$id3 = new \getID3();
		$id3->encoding = 'UTF-8';
		$info['properties'] = json_encode( $id3->analyze($file) );
		return $info;
	}
}