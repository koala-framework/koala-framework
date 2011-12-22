<?php
/**
 * @group Validate
 */
class Kwf_Validate_PasswordTest extends Kwf_Test_TestCase
{
    public function test3of4()
    {
        $v = new Kwf_Validate_Password3of4();

        // length
        $this->assertFalse($v->isValid("aB9§"));
        $this->assertFalse($v->isValid(""));

        // 1 of 4
        $this->assertFalse($v->isValid("abcdefgh"));
        // 2 of 4
        $this->assertFalse($v->isValid("abcdEFGH"));
        // 3 of 4
        $this->assertTrue($v->isValid("abcdEF12"));
        // 4 of 4
        $this->assertTrue($v->isValid("abc\EF12"));
        
    }
}
