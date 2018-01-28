<?php

namespace components;

class Field extends FieldInputs {

    /**
     * @var \components\DatabaseConnector model
     */
    public $model;
    /**
     * @var string database attribute of element
     */
    public $attribute;
    /**
     * @var array $config
     */
    public $config;
    /**
     * @var Form form
     */
    public $form;

    /**
     * @var string label text
     */
    private $label = false;
    /**
     * @var array html attributes
     */
    private $htmlOptions = [];
    /**
     * @var bool for radio and checkbox list
     */
    private $loop = false;

    protected $messages = [];
    /**
     * @var string icon font awesome icon name
     */
    protected $icon = "pencil";
    /**
     * @var string type of input
     */
    protected $type = "text";

    protected $addtionalContent = "";

    public function __construct($form, $config) {
        $this->form = $form;
        $this->config = $config;
        $this->messages = [
            'success' => '',
            'error' => 'Incorrect input value',
        ];
    }

    public function __toString() {
        $id = $this->form->id . "-" . $this->attribute;
        $class = "";

        if ($this->type === 'picture') {
            $this->type = "file";
            $this->htmlOptions['accept'] = "image/*";
            $this->htmlOptions['class'] = "hidden";
            $txt = "";
            if (isset($this->htmlOptions['placeholder'])) {
	            $txt = $this->htmlOptions['placeholder'];
            }
            $this->addtionalContent = <<< HTML
            	<div class="input-{$this->attribute}"><span>{$txt}</span></div>
HTML;

        }

        if ($this->loop) {
            $values = $this->htmlOptions['values'];
            unset($this->htmlOptions['values']);
        } else {
            $class = "form-control";
        }

        $attrString = "";
        foreach ($this->htmlOptions as $attr => $value) {
            switch ($attr) {
                case "id":
                    $id = $value;
                    break;
                case "class":
                    $class = $value;
                    break;
                case "name":
                    break;
                default:
                    $attrString .= $attr . "='" . $value . "' ";
                    break;
            }
        }
        $attrString = substr($attrString, 0, -1);
        $label = "";
        if ($this->label) {
            $label = "<label for='$id' data-error='' data-success=''>{$this->label}</label>";
        }

        if ($this->form->inline) {
            $this->form->view->registerCss(<<< CSS
                div#$id {
                    display: inline-block;
                }
CSS
            );
        }
        $parentAttrString = "";
        foreach ($this->config as $attrName => $attrValue) {
            if ($attrName === 'class') {
                continue;
            }
            $parentAttrString .= $attrName . '="' . $attrValue . '"';
        }
        $parentClasses = $this->config['class'] ?? "";
        $inputValue = $this->model->{$this->attribute} ?? "";
        if ($this->loop && isset($values)) {
            $funcName = "list" . ucfirst($this->type);
            return $this->{$funcName}($parentClasses, $parentAttrString, $label, $values, $id, $class, $attrString);
        } else {
            switch ($this->type) {
                case 'password':
                case 'hidden':
                case 'text':
                case 'email':
                case 'file':
                    return $this->input($parentClasses, $parentAttrString, $label, $id, $class, $inputValue, $attrString);
                    break;
                case 'checkbox':
                    return $this->input($parentClasses, $parentAttrString, $label, $id, $class, $inputValue, $attrString, 'checkbox');
                    break;
                case 'radio':
                    return $this->input($parentClasses, $parentAttrString, $label, $id, $class, $inputValue, $attrString, 'radio');
                    break;
                case 'textarea':
                    return $this->input($parentClasses, $parentAttrString, $label, $id, $class, $inputValue, $attrString, 'textarea');
                    break;
                default:
                    $this->type = "text";
                    return $this->input($parentClasses, $parentAttrString, $label, $id, $class, $inputValue, $attrString);
                    break;
            }
        }
    }

    public function label(string $value) {
        if ($value) {
            $this->label = $value;
        } else {
            $this->label = false;
        }
        return $this;
    }

    public function passwordInput(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "password";
        $this->icon = "lock";
        return $this;
    }

    public function hiddenInput(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "hidden";
        $this->label(false);
        return $this;
    }

    public function textInput(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        return $this;
    }

    public function emailInput(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "email";
        return $this;
    }

    public function fileInput(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "file";
        return $this;
    }

    public function pictureInput(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "picture";
        return $this;
    }

    public function radioList(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "radio";
        $this->loop = true;
        return $this;
    }

    public function checkboxList(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "checkbox";
        $this->loop = true;
        return $this;
    }

    public function textArea(array $options = []) {
        $this->htmlOptions = array_merge($this->htmlOptions, $options);
        $this->type = "textarea";
        return $this;
    }
}