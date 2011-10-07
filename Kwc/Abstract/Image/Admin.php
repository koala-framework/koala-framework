<?php
class Kwc_Abstract_Image_Admin extends Kwc_Abstract_Composite_Admin
{
    public function gridColumns()
    {
        $ret = array();
        $c = new Kwf_Grid_Column('pic', trlKwf('Image'), 100);
            $c->setData(new Kwf_Data_Kwc_Image($this->_class, 'gridRow'))
            ->setRenderer('mouseoverPic');
        $ret['pic'] = $c;
        $c = new Kwf_Grid_Column('pic_large');
            $c->setData(new Kwf_Data_Kwc_Image($this->_class, 'gridRowLarge'));        
        $ret['pic_large'] = $c;

        if (Kwc_Abstract::getSetting($this->_class, 'imageCaption')) {
            $c = new Kwf_Grid_Column('image_caption', trlKwf('Image caption'));
            $c->setData(new Kwf_Data_Kwc_Table(Kwc_Abstract::getSetting($this->_class, 'ownModel'), 'image_caption', $this->_class));
            $c->setEditor(new Kwf_Form_Field_TextField());
            $ret['image_caption'] = $c;
        }
        return $ret;
    }
}
