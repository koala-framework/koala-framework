<?php
class Vpc_Formular_Multicheckbox_Index extends Vpc_Formular_Field_Abstract
{
    protected $_defaultSettings = array('multicheckbox' => array(), 'name' => '', 'horizontal' => 0);

    public function getTemplateVars($mode)
    {
        $this->start();

        $return['horizontal'] = $this->getSetting('horizontal');
        $return['multicheckbox'] = $this->getSetting('multicheckbox');
        $return['id'] = $this->getComponentId();

        $return['template'] = 'Formular/Multicheckbox.html';
        return $return;
    }

    private function start()
    {
        $name = $this->getSetting('name');

        // Variante 1  -> Array selber mitgehen
        $values = array(array('value' => 'value1', 'text' => 'Box1', 'checked' => '1'),
        array('value' => 'value2', 'text' => 'Box2', 'checked' => '0'),
        array('value' => 'value3', 'text' => 'Box3', 'checked' => '0'),
        array('value' => 'value4', 'text' => 'Box4', 'checked' => '1'));

        //$this->setSetting('multicheckbox', $this->_setMulitcheckboxNew($name, $values));

        //Variaten 2 -> Array wird mitels der ID geholt
        if ($this->getSetting('multicheckbox') == array()) {
            $this->setSetting('multicheckbox', $this->_setMulitcheckboxbyIds());
        }
    }


    //erstellt Checkboxen mittels der Component Id's
    private function _setMulitcheckboxbyIds()
    {
        //ComponentId, PageKey und ComponentKey werden aus der Datenbank geholt
        $table = $this->_getTable('Vpc_Formular_Multicheckbox_CheckboxesModel');
        $checkboxes = $table->fetchAll(array('component_id = ?'  => $this->getComponentId(),
        'page_key = ?'      => $this->getPageKey(),
        'component_key = ?' => $this->getComponentKey()));
        //ids werden rausgeschrieben
        $ids = array();
        foreach($checkboxes as $checkbox) {
            $ids[] = $checkbox->id;
        }

		$multicheckbox = array();
        //instanzen der Komponentenwerden erzeugt
        foreach($ids as $id){
            $temp = $this->createComponent('Vpc_Formular_Checkbox_Index', null, $id);
            $multicheckbox[] = $temp->getTemplateVars('');
        }
        return $multicheckbox;
    }

    //erstellt Checkboxen mittels der übergebenen Werte
    private function _setMulitcheckboxNew($name, $values)
    {
        $cnt = 0;
        foreach($values as $value){
            $temp = $this->createComponent('Vpc_Formular_Checkbox_Index', null, $cnt);
            $temp->setSetting('name', $name);
            $temp->setSetting('value', $value['value']);
            $temp->setSetting('text', $value['text']);
            $temp->setSetting('checked', $value['checked']);
            $multicheckbox[] = $temp->getTemplateVars('');
            $cnt++;
        }
        return $multicheckbox;
    }


    public function processInput()
    {
        if ($_POST != array()) {

            $multicheckbox = $this->getSetting('multicheckbox');
            foreach($multicheckbox as $key => $checkbox) {
                $checkbox['checked'] = 0;
                $multicheckbox[$key] = $checkbox;
            }
            foreach($multicheckbox as $key => $checkbox) {
                if (array_key_exists($checkbox['name'], $_POST)) {
                    $checkbox['checked'] = 1;
                }
                $multicheckbox[$key] = $checkbox;
            }
            $this->setSetting('multicheckbox', $multicheckbox);
        }
    }

    public function validateField($mandatory)
    {

        $multicheckbox = $this->getSetting('multicheckbox');
        foreach($multicheckbox as $key => $checkbox) {
            if (array_key_exists($checkbox['name'], $_POST)) {
                return true;
            }
        }
        if ($mandatory) {
            return 'Feld '.$this->_errorField.' ist ein Pflichtfeld, bitte ausfüllen';
        }
        return true;
    }


    public function setName($name)
    {
        $this->start();
        //hier muss als Spezialfall die Startmethode aufgerufen werden
        $names = array();
        $this->setSetting('name', $name);

        $multicheckbox = $this->getSetting('multicheckbox');
        $cnt = 0;
        $filter = new Zend_Filter_Alpha();
        foreach($multicheckbox as $key => $checkbox) {
            $part1 = $filter->filter($this->getName('name'));
            $part2 = $filter->filter($checkbox['value']);

            //überprüfung ob der Feldname schon einmal existiert
            $newName = $part1.'_'.$part2;
            $tempName = $newName;
            $cnt2 = 1;
            while (in_array($newName, $names)) {
                $newName = $tempName.$cnt2;
                $cnt2++;
            }
            $names[] = $newName;

            $checkbox['name'] =  $newName;
            $cnt++;
            $multicheckbox[$key] = $checkbox;
        }

        $this->setSetting('multicheckbox', $multicheckbox);
    }



}