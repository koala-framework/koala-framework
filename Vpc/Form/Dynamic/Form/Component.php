<?php
class Vpc_Form_Dynamic_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['paragraphs'] = $this->getData()->parent->getChildComponent('-paragraphs');
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Vps_Form('form');
        $referenceMap= array();
        foreach ($this->getData()->parent->getChildComponent('-paragraphs')->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $f = $c->getComponent()->getFormField();
            $this->_form->fields->add($f);
            if ($f instanceof Vps_Form_Field_File) {
                $referenceMap[$f->getName()] = array(
                    'refModelClass' => 'Vps_Uploads_Model',
                    'column' => $f->getName()
                );
            }
        }
        $this->_form->setModel($this->_createModel($referenceMap));
    }

    protected function _createModel($referenceMap)
    {
        return new Vps_Model_Mail(array(
            'componentClass' => get_class($this),
            'referenceMap' => $referenceMap,
            'mailerClass' => 'Vps_Mail'
        ));
    }

    protected function _beforeSave(Vps_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Registry::get('config')->server->domain;
        }
        $row->setFrom("noreply@$host");
        $settings = $this->getData()->parent->getComponent()->getRow(); //TODO interface dafür machen, nicht auf row direkt zugreifen
        $row->addTo($settings->recipient);
        $row->setSubject($settings->subject);

        $msg = '';
        foreach ($this->getData()->parent->getChildComponent('-paragraphs')->getRecursiveChildComponents(array('flags'=>array('formField'=>true))) as $c) {
            $f = $c->getComponent()->getFormField();
            if ($f->getName() && $f->getFieldLabel()) {
                if ($f instanceof Vps_Form_Field_File) {
                    $uploadRow = $row->getParentRow($f->getName());
                    if ($uploadRow) {
                        $row->addAttachment($uploadRow);
                        $msg .= $f->getFieldLabel().": {$uploadRow->filename}.{$uploadRow->extension} ".trlVps('attached')."\n";
                    }
                } else if ($f instanceof Vps_Form_Field_Checkbox) {
                    if ($row->{$f->getName()}) {
                        $msg .= $f->getFieldLabel().': '.$this->getData()->trlVps('on')."\n";
                    } else {
                        $msg .= $f->getFieldLabel().': '.$this->getData()->trlVps('off')."\n";
                    }
                } else {
                    $msg .= $f->getFieldLabel().': '.$row->{$f->getName()}."\n";
                }
            }
        }
        $row->setMailContentManual(true);
        $row->setBodyText($msg);
    }
}
