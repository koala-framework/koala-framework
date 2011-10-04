<?php
class Vpc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Unsubscribe_Component
    extends Vpc_Basic_LinkTag_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Unsubscribe Newsletter')
        ));
        return $ret;
    }

    protected function _getNewsletterComponent()
    {
        $nlData = null;
        $d = $this->getData()->parent;
        while ($d) {
            if (is_instance_of($d->componentClass, 'Vpc_Newsletter_Component')) {
                $nlData = $d;
                break;
            }
            if (!$d->parent) break;
            $d = $d->parent;
        }

        if (!$nlData) {
            throw new Vps_Exception("Newsletter component can not be found");
        }
        return $nlData;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $nlData = $this->_getNewsletterComponent();
        $ret['unsubscribe'] = $nlData->getChildComponent('-unsubscribe');
        return $ret;
    }
}
