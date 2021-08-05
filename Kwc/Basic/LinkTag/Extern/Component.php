<?php
/**
 * @package Kwc
 * @subpackage Basic
 */
class Kwc_Basic_LinkTag_Extern_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = array_merge(parent::getSettings($param), array(
            'dataClass' => 'Kwc_Basic_LinkTag_Extern_Data',
            'ownModel'     => 'Kwc_Basic_LinkTag_Extern_Model',
            'componentName' => trlKwfStatic('Link.Extern'),
            'hasPopup'      => true, //TODO: bezeichnung von diesem setting ist scheiße
            'openType'      => null, //wenn hasPopup auf false
        ));
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);

        $row = $this->_getRow();
        $rel = $this->getData()->rel ? $this->getData()->rel : array();
        if ($row->rel_nofollow) {
            $rel[] = 'nofollow';
        }
        if ($row->rel_noopener) {
            $rel[] = 'noopener';
        }
        if ($row->rel_noreferrer) {
            $rel[] = 'noreferrer';
        }
        $ret['rel'] = implode(' ', array_unique($rel));

        return $ret;
    }

}
