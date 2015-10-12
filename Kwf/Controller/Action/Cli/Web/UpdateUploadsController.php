<?php
class Kwf_Controller_Action_Cli_Web_UpdateUploadsController extends Kwf_Controller_Action_Cli_Abstract
{
    private $_progressBar;

    public static function getHelp()
    {
        return 'Asynchron Updates for kwf upgrade to version 3.9';
    }

    private function _getUpdate()
    {
        $update = new Kwf_Update_20150309Legacy39000('Kwc_Root_Category_Update_20150309Legacy00002');
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(
            Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
            Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
            Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT
        ));
        $c->setTextWidth(50);
        $progress = new Zend_ProgressBar($c, 0, $update->countUploads());
        $update->setProgressBar($progress);
        return $update;
    }

    public function renameUploadsAction()
    {
        $this->_getUpdate()->renameUploads();
    }

    public function createHashesAction()
    {
        $this->_getUpdate()->createHashes();
    }

    public function moveOldFilesAction()
    {
        $this->_getUpdate()->moveOldFiles();
    }

    public function calculateDimensionsAction()
    {
        $update = new Kwf_Update_20151012UploadsDimensions('Kwf_Update_20151012UploadsDimensions');
        $c = new Zend_ProgressBar_Adapter_Console();
        $c->setElements(array(
            Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
            Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
            Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT
        ));
        $c->setTextWidth(50);
        $progress = new Zend_ProgressBar($c, 0, $update->countUploads());
        $update->setProgressBar($progress);
        $update->calculateDimensions();
        exit;
    }
}
