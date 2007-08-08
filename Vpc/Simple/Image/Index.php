<?php
class Vpc_Simple_Image_Index extends Vpc_Abstract
{
	protected $_tablename = 'Vpc_Simple_Image_FileDataModel';
    public $controllerClass = 'Vpc_Simple_Image_IndexController';
   	const NAME = 'Standard.Image';

	/*public static function getStaticSettings()
	{
		$config = array ();
		$config['path'] = 'simpleimage/';
		$config['thumbnail_width'] = 80;
		$config['thumbnail_height'] = 100;
		$config['pic_width'] = 400;
		$config['pic_width'] = 300;
		$config['typesAllowed'] = 'jpg, png, gif';

		return $config;
	}*/

	protected $_settings = array(
        'typesAllowed' => 'jpg, gif, png',
        'directory'    => 'SimpleImage/'
        );


	public function getTemplateVars()
	{

		$return['template'] = 'Simple/Image.html';
		return $return;
	}

}