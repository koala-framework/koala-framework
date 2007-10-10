<?php
class Vpc_Composite_ContentImage_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($component, $view)
    {
        $config['tabs']['Image'] = $view->getConfig($component->image);
        $config['tabs']['Content'] = $view->getConfig($component->content);
        $config['activeItem'] = 'Content';
        return $config;
    }

    public function setup()
    {
        $this->copyTemplate('Index.html', 'Composite/ContentImage.html');

        Vpc_Admin::getInstance('Vpc_Paragraphs_Index')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Image_Index')->setup();
    }

    public function delete($component)
    {
        Vpc_Admin::getInstance($component->content)->delete($component->content);
        Vpc_Admin::getInstance($component->image)->delete($component->image);
    }
}
