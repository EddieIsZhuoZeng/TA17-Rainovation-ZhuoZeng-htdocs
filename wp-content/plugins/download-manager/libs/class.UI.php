<?php
/**
 * User: shahnuralam
 * Date: 17/11/18
 * Time: 1:06 AM
 */

namespace WPDM;


use WPDM\Template;

class UI
{


    static function button($label, $attrs = []){
        $button = "<button";
        foreach ($attrs as $name => $val){
            $button .= " {$name}='$val'";
        }
        $button .= ">{$label}</button>";
        return $button;
    }

    static function card($heading = '', $content = [], $footer = '', $attrs = []){
        $template = new Template();
        return $template->assign('heading', $heading)
            ->assign('attrs', $attrs)
            ->assign('content', $content)
            ->assign('footer', $footer)
            ->fetch("tpls/ui-blocks/card.php", WPDM_BASE_DIR);
    }

    static function table($thead, $data, $css){
        $template = new Template();
        return $template->assign('thead', $thead)
            ->assign('data', $data)
            ->assign('css', $css)
            ->fetch("tpls/ui-blocks/table.php", WPDM_BASE_DIR);
    }

}
