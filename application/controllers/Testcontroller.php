<?php
class TestController extends Vps_Controller_Action
{
    public function indexAction()
    {
      /*  $sql = 'SELECT * FROM vps_components';
        $db = Zend_Registry::get('dao')->getDb();
        $rows = $db->fetchAll($sql);
        
        p ($rows);
        //$array = $rows->toArray();
        $pdffile = new Vps_Pdf_Index($rows, "Vps Components");
        $pdffile->setColumnWidth('id', 30);
        $pdffile->setColumnWidth('component', 220);
        $pdffile->setColumnWidth('visible', 40);
        $pdffile->setColumnWidth('page_id', 40);
        $pdffile->setBorderSettings('right', 240);
        $pdffile->setBorderSettings('left', 50);
        $pdffile->insertTextBox("By default, text strings are interpreted using the character encoding method of the current locale. If you have a string that uses a different encoding method (such as a UTF-8 string read from a file on disk, or a MacRoman string obtained from a legacy database), you can indicate the character encoding at draw time and Zend_Pdf will handle the conversion for you. You can supply source strings in any encoding method supported by PHP's iconv() function:", 38, 370, 725);
        $pdffile ->savePdf("myFileName");*/
      
        $textarea = new Vps_Pdf_TextArea('headline', 'meinText');
        $textarea->setSetting('maxlength', 100);
        $textarea->setSetting('styles', 100, 'headline');
        $textarea->test();
    }
}
