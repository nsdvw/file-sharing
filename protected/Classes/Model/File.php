<?php namespace Model\File;

class File
{
	public $name;
	public $description;
	public $author_id;
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
		$mediaInfo = new \Model\File\MediaInfo($tmp_name);
		$this->properties = json_encode($mediaInfo);
	}
}