<?php
class Kwc_Abstract_Pdf_ContentSender extends Kwf_Component_Abstract_ContentSender_Download
{
    protected function _getPdfComponent()
    {
        return $this->_data->parent;
    }

    public function outputPdf($name = '', $dest = 'I')
    {
        return $this->sendDownload($name, $dest);
    }
    
    public function sendDownload($name = '', $dest = 'I')
    {
        $masterClass = $this->_getMasterClass($this->_data);
        $pdfComponent = $this->_getPdfComponent();
        if ($pdfComponent instanceof Kwf_Component_Data) {
            $pdfComponent = $pdfComponent->getComponent();
        }
        $pdf = new $masterClass($pdfComponent);
        $pdfComponent->getPdfWriter($pdf)->writeContent();
        return $pdf->output($name, $dest);
    }

    protected function _getMasterClass($component)
    {
        $masterClass = Kwc_Admin::getComponentFile($component->componentClass, 'PdfMaster', 'php', true);
        if (!$masterClass) { $masterClass = 'Kwf_Pdf_TcPdf'; }
        return $masterClass;
    }
}
