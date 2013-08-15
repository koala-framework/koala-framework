<?php
class Kwc_ColumnsResponsive_Component extends Kwc_Abstract_List_Component
{
    public static $needsParentComponentClass = true;
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Columns');
        $ret['componentIcon'] = new Kwf_Asset('application_tile_horizontal');

        $ret['assets']['files'][] = 'kwf/Kwc/ColumnsResponsive/Component.js';
        $ret['assets']['dep'][] = 'KwfResponsiveEl';

        $ret['generators']['child'] = array(
            'class' => 'Kwc_ColumnsResponsive_Generator',
            'component' => $parentComponentClass
        );
        $ret['extConfig'] = 'Kwc_ColumnsResponsive_ExtConfig';

        $ret['childModel'] = new Kwc_ColumnsResponsive_Model(array(
            'columns' => array('id', 'component_id', 'name'),
            'primaryKey' => 'id'
        ));
        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        $ret['columns'] = array(
            '2col-50_50' => array(
                'columns' => 2,
                'colSpans' => array(1,1),
                'name' => trlKwfStatic('2 Columns (50% - 50%)')
            ),
            '2col-33_66' => array(
                'columns' => 3,
                'colSpans' => array(1,2),
                'name' => trlKwfStatic('2 Columns (33% - 66%)')
            ),
            '2col-66_33' => array(
                'columns' => 3,
                'colSpans' => array(2,1),
                'name' => trlKwfStatic('2 Columns (66% - 33%)')
            ),
            '2col-25_75' => array(
                'columns' => 4,
                'colSpans' => array(1,3),
                'name' => trlKwfStatic('2 Columns (25% - 75%)')
            ),
            '2col-75_25' => array(
                'columns' => 4,
                'colSpans' => array(3,1),
                'name' => trlKwfStatic('2 Columns (75% - 25%)')
            ),
            '3col-33_33_33' => array(
                'columns' => 3,
                'colSpans' => array(1,1,1),
                'name' => trlKwfStatic('3 Columns (33% - 33% - 33%)')
            ),
            '3col-50_25_25' => array(
                'columns' => 4,
                'colSpans' => array(2,1,1),
                'name' => trlKwfStatic('3 Columns (50% - 25% - 25%)')
            ),
            '3col-25_50_25' => array(
                'columns' => 4,
                'colSpans' => array(1,2,1),
                'name' => trlKwfStatic('3 Columns (25% - 50% - 25%)')
            ),
            '3col-25_25_50' => array(
                'columns' => 4,
                'colSpans' => array(1,1,2),
                'name' => trlKwfStatic('3 Columns (25% - 25% - 50%)')
            )
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $ret['row'];

        $columnTypes = $this->_getSetting('columns');
        $type = $row->type;
        if (!$type) {
            //default is first
            $type = array_shift(array_keys($columnTypes));
        }
        $columns = $columnTypes[$type];


        $ret['cssClass'] .= " col{$type}";
        foreach($ret['listItems'] as $key => $value) {
            $ret['listItems'][$key]['class'] .= " span{$columns['colSpans'][$key]}";
        }
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ownWidth = parent::_getChildContentWidth($child);

        $component = $child->parent;
        $columnTypes = $this->_getSetting('columns');
        $columns = $columnTypes[$this->getRow()->type];

        $widthCalc = $columns['colSpans'][$child->id - 1] / $columns['columns'];
        return floor($ownWidth * $widthCalc);
    }
}
