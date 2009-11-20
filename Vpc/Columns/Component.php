<?php
class Vpc_Columns_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['columns'] = array(
            'class' => 'Vps_Component_Generator_Table',
            'model' => 'Vpc_Columns_ColumnsModel',
            'component' => 'Vpc_Paragraphs_Component'
        );

        $ret['componentName'] = trlVps('Columns');
        $ret['componentIcon'] = new Vps_Asset('application_tile_horizontal');

        $ret['ownModel'] = 'Vpc_Columns_Model';

        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Columns/EditButton.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Columns/Panel.js';
        $ret['assetsAdmin']['dep'][] = 'VpsAutoForm';
        $ret['assetsAdmin']['dep'][] = 'VpsComponent';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $s = new Vps_Component_Select();
        $s->whereGenerator('columns');
        $s->order('pos');
        $ret['columns'] = $this->getData()->getChildComponents($s);
        return $ret;
    }
}
