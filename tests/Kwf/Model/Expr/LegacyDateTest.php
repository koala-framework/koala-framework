<?php
/**
 * @group Expr
 */
class Kwf_Model_Expr_LegacyDateTest extends Kwf_Test_TestCase
{
    public function testLegacyHigherDate()
    {
        // serialized higher date vor 1.11
        $str = "O:32:\"Kwf_Model_Select_Expr_HigherDate\":2:{s:9:\"\0*\0_field\";s:3:\"col\";s:9:\"\0*\0_value\";s:19:\"2010-01-01 15:38:10\";}";
        $objInKwf1_11 = unserialize($str);

        $this->assertTrue($objInKwf1_11 instanceof Kwf_Model_Select_Expr_Higher);
        $this->assertEquals('Kwf_DateTime', get_class($objInKwf1_11->getValue()));
        $this->assertEquals('2010-01-01 15:38:10', $objInKwf1_11->getValue()->format('Y-m-d H:i:s'));
    }

    public function testLegacyLowerDate()
    {
        // serialized lower date vor 1.11
        $str = "O:33:\"Kwf_Model_Select_Expr_SmallerDate\":2:{s:9:\"\0*\0_field\";s:3:\"col\";s:9:\"\0*\0_value\";s:19:\"2010-01-01 15:38:10\";}";
        $objInKwf1_11 = unserialize($str);

        $this->assertTrue($objInKwf1_11 instanceof Kwf_Model_Select_Expr_Lower);
        $this->assertEquals('Kwf_DateTime', get_class($objInKwf1_11->getValue()));
        $this->assertEquals('2010-01-01 15:38:10', $objInKwf1_11->getValue()->format('Y-m-d H:i:s'));
    }
}
