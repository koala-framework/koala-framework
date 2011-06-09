<?php
class Vpc_Basic_Table_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['assetsAdmin']['dep'][] = 'ExtGridCheckboxSelectionModel';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/Table/TableGridPanel.js';
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Basic/Table/TableXlsImportForm.js';

        $ret['componentName'] = trlVps('Table');
        $ret['ownModel'] = 'Vpc_Basic_Table_Model';
        $ret['childModel'] = 'Vpc_Basic_Table_ModelData';

        $ret['maxColumns'] = 26;

        // row styles: the key is put in the proper <tr> tag
        // if no tag is set in the sub-array, td is used
        // simple string example: 'bold' => trlVps('Bold')
        // complex arrayexample: 'headline' => array('name' => trlVps('Headline'), 'tag'  => 'th')
        $ret['rowStyles'] = array(
            'headline' => array(
                'name' => trlVps('Headline'),
                'tag'  => 'th'
            )
        );

        // tableStyles: the key is the table-css-class, the value the name for
        // settings page in backend
        // e.g.: 'green' => trlVps('Green')
        $ret['tableStyles'] = array('green' => trlVps('Green'));
        $ret['cssClass'] = 'webStandard';

        $ret['extConfig'] = 'Vpc_Basic_Table_ExtConfig';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['settingsRow'] = $this->_getRow();
        $ret['columnCount'] = $this->getColumnCount();

        $dataSelect = new Vps_Model_Select();
        $dataSelect->whereEquals('visible', 1);
        $dataSelect->order('pos', 'ASC');
        $ret['dataRows'] = $this->_getRow()->getChildRows('tableData', $dataSelect);

        $ret['rowStyles'] = $this->_getSetting('rowStyles');
        return $ret;
    }

    public function getColumnCount($isAdmin = false)
    {
        if ($isAdmin) {
            $dataSelect = new Vps_Model_Select();
            $dataSelect->whereEquals('visible', 1);
            $rows = $this->_getRow()->getChildRows('tableData', $dataSelect);
        } else {
            $rows = $this->_getRow()->getChildRows('tableData');
        }
        $ret = 0;
        foreach ($rows as $row) {
            for ($i=1; $i<=$this->_getSetting('maxColumns'); $i++) {
                if (!empty($row->{'column'.$i}) && $i > $ret) $ret = $i;
            }
        }
        return $ret;
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getCacheMeta();
        $ret[] = new Vps_Component_Cache_Meta_Static_ChildModel();
        return $ret;
    }
}
