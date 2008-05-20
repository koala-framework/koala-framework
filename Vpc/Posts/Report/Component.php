<?php
class Vpc_Posts_Report_Component extends Vpc_Formular_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childComponentClasses']['success'] = 'Vpc_Posts_Report_Success_Component';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        return $ret;
    }

    protected function _init()
    {
        parent::_init();
        $c = $this->_createFieldComponent('Textarea',
            array('name'=>'content', 'width'=>470, 'height'=>150, 'value' => '')
        );
        $c->store('name', 'content');
        $c->store('fieldLabel', 'Bitte geben Sie einen Grund an, weshalb Sie diesen Beitrag melden wollen:');
        $c->store('isMandatory', false);

        $c = $this->_createFieldComponent('Submit', array('name'=>'sbmt', 'width'=>150, 'text' => 'Beitrag melden'));
        $c->store('name', 'sbmt');
        $c->store('fieldLabel', '&nbsp;');
    }

    protected function _getValues()
    {
        //TODO, ist a schas hier, überschreibt _getValues von überklasse
        //die a bissi anders arbeiteit (whyever)
        $ret = array();
        foreach ($this->getChildComponents() as $c) {
            if ($c instanceof Vpc_Formular_Field_Interface) {
                $name = $c->getStore('name');
                $ret[$name] = $c->getValue();
            }
        }
        return $ret;
    }

    protected function _processForm()
    {
        if (isset($_POST['sbmt'])) {
            $postsComponent = $this->getParentComponent();

            $values = $this->_getValues();
            $postsModel = $postsComponent->getTable();

            $reportMail = $this->getSetting(get_class($postsComponent), 'reportMail');
            $reportMailName = $this->getSetting(get_class($postsComponent), 'reportMailName');

            $postRow = $postsModel->fetchRow(array(
                'id = ?' => $this->_getParam('reportPost')
            ));

            if ($reportMail && $postRow) {
                // report email senden
                $mail = new Vps_Mail('Posts/ReportPost');
                $mail->subject = 'Ein Beitrag wurde gemeldet!';
                $mail->addTo($reportMail, $reportMailName);

                $mail->postUrl = $this->getParentComponent()->getUrl();
                $mail->content = $values['content'];
                $mail->postContent = htmlspecialchars($postRow->content);

                $mail->applicationName = Zend_Registry::get('config')->application->name;
                $mail->send();
            }
        } else {
            // wird in parentComponent in getTemplateVars gefangen.
            // ist leer damit kein fehler ausgegeben wird
            throw new Vps_ClientException();
        }
    }
}
