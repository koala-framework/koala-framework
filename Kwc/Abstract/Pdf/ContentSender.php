<?php
class Kwc_Abstract_Pdf_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    protected function _getPdfComponent()
    {
        return $this->_data->parent;
    }

    public function sendContent($name = '', $dest = 'I')
    {
        $plugins = $this->_data->getPlugins('Kwf_Component_Plugin_Interface_View');
        if ($plugins) {
            if (count($plugins) > 1 || !is_instance_of($plugins[0], 'Kwf_Component_Plugin_Password_Component')) {
                throw new Kwf_Exception("For pdf only one plugin of type 'Kwf_Component_Plugin_Password_Component' is allowed.");
            }
            $p = new $plugins[0]($this->_data->componentId);
            if ($p->processOutput('')) {
                parent::sendContent($includeMaster);
                return false;
            }
        }

        $masterClass = Kwc_Admin::getComponentFile($this->_data->componentClass, 'PdfMaster', 'php', true);
        if (!$masterClass) { $masterClass = 'Kwf_Pdf_TcPdf'; }
        $pdfComponent = $this->_getPdfComponent();
        if ($pdfComponent instanceof Kwf_Component_Data) {
            $pdfComponent = $pdfComponent->getComponent();
        }
        $pdf = new $masterClass($pdfComponent);
        $pdfComponent->getPdfWriter($pdf)->writeContent();
        return $pdf->output($name, $dest);
    }
}
