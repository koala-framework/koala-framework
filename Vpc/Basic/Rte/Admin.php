<?php
class Vpc_Basic_Rte_Admin extends Vpc_Admin
{
    /*
    public function getControllerClass()
    {
        return 'Vpc.Rte.Index';
    }
*/

    public function setup()
    {
        $this->copyTemplate('Index.html', 'Basic/Rte.html');

        $fields['text'] = 'text NOT NULL';
        $this->createTable('vpc_rte', $fields);
    }
}