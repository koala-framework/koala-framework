<?php
class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component
    extends Kwc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret = array_merge(parent::getSettings($param), array(
            'componentName' => trlKwfStatic('Unsubscribe Newsletter')
        ));
        return $ret;
    }

    protected function _getNewsletterComponent()
    {
        $nlData = null;
        $d = $this->getData()->parent;
        while ($d) {
            if (is_instance_of($d->componentClass, 'Kwc_Newsletter_Component')) {
                $nlData = $d;
                break;
            }
            if (!$d->parent) break;
            $d = $d->parent;
        }

        if (!$nlData) {
            throw new Kwf_Exception("Newsletter component can not be found");
        }
        return $nlData;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $nlData = $this->_getNewsletterComponent();
        $ret['unsubscribe'] = $nlData->getChildComponent('_unsubscribe');
        return $ret;
    }
}
