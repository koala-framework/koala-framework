<?php
class Vpc_Root_Category_Trl_Generator extends Vpc_Root_Category_Cc_Generator
{
    public function getPagesControllerConfig($component)
    {
        $ret = parent::getPagesControllerConfig($component);
        $ret['actions']['properties'] = true;
        $ret['actions']['visible'] = true;
        return $ret;
    }

    protected function _formatConfig($parentData, $row)
    {
        $ret = parent::_formatConfig($parentData, $row);
        $dbRow = $this->_getModel()->getRow($ret['componentId']);
        if (!$dbRow) {
            $dbRow = $this->_getModel()->createRow(array(
                'component_id' => $ret['componentId'],
                'name' => $row->getRow()->name,
                'filename' => $row->getRow()->filename,
                'visible' => $row->isHome, //home ist standardmäßig immer sichtbar, andere seiten nicht
                'custom_filename' => $row->getRow()->custom_filename
            ));
        }
        $ret['row'] = $dbRow;
        $ret['name'] = $dbRow->name;
        $ret['filename'] = $dbRow->filename;
        $ret['visible'] = $row->isHome ? true : $dbRow->visible;
        $ret['isHome'] = $row->isHome;
        return $ret;
    }
}
