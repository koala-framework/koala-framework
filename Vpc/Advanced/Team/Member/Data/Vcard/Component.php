<?php
class Vpc_Advanced_Team_Member_Data_Vcard_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        $ret['defaultVcardValues'] = array();
        return $ret;
    }

    public function sendContent()
    {
        $dataRow = (object)$this->getData()->parent->getComponent()->getRow()->toArray();
        $imageData = $this->getData()->parent->parent->getChildComponent('-image');
        $defaults = $this->_getDefaultValues();
        self::outputVcard($dataRow, $defaults, $imageData);
    }

    /**
     * Set default vCard settings here or in Team_Component
     */
    private function _getDefaultValues()
    {
        $teamComponent = $this->getData()->parent->parent->parent;
        if (Vpc_Abstract::hasSetting($teamComponent->componentClass, 'defaultVcardValues')) {
            $setting = Vpc_Abstract::getSetting($teamComponent->componentClass, 'defaultVcardValues');
        }

        if (isset($setting)) {
            return $setting;
        } else {
            return $this->_getSetting('defaultVcardValues');
        }
    }

    public static function outputVcard($dataRow, $defaults, $imageData)
    {
        $content = self::getVcardContent($dataRow, $defaults, $imageData);
        $filename = self::getFilename($dataRow);

        if (!$filename) $filename = 'vcard';
        header('Content-Type: text/x-vcard');
        header('Content-Disposition: attachment; filename="'.$filename.'.vcf"');
        echo $content;
    }

    public static function getFilename($dataRow)
    {
        if ($dataRow && (!empty($dataRow->firstname) || !empty($dataRow->lastname))) {
            $filename = $dataRow->lastname.'_'.$dataRow->firstname;
            $filter = new Vps_Filter_Ascii();
            return $filter->filter($filename);
        }
        return null;
    }

    /**
     * Gibt vCard Daten zurück. Statisch weil es auch von der Trl_Component
     * aufgerufen wird.
     */
    public static function getVcardContent($dataRow, $defaults, $imageData)
    {
        $lines = array();
        $lines[] = 'BEGIN:VCARD';
        $lines[] = 'VERSION:2.1';
        if (!empty($dataRow->title) || !empty($dataRow->lastname) || !empty($dataRow->firstname)) {
            // reihenfolge: nachname, vorname, weitere vornamen, titel vor name, titel nach name
            $lines[] = 'N:'.$dataRow->lastname.';'.$dataRow->firstname.';;'.$dataRow->title.';';
        }
        if (!empty($dataRow->lastname) || !empty($dataRow->firstname)) {
            $lines[] = 'FN:'.$dataRow->firstname.' '.$dataRow->lastname;
        }
        if (isset($defaults['ORG'])) {
            $lines[] = 'ORG:'.$defaults['ORG'];
        }
        if (!empty($dataRow->working_position)) {
            $lines[] = 'ROLE:'.$dataRow->working_position;
        }
        if (!empty($dataRow->phone)) {
            $lines[] = 'TEL;PREF;WORK;VOICE:'.$dataRow->phone;
        }
        if (!empty($dataRow->mobile)) {
            $lines[] = 'TEL;WORK;CELL:'.$dataRow->mobile;
        }
        if (isset($defaults['TEL;WORK;FAX'])) {
            $lines[] = 'TEL;WORK;FAX:'.$defaults['TEL;WORK;FAX'];
        }
        if (!empty($dataRow->email)) {
            $lines[] = 'EMAIL;WORK:'.$dataRow->email;
        }
        if (isset($defaults['URL;WORK'])) {
            $lines[] = 'URL;WORK:'.$defaults['URL;WORK'];
        }
        if (isset($defaults['NOTE'])) {
            $lines[] = 'NOTE;ENCODING=QUOTED-PRINTABLE:'.Zend_Mime::encodeQuotedPrintable($defaults['NOTE']);
        }
        if (isset($defaults['ADR;WORK'])) {
            $lines[] = 'ADR;WORK;;ENCODING=QUOTED-PRINTABLE:'.Zend_Mime::encodeQuotedPrintable($defaults['ADR;WORK']);
        }
        
        if ($imageData && $imageData->hasContent()) {
            $data = call_user_func_array(
                array($imageData->componentClass, 'getMediaOutput'),
                array($imageData->componentId, 'default', $imageData->componentClass)
            );
            $type = explode('/', $data['mimeType']);
            $lines[] = 'PHOTO;TYPE='.strtoupper($type[1]).';ENCODING=BASE64:'.base64_encode($data['contents']);
        }

        $lines[] = 'REV:'.date('Y-m-d').'T'.date('H:i:s').'Z';
        $lines[] = 'MAILER:';
        $lines[] = 'END:VCARD';

        return implode("\n", $lines);
    }
}
