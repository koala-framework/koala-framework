<?php
class Vpc_Basic_Table_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Table');
        $ret['ownModel'] = 'Vpc_Basic_Table_Model';
        $ret['childModel'] = 'Vpc_Basic_Table_ModelData';

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
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['settingsRow'] = $this->_getRow();

        $dataSelect = new Vps_Model_Select();
        $dataSelect->order('pos', 'ASC');
        $ret['dataRows'] = $this->_getRow()->getChildRows('tableData', $dataSelect);

        $ret['rowStyles'] = $this->_getSetting('rowStyles');
        return $ret;
    }

    public function getColumnCount()
    {
        if (!$this->getRow() || !$this->getRow()->columns) {
            throw new Vps_ClientException("Please set first the amount of columns in the settings section.");
        }
        return $this->getRow()->columns;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        $ret['tableData'] = array(
            'model' => $this->getChildModel(),
            'componentId' => $this->getData()->componentId
        );
        return $ret;
    }
}
