<?php
class Vpc_Simple_Image_Index extends Vpc_Abstract
{
	public static function getStaticSettings()
	{
		$config = array ();
		$config['path'] = 'simpleimage/';
		$config['thumbnail_width'] = 80;
		$config['thumbnail_height'] = 100;
		$config['pic_width'] = 400;
		$config['pic_width'] = 300;
		$config['typesAllowed'] = 'jpg, png, gif';

		return $config;
	}

	public function getTemplateVars()
	{

		$return['template'] = 'Simple/Image.html';
		return $return;
	}

}