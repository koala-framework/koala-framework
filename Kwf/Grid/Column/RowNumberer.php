<?php
class Kwf_Grid_Column_RowNumberer extends Kwf_Grid_Column
{
    public function __construct($width = 30)
    {
        parent::__construct();
        $this->setWidth($width);
        $this->setHeader('');
    }

    public function getMetaData($model, $tableInfo = null)
    {
        $ret = array(
            'header' => $this->getHeader(),
            'class' => 'Ext2.grid.RowNumberer',
            'config' => array(
                'width' => $this->getWidth()
            )
        );
        return $ret;
    }
}
