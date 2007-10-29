<?php
class Vpc_Basic_Rte_Index extends Vpc_Abstract
{
   protected $_settings = array(
        'text' => 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.',
        'fieldLabel' => 'Rich Text Editor',
        'width' => 500,
        'height' => 200,
        'enableAlignments' => true,
        'enableColors' => true,
        'enableFont' => true,
        'enableFontSize' => true,
        'enableFormat' => true,
        'enableLinks' => true,
        'enableLists' => true,
        'enableSourceEdit' => true,
        'imageClass'        => 'Vpc_Basic_Image_Index',
        'imageSettings'     => array()
    );

    protected $_tablename = 'Vpc_Basic_Rte_IndexModel';
    const NAME = 'Standard.Rte';

    private $_images;

    function getTemplateVars()
    {
        $return['text'] = $this->getSetting('text');
        $return['template'] = 'Rte.html';
        return $return;
    }
    protected function _init()
    {
    }

    private function _getSessionContent()
    {
        $s = new Zend_Session_Namespace('Vpc_Basic_Rte_Index');
        if ($s->html) {
            return $s->html;
        } else {
            $this->getSetting('text')
        }
    }

    public function getChildComponents()
    {
        return $this->getImageComponents();
    }

    protected function _getImageComponents()
    {
        if (isset($this->_images)) return $this->_images;

        $this->_images = array();
        $this->parse($this->_getSessionContent());
        $this->images = array();
        preg_match_all('#<img.+src="([^"]+)"[^>]*>#', $html, $m);
        foreach ($m[1] as $k=>$src) {
            $k = $k+1;
            //todo: $k aus src auslesen, nicht einfach mitzählen
            $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Index');
            $this->images[$k] = $this->createComponent($imageClass, $k,
                                            $this->getSetting('imageSettings'));
        }
        return $this->_images;
    }
    
    public function setSessionHtml($html)
    {
        $s = new Zend_Session_Namespace('Vpc_Basic_Rte_Index');
        $s->html = $html;
        $this->_parse
    }

    public function addImage()
    {
        if (count($this->_getImageComponents())) {
            $k = max(array_keys($this->_getImageComponents()));
        } else {
            $k = 0;
        }
        $k++;
        $imageClass = $this->_getClassFromSetting('imageClass', 'Vpc_Basic_Image_Index');
        $this->images[$k] = $this->createComponent($imageClass, $k,
                                            $this->getSetting('imageSettings'));
        return $this->images[$k];
    }


}