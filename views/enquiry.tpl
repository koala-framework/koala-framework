<div class="enquiryReview">
    <div class="headers">
        <div class="subject"><?= $this->subject; ?></div>
        <div class="detail">
            <div>
                <label><?= trlVps('From'); ?>:</label>
                <div><?= $this->from['name']; ?> &lt;<?= $this->from['email']; ?>&gt;</div>
            </div>
            <div>
                <label><?= trlcVps('email', 'To'); ?>:</label>
                <div>
                <?php
                    $first = true;
                    foreach ($this->to as $to) {
                        if (!$first) echo '; ';
                        echo $to['name'].' &lt;'.$to['email'].'&gt;';
                        $first = false;
                    }
                ?>
                </div>
            </div>
            <? if ($this->cc) { ?>
                <div>
                    <label><?= trlVps('Copy'); ?>:</label>
                    <div>
                    <?php
                        $first = true;
                        foreach ($this->cc as $cc) {
                            if (!$first) echo '; ';
                            echo $cc['name'].' &lt;'.$cc['email'].'&gt;';
                            $first = false;
                        }
                    ?>
                    </div>
                </div>
            <? } ?>
            <div><label><?= trlVps('Date'); ?>:</label> <?= $this->dateTime($this->send_date); ?></div>
        </div>
    </div>


    <div class="message"><?= nl2br($this->mailContent); ?></div>
</div>