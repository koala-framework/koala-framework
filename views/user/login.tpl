<? if ($this->pages) { ?>
    <a class="kwfUp-frontendLink" href="<?=$this->baseUrl?>/">
        <div class="kwfUp-innerFrontendLink">
            <div class="kwfUp-front"><?=trlKwf('Go to<br />Website');?></div>
            <div class="kwfUp-back"></div>
        </div>
    </a>
<? } ?>
<div class="kwfUp-content">
    <? if ($this->untagged) { ?>
        <div class="kwfUp-untagged"><?=trlKwf('WARNING: untagged')?></div>
    <? } ?>
    <?php if($this->image) { ?>
        <div class="kwfUp-image" style="margin-top: -<?= $this->imageSize['height'] ?>px">
            <img src="<?= $this->image ?>" width="<?= $this->imageSize['width'] ?>" height="<?= $this->imageSize['height'] ?>" />
        </div>
    <?php } else { ?>
        <h1><?php echo $this->applicationName; ?> Login</h1>
    <?php } ?>
    <?=$this->errorsHtml?>
    <form action="<?= htmlspecialchars($this->action) ?>" method="<?=$this->method?>">
        <?php $this->formField($this->form) ?>
        <button class="submit" type="submit" name="<?= $this->formName ?>" value="submit"><?=trlKwf('Login')?></button>
    </form>
    <p>
        <a class="kwfUp-lostPassword" href="<?=$this->lostPasswordLink?>"><?=trlKwf('Lost password?')?></a>
    </p>
    <? if ($this->redirects) { ?>
        <div class="externalAuthButtons">
            <p><?=trlKwf('Login with:')?></p>
            <ul>
            <?php foreach ($this->redirects as $r) { ?>
                <li>
                    <form method="GET" action="<?=htmlspecialchars($r['url'])?>">
                    <input type="hidden" name="authMethod" value="<?=htmlspecialchars($r['authMethod'])?>" />
                    <input type="hidden" name="redirect" value="<?=htmlspecialchars($r['redirect'])?>" />
                    <?=$r['formOptions']?>
                    <button>
                        <? if ($r['icon']) { ?>
                            <img src="<?=htmlspecialchars($r['icon'])?>" />
                        <? } else { ?>
                            <?=htmlspecialchars($r['name'])?>
                        <? } ?>
                    </button>
                    </form>
                </li>
            <?php } ?>
            </ul>
        </div>
    <? } ?>
</div>
