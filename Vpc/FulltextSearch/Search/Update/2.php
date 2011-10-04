<?php
class Vpc_FulltextSearch_Search_Update_2 extends Vps_Update
{
    public function update()
    {
        echo "\n\nstarting fulltext rebuild in background...\n";
        system("php bootstrap.php fulltext rebuild >/dev/null 2>&1 &");
    }
}
