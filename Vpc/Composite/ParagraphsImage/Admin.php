<?php
class Vpc_Composite_ParagraphsImage_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($component)
    {
        $config['tabs']['Image'] = $this->getConfig($component->image);
        $config['tabs']['Paragraphs'] = $this->getConfig($component->paragraphs);
        $config['activeItem'] = 'Paragraphs';
        return $config;
    }

    public function setup()
    {
        $this->copyTemplate('Template.html', 'Composite/ParagraphsImage.html');

        Vpc_Admin::getInstance('Vpc_Paragraphs_Component')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Image_Component')->setup();
    }

    public function delete($component)
    {
        Vpc_Admin::getInstance($component->paragraphs)->delete($component->paragraphs);
        Vpc_Admin::getInstance($component->image)->delete($component->image);
    }
}
