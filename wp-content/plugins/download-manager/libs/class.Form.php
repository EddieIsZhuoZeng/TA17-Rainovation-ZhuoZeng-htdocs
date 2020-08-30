<?php


namespace WPDM;


class Form
{
    private $formFields;
    private $method = 'post';
    private $action = '';
    private $name = 'form';
    private $id = 'form';
    private $class = 'form';
    public  $submit_button = [];
    public  $error = '';
    public  $noForm = false;

    function __construct($formFields, $attrs = array())
    {
        if(!isset($attrs['id'])){
            $this->error = '<div class="alert alert-danger">Form ID is require, field id is missing in $attrs<br/><pre style="border-radius: 0;margin-top: 10px;margin-bottom: 5px">'.print_r($attrs, 1).'</pre></div>';
            return;
        }
        $this->formFields = apply_filters("{$attrs['id']}_fields", $formFields, $attrs);
        foreach ($attrs as $name => $value){
            $this->$name = $value;
        }
    }

    function div($class = '', $id = ''){
        return "<div class='{$class}' id='row_{$id}'>";
    }
    function divClose(){
        return "</div>";
    }

    function label($label, $for = ''){
        return "<label form='{$for}'>{$label}</label>";
    }

    function row($id, $fields){
         $row = $this->div("form-row", $id);
         if(isset($fields['label']))
             $this->label($fields['label'], $id);

         foreach ($fields['cols'] as $id => $field){
             $row .= $this->formGroup($id, $field);
         }

         $row .= $this->divClose();
         return $row;
    }

    function formGroup($id, $field){
        $grid_class = isset($field['grid_class'])?$field['grid_class']:'';
        $field_html = $this->div("form-group {$grid_class}", $id);
        $type = $field['type'];
        $field_html .= $this->div("input-wrapper {$type}-input-wrapper", $id."_wrapper");
        if(isset($field['label']))
            $field_html .= $this->label($field['label'], $id);
        $input = $this->$type($field['attrs']);
        if(in_array($type, ['reCaptcha', 'hidden'])) return $input;

        $prepend = isset($field['prepend']) ? $field['prepend'] : null;
        $append = isset($field['append']) ? $field['append'] : null;
        $input = $this->inputGroup($input, $prepend, $append);
        $field_html .= $input;
        $field_html .= $this->divClose();
        $field_html .= $this->divClose();
        return $field_html;
    }

    function inputGroup($input, $prepend = null, $append = null){
        if(!$prepend && !$prepend) return $input;
        $input_group = "<div class='input-group'>";
        $input_group .= $prepend ? "<div class='input-group-prepend'><span class='input-group-text'>{$prepend}</span></div>" : "";
        $input_group .= $input;
        $input_group .= $append ? "<div class='input-group-append'><span class='input-group-text'>{$append}</span></div>" : "";
        $input_group .= "</div>";
        return $input_group;
    }

    function heading($attrs){
        $_attrs = "";
        $text = $attrs['text'];
        unset($attrs['text']);
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<div class=''>{$text}</div>";
    }

    function hidden($attrs){
        $_attrs = "";
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='hidden' $_attrs />";
        return $text;
    }

    function text($attrs){
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control ".$attrs['class']: "form-control";
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        $text = "<input type='text' $_attrs />";
        return $text;
    }

    function email($attrs){
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control ".$attrs['class']: "form-control";
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<input type='email' $_attrs />";
    }

    function password($attrs){
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control ".$attrs['class']: "form-control";
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        return "<input type='password' $_attrs />";
    }

    function select($attrs){
        $_attrs = "";
        $attrs['class'] = isset($attrs['class']) ? "form-control ".$attrs['class']: "form-control";
        $options = $attrs['options'];
        unset($attrs['options']);
        foreach ($attrs as $key => $value){
            $_attrs .= "{$key}='{$value}' ";
        }
        $_options = "";
        foreach ($options as $value => $label){
            $_options .= "<option value='{$value}'>{$label}</option>\r\n";
        }
        return "<select $_attrs>\r\n{$_options}\r\n</select>";
    }

    function reCaptcha($attrs){
        ob_start();
        ?>
        <div class="form-group row">
            <div class="col-sm-12">
                <input type="hidden" id="<?php echo $attrs['id'] ?>" name="<?php echo $attrs['name'] ?>" value=""/>
                <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
                        async defer></script>
                <div id="<?php echo $attrs['id'] ?>_field"></div>
                <style>
                    #<?php echo $attrs['id'] ?>_field iframe{ transform: scale(1.16); margin-left: 24px; margin-top: 5px; margin-bottom: 5px; }
                    #<?php echo $attrs['id'] ?>_field{ padding-bottom: 10px !important; }
                </style>
                <script type="text/javascript">
                    var verifyCallback = function (response) {
                        jQuery('#<?php echo $attrs['id'] ?>').val(response);
                    };
                    var widgetId2;
                    var onloadCallback = function () {
                        grecaptcha.render('<?php echo $attrs['id'] ?>_field', {
                            'sitekey': '<?php echo get_option('_wpdm_recaptcha_site_key'); ?>',
                            'callback': verifyCallback,
                            'theme': 'light'
                        });
                    };
                </script>
            </div>

        </div>
        <?php
        $captcha = ob_get_clean();
        return $captcha;
    }

    function render(){
        if($this->error) return $this->error;
        $form_html = $this->noForm ? "" : "<form method='{$this->method}' action='{$this->action}' name='{$this->name}' id='{$this->id}' class='{$this->class}'>";
        $before_form_fields = "";
        $form_html .= apply_filters("{$this->id}_before_fields", $before_form_fields, $this);
        foreach ($this->formFields as $id => $field){
            if(isset($field['cols']))
                $form_html .= $this->row($id, $field);
            else
                $form_html .= $this->formGroup($id, $field);
        }
        if($this->submit_button){
            $form_html .= "<button class='{$this->submit_button['class']}'>{$this->submit_button['label']}</button>";
        }
        $after_form_fields = "";
        $form_html .= apply_filters("{$this->id}_before_fields", $after_form_fields, $this);
        $form_html .= $this->noForm ? "" : "</form>";
        return $form_html;
    }
}
