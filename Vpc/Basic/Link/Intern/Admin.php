<?php
class Vpc_Basic_Link_Intern_Admin extends Vpc_Admin
{
    public function getControllerConfig($component)
    {
        $pagesControllerUrl = $this->getControllerUrl($component, 'Vpc_Basic_Link_PagesController');
        $config = array(
            "pagesControllerUrl"    => $pagesControllerUrl
        );
        return $config;
    }

    public function getControllerClass()
    {
        return 'Vpc.Basic.Link.Panel';
    }

    public function setup()
    {
        $this->copyTemplate('Template.html', 'Basic/Link.html');

        $fields['type'] = "enum('intern','extern','mailto') NOT NULL";
        $fields['target'] = "varchar(255) NOT NULL";
        $fields['rel'] = "varchar(255) NOT NULL";
        $this->createTable('vpc_basic_link_intern', $fields);
    }
}