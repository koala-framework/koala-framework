<?php
class Kwc_Basic_Download_Pdf extends Kwc_Abstract_Pdf
{
    public function writeContent()
    {
        $fileSizeHelper = new Kwf_View_Helper_FileSize();
        $encodeTextHelper = new Kwf_View_Helper_Link();
        $vars = $this->_component->getTemplateVars();
        if ($vars['icon']) {
            $this->_pdf->Image($vars['icon']->getFilename(), $this->_pdf->getX(), $this->_pdf->getY(), 3, 3, 'PNG');
        }
        $this->_pdf->setX($this->_pdf->getX() + 4);
        if ($vars['filesize']) {
            $filesize = ' (' . $fileSizeHelper->fileSize($vars['filesize']) . ')';
        } else {
            $filesize = '';
        }

        $downloadTagVars = $this->_component->getData()
            ->getChildComponent('-downloadTag')->getComponent()->getTemplateVars();
        $protocol = Kwf_Config::getValue('server.https') ? 'https' : 'http';
        $link = $protocol . '://' . $this->_component->getData()->getDomain() . $downloadTagVars['url'];

        $this->_pdf->Cell(0, 0, $vars['infotext'].$filesize, '', 1, '', 0, $link);
        $this->Ln(1);

    }

}
