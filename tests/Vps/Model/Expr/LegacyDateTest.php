<?php
/**
 * @group Expr
 */
class Vps_Model_Expr_LegacyDateTest extends Vps_Test_TestCase
{
    public function testLegacyHigherDate()
    {
        // serialized higher date vor 1.11
        $str = "O:32:\"Vps_Model_Select_Expr_HigherDate\":2:{s:9:\"\0*\0_field\";s:3:\"col\";s:9:\"\0*\0_value\";s:19:\"2010-01-01 15:38:10\";}";
        $objInVps1_11 = unserialize($str);

        $this->assertTrue($objInVps1_11 instanceof Vps_Model_Select_Expr_Higher);
        $this->assertEquals('Vps_DateTime', get_class($objInVps1_11->getValue()));
        $this->assertEquals('2010-01-01 15:38:10', $objInVps1_11->getValue()->format('Y-m-d H:i:s'));
    }

    public function testLegacyLowerDate()
    {
        // serialized lower date vor 1.11
        $str = "O:33:\"Vps_Model_Select_Expr_SmallerDate\":2:{s:9:\"\0*\0_field\";s:3:\"col\";s:9:\"\0*\0_value\";s:19:\"2010-01-01 15:38:10\";}";
        $objInVps1_11 = unserialize($str);

        $this->assertTrue($objInVps1_11 instanceof Vps_Model_Select_Expr_Lower);
        $this->assertEquals('Vps_DateTime', get_class($objInVps1_11->getValue()));
        $this->assertEquals('2010-01-01 15:38:10', $objInVps1_11->getValue()->format('Y-m-d H:i:s'));
    }
}
