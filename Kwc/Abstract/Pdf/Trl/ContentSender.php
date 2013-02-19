<?php
class Kwc_Abstract_Pdf_Trl_ContentSender extends Kwc_Abstract_Pdf_ContentSender
{
    protected function _getMasterClass($component)
    {
        $masterClass = Kwc_Admin::getComponentFile($component->chained->componentClass, 'PdfMaster', 'php', true);
        if (!$masterClass) { $masterClass = 'Kwf_Pdf_TcPdf'; }
        return $masterClass;
    }
}
