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
        $filename = 'vcard';
        $dataRow = $this->_getDataRow();
        if ($dataRow && (!empty($dataRow->firstname) || !empty($dataRow->lastname))) {
            $filename = $dataRow->lastname.'_'.$dataRow->firstname;
            $filter = new Vps_Filter_Ascii();
            $filename = $filter->filter($filename);
        }
        header('Content-Type: text/x-vcard');
        header('Content-Disposition: attachment; filename="'.$filename.'.vcf"');
        echo $this->_getVcardContent();
    }

    protected function _getVcardImageData()
    {
        return $this->getData()->parent->parent->getChildComponent('-image');
    }

    private function _getDataRow()
    {
        return $this->getData()->parent->getComponent()->getRow();
    }

    private function _getVcardContent()
    {
        $dataRow = $this->_getDataRow();
        $defaults = $this->_getSetting('defaultVcardValues');

        $lines = array();
        $lines[] = 'BEGIN:VCARD';
        $lines[] = 'VERSION:2.1';
        if (!empty($dataRow->lastname) || !empty($dataRow->firstname)) {
            $lines[] = 'N:'.$dataRow->lastname.';'.$dataRow->firstname.';;;';
        }
        if (!empty($dataRow->lastname) || !empty($dataRow->firstname)) {
            $lines[] = 'FN:'.$dataRow->firstname.' '.$dataRow->lastname;
        }
        if (isset($defaults['ORG'])) {
            $lines[] = 'ORG:'.$defaults['ORG'];
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
        $imageData = $this->_getVcardImageData();
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
