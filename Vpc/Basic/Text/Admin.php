<?php
class Vpc_Basic_Text_Admin extends Vpc_Admin
{
    /*
    public function getControllerClass()
    {
        return 'Vpc.Rte.Panel';
    }
*/

    public function setup()
    {
        $this->copyTemplate('Template.html', 'Basic/Rte.html');

        Vpc_Admin::getInstance('Vpc_Basic_Image_Component')->setup();
        Vpc_Admin::getInstance('Vpc_Basic_Link_Intern')->setup();

        $fields['text'] = 'text NOT NULL';
        $this->createTable('vpc_rte', $fields);
    }
}