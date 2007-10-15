<?php
class Vpc_Composite_TextImages_Admin extends Vpc_Admin
{
    public function getControllerClass()
    {
        return 'Vps.Component.TabPanel';
    }

    public function getControllerConfig($component, $view)
    {
        $config['tabs']['Text']['config']['controllerUrl'] = $view->getControllerUrl($component, 'Vpc_Composite_TextImages_Text');
        $config['tabs']['Text']['class'] = 'Vps.Auto.FormPanel';
        $config['tabs']['Images']['config']['controllerUrl'] = $view->getControllerUrl($component, 'Vpc_Composite_TextImages_Images');
        $config['tabs']['Images']['class'] = 'Vps.Auto.GridPanel';
        $config['activeItem'] = 'Text';
        return $config;
    }

    public function setup()
    {
        $this->copyTemplate('Index.html', 'Composite/TextImages.html');

        Vpc_Admin::getInstance('Vpc_Basic_Text_Index')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Image_Index')->setup();

        $fields['image_position'] = "enum('left', 'right', 'alternate') default NULL";
        $fields['enlarge'] = 'tinyint(3) NOT NULL';
        $this->createTable('vpc_composite_textimage', $fields);
    }

    public function delete($component)
    {
        foreach ($component->getChildComponents() as $c) {
            Vpc_Admin::getInstance($c)->delete($c);
        }
    }
}
