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
        $fields['text'] = 'text NOT NULL';
        $this->createFormTable('vpc_rte', $fields);
    }
}