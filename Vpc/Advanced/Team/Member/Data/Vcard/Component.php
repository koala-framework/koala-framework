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
        header('Content-Type: text/x-vcard; charset=us-ascii');
        header('Content-Length: '.strlen($content));
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
        $vcard = new Contact_Vcard_Build('2.1');

        $vcard->setName(utf8_decode($dataRow->lastname), utf8_decode($dataRow->firstname), '',
            utf8_decode($dataRow->title), '');
        $vcard->addParam('CHARSET', 'ISO-8859-1');

        $vcard->setFormattedName(utf8_decode($dataRow->firstname).' '.utf8_decode($dataRow->lastname));
        $vcard->addParam('CHARSET', 'ISO-8859-1');

        if (isset($defaults['ORG'])) {
            $vcard->addOrganization(utf8_decode($defaults['ORG']));
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        if (!empty($dataRow->working_position)) {
            $vcard->setRole(utf8_decode($dataRow->working_position));
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        if (!empty($dataRow->phone)) {
            $vcard->addTelephone(utf8_decode($dataRow->phone));
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('TYPE', 'PREF');
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        if (!empty($dataRow->mobile)) {
            $vcard->addTelephone(utf8_decode($dataRow->mobile), 'mobile');
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        $fax = null;
        if (!empty($dataRow->fax)) {
            $fax = $dataRow->fax;
        } else if (isset($defaults['TEL;WORK;FAX'])) {
            $fax = $defaults['TEL;WORK;FAX'];
        }
        if ($fax) {
            $vcard->addTelephone(utf8_decode($fax), 'fax');
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        if (!empty($dataRow->email)) {
            $vcard->addEmail(utf8_decode($dataRow->email));
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        if (isset($defaults['URL;WORK'])) {
            $vcard->setURL(utf8_decode($defaults['URL;WORK']));
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        if (isset($defaults['NOTE'])) {
            $vcard->setNote(utf8_decode($defaults['NOTE']));
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }
        if (isset($defaults['ADR;WORK'])) {
            $values = explode(';', utf8_decode($defaults['ADR;WORK']));
            for ($i=0; $i<=6; $i++) {
                if (!isset($values[$i])) $values[$i] = '';
            }
            $vcard->addAddress($values[0], $values[1], $values[2], $values[3], $values[4], $values[5], $values[6]);
            $vcard->addParam('TYPE', 'WORK');
            $vcard->addParam('CHARSET', 'ISO-8859-1');
        }

        if ($imageData && $imageData->hasContent()) {
            $data = call_user_func_array(
                array($imageData->componentClass, 'getMediaOutput'),
                array($imageData->componentId, 'default', $imageData->componentClass)
            );
            $type = explode('/', $data['mimeType']);
            $type[1] = strtoupper($type[1]);
            if ($type[1] == 'PJPEG') $type[1] = 'JPEG';

            $vcard->setPhoto(base64_encode($data['contents']));
            $vcard->addParam('TYPE', $type[1]);
            $vcard->addParam('ENCODING', 'BASE64');
        }

        $vcard->setRevision(date('Y-m-d').'T'.date('H:i:s').'Z');

        return $vcard->fetch();
    }
}
