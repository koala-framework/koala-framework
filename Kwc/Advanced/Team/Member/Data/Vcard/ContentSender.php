<?php
class Kwc_Advanced_Team_Member_Data_Vcard_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    public function sendContent($includeMaster)
    {
        $dataRow = (object)$this->getData()->parent->getComponent()->getRow()->toArray();
        if (!isset($dataRow->lastname) || !isset($dataRow->firstname)) {
            throw new Kwf_Exception_NotFound();
        }
        $dataRow = (object)$this->_data->parent->getComponent()->getRow()->toArray();
        $imageData = $this->_data->parent->parent->getChildComponent('-image');
        $this->_outputVcard($dataRow, $imageData);
    }

    /**
     * Set default vCard settings here or in Team_Component
     */
    protected function _getDefaultValues()
    {
        $teamComponent = $this->_data->parent->parent->parent;
        if (Kwc_Abstract::hasSetting($teamComponent->componentClass, 'defaultVcardValues')) {
            $setting = Kwc_Abstract::getSetting($teamComponent->componentClass, 'defaultVcardValues');
        }

        if (isset($setting)) {
            return $setting;
        } else {
            return Kwc_Abstract::getSetting($this->_data->componentClass, 'defaultVcardValues');
        }
    }

    protected function _outputVcard($dataRow, $imageData)
    {
        $content = $this->_getVcardContent($dataRow, $imageData);
        $filename = $this->_getFilename($dataRow);

        if (!$filename) $filename = 'vcard';
        header('Content-Type: text/x-vcard; charset=us-ascii');
        header('Content-Length: '.strlen($content));
        header('Content-Disposition: attachment; filename="'.$filename.'.vcf"');
        echo $content;
    }

    protected function _getFilename($dataRow)
    {
        if ($dataRow && (!empty($dataRow->firstname) || !empty($dataRow->lastname))) {
            $filename = $dataRow->lastname.'_'.$dataRow->firstname;
            $filter = new Kwf_Filter_Ascii();
            return $filter->filter($filename);
        }
        return null;
    }

    /**
     * Gibt vCard Daten zurück. Statisch weil es auch von der Trl_Component
     * aufgerufen wird.
     */
    protected function _getVcardContent($dataRow, $imageData)
    {
        $defaults = $this->_getDefaultValues();

        require_once Kwf_Config::getValue('externLibraryPath.pearContactVcardBuild').'/Contact/Vcard/Build.php';
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
        if (isset($defaults['ADR;WORK']) || !empty($dataRow->street) || !empty($dataRow->city) || !empty($dataRow->zip)) {
            /**
             * muss ein array mit folgenden werten liefern:
             * 0 => ''
             * 1 => ''
             * 2 => street
             * 3 => city
             * 4 => province
             * 5 => zip
             * 6 => country
             */
            $values = array();
            if (!empty($defaults['ADR;WORK'])) {
                $values = explode(';', utf8_decode($defaults['ADR;WORK']));
            }
            for ($i=0; $i<=6; $i++) {
                if (!isset($values[$i])) $values[$i] = '';
            }
            if (!empty($dataRow->street)) $values[2] = utf8_decode($dataRow->street);
            if (!empty($dataRow->city)) $values[3] = utf8_decode($dataRow->city);
            if (!empty($dataRow->zip)) $values[5] = utf8_decode($dataRow->zip);
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

            if ($type[1] == 'JPEG') {
                $vcard->setPhoto(base64_encode($data['contents']));
                $vcard->addParam('TYPE', $type[1]);
                $vcard->addParam('ENCODING', 'BASE64');
            }
        }

        $vcard->setRevision(date('Y-m-d').'T'.date('H:i:s').'Z');

        return $vcard->fetch();
    }
}
