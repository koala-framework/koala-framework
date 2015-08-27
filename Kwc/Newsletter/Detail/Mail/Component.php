<?php
class Kwc_Newsletter_Detail_Mail_Component extends Kwc_Mail_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['content'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Newsletter_Detail_Mail_Paragraphs_Component'
        );
        $select = new Kwf_Model_Select();
        $select->whereEquals('unsubscribed', false);
        $select->whereEquals('activated', true);
        $select->order('id', 'ASC');
        $ret['recipientSources'] = array(
            'n' => array(
                'model' => 'Kwc_Newsletter_Subscribe_Model',
                'select' => $select
            )
        );
        $ret['trackViews'] = true;
        return $ret;
    }

    public static function validateSettings($settings, $componentClass)
    {
        $rs = $settings['recipientSources'];
        foreach(array_keys($rs) as $key) {
            if (!is_array($rs[$key])) {
                throw new Kwf_Exception('recipientSource has to be an array');
            }
            if (!isset($rs[$key]['model'])) {
                throw new Kwf_Exception('recipientSource model setting is not defined');
            }
            if (!is_string($rs[$key]['model'])) {
                throw new Kwf_Exception('recipientSource model setting has to be string');
            }
            if ((count($rs) > 1) && !isset($rs[$key]['title'])) {
                throw new Kwf_Exception('when more than one recipientSource is set you have to define a title for every one');
            }
            if (isset($rs[$key]['select']) && !$rs[$key]['select'] instanceof Kwf_Model_Select) {
                throw new Kwf_Exception('recipientSource select setting has to be correct instanceof Kwf_Model_Select');
            }
        }
    }
}
