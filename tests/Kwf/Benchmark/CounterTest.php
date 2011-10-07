<?php
/**
 * @group Benchmark
 */
class Vps_Benchmark_CounterTest extends Vps_Test_TestCase
{
    public function testMemcache()
    {
        $this->_test(new Vps_Benchmark_Counter_Memcache());
    }

    public function testFile()
    {
        $this->_test(new Vps_Benchmark_Counter_File());
    }

    private function _test($counter)
    {
        $name = 'test-counter';
        $start = $counter->getValue($name);
        $counter->increment($name);
        $this->assertEquals($start+1, $counter->getValue($name));
        $counter->increment($name, 100);
        $this->assertEquals($start+101, $counter->getValue($name));
    }
}
