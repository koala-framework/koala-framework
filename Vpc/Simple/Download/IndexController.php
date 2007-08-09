<?php
class Vpc_Simple_Download_IndexController extends Vps_Controller_Action
{
	public function indexAction()
	{
		//d (file_exists('/www/usr/lorenz/vps/Vpc/Simple/Download/jscripts/SWFUpload/SWFUpload.swf'));
		/*p ('war da');
		$cfg['url'] = 'dd';
        $this->view->ext('Vpc.Simple.uploadform', $cfg);*/

		$cfg['url'] = 'url';
        $this->view->ext('Vpc.Simple.Download.Index', $cfg);
    }





}
