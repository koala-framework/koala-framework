<?php
class Kwc_Basic_Table_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['assetsAdmin']['dep'][] = 'ExtGridCheckboxSelectionModel';
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Basic/Table/TableGridPanel.js';

        $ret['componentName'] = trlKwfStatic('Table');
        $ret['componentIcon'] = 'table';
        $ret['componentCategory'] = 'content';
        $ret['componentPriority'] = 35;
        $ret['ownModel'] = 'Kwc_Basic_Table_Model';
        $ret['childModel'] = 'Kwc_Basic_Table_ModelData';

        $ret['maxColumns'] = 26;

        // row styles: the key is put in the proper <tr> tag
        // if no tag is set in the sub-array, td is used
        // simple string example: 'bold' => trlKwf('Bold')
        // complex arrayexample: 'headline' => array('name' => trlKwf('Headline'), 'tag'  => 'th')
        $ret['rowStyles'] = array(
            'headline' => array(
                'name' => trlKwfStatic('Headline'),
                'tag'  => 'th'
            )
        );

        // tableStyles: the key is the table-css-class, the value the name for
        // settings page in backend
        // e.g.: 'green' => trlKwf('Green')
        $ret['tableStyles'] = array('standard' => trlKwfStatic('Standard'));
        $ret['rootElementClass'] = 'kwfUp-webStandard';

        $ret['extConfig'] = 'Kwc_Basic_Table_ExtConfig';
        $ret['apiContent'] = 'Kwc_Basic_Table_ApiContent';
        $ret['apiContentType'] = 'table';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['settingsRow'] = $this->_getRow();
        $ret['tableStyle'] = $this->_getRow()->table_style;
        $ret['columnCount'] = $this->getColumnCount();
        if (Kwf_Config::getValue('kwc.responsive')) {
            $ret['rootElementClass'] .= ' responsive'.ucfirst($this->_getRow()->responsive_style);
        }

        $dataSelect = new Kwf_Model_Select();
        $dataSelect->whereEquals('visible', 1);
        $dataSelect->order('pos', 'ASC');
        $ret['dataRows'] = array();
        $rows = $this->_getRow()->getChildRows('tableData', $dataSelect);
        foreach ($rows as $row) {
            $rowData = array();
            $rowData['cssStyle'] = $row->css_style;
            $rowData['data'] = array();
            for ($i = 1; $i <= $ret['columnCount']; $i++) {
                $rowData['data']['column'.$i] = array('value'=>$row->{'column'.$i}, 'cssClass'=>'');
            }
            $ret['dataRows'][] = $rowData;
        }
        $ret['dataRows'] = Kwc_Basic_Table_Component::addDefaultCssClasses($ret['dataRows'], $this->_getSetting('rowStyles'));
        $ret['headerRows'] = array();
        if (isset($ret['dataRows'][0]['htmlTag']) && $ret['dataRows'][0]['htmlTag'] == 'th') {
            $ret['headerRows'] = array(array_shift($ret['dataRows']));
        }
        return $ret;
    }

    /**
     * used from Kwc_Basic_Table_Trl_Component
     */
    public static function addDefaultCssClasses($dataArray, $rowStyles)
    {
        $count = 0;
        $ret = array();
        foreach ($dataArray as $dataItem) {
            if (!isset($dataItem['cssStyle'])) {
                $dataItem['cssClass'] = $count%2 == 0 ? 'odd' : 'even';
                $dataItem['htmlTag'] = 'td';
            } else {
                $dataItem['cssClass'] = $dataItem['cssStyle']. ' ' . ($count%2 == 0 ? 'odd' : 'even');
                $dataItem['htmlTag'] = $rowStyles[$dataItem['cssStyle']]['tag'];
            }
            for ($i = 1; $i < count($dataItem['data'])+1; $i++) {
                if (empty($dataItem['data']['column'.$i]['cssClass'])) {
                    $dataItem['data']['column'.$i]['cssClass'] .= 'col'.$i;
                } else {
                    $dataItem['data']['column'.$i]['cssClass'] .= ' col'.$i;
                }

                if ($i == 1) {
                    $dataItem['data']['column'.$i]['cssClass'] .= ' first';
                } else if ($i == count($dataItem['data'])) {
                    $dataItem['data']['column'.$i]['cssClass'] .= ' last';
                }
            }
            $ret[] = $dataItem;
            $count++;
        }
        return $ret;
    }

    public function getColumnCount($isAdmin = false)
    {
        if ($isAdmin) {
            $dataSelect = new Kwf_Model_Select();
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

    public function hasContent()
    {
        $dataSelect = new Kwf_Model_Select();
        $dataSelect->whereEquals('visible', 1);
        $dataSelect->order('pos', 'ASC');
        $rows = $this->_getRow()->getChildRows('tableData', $dataSelect);
        if (count($rows)) {
            return true;
        } else {
            return false;
        }
    }
}
