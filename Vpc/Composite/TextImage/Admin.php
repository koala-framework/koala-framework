<?php
class Vpc_Composite_TextImage_Admin extends Vpc_Admin
{
    public function setup()
    {
        $this->copyTemplate('Index.html', 'Composite/TextImage.html');

        Vpc_Admin::getInstance('Vpc_Basic_Text_Index')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Image_Index')->setup();
    }

    public function delete($component)
    {
        Vpc_Admin::getInstance($component->image)->delete($component->image);
        Vpc_Admin::getInstance($component->text)->delete($component->text);
    }
}
