<?php
class Kwc_Newsletter_EditSubscriber_Component extends Kwc_Form_Component
{
    protected $_recipient;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Save');
        $ret['generators']['child']['component']['success'] =
            'Kwc_Newsletter_EditSubscriber_Success_Component';
        $ret['viewCache'] = false;
        $ret['flags']['skipFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        $ret['flags']['processInput'] = true;
        $ret['flags']['passMailRecipient'] = true;
        unset($ret['plugins']['useViewCache']);
        return $ret;
    }

    public function processInput(array $postData)
    {
        if (!isset($postData['recipient'])) {
            throw new Kwf_Exception_NotFound();
        }
        $this->_recipient = Kwc_Mail_Redirect_Component::parseRecipientParam($postData['recipient']);
    }

    protected function _initForm()
    {
        $formClass = Kwc_Admin::getComponentClass($this, 'FrontendForm');
        $this->_form = new $formClass('form', $this->getData()->componentClass, null);
        if ($this->_recipient) {
            $this->_form->setId($this->_recipient->id);
        }
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        parent::_beforeSave($row);

        $logMessages = array(
            $this->getData()->trlKwf('Changed data:')
        );
        foreach ($row->getDirtyColumns() as $column) {
            $columnName = $column;

            switch ($column) {
                case 'gender':
                    $columnName = $this->getData()->trlKwf('Gender');
                    break;
                case 'title':
                    $columnName = $this->getData()->trlKwf('Title');
                    break;
                case 'firstname':
                    $columnName = $this->getData()->trlKwf('Firstname');
                    break;
                case 'lastname':
                    $columnName = $this->getData()->trlKwf('Lastname');
                    break;
                case 'email':
                    $columnName = $this->getData()->trlKwf('Email');
                    break;
            }

            $logMessages[] = $this->getData()->trlKwf('{0}: "{1}" to "{2}"', array($columnName, $row->getCleanValue($column), $row->{$column}));
        }

        if (count($logMessages) > 1) {
            $row->setLogSource($this->getData()->getAbsoluteUrl());
            $row->writeLog(implode("\n", $logMessages));
        }
    }


}
