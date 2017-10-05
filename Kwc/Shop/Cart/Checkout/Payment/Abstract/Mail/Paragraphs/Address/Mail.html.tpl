<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>
            <?=Kwf_Util_HtmlSpecialChars::filter($this->order->title.' '.$this->order->firstname.' '.$this->order->lastname);?>
        </td>
    </tr>
    <tr>
        <td>
            <?=Kwf_Util_HtmlSpecialChars::filter($this->order->street);?>
        </td>
    </tr>
    <tr>
        <td>
            <?=Kwf_Util_HtmlSpecialChars::filter($this->order->zip);?> <?=Kwf_Util_HtmlSpecialChars::filter($this->order->city);?>
        </td>
    </tr>
    <tr>
        <td>
            <?=Kwf_Util_HtmlSpecialChars::filter($this->order->country);?>
        </td>
    </tr>
</table>
