<?php
class Kwc_Basic_Download_Pdf extends Kwc_Abstract_Pdf
{
    public function writeContent()
    {
        $fileSizeHelper = new Kwf_View_Helper_FileSize();
        $encodeTextHelper = new Kwf_View_Helper_Link();
        $vars = $this->_component->getTemplateVars(new Kwf_Component_Renderer());
        if ($vars['icon']) {
            $this->_pdf->Image($vars['icon']->getFilename(), $this->_pdf->getX(), $this->_pdf->getY(), 3, 3, 'PNG');
        }
        $this->_pdf->setX($this->_pdf->getX() + 4);
        if ($vars['filesize']) {
            $filesize = ' (' . $fileSizeHelper->fileSize($vars['filesize']) . ')';
        } else {
            $filesize = '';
        }

        $link = $this->_getDownloadUrl();

        $this->_pdf->Cell(0, 0, $vars['infotext'].$filesize, '', 1, '', 0, $link);
        $this->Ln(1);

    }

    protected function _getDownloadUrl()
    {
        $downloadTagVars = $this->_component->getData()
            ->getChildComponent('-downloadTag')->getComponent()->getTemplateVars(new Kwf_Component_Renderer());
        $domain = $this->_component->getData()->getDomain();
        $protocol = Kwf_Util_Https::domainSupportsHttps($domain) ? 'https' : 'http';
        return $protocol . '://' . $domain . $downloadTagVars['url'];
    }
}
