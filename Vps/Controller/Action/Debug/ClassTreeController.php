<?php
class Vps_Controller_Action_Debug_ClassTreeController extends Vps_Controller_Action
{

    private function _graphData($class)
    {
        static $processed = array();
        $ret = '';
        do {
            if (in_array($class, $processed)) break;
            $processed[] = $class;
            $ret .= get_parent_class($class)."->{$class};\n";
            $childs = Vpc_Abstract::getChildComponentClasses($class);
            foreach ($childs as $child) {
                if ($child) {
//                     $ret .= "{$class}->{$child}[arrowtail=odiamond]\n";
                    $ret .= $this->_graphData($child);
                }
            }
        } while($class = get_parent_class($class));
        return $ret;
    }
    public function indexAction()
    {
        $graph  = 'digraph hierarchy {';
        $graph .= 'edge[dir=back, arrowtail=empty] ';
        $graph .= $this->_graphData(Vps_Registry::get('config')->vpc->rootComponent);
//         $graph .= $this->_graphData('Vpc_Rotary_Home_Component');
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
//         echo $graph;
        exit;
    }

}