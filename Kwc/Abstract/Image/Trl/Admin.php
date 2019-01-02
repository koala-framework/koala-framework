<?php
class Kwc_Abstract_Image_Trl_Admin extends Kwc_Abstract_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('pic', trlKwf('Image'), 100);
            $c->setData(new Kwc_Abstract_Image_Trl_ImageData('gridRow'))
            ->setRenderer('mouseoverPic');
        $ret['pic'] = $c;
        $c = new Kwf_Grid_Column('pic_large');
            $c->setData(new Kwc_Abstract_Image_Trl_ImageData('gridRowLarge'));
        $ret['pic_large'] = $c;

        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $hasImageCaption = Kwc_Abstract::getSetting($masterComponentClass, 'imageCaption');
        if ($hasImageCaption) {
            $c = new Kwf_Grid_Column('image_caption', trlKwf('Image caption'));
            $c->setData(new Kwf_Data_Kwc_Table(Kwc_Abstract::getSetting($this->_class, 'ownModel'), 'image_caption', $this->_class));
            $c->setEditor(new Kwf_Form_Field_TextField());
            $ret['image_caption'] = $c;
        }
        return $ret;
    }

    public function exportContent(Kwf_Component_Data $cmp)
    {
        $ret = parent::exportContent($cmp);
        $masterCC = Kwc_Abstract::getSetting($cmp->componentClass, 'masterComponentClass');
        $trlRow = $cmp->getComponent()->getRow();
        $masterRow = $cmp->chained->getComponent()->getRow();
        if (Kwc_Abstract::getSetting($masterCC, 'altText')) {
            $ret['alt_text'] = $trlRow->alt_text ? $trlRow->alt_text : $masterRow->alt_text;
        }
        if (Kwc_Abstract::getSetting($masterCC, 'titleText')) {
            $ret['title_text'] = $trlRow->title_text ? $trlRow->title_text : $masterRow->title_text;
        }
        if (Kwc_Abstract::getSetting($masterCC, 'imageCaption')) {
            $ret['image_caption'] = $trlRow->image_caption ? $trlRow->image_caption : $masterRow->image_caption;
        }
        return $ret;
    }

    public function importContent(Kwf_Component_Data $cmp, $data)
    {
        parent::importContent($cmp, $data);
        $ownRow = $cmp->getComponent()->getRow();
        if (isset($data['alt_text'])) {
            $ownRow->alt_text = $data['alt_text'];
        }
        if (isset($data['title_text'])) {
            $ownRow->title_text = $data['title_text'];
        }
        if (isset($data['image_caption'])) {
            $ownRow->image_caption = $data['image_caption'];
        }
        $ownRow->save();
    }
}
