<div class="enquiryReview">
    <div class="headers">
        <div class="subject"><?= htmlspecialchars($this->subject); ?></div>
        <div class="detail">
            <?php if (!empty($this->from['name']) || !empty($this->from['email'])) { ?>
                <div>
                    <label><?= trlKwf('From'); ?>:</label>
                    <div>
                        <?php if (!empty($this->from['name'])) echo htmlspecialchars($this->from['name']); ?>
                        <?php if (!empty($this->from['email'])) echo '&lt;'.htmlspecialchars($this->from['email']).'&gt;'; ?>
                    </div>
                </div>
            <?php } ?>
            <div>
                <label><?= trlcKwf('email', 'To'); ?>:</label>
                <div>
                <?php
                    $first = true;
                    foreach ($this->to as $to) {
                        if (!$first) echo '; ';
                        echo htmlspecialchars($to['name']).' &lt;'.htmlspecialchars($to['email']).'&gt;';
                        $first = false;
                    }
                ?>
                </div>
            </div>
            <?php if ($this->cc) { ?>
                <div>
                    <label><?= trlKwf('Copy'); ?>:</label>
                    <div>
                    <?php
                        $first = true;
                        foreach ($this->cc as $cc) {
                            if (!$first) echo '; ';
                            echo htmlspecialchars($cc['name']).' &lt;'.htmlspecialchars($cc['email']).'&gt;';
                            $first = false;
                        }
                    ?>
                    </div>
                </div>
            <?php } ?>
            <div><label><?= trlKwf('Date'); ?>:</label> <?= $this->dateTime($this->send_date); ?></div>
        </div>
    </div>


    <div class="message"><?= htmlspecialchars($this->mailContent); ?></div>
</div>
