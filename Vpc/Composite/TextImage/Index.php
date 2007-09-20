<?php
class Vpc_Composite_TextImage_Index extends Vpc_Abstract
{
    const NAME = 'Standard.TextImage';
    public $text;
    public $pic;
    
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['text'] = $this->text->getTemplateVars('');
        $return['pic'] = $this->pic->getTemplateVars('');
        $return['template'] = 'Composite/TextImage.html';
        return $return;
    }
    
    public function init ()
    {
        $this->text = $this->createComponent('Vpc_Simple_Text_Index', 1);
        $this->pic = $this->createComponent('Vpc_Simple_Image_Index', 2);
    }

}