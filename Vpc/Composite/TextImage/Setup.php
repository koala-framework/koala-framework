<?php
class Vpc_Simple_TextImage_Setup extends Vpc_Setup_Abstract
{
    public function setup()
    {
        $this->copyTemplate('TextImage.html');
        
        Vpc_Setup_Abstract::staticSetup('Vpc_Simple_Text_Setup');
        Vpc_Setup_Abstract::staticSetup('Vpc_Simple_Image_Setup');
    }

    public function deleteEntry($pageId, $componentKey)
    {
        Vpc_Setup_Abstract::staticSetup('Vpc_Simple_Text_Setup', $pageId, $componentKey . '-1');
        Vpc_Setup_Abstract::staticSetup('Vpc_Simple_Image_Setup', $pageId, $componentKey . '-2');
    }
}
