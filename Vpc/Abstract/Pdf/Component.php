<?php
abstract class Vpc_Abstract_Pdf_Component extends Vpc_Abstract
{
    protected function _getPdfComponent()
    {
        return $this->getData()->parent;
    }

    public function sendContent($output = 'I', $filename = null)
    {
        if ($output == 'I') {
            $plugins = $this->getData()->getPlugins('Vps_Component_Plugin_Interface_View');
            if ($plugins) {
                if (count($plugins) > 1 || !is_instance_of($plugins[0], 'Vps_Component_Plugin_Password_Component')) {
                    throw new Vps_Exception("For pdf only one plugin of type 'Vps_Component_Plugin_Password_Component' is allowed.");
                }
                $p = new $plugins[0]($this->getData()->componentId);
                if ($p->processOutput('')) {
                    parent::sendContent();
                    return false;
                }
            }
        }

        $masterClass = Vpc_Admin::getComponentFile(get_class($this), 'PdfMaster', 'php', true);
        if (!$masterClass) { $masterClass = 'Vps_Pdf_TcPdf'; }
        $pdfComponent = $this->_getPdfComponent();
        if ($pdfComponent instanceof Vps_Component_Data) {
            $pdfComponent = $pdfComponent->getComponent();
        }
        $pdf = new $masterClass($pdfComponent);
        $pdfComponent->getPdfWriter($pdf)->writeContent();
        $pdf->output($filename, $output);
        return true;
    }
}
