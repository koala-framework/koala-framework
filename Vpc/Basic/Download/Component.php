<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract
{
    protected $_downloadTag;

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

    public function getChildComponent()
    {
        if (!$this->_downloadTag) {
            $class = $this->_getClassFromSetting('downloadTag', 'Vpc_Basic_DownloadTag_Component');
            $this->_downloadTag = $this->createComponent($class, 'tag');
        }
        return $this->_downloadTag;
    }

    public function getChildComponents()
    {
        return array($this->getChildComponent());
    }


    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['downloadTag'] = $this->getChildComponent()->getTemplateVars();

        $return['infotext'] = $this->_getRow()->infotext;
        if (!$this->_getSetting('showFilesize')) {
            $return['filesize'] = '';
        }
        return $return;
    }

}
