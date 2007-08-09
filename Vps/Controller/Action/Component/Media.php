<?php
class Vps_Controller_Action_Component_Media extends Vps_Controller_Action
{
	public function indexAction()
	{

		$pw = 'jupidu';
		$pw_thumbnail = 'thumbi';
		//nachschauen, ob es bild skaliert schon gibt
		$path = $this->getRequest()->getPathInfo();
		$start = strripos($path, '.');
		$fileextension = substr($path, $start);
		$filename = $this->_getParam('componentId').$fileextension;


		if (!file_exists('./public/media/'.$filename)){
	        $id = $this->getRequest()->getParam('componentId');
	        $this->component = Vpc_Abstract::createInstance(Zend_Registry::get('dao'), 'Vpc_Simple_Image_Index', $id);

	        $tablename = 'Vpc_Simple_Image_FileDataModel';
			$table = new $tablename;

			$row = $table->find($this->component->getSetting('vps_upload_id'))->current();
			$pathFind = $row->path;
			$width = $this->component->getSetting('width');
			$height = $this->component->getSetting('height');
			$this->_newSize($pathFind, $filename, $width, $height);
		}
		;
		if ($this->_getParam('hash') == md5 ($pw.$this->_getParam('componentId'))){
		header('Content-type: image/jpg');
		readfile('./public/media/'.$filename);
		die();
		}
		else if ($this->_getParam('hash') == md5 ($pw_thumbnail.$this->_getParam('componentId'))){
			$im = new Imagick();
			$width = 100;
			$height = 100;
			$im->readImage('./public/media/'.$filename);
			$scale = $im ->getImageGeometry();
			$widthRatio = $scale['width'] / $width;
			$heightRatio = $scale['height'] / $height;
			if ($widthRatio > $heightRatio){
				$width = $scale['width'] / $widthRatio;
				$height = $scale['height'] / $widthRatio;
			} else {
				$width = $scale['width'] / $heightRatio;
				$height = $scale['height'] / $heightRatio;
			}
			$im->thumbnailImage($width, $height);
			$im->writeImage( './public/thumbnail/'.$filename);
			$im->destroy();
			header('Content-type: image/jpg');
			readfile('./public/thumbnail/'.$filename);
			die();


		} else {
			$response = new Zend_Http_Response(404, array('POST' => 'HTTP/1.0 404 Not Found'));
			 echo $response->getStatus() . ": " . $response->getMessage();
		}
	}

	private function _newSize($pathFind, $filename, $width, $height)
	{

		if ($this->component->getSetting('style') == '') $style = ($this->component->getSetting('default_style'));
		else $style = ($this->component->getSetting('style'));
		/* Create the Imagick object */
		$im = new Imagick();

		$config = Zend_Registry :: get('config');
		/* Read the image file */
		$config = Zend_Registry :: get('config');
		$im->readImage($config->uploads.$pathFind);

		//Bildausschnitt
		if ($style == 'crop'){
			$scale = $im ->getImageGeometry();
			$x = ($scale['width']-$width)/2;
			$y = ($scale['height']-$height)/2;
			$im->cropImage ( $width, $height, $x, $y);
			$im->writeImage( './public/media/'.$filename);
		}
		//Bildmaxgröße
		elseif ($style == 'scale'){
			$scale = $im ->getImageGeometry();
			$widthRatio = $scale['width'] / $width;
			$heightRatio = $scale['height'] / $height;
			if ($widthRatio > $heightRatio){
				$width = $scale['width'] / $widthRatio;
				$height = $scale['height'] / $widthRatio;
			} else {
				$width = $scale['width'] / $heightRatio;
				$height = $scale['height'] / $heightRatio;
			}
			$im->thumbnailImage($width, $height);
			$im->writeImage( './public/media/'.$filename);
		}

		//Bildmaxgröße + Hintergrund
		elseif ($style == 'scale_bg'){
			$Imagick = new Imagick();
			$ImagickPixel = new ImagickPixel();

			/* This ImagickPixel is used to set background color */
			if ($this->component->getSetting('color') == '')
				$color = ($this->component->getSetting('default_color'));
		 	else
		 		$color = ($this->component->getSetting('color'));
			$ImagickPixel->setColor( $color );

			/* Create new image, set color to gray and format to png*/
			$Imagick->newImage( $width, $height, $ImagickPixel );
			$Imagick->setImageFormat( 'png' );

			$scale = $im ->getImageGeometry();
			$widthRatio = $scale['width'] / $width;
			$heightRatio = $scale['height'] / $height;

			if ($widthRatio > $heightRatio){
				$height1 = $height;
				$width = $scale['width'] / $widthRatio;
				$height = $scale['height'] / $widthRatio;
				$x = 0;
				$y = ($height1 - $height) / 2;


			} else {
				$width1 = $width;
				$width = $scale['width'] / $heightRatio;
				$height = $scale['height'] / $heightRatio;
				$x = ($width1 - $width) / 2;
				$y = 0;
			}
			$im->thumbnailImage($width, $height);
			$Imagick->compositeImage($im, $im->getImageCompose(), $x, $y);
			$Imagick->writeImage( './public/media/'.$filename);
			$Imagick->destroy();
			//$im->compositeImage($Imagick, $Imagick->getImageCompose(), $x, $y);
			//$im->writeImage( './public/media/'.$filename);
		}

		// Verzerren
		elseif ($style == 'deform'){
			$im->thumbnailImage( $width, $height );
			$im->writeImage( './public/media/'.$filename);
		}
		$im->destroy();
	}
}