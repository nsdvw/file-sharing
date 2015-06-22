<?php namespace Model\File;

class MediaInfo
{
	public $size;
	public $mime_type;
	public $resolution_x;
	public $resolution_y;
	public $frame_rate;
	public $encoding;
	public $playtime;
	public $bitrate;
	public $bits_per_sample;

	public function __construct($file)
	{
		$this->size = filesize($file);
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$this->mime_type = $finfo->file($file);
		$id3 = new \getID3();
		$id3->encoding = 'UTF-8';
		$finfo = $id3->analyze($file);
		if (isset($finfo['video']['resolution_x'])) {
			$this->resolution_x = $finfo['video']['resolution_x'];
		}
		if (isset($finfo['video']['resolution_y'])) {
			$this->resolution_y = $finfo['video']['resolution_y'];
		}
		if (isset($finfo['video']['frame_rate'])) {
			$this->frame_rate = $finfo['video']['frame_rate'];
		}
		if (isset($finfo['encoding'])) {
			$this->encoding = $finfo['encoding'];
		}
		if (isset($finfo['playtime_string'])) {
			$this->playtime = $finfo['playtime_string'];
		}
		if (isset($finfo['bitrate'])) {
			$this->bitrate = $finfo['bitrate'];
		}
		if (isset($finfo['video']['bits_per_sample'])) {
			$this->bits_per_sample = $finfo['video']['bits_per_sample'];
		}
	}

	static public function formatSize($size)
	{
		if ($size > pow(1024, 3)) {
			$size = round($size / pow(1024, 3), 2) . ' Гб';
		} elseif ($size > pow(1024, 2)) {
			$size = round($size / pow(1024, 2), 2) . ' Мб';
		}elseif ($size > 1024) {
			$size = round($size / 1024, 2) . ' Кб';
		}
		return $size;
	}
}