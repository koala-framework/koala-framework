<div class="<?=$this->cssClass?>">
    <div class="webStandard">
        <h1 class="mainHeadline"><?=trlVps('Register')?></h1>
        <p>
            <?=trlVps('With a free {0} account you can take part actively in the forum and write own posts. In addition, you have the possibility to observe single discussions and get informed by e-mail about new posts on the subject.', Vps_Registry::get('config')->application->name)?>
        </p>
    </div>

    <?=$this->component($this->form)?>
</div>