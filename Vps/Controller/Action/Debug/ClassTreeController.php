<?php
class Vps_Controller_Action_Debug_ClassTreeController extends Vps_Controller_Action
{

    private function _graphData($class)
    {
        static $processed = array();
        $ret = '';
        if (in_array($class, $processed)) return;
        $processed[] = $class;
        foreach (Vpc_Abstract::getSetting($class, 'generators') as $generator) {
            $shape = 'ellipse';
            /*
            NOT PORTED to flags
            if (is_instance_of($generator['class'], 'Vps_Component_Generator_Page_Interface')) {
                $shape = 'box';
            } else if (is_instance_of($generator['class'], 'Vps_Component_Generator_Box_Interface')) {
                $shape = 'hexagon';
            }
            */
            $fontColor = 'blue';
            if (file_exists(VPS_PATH.'/'.str_replace('_', '/', $class).'.php')) {
                $fontColor = 'red';
            }
            if (!is_array($generator['component'])) $generator['component'] = array($generator['component']);
            foreach ($generator['component'] as $child) {
                if ($child) {
                    $ret .= "\"{$child}\" [shape=$shape, label=\"{$child}\", color=$fontColor, fontsize=10];\n";
                    $ret .= "{$class}->{$child}[arrowtail=odiamond];\n";
                    $ret .= $this->_graphData($child);
                }
            }
        }
//         do {
//         $ret .= get_parent_class($class)."->{$class};\n";
//             if (in_array($class, $processed)) break;
//         } while($class = get_parent_class($class));
        return $ret;
    }
    public function indexAction()
    {
        $graph  = 'digraph hierarchy {';
        $graph .= 'edge[dir=back, arrowtail=empty] ';
        $graph .= $this->_graphData(Vps_Component_Data_Root::getComponentClass());
        $graph .= '}';

        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "r")
        );
        $process = proc_open('dot -Tpng', $descriptorspec, $pipes);
        if (is_resource($process)) {
            fwrite($pipes[0], $graph);
            fclose($pipes[0]);
            $image = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            $errout = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $returnValue = proc_close($process);
        } else {
            throw new Vps_Exception('Can\'t open process.');
        }
        if ($returnValue) {
//             throw new Vps_Exception('Error: '.$errout);
        }
        header('Content-Type: image/png');
        echo $image;
        exit;
    }

}