<?php
abstract class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Abstract_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
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
        $ret['target'] = $this->_getTargetComponent();
        return $ret;
    }

    abstract protected function _getTargetComponent();
}
