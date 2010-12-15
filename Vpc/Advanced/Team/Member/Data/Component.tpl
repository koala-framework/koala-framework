<div class="<?=$this->cssClass?>">

    <ul>

        <? if (!empty($this->row->title) || !empty($this->row->firstname) || !empty($this->row->lastname)) { ?>
            <li>
                <? if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['nameLabel'].$this->labelSeparator; ?></label>
                <? } ?>
                <?= $this->row->title; ?> <?= $this->row->firstname; ?> <?= $this->row->lastname; ?>
            </li>
        <? } ?>

        <? if (!empty($this->workingPosition)) { ?>
            <li>
                <? if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['positionLabel'].$this->labelSeparator; ?></label>
                <? } ?>
                <?= $this->workingPosition; ?>
            </li>
        <? } ?>

        <? if (!empty($this->row->phone)) { ?>
            <li>
                <? if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['phoneLabel'].$this->labelSeparator; ?></label>
                <? } ?>
                <?= $this->row->phone; ?>
            </li>
        <? } ?>

        <? if (!empty($this->row->mobile)) { ?>
            <li>
                <? if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['mobileLabel'].$this->labelSeparator; ?></label>
                <? } ?>
                <?= $this->row->mobile; ?>
            </li>
        <? } ?>

        <? if (!empty($this->row->fax)) { ?>
            <li>
                <? if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['faxLabel'].$this->labelSeparator; ?></label>
                <? } ?>
                <?= $this->row->fax; ?>
            </li>
        <? } ?>

        <? if (!empty($this->row->email)) { ?>
            <li>
                <? if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['emailLabel'].$this->labelSeparator; ?></label>
                <? } ?>
                <?=$this->mailLink($this->row->email); ?>
            </li>
        <? } ?>

        <? if ($this->vcard) { ?>
            <li>
                <? if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['vcardLabel'].$this->labelSeparator; ?></label>
                <? } ?>
                <?= $this->componentLink($this->vcard, '<img src="/assets/vps/images/fileicons/vcard.png" height="11" width="16" /> vCard Download'); ?>
            </li>
        <? } ?>

    </ul>

</div>
