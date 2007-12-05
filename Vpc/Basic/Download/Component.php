<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract
{
    public $downloadTag;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Download_Model',
            'componentName' => 'Standard.Download',
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
        $this->downlaodTag = $this->createComponent($class, 'tag');
    }

    public function getChildComponents()
    {
        return array($this->downlaodTag);
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['downloadTag'] = $this->downlaodTag->getTemplateVars();

        $return['infotext'] = $this->_row->infotext;
        if (!$this->_getSetting('showFilesize')) {
            $return['filesize'] = '';
        }
        return $return;
    }

}
