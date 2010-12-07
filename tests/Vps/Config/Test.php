<?php

/**
 * @group Vps_Config
 */
class Vps_Config_Test extends Vps_Test_TestCase
{
    public function testStaticConfigMerge()
    {
        // main config
        $mainContent = array(
            'foo' => 'bar',
            'blubb' => array(
                'firstname' => 'Hermann'
            ),
            'tags' => array(
                'tag1', 'tag2', 'tag3'
            )
        );
        $main = new Zend_Config($mainContent, true);

        // config to merge
        $mergeContent = array(
            'blubb' => array(
                'lastname' => 'Kunz'
            ),
            'tags' => array(
                'newtag1', 'newtag2'
            )
        );
        $merge = new Zend_Config($mergeContent, true);

        // do it
        $merged = Vps_Config_Web::mergeConfigs($main, $merge);
        $mergedArray = $merged->toArray();

        $expectedResult = $mainContent;
        $expectedResult['blubb']['lastname'] = 'Kunz';
        $expectedResult['tags'] = array('newtag1', 'newtag2');

        $this->assertEquals($expectedResult, $mergedArray);

        // config to merge
        $mergeContent = array(
            'blubb' => array(
                'firstname' => 'Kurt'
            )
        );
        $merge = new Zend_Config($mergeContent, true);

        // do it
        $merged = Vps_Config_Web::mergeConfigs($merged, $merge);
        $mergedArray = $merged->toArray();

        $expectedResult['blubb']['firstname'] = 'Kurt';

        $this->assertEquals($expectedResult, $mergedArray);
    }
}
