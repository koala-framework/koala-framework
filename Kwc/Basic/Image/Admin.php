<?php
class Kwc_Basic_Image_Admin extends Kwc_Abstract_Image_Admin
{
    protected function _onRowAction($row)
    {

        if ($row instanceof Kwc_Abstract_Image_Row) {
            $components = Kwf_Component_Data_Root::getInstance()->getComponentsByDbId(
                $row->component_id, array('ignoreVisible' => true, 'componentClass'=>$this->_class)
            );
            foreach ($components as $c) {
                $cacheId = $c->componentClass.'_'.str_replace('-', '__', $c->dbId).'_default';
                Kwf_Media::getOutputCache()->remove($cacheId);
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
        $fields['kwf_upload_id'] = 'int(11) DEFAULT NULL';
        $this->createFormTable('kwc_basic_image', $fields);
    }
}
