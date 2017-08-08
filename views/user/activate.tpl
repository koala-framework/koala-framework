<div class="kwfUp-content">
    <h1>
        <?php if ($this->isActivate) { ?>
            <?=trlKwf('Activate Useraccount')?>
        <?php } else { ?>
            <?=trlKwf('Reset Password')?>
        <?php } ?>
    </h1>
    <p>
        <?=trlKwf('Your email address:')?> <strong><?=htmlspecialchars($this->email)?></strong>
    </p>

    <?=$this->errorsHtml?>

    <p><?=trlKwf('Activate with:')?></p>
    <ul>
        <?php if ($this->showPassword) { ?>
            <li>
                <a href="<?=htmlspecialchars($this->passwordUrl)?>"><?=trlKwf('Password')?></a>
            </li>
        <?php } ?>
        <?php foreach ($this->redirects as $r) { ?>
            <li>
                <form method="GET" action="<?=htmlspecialchars($r['url'])?>">
                <input type="hidden" name="authMethod" value="<?=htmlspecialchars($r['authMethod'])?>" />
                <input type="hidden" name="code" value="<?=htmlspecialchars($r['code'])?>" />
                <?=$r['formOptions']?>
                <button>
                    <?php if ($r['icon']) { ?>
                        <img src="<?=htmlspecialchars($r['icon'])?>" />
                    <?php } else { ?>
                        <?=htmlspecialchars($r['name'])?>
                    <?php } ?>
                </button>
                </form>
            </li>
        <?php } ?>
    </ul>
</div>
