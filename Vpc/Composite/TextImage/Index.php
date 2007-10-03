<?php
class Vpc_Composite_TextImage_Index extends Vpc_Abstract
{
    const NAME = 'Standard.TextImage';
    public $text;
    public $image;

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        $return['template'] = 'Composite/TextImage.html';
        return $return;
    }

    public function init()
    {
        $this->text = $this->createComponent('Vpc_Basic_Text_Index', 1);
        $this->image = $this->createComponent('Vpc_Basic_Image_Index', 2);
    }

    public function getChildComponents()
    {
        return array($this->text, $this->image);
    }

}