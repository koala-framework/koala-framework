<div class="kwfUp-content">
    <h1>
        <?php if ($this->isActivated) { ?>
            <?=trlKwf('Reset Password')?>
        <?php } else { ?>
            <?=trlKwf('Activate Useraccount')?>
        <?php } ?>
    </h1>
    <p>
        <?=trlKwf('Your email address:')?> <strong><?=Kwf_Util_HtmlSpecialChars::filter($this->email)?></strong>
    </p>

    <?=$this->errorsHtml?>

    <p><?=trlKwf('Activate with:')?></p>
    <ul>
        <?php if ($this->showPassword) { ?>
            <li>
                <a href="<?=Kwf_Util_HtmlSpecialChars::filter($this->passwordUrl)?>"><?=trlKwf('Password')?></a>
            </li>
        <?php } ?>
        <?php foreach ($this->redirects as $r) { ?>
            <li>
                <form method="GET" action="<?=Kwf_Util_HtmlSpecialChars::filter($r['url'])?>">
                <input type="hidden" name="authMethod" value="<?=Kwf_Util_HtmlSpecialChars::filter($r['authMethod'])?>" />
                <input type="hidden" name="code" value="<?=Kwf_Util_HtmlSpecialChars::filter($r['code'])?>" />
                <?=$r['formOptionsHtml']?>
                <button>
                    <?php if ($r['icon']) { ?>
                        <img src="<?=Kwf_Util_HtmlSpecialChars::filter($r['icon'])?>" />
                    <?php } else { ?>
                        <?=Kwf_Util_HtmlSpecialChars::filter($r['name'])?>
                    <?php } ?>
                </button>
                </form>
            </li>
        <?php } ?>
    </ul>
</div>
