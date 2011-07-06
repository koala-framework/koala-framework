<?php
class Vps_Controller_Action_Debug_ClassTreeController extends Vps_Controller_Action
{
    private function _graphData($class, $parent = '')
    {
        if (is_instance_of($class, 'Vpc_Paragraphs_Component')) return;
        static $processed = array();
        $ret = '';
        $last = $class . $parent;
        foreach (Vpc_Abstract::getSetting($class, 'generators') as $generatorKey => $generator) {
            $g = $this->_getGenerator($class, $generatorKey);
            $shape = $this->_getShape($g);
            foreach ($this->_getChildren($generator) as $child) {
                $color = $this->_getColor($child);
                $fontcolor = in_array($child, $processed) ? 'grey40' : 'black';
                $classname = $this->_getClassName($child, $class);
                $label = '';
                if ($g->getGeneratorFlag('table')) $label = 'headlabel = "*"';
                $name = $child . $class;
                $node = "Node_$name";
                $ret .= "{ $name [label=\"$classname\" shape=$shape color=$color fontcolor=\"$fontcolor\" fontsize=12] } \n";
                $ret .= "{ $node [shape=point] }\n";
                $ret .= "{ $node -> $name [arrowhead=none $label] }\n";
                $ret .= "{ $last -> $node [arrowhead=none] rank=same }\n";
                $ret .= "\n";
                $last = $node;
                if (!in_array($child, $processed)) {
                    $ret .= $this->_graphData($child, $class);
                }
                $processed[] = $child;
            }
        }
        return $ret;
    }

    private function _getShape($generator)
    {
        $ret = 'ellipse';
        if ($generator->getGeneratorFlag('page')){
            $ret = 'box';
        } else if ($generator->getGeneratorFlag('box')) {
            $ret = 'hexagon';
        }
        return $ret;
    }

    private function _getGenerator($class, $generatorKey)
    {
        return Vps_Component_Generator_Abstract::getInstance($class, $generatorKey);
    }

    private function _getChildren($generator)
    {
        $components = $generator['component'];
        if (!is_array($components)) $components = array($components);
        $ret = array();
        foreach ($components as $component) {
            if ($component) $ret[] = $component;
        }
        return $ret;
    }

    private function _getColor($child)
    {
        $ret = 'blue';
        if (file_exists(VPS_PATH.'/'.str_replace('_', '/', $child).'.php')) {
            $ret = 'red';
        }
        return $ret;
    }

    private function _getClassName($class, $parent = null)
    {
        $parentPre = substr($parent, 0, -9);
        $ret = substr($class, 0, -10);
        if (substr($ret, 0, strlen($parentPre)) == $parentPre) {
            $ret = substr($ret, strlen($parentPre));
        } else {
            $ret = substr($ret, 4);
            if (!file_exists(VPS_PATH.'/'.str_replace('_', '/', $class).'.php')) {
                $ret = substr($ret, strpos($ret, '_') + 1);
            }
        }
        return $ret;
    }

    public function indexAction()
    {
        $class = $this->_getParam('class');
        if (!$class) throw new Vps_Exception_Client('Bitte Klasse als Get-Parameter "class" angeben.');

        $classname = $this->_getClassName($class);
        $color = $this->_getColor($class);

        $graph  = "digraph hierarchy {\n";
        $graph .= "rankdir = LR;\n\n";
        $graph .= "concentrate=true;\n\n";
        $graph .= "{ {$class} [label=\"$class\" shape=ellipse color=$color fontsize=10] } \n\n";
        $graph .= $this->_graphData($class);
        $graph .= '}';

        if (!is_null($this->_getParam('src'))) {
            d(nl2br($graph));
        }

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