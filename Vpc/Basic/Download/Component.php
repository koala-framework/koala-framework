<?php
class Vpc_Basic_Download_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'tablename' => 'Vpc_Basic_Download_Model',
            'componentName' => trlVps('Download'),
            'componentIcon' => new Vps_Asset('folder_link'),
            'showFilesize' => true,
            'childComponentClasses'   => array(
                'downloadTag' => 'Vpc_Basic_DownloadTag_Component',
            ),
            'default'   => array(
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['infotext'] = $this->_getRow()->infotext;
        if (!$this->_getSetting('showFilesize')) {
            $return['filesize'] = null;
        } else {
            $tag = $this->getData()->getChildComponent('-downloadTag')->getComponent();
            $return['filesize'] = $tag->getFilesize();
        }
        return $return;
    }

    public function getSearchVars()
    {
        $ret = parent::getSearchVars();
        $ret['text'] .= ' '.$this->_getRow()->infotext;
        return $ret;
    }
}
