<?php
class Vpc_Composite_ParagraphsImage_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($component, $view)
    {
        $config['tabs']['Image'] = $view->getConfig($component->image);
        $config['tabs']['Paragraphs'] = $view->getConfig($component->paragraphs);
        $config['activeItem'] = 'Paragraphs';
        return $config;
    }

    public function setup()
    {
        $this->copyTemplate('Index.html', 'Composite/ParagraphsImage.html');

        Vpc_Admin::getInstance('Vpc_Paragraphs_Index')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Image_Index')->setup();
    }

    public function delete($component)
    {
        Vpc_Admin::getInstance($component->paragraphs)->delete($component->paragraphs);
        Vpc_Admin::getInstance($component->image)->delete($component->image);
    }
}
