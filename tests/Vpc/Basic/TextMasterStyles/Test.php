<?php
/**
 * @group Vpc_Basic_Text
 * @group StylesModel
 */
class Vpc_Basic_TextMasterStyles_Test extends Vps_Test_TestCase
{
    public function testIt()
    {
        $c = ".webStandard h1 { font-size: 12px; } /* Headline 1 */\n";
        $c .= ".webStandard h1.red { color: red; } /* Headline Red */\n";
        $c .= ".webStandard h1 { font-size: 12px; }\n";
        $c .= ".webStandard span.red { color: red; } /* Red */\n";
        $s = Vpc_Basic_Text_StylesModel::parseMasterStyles($c);
        $this->assertEquals(array(
            array(
                'id' => 'master0',
                'name' => 'Headline 1',
                'tagName' => 'h1',
                'className' => false
            ),
            array(
                'id' => 'master1',
                'name' => 'Headline Red',
                'tagName' => 'h1',
                'className' => 'red'
            ),
            array(
                'id' => 'master2',
                'name' => 'Red',
                'tagName' => 'span',
                'className' => 'red'
            )
        ), $s);
    }
}
