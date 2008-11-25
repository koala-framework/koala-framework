<?php
class Vps_Update_23304 extends Vps_Update
{
    protected function _init()
    {
        $this->_actions[] = new Vps_Update_Action_Db_RenameField(array(
            'table' => 'vps_pages',
            'field' => 'type',
            'newName' => 'category',
        ));
    }

    public function update()
    {
        parent::update();
        $str = file_get_contents('application/config.ini');
        $str = str_replace('vpc.pageTypes.', 'vpc.pageCategories.', $str);
        file_put_contents('application/config.ini', $str);
    }
}
