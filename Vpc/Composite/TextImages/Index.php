<?php
class Vpc_Composite_TextImages_Index extends Vpc_Abstract
{
    const NAME = 'Standard.TextImages';
    public $text;
    public $images;
    protected $_settings = array(
        'text' => array(),
        'images' => array(),
        'image_position' => 'alternate' // 'left', 'right', 'alternate'
    );
    protected $_tablename = 'Vpc_Composite_TextImage_IndexModel';

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['images'] = $this->images->getTemplateVars('');
        $return['imagePosition'] = $this->getSetting('image_position');
        $return['template'] = 'Composite/TextImages.html';
        return $return;
    }

    public function init()
    {
        // Text
        $st = isset($this->_settings['text']) ? $this->_settings['text'] : array();
        $this->text = $this->createComponent('Vpc_Basic_Text_Index', 1, $st);

        // Images
        $si = isset($this->_settings['images']) ? $this->_settings['images'] : array();
        $this->images = $this->createComponent('Vpc_Composite_Images_Index', 2, $si);
    }

    public function getChildComponents()
    {
        return array($this->text, $this->images);
    }

}