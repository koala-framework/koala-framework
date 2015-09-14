<?php
class Kwf_Controller_Action_Debug_RequirementsController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $ret = "<ul>";
        foreach (Kwf_Util_Check_Config::getCheckResults() as $result) {
            $color = "black";
            if ($result['status'] == 'ok') {
                $color = "green";
            } else if ($result['status'] == 'failed') {
                $color = "red";
            } else if ($result['status'] == 'warning') {
                $color = "yellow";
            }

            $ret .= "<li style=\"margin: 5px; border: 3px solid {$color}; padding: 2px;\">{$result['checkText']}";
            if ($result['message']) $ret .= "<br />{$result['message']}";
            $ret .= "</li>";
        }
        $ret .= "</ul>";

        echo $ret;
        exit;
    }
}

