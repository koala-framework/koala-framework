<?php
class Vps_Controller_Action_Component_ComponentsController extends Vps_Controller_Action_Auto_Tree
{
    protected $_primaryKey = 'component';
    protected $_textField = 'component';
    protected $_buttons = array(
        'reload'    => true
    );
    protected $_rootVisible = false;
    protected $_icons = array (
        'root'      => 'asterisk_yellow',
        'page'      => 'page',
        'component' => 'page_white',
        'box'       => 'page_white_database',
        'default'   => 'page_error',
        'invisible'   => 'page_error'
    );
    protected $_modelName = 'Vps_Component_Generator_Model';
    
    protected function _formatNode($row)
    {
        $data = parent::_formatNode($row);
        if ($row->class == 'root') {
            $icon = 'root';
        /*NOT PORTED to flags
        } else if (is_instance_of($row->class, 'Vps_Component_Generator_Box_Interface')) {
            $icon = 'box';
        } else if (is_instance_of($row->class, 'Vps_Component_Generator_Page_Interface')) {
            $icon = 'page';
        */
        } else {
            $icon = 'component';            
        }
        $data['expanded'] = $row->class == 'root';
        $data['bIcon'] = $this->_icons[$icon]->__toString();
        $data['text'] .= ': ' . $row->name;
        return $data;
    }

    private $_processed = array();
    private $_x = 0;
    private $_y = 0;
    private $_maxX = 0;
    private $_maxY = 0;
    private function _process($class, $type)
    {
        $ret = '';
        $width = 300;
        $height = 30;
        if ($type == 'root' || $type=='component' || $type=='plugin') {
            $ret .= '<ellipse cx="'.($this->_x+($width/2)).'" cy="'.($this->_y+($height/2)).'" rx="'.($width/2).'" ry="'.($height/2).'" />';
        } else if ($type == 'page') {
            $ret .= '<rect x="'.$this->_x.'" y="'.$this->_y.'" width="'.$width.'" height="'.$height.'" />';
        } else if ($type == 'box') {
            $ret .= '<rect x="'.$this->_x.'" y="'.$this->_y.'" width="'.$width.'" height="'.$height.'" />';
            $ret .= '<ellipse cx="'.($this->_x+($width/2)).'" cy="'.($this->_y+($height/2)).'" rx="'.($width/2).'" ry="'.($height/2).'" />';
        }
        $ret .= '<text x="'.($this->_x+10).'" y="'.($this->_y+20).'">'.$class.'</text>';
        $this->_y += 40;

        $classes = array();
        foreach (Vpc_Abstract::getSetting($class, 'generators') as $generator) {
            if (is_instance_of($generator['class'], 'Vps_Component_Generator_Box_Interface')) {
                $type = 'box';
            } else if (is_instance_of($generator['class'], 'Vps_Component_Generator_Page_Interface')) {
                $type = 'page';
            } else {
                $type = 'component';
            }
            if (!is_array($generator['component'])) $generator['component'] = array($generator['component']);
            foreach ($generator['component'] as $c) {
                $classes[] = array(
                    'type' => $type,
                    'componentClass' => $c
                );
            }
        }
        $plugins = Vpc_Abstract::getSetting($class, 'plugins');
        if (is_array($plugins)) {
            foreach ($plugins as $c) {
                $classes[] = array(
                    'type' => 'plugin',
                    'componentClass' => $c
                );
            }
        }
        $this->_x += 100;
        $this->_maxX = max($this->_maxX, $this->_x+$width);
        $this->_maxY = max($this->_maxY, $this->_y+$height);
        foreach ($classes as $i) {
            $class = $i['componentClass'];
            if ($class && !in_array($class, $this->_processed)) {
                $this->_processed[] = $class;
                $ret .= $this->_process($class, $i['type']);
            }
        }
        $this->_x -= 100;
        return $ret;
    }
    public function graphAction()
    {
        $svg = $this->_process(Vps_Component_Data_Root::getComponentClass(), 'root');

$svg = '<?xml version="1.0" encoding="iso-8859-1"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
    "http://www.w3.org/TR/2001/
    REC-SVG-20010904/DTD/svg10.dtd">
<svg width="'.$this->_maxX.'" height="'.$this->_maxY.'"
    xmlns="http://www.w3.org/2000/svg"
    xmlns:xlink="http://www.w3.org/1999/xlink">
    <g style="stroke:black;fill:white" transform="translate(0,0)">
    '.$svg.'
        <!--<rect x="0" y="0" width="100" height="30" />
        <text x="20" y="20" width="100">Root</text>

        <ellipse cx="170" cy="100" rx="60" ry="20" />
        <text x="170" y="100">Foo</text>

        <line x1="50" y1="30" x2="50" y2="100" />
        <line x1="50" y1="100" x2="110" y2="100" />-->
    </g>
</svg>';
        $file = tempnam('/tmp', 'svg');
        file_put_contents($file.'.svg', $svg);
        system("convert $file.svg $file.png");
        header('Content-Type: image/png');
        echo file_get_contents("$file.png");
        unlink("$file");
        unlink("$file.svg");
        unlink("$file.png");
        exit;
    }
}
