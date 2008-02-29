<?php

        p (trlVps('zwischen {0} und {1}', array('whatever', $hallo)));
        p (trlVps('whatever {0} und {1}', array(134, 23)));
        p (trlVps('whatever {0}', 1212));
        p (trlVps('erst {hallo} dann {tschuess}', array('hallo' => 'zeas', 'tschuess' => 'habedere')));