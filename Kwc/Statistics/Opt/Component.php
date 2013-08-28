<?php
/**
 * Provides Form to let user choose Opt-In or Opt-Out
 *
 * @see Kwc_Statistics_OptBox_Component
 */
class Kwc_Statistics_Opt_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Cookie Opt In / Opt Out');
        $ret['assets']['files'][] = 'kwf/Kwc/Statistics/Opt/Component.js';
        $ret['viewCache'] = false;
        $ret['plugins'] = array();
        return $ret;
    }

    protected function _initForm()
    {
        $this->_form = new Kwc_Statistics_Opt_FrontendForm(
            'form', $this->getData()->componentClass, Kwf_Statistics::isUserOptIn($this->getData())
        );
    }

    protected function _processInput($postData)
    {
        if (isset($postData['optValue'])) {
            $value = $postData['optValue'] && $postData['optValue'] == 'in' ?
                Kwf_Statistics::OPT_IN : Kwf_Statistics::OPT_OUT;
            Kwf_Statistics::setUserOptValue($value);
            if (isset($postData['redirect'])) {
                header('Location: ' . $postData['redirect']);
                exit;
            }
        }
        parent::_processInput($postData);
    }

    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $value = $row->opt ? Kwf_Statistics::OPT_IN : Kwf_Statistics::OPT_OUT;
        Kwf_Statistics::setUserOptValue($value);
    }
}
