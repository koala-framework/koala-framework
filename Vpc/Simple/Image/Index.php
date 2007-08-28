<?php
class Vpc_Simple_Image_Index extends Vpc_Abstract
{
    protected $_tablename = 'Vpc_Simple_Image_IndexModel';
    const NAME = 'Standard.Image';
    protected $_settings = array(
        'typesAllowed' 	    => 'jpg, gif, png',
        'directory'   	    => 'SimpleImage/',
        'size'              => array(), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
        'default_style'		=> 'crop',
        'style' 	        => '',
        'allow'		        => array('crop', 'scale', 'scale_bg', 'deform'), //keywords: crop, scale, scale_bg, deform
        'default_color'		=> 'black',
        'allow_color'		=> 1,
        'color'				=> '',
    );
    const SIZE_NORMAL = '';
    const SIZE_THUMB = '.thumb';
    const SIZE_ORIGINAL = '.original';
    
    
    public function getTemplateVars()
    {
        $return['url'] = $this->getImageUrl();
        $return['template'] = 'Simple/Image.html';
        return $return;
    }
    
    public function getImageUrl($size = self::SIZE_NORMAL)
    {
        $rowset = $this->_getTable()->find($this->getDbId(), $this->getComponentKey());
        if ($rowset->count() == 1) {
            $row = $rowset->current();
            $filename = $row->name != '' ? $row->name : 'unnamed';
            $filename .= $size;
            $id = $this->getId();
            $checksum = md5('l4Gx8SFe' . $id);
            $rowset2 = $this->_getTable('Vps_Dao_File')->find($row->vps_upload_id);
            if ($rowset2->count() == 1) {
                $extension = substr(strrchr($rowset2->current()->path, '.'), 1);
                return "/media/$id/$checksum/$filename.$extension";
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public function getExtensions()
    {
        $extensions = array();
        foreach (explode(',', $this->getSetting('typesAllowed')) as $extension) {
            $extensions[] = trim(strtolower($extension));
        }
        return $extensions;
    }
    
}