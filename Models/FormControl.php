<?php

namespace Wikibots\Models;

class FormControl
{
    public function __construct(protected string $tagName, protected bool $isTagPaired, protected array $attributes, protected ?string $iniKey, protected string|array|null $value) {}

    public function render() : string
    {
        $result = '';
        if ($this->tagName === 'label') {
            $result .= '<tr>';
        }
        $result .= '<td'.(($this->tagName === 'label' && in_array($this->value, ['TRUE', 'FALSE'])) ? ' style="text-align: right"' : '').'>';

        $result .= '<'.$this->tagName.' ';
        if (!is_null($this->iniKey)) {
            $result .= 'name="'.$this->iniKey.'" ';
        }

        if (@$this->attributes['type'] === 'radio') {
            $result .=' id="'.$this->iniKey.($this->value === 'true' ? '1' : '0').'" ';
        } else if ($this->tagName !== 'label') {
            $result .= ' id="'.$this->iniKey.'" ';
        }

        foreach ($this->attributes as $key => $value) {
            $result .= $key.'="'.$value.'" ';
        }

        if ($this->isTagPaired) {
            $result .= ' required>'.($this->value ?? '').'</'.$this->tagName.'>';
        } else {
            $result .= (is_null($this->value) ? '' : 'value="'.$this->value.'"').' required />';
        }

        $result .= '</td>';
        if ($this->tagName !== 'label') {
            $result .= '</tr>';
        }
        return $result;
    }
}
