<?php
class Vps_Update_28949 extends Vps_Update
{
    public function update()
    {
        if (file_exists('benchmark.rrd')) {
            system("rrdtool tune benchmark.rrd --data-source-type load:GAUGE");
        }
    }
}
