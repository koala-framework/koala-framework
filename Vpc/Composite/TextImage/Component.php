<?php
class Vpc_Composite_TextImage_Component extends Vpc_Abstract
{
    protected $_text;
    protected $_image;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'TextImage',
            'tablename'         => 'Vpc_Composite_TextImage_Model',
            'childComponentClasses' => array(
                'text'         => 'Vpc_Basic_Text_Component',
                'image'        => 'Vpc_Basic_Image_Enlarge_Component',
            ),
            'default'           => array(
                'image_position'    => 'left' // 'left', 'right', 'alternate'
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->getChildComponent('text')->getTemplateVars('');
        $return['image'] = $this->getChildComponent('image')->getTemplateVars('');
        $return['image_position'] = $this->_getRow()->image_position;
        return $return;
    }

    protected function getChildComponent($type)
    {
        if ($type == 'text') {
            if (!$this->_text) {
                $textClass = $this->_getClassFromSetting('text', 'Vpc_Basic_Text_Component');
                $this->_text = $this->createComponent($textClass, 'text');
            }
            return $this->_text;
        } else if ($type == 'image') {
            if (!$this->_image) {
                $imageClass = $this->_getClassFromSetting('image', 'Vpc_Basic_Image_Component');
                $this->_image = $this->createComponent($imageClass, 'image');
            }
            return $this->_image;
        }
        return null;
    }

    public function getChildComponents()
    {
        return array(
            $this->getChildComponent('text'),
            $this->getChildComponent('image')
        );
    }

    public function getTextImageRow()
    {
        return $this->_getRow();
    }

}
