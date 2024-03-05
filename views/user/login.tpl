<?php if ($this->pages) { ?>
    <a class="kwfUp-frontendLink" href="/">
        <div class="kwfUp-innerFrontendLink">
            <div class="kwfUp-front"><?=trlKwf('Go to<br />Website');?></div>
            <div class="kwfUp-back"></div>
        </div>
    </a>
<?php } ?>
<div class="kwfUp-content">
    <?php if ($this->untagged) { ?>
        <div class="kwfUp-untagged"><?=trlKwf('WARNING: untagged')?></div>
    <?php } ?>
    <?php if($this->image) { ?>
        <div class="kwfUp-image" style="margin-top: -<?= $this->imageSize['height'] ?>px">
            <img src="<?= $this->image ?>" width="<?= $this->imageSize['width'] ?>" height="<?= $this->imageSize['height'] ?>" />
        </div>
    <?php } else { ?>
        <h1><?php echo Kwf_Util_HtmlSpecialChars::filter($this->applicationName); ?> Login</h1>
    <?php } ?>
    <?=$this->errorsHtml?>
    <form action="<?= Kwf_Util_HtmlSpecialChars::filter($this->action) ?>" method="<?=$this->method?>">
        <?php $this->formField($this->form) ?>
        <button class="submit" type="submit" name="<?= $this->formName ?>" value="submit"><?=trlKwf('Login')?></button>
    </form>
    <p>
        <a class="kwfUp-lostPassword" href="<?=Kwf_Util_HtmlSpecialChars::filter($this->lostPasswordLink)?>"><?=trlKwf('Lost password?')?></a>
    </p>
    <?php if ($this->redirects) { ?>
        <div class="externalAuthButtons">
            <p><?=trlKwf('Login with:')?></p>
            <ul>
            <?php foreach ($this->redirects as $r) { ?>
                <li>
                    <form method="GET" action="<?=Kwf_Util_HtmlSpecialChars::filter($r['url'])?>">
                    <input type="hidden" name="authMethod" value="<?=Kwf_Util_HtmlSpecialChars::filter($r['authMethod'])?>" />
                    <input type="hidden" name="redirect" value="<?=Kwf_Util_HtmlSpecialChars::filter($r['redirect'])?>" />
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
    <?php } ?>
</div>
