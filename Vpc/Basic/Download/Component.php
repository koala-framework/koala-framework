<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract
{
    public $downloadTag;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Download_Model',
            'componentName' => 'Download',
            'showFilesize' => true,
            'childComponentClasses'   => array(
                'downloadTag' => 'Vpc_Basic_DownloadTag_Component',
            ),
            'default'   => array(
                'filename' => 'unnamed'
            )
        ));
    }
    public function _init()
    {
        $class = $this->_getClassFromSetting('downloadTag', 'Vpc_Basic_DownloadTag_Component');
        $this->downloadTag = $this->createComponent($class, 'tag');
    }

    public function getChildComponents()
    {
        return array($this->downloadTag);
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['downloadTag'] = $this->downloadTag->getTemplateVars();

        $return['infotext'] = $this->_row->infotext;
        if (!$this->_getSetting('showFilesize')) {
            $return['filesize'] = '';
        }
        return $return;
    }

}
