<div class="<?=$this->rootElementClass?>">

    <ul>

        <?php if (!empty($this->row->title) || !empty($this->row->firstname) || !empty($this->row->lastname)) { ?>
            <li>
                <?php if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['nameLabel'].$this->labelSeparator; ?></label>
                <?php } ?>
                <strong><?= $this->row->title; ?> <?= $this->row->firstname; ?> <?= $this->row->lastname; ?></strong>
            </li>
        <?php } ?>

        <?php if (!empty($this->workingPosition)) { ?>
            <li>
                <?php if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['positionLabel'].$this->labelSeparator; ?></label>
                <?php } ?>
                <?= $this->workingPosition; ?>
            </li>
        <?php } ?>

        <?php if (!empty($this->row->phone)) { ?>
            <li>
                <?php if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['phoneLabel'].$this->labelSeparator; ?></label>
                <?php } ?>
                <?= $this->row->phone; ?>
            </li>
        <?php } ?>

        <?php if (!empty($this->row->mobile)) { ?>
            <li>
                <?php if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['mobileLabel'].$this->labelSeparator; ?></label>
                <?php } ?>
                <?= $this->row->mobile; ?>
            </li>
        <?php } ?>

        <?php if (!empty($this->row->fax)) { ?>
            <li>
                <?php if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['faxLabel'].$this->labelSeparator; ?></label>
                <?php } ?>
                <?= $this->row->fax; ?>
            </li>
        <?php } ?>

        <?php if (!empty($this->row->email)) { ?>
            <li>
                <?php if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['emailLabel'].$this->labelSeparator; ?></label>
                <?php } ?>
                <?=$this->mailLink($this->row->email); ?>
            </li>
        <?php } ?>

        <?php if ($this->vcard) { ?>
            <li>
                <?php if ($this->showLabels) { ?>
                    <label><?= $this->placeholder['vcardLabel'].$this->labelSeparator; ?></label>
                <?php } ?>
                <?= $this->componentLink($this->vcard, '<img src="/assets/kwf/images/fileicons/vcard.png" height="11" width="16" /> vCard Download'); ?>
            </li>
        <?php } ?>

    </ul>

</div>
