<?php
/*
 * http://vps11.franz.vivid/vps/vpctest/Vpc_Flash_Component
 *
 */
class Vpc_Flash_Flash_Component extends Vpc_Abstract_Flash_Component
{
    protected function _getFlashData()
    {
        $ret = parent::_getFlashData();
        $ret['url'] = '/assets/vps/tests/Vpc/Flash/Flash/demo.swf';
        $ret['width'] = 558;
        $ret['height'] = 168;
        $ret['params'] = array(
            'allowfullscreen' => 'true'
        );
        return $ret;
    }

    protected function _getFlashVars()
    {
        $ret = parent::_getFlashVars();
        $ret['allowfullscreen'] = true;
        return $ret;
    }
}
/*
<embed height="168" width="558"
flashvars="loc=de_DE&amp;required_version=9,0,124,0"
wmode="opaque"
quality="high"
bgcolor="#FFFFFF"
name="shell.swf"
id="shell_object-embed"
src="demo"
type="application/x-shockwave-flash">
*/