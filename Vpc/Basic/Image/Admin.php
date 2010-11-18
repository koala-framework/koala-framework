<?php
class Vpc_Basic_Image_Admin extends Vpc_Abstract_Image_Admin
{
    protected function _onRowAction($row)
    {

        if ($row instanceof Vpc_Abstract_Image_Row) {
            $components = Vps_Component_Data_Root::getInstance()->getComponentsByDbId(
                $row->component_id, array('ignoreVisible' => true, 'componentClass'=>$this->_class)
            );
            foreach ($components as $c) {
                $cacheId = $c->componentClass.'_'.str_replace('-', '__', $c->dbId).'_default';
                Vps_Media::getOutputCache()->remove($cacheId);
            }
        }
    }

    public function setup()
    {
        $fields['filename'] = 'varchar(255) DEFAULT NULL';
        $fields['width'] = 'int(11) DEFAULT NULL';
        $fields['height'] = 'int(11) DEFAULT NULL';
        $fields['scale'] = 'varchar(255) DEFAULT NULL';
        $fields['enlarge'] = 'tinyint(3) DEFAULT 0';
        $fields['vps_upload_id'] = 'int(11) DEFAULT NULL';
        $this->createFormTable('vpc_basic_image', $fields);
    }
}
