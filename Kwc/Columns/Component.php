<?php
class Kwc_Columns_Component extends Kwc_Abstract_List_Component
{
    public static $needsParentComponentClass = true;
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Columns');
        $ret['componentIcon'] = new Kwf_Asset('application_tile_horizontal');
        $ret['childModel'] = 'Kwc_Columns_Model';

        $ret['generators']['child'] = array(
            'class' => 'Kwc_Columns_Generator',
            'component' => $parentComponentClass
        );
        $ret['extConfig'] = 'Kwc_Columns_ExtConfig';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Columns/List.js';

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
            ),
            '4col-25_25_25_25' => array(
                'columns' => 4,
                'colSpans' => array(1,1,1,1),
                'name' => trlKwfStatic('4 Columns (25% - 25% - 25% - 25%)')
            )
        );
        return $ret;
    }

    public function getChildModel()
    {
        return self::getColumnsModel($this->getData()->componentClass);
    }

    public static function getColumnsModel($componentClass)
    {
        static $models = array();
        if (!isset($models[$componentClass])) {
            $m = Kwc_Abstract::getSetting($componentClass, 'childModel');
            $models[$componentClass] = new $m(array('componentClass' => $componentClass));
        }
        return $models[$componentClass];
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

        $i = 1;
        $ret['cssClass'] .= " col{$type}";
        foreach($ret['listItems'] as $key => $value) {
            $cls = " span{$columns['colSpans'][$i-1]}";
            if ($i == 1) $cls .= " lineFirst";
            if ($i == count($columns['colSpans'])) $cls .= " lineLast";
            $ret['listItems'][$key]['class'] .= $cls;
            ($i == count($columns['colSpans'])) ? $i = 1 : $i++;
        }
        return $ret;
    }

    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ownWidth = parent::_getChildContentWidth($child);
        $widthCalc = $child->row->col_span / $child->row->columns;
        $ret = floor($ownWidth * $widthCalc);
        if ($ret < 480) {
            $ret = min($ownWidth, 480);
        }
        return $ret;
    }
}
