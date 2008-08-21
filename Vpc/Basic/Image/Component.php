<?php
class Vpc_Basic_Image_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName'     => trlVps('Image'),
            'componentIcon'     => new Vps_Asset('picture'),
            'tablename'         => 'Vpc_Basic_Image_Model',

            'dimensions'        => array(300, 200, Vps_Media_Image::SCALE_BESTFIT), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), null: Bild in Originalgröße)
//             'ouputDimensions'   => array('mini'  => array(20, 20, Vps_Media_Image::SCALE_BESTFIT),
//                                          'thumb' => array(100, 100, Vps_Media_Image::SCALE_BESTFIT)),
            'editComment'       => false,
            'editFilename'      => false,
            'allowBlank'        => true,
            'default'           => array(
                'filename'   => 'filename'
            ),
            'pdfMaxWidth'       => 0,
            'imgCssClass'       => '',
            'emptyImage'        => false,
            'useParentImage'    => false
        ));
        $ret['assetsAdmin']['dep'][] = 'VpsSwfUpload';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getImageRow();
        if (Vpc_Abstract::getSetting(get_class($this), 'editComment')) {
            $ret['comment'] = $ret['row']->comment;
        }
        $ret['imgCssClass'] = $this->_getSetting('imgCssClass');
        return $ret;
    }

    public function getImageUrl()
    {
        return $this->getImageRow()->getFileUrl();
    }

    public function getImageDimensions()
    {
        return $this->getImageRow()->getImageDimensions();
    }

    //für Pdf
    public function getImageRow()
    {
        if ($this->_getSetting('useParentImage')) {
            return $this->getTable()->findRow($this->getData()->parent->dbId);
        } else {
            return $this->_getRow();
        }
    }
}
