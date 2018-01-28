<?php

namespace components;

class FieldInputs {

    protected function input($parentClasses, $parentAttrString, $label, $id, $class, $inputValue, $attrString, $input = 'input') {
        $return = <<< HTML
            <div class="form-element $parentClasses" $parentAttrString>
HTML;
        switch ($input) {
            case 'textarea':
                $return .= <<< HTML
                <textarea id="$id" class="$class" name="{$this->attribute}" $attrString>$inputValue</textarea>
HTML;
                break;
            case 'checkbox':
            case 'radio':
                $checked = $inputValue ? "checked" : "";
                $return .= <<< HTML
                <input type="{$this->type}" id="$id" class="$class" name="{$this->attribute}" $checked $attrString>
HTML;
                break;
            default:
                $return .= <<< HTML
                <input type="{$this->type}" id="$id" class="$class" name="{$this->attribute}" value="$inputValue" $attrString>
HTML;
                break;
        }
	    $return .= $this->addtionalContent;
        $return .= <<< HTML
                $label
            </div>
HTML;
        return $return;
    }

    protected function listCheckbox($parentClasses, $parentAttrString, $label, $values, $id, $class, $attrString) {
        $num = 0;
        $output = <<< HTML
                <div class="form-group field-{$this->attribute} $parentClasses" $parentAttrString>
                    $label
HTML;
        foreach ($values as $value => $labelText) {
            $checked = $this->model->{$this->attribute} === $value ? "checked" : "";
            $output .= <<< HTML
                    <div class="checkbox">
                        <label><input type="checkbox" id="{$id}_{$num}" class="$class" name="{$this->attribute}" value="$value" $checked $attrString> $labelText</label>
                    </div>
HTML;
            $num++;
        }
	    $output .= $this->addtionalContent;
        $output .= "</div>";
        return $output;
    }

    protected function listRadio($parentClasses, $parentAttrString, $label, $values, $id, $class, $attrString) {
        $num = 0;
        $output = <<< HTML
            <section class="field-{$this->attribute} $parentClasses" $parentAttrString>
                $label
HTML;
        foreach ($values as $value => $labelText) {
            $checked = $this->model->{$this->attribute} === $value ? "checked" : "";
            $output .= <<< HTML
                <div class="form-group">
                    <input type="radio" id="{$id}_{$num}" class="$class" name="{$this->attribute}" value="$value" $checked $attrString>
                    <label for="{$id}_{$num}">$labelText</label>
                </div>
HTML;
            $num++;
        }
	    $output .= $this->addtionalContent;
        $output .= "</section>";
        return $output;
    }
}