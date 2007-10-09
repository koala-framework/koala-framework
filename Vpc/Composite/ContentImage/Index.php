<?php
class Vpc_Composite_ContentImage_Index extends Vpc_Abstract
{
    const NAME = 'Standard.ContentImage';
    public $content;
    public $image;

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['content'] = $this->content->getTemplateVars('');
        $return['image'] = $this->image->getTemplateVars('');
        $return['template'] = 'Composite/ContentImage.html';
        return $return;
    }

    public function init()
    {
        $this->content = $this->createComponent('Vpc_Paragraphs_Index', 1);
        $this->image = $this->createComponent('Vpc_Basic_Image_Index', 2);
    }

    public function getChildComponents()
    {
        return array($this->content, $this->image);
    }

}