<?php
/**
 * Controller that receives a file, saves it in temp and allows download of that file
 * After 1h the file is deleted
 *
 * Useful for client side generated downloads to support older browsers
 */
class Kwf_Controller_Action_Media_PostBackController extends Kwf_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        //collect garbage
        foreach (glob('temp/postback-*') as $f) {
            if (time() - filemtime($f) > 60*60) {
                unlink($f);
            }
        }
    }

    public function jsonUploadAction()
    {
        $contents = $this->getRequest()->getRawBody();
        if (!$contents) {
            throw new Kwf_Exception('contents not set');
        }
        $key = time().uniqid();
        $tempNam = 'temp/postback-'.$key;
        if ($this->_getParam('upload-type') == 'base64') {
            $contents = base64_decode($contents);
        }
        file_put_contents($tempNam, $contents);
        $data = array(
            'file' => $tempNam,
            'mimeType' => $this->getRequest()->getHeader('Content-Type'),
            'mtime' => time(),
            'downloadFilename' => $this->getRequest()->getHeader('X-Download-Filename')
        );
        file_put_contents($tempNam.'-meta', serialize($data));
        $this->view->downloadUrl = '/kwf/media/post-back/download?key='.$key;
    }

    public function downloadAction()
    {
        $key = $this->_getParam('key');
        if (!preg_match('#^[a-z0-9]+$#', $key)) throw new Kwf_Exception_NotFound();
        if (!file_exists('temp/postback-'.$key.'-meta')) throw new Kwf_Exception_NotFound();
        $data = unserialize(file_get_contents('temp/postback-'.$key.'-meta'));
        Kwf_Media_Output::output($data);
    }
}
