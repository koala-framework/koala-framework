<?php
class Vpc_Advanced_GoogleMap_Component extends Vpc_Abstract
{
    protected $_text;

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => 'Google Maps',
            'tablename' => 'Vpc_Advanced_GoogleMap_Model',
        	'childComponentClasses' => array(
                'text'         => 'Vpc_Basic_Text_Component'
            ),
            'default' => array(
                'content' => Vpc_Abstract::LOREM_IPSUM
            ),
            'assets' => array(
                'files' => array('vps/Vpc/Advanced/GoogleMap/Component.js'),
                'dep'   => array('VpsGoogleMap', 'ExtUtilJson')
            ),
            'assetsAdmin' => array(
                'dep'   => array('VpsGoolgeMapField')
            )
        ));

    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $pos = strpos($this->_getRow()->coordinates, ";");

        $ret['content'] =  $this->getChildComponent('text')->getTemplateVars();
        $options = array();
        $options['zoom_properties'] = $this->_getRow()->zoom_properties;
        $options['zoom'] = $this->_getRow()->zoom;
        $options['height'] = $this->_getRow()->height;
        $options['width'] = $this->_getRow()->width;
        $options['longitude'] = substr($this->_getRow()->coordinates, 0, $pos);
        $options['latitude'] = substr($this->_getRow()->coordinates, $pos + 1, strlen($this->_getRow()->coordinates) - 1);
        $options['coordinates'] = str_replace(';', ',', $this->_getRow()->coordinates);
        $options['scale'] = $this->_getRow()->scale;
        $options['satelite'] = $this->_getRow()->satelite;
        $options['overview'] = $this->_getRow()->overview;
        $ret['options'] = Zend_Json::encode($options);
        return $ret;
    }

    protected function getChildComponent($type)
    {

        if ($type == 'text') {
            if (!$this->_text) {
                $textClass = $this->_getClassFromSetting('text', 'Vpc_Basic_Text_Component');
                $this->_text = $this->createComponent($textClass, 'text');
            }
            return $this->_text;
        }
        return null;
    }

    public function getChildComponents()
    {
        return array(
            $this->getChildComponent('text')
        );
    }

    public function getTextImageRow()
    {
        return $this->_getRow();
    }
}
