<?php
abstract class Kwc_Newsletter_Detail_Mail_Paragraphs_LinkTag_Abstract_Component extends Kwc_Basic_LinkTag_Abstract_Component
{
    protected function _getNewsletterComponent()
    {
        $ret = $this->getData()->getParentByClass('Kwc_Newsletter_Component');
        if (!$ret) {
            $ret = Kwf_Component_Data_Root::getInstance()->getComponentByClass(
                'Kwc_Newsletter_Component', array('subroot' => $this->getData()->getSubroot())
            );
        }

        if (!$ret) {
            throw new Kwf_Exception("Newsletter component can not be found");
        }
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['target'] = $this->_getTargetComponent();
        return $ret;
    }

    abstract protected function _getTargetComponent();
}
