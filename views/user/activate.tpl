<div class="content">
    <h1>
        <? if ($this->isActivate) { ?>
            <?=trlKwf('Activate Useraccount')?>
        <? } else { ?>
            <?=trlKwf('Reset Password')?>
        <? } ?>
    </h1>
    <p>
        <?=trlKwf('Your email address:')?> <strong><?=$this->email?></strong>
    </p>

    <?=$this->errorsHtml?>

    <? if ($this->redirects) { ?>
        <p><?=trlKwf('Activate with:')?></p>
        <ul>
        <?php foreach ($this->redirects as $r) { ?>
            <li><a href="<?=htmlspecialchars($r['url'])?>"><?=htmlspecialchars($r['name'])?></a></li>
        <?php } ?>
        </ul>
    <? } ?>
</div>
