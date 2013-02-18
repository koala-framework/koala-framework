<?php
class Kwc_Abstract_Pdf_ContentSender extends Kwf_Component_Abstract_ContentSender_Abstract
{
    protected function _getPdfComponent()
    {
        return $this->_data->parent;
    }

    public function sendContent($includeMaster)
    {
        if ($this->checkAllowed()) {
            $this->outputPdf();
        }
    }

    public function outputPdf($name = '', $dest = 'I')
    {
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

    protected function checkAllowed()
    {
        $valid = Kwf_Media_Output_Component::isValid($this->_data->componentId);
        if ($valid == Kwf_Media_Output_IsValidInterface::ACCESS_DENIED) { // send non pdf content to show login-plugin
            $contentSender = new Kwf_Component_Abstract_ContentSender_Default($this->_data);
            $contentSender->sendContent(true);
            return false;
        } else if ($valid == Kwf_Media_Output_IsValidInterface::INVALID) {
            throw new Kwf_Exception_NotFound();
        }
        return $valid;
    }
}
