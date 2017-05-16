<?php
class Kwc_Columns_Abstract_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Columns');
        $ret['componentIcon'] = 'application_tile_horizontal';
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 100;
        $ret['childModel'] = 'Kwc_Columns_Abstract_Model';
        $ret['ownModel'] = 'Kwc_Columns_Abstract_OwnModel';

        $ret['generators']['child'] = array(
            'class' => 'Kwc_Columns_Abstract_Generator',
            'component' => 'Kwc_Paragraphs_Component'
        );
        $ret['extConfig'] = 'Kwc_Columns_Abstract_ExtConfig';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Columns/Abstract/List.js';
        $ret['layoutClass'] = 'Kwc_Columns_Abstract_Layout';

        $columnsTrl = trlKwfStatic('Columns');
        $ret['columns'] = array(
            '2col-50_50' => array(
                'colSpans' => array(1,1),
                'name' => "2 $columnsTrl (50% - 50%)"
            ),
            '2col-33_66' => array(
                'colSpans' => array(1,2),
                'name' => "2 $columnsTrl (33% - 66%)"
            ),
            '2col-66_33' => array(
                'colSpans' => array(2,1),
                'name' => "2 $columnsTrl (66% - 33%)"
            ),
            '2col-25_75' => array(
                'colSpans' => array(1,3),
                'name' => "2 $columnsTrl (25% - 75%)"
            ),
            '2col-75_25' => array(
                'colSpans' => array(3,1),
                'name' => "2 $columnsTrl (75% - 25%)"
            ),
            '3col-33_33_33' => array(
                'colSpans' => array(1,1,1),
                'name' => "3 $columnsTrl (33% - 33% - 33%)"
            ),
            '3col-50_25_25' => array(
                'colSpans' => array(2,1,1),
                'name' => "3 $columnsTrl (50% - 25% - 25%)"
            ),
            '3col-25_50_25' => array(
                'colSpans' => array(1,2,1),
                'name' => "3 $columnsTrl (25% - 50% - 25%)"
            ),
            '3col-25_25_50' => array(
                'colSpans' => array(1,1,2),
                'name' => "3 $columnsTrl (25% - 25% - 50%)"
            ),
            '4col-25_25_25_25' => array(
                'colSpans' => array(1,1,1,1),
                'name' => "4 $columnsTrl (25% - 25% - 25% - 25%)"
            ),
            '5col-20_20_20_20_20' => array(
                'colSpans' => array(1,1,1,1,1),
                'name' => "5 $columnsTrl (20% - 20% - 20% - 20% - 20%)"
            )
        );
        return $ret;
    }

    public function getChildModel()
    {
        return self::createChildModel($this->getData()->componentClass);
    }

    public static function createChildModel($componentClass)
    {
        return Kwc_Columns_Abstract_ModelFactory::getModelInstance(array(
            'componentClass' => $componentClass
        ));
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $row = $ret['row'];

        $columnTypes = $this->_getSetting('columns');
        $type = $row->type;
        if (!$type) {
            //default is first
            $type = array_keys($columnTypes);
            $type = array_shift($type);
        }
        $columns = $columnTypes[$type];

        $i = 1;
        $ret['rootElementClass'] .= " col{$type}";
        foreach ($ret['listItems'] as $key => $value) {
            $cls = " span{$columns['colSpans'][$i-1]}";
            if ($i == 1) $cls .= " ".$this->_getBemClass("listItem--lineFirst", "lineFirst");
            if ($i == count($columns['colSpans'])) $cls .= " ".$this->_getBemClass("listItem--lineLast", "lineLast");
            $ret['listItems'][$key]['class'] .= $cls;
            ($i == count($columns['colSpans'])) ? $i = 1 : $i++;
            if (!$ret['listItems'][$key]['data']->hasContent()) {
                $ret['listItems'][$key]['class'] .= " ".$this->_getBemClass("listItem--emptyContent", "emptyContent");
            }
        }
        return $ret;
    }
}
