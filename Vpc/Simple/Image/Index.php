<?php
class Vpc_Simple_Image_Index extends Vpc_Abstract
{
	protected $_tablename = 'Vpc_Simple_Image_IndexModel';
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
        'typesAllowed' 	    => 'jpg, gif, png',
        'directory'   	    => 'SimpleImage/',
        'filesize'	   	    => 'free',
        'default_style'		=> 'crop',
        'enableName' 		=> 1,
        'style' 	        => '',
        'allow'		        => array('crop', 'scale', 'scale_bg', 'deform'), //keywords: crop, scale, scale_bg, deform
        'default_color'		=> 'black',
        'allow_color'		=> 1,
		'color'				=> '',

        );


	public function getTemplateVars()
	{
		$return['template'] = 'Simple/Image.html';
		return $return;
	}

}