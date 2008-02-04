<?php
class Vpc_Composite_TextImages_Component extends Vpc_Abstract
{
    protected $_text;
    protected $_images;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName'     => 'TextImages',
            'tablename'         => 'Vpc_Composite_TextImage_Model',
            'childComponentClasses' => array(
                'text'         => 'Vpc_Basic_Html_Component',
                'images'       => 'Vpc_Composite_Images_Component',
            ),
            'default'           => array(
                'image_position'    => 'alternate' // 'left', 'right', 'alternate'
            )
        ));
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->getChildComponent('text')->getTemplateVars('');
        $return['images'] = $this->getChildComponent('images')->getTemplateVars('');
        $return['imagePosition'] = $this->_getRow()->image_position;
        return $return;
    }

    protected function getChildComponent($type)
    {
        if ($type == 'text') {
            if (!$this->_paragraphs) {
                $textClass = $this->_getClassFromSetting('text', 'Vpc_Basic_Text_Component');
                $this->_text = $this->createComponent($textClass, 'text');
            }
            return $this->_text;
        } else if ($type == 'images') {
            if (!$this->_images) {
                $imagesClass = $this->_getClassFromSetting('images', 'Vpc_Composite_Images_Component');
                $this->_images = $this->createComponent($imagesClass, 'images');
            }
            return $this->_images;
        }
        return null;
    }

    public function getChildComponents()
    {
        return array(
            $this->getChildComponent('text'),
            $this->getChildComponent('images')
        );
    }

    public function getChildComponents()
    {
        return array($this->text, $this->images);
    }

}
