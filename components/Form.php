<?php
namespace components;

class Form {

    public $inline = false;
    public $view;
    public $id;

    private static $model;
    private $attribute;

    public function __construct($view, $inline) {
        $this->view = $view;
        self::$model = $view->model ?? null;
        $this->inline = $inline;
    }

    public function field(DatabaseConnector $model, string $attribute, array $config = []) {
        $field = new Field($this, $config);
        $field->model = $model;
        $field->attribute = $attribute;
        $labels = $model->attributeLabels();
        if (isset($labels[$attribute])) {
            $label = $labels[$attribute];
        } else {
            $label = ucfirst($attribute);
        }
        $field->label($label);
        return $field;
    }

    public function submitBtn(string $text = null, $bpClass = 'info', array $attributes = []) {
        $text = $text ?? "Save";
        $attrString = "";
        foreach ($attributes as $attrName => $attrValue) {
            if ($attrName === 'class' || $attrName === 'type') {
                continue;
            }
            $attrString .= $attrName . '="' . $attrValue . "' ";
        }
        $additionalClass = $attributes['class'] ?? "";
        $id = $attributes['id'] ?? "submitBtn";
        if ($this->inline) {
            $this->view->registerCss(<<< CSS
                button#$id {
                    display: inline-block;
                }
CSS
            );
        }
        return "<button id='$id' type='submit' class='btn btn-$bpClass $additionalClass' $attrString>$text</button>";
    }

    /**
     * @param Controller $view
     * @param array $config
     * @return Form
     */
    public static function begin(Controller $view, array $config = []) {
        /* default values */
        $defaultConfig = [
            'action' => "",
            'method' => "post",
            'id' => "",
            'inline' => false,
        ];

        $config = array_merge($defaultConfig, $config);
        foreach ($defaultConfig as $key => $val) {
            ${$key} = $config[$key];
            unset($config[$key]);
        }

        $form = new self($view, $inline);
        $form->id = $id;

        $attrString = "";
        if (isset($config['fieldOptions'])) {
            foreach ($config['fieldOptions'] as $attrName => $attrValue) {
                $attrString .= $attrName . '="' . $attrValue . '"';
            }
        }

        if ($inline) {
            $view->registerCss(<<< CSS
                form#$id {
                    display: inline-block;
                }
CSS
            );
        }
        echo "<form id='$id' action='$action' method='$method' enctype='multipart/form-data' $attrString>";
        return $form;
    }

    public static function end() {
        echo "</form>";
    }
}