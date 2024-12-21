<?php

namespace Wikibots\Models;

class FormControl
{
    public function __construct(private string $tagName, private bool $isTagPaired, private array $attributes, private ?string $iniKey, private ?string $value) {}

    public function render() : string
    {
        $result = '<'.$this->tagName.' ';
        if (!is_null($this->iniKey)) {
            $result .= 'name="'.$this->iniKey.'" ';
        }

        if (@$this->attributes['type'] === 'radio') {
            $result .=' id="'.$this->iniKey.($this->value === 'true' ? '1' : '0').'" ';
        }

        foreach ($this->attributes as $key => $value) {
            $result .= $key.'="'.$value.'" ';
        }

        if ($this->isTagPaired) {
            $result .= '>'.($this->value ?? '').'</'.$this->tagName.'>';
        } else {
            $result .= (is_null($this->value) ? '' : 'value="'.$this->value.'"').' />';
        }
        return $result;
    }
}