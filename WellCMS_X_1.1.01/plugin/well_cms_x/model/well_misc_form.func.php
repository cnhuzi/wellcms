<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
 
// hook model_website_form_start.php

function well_form_radio($name, $arr, $checked = 0, $disabled = FALSE)
{
    empty($arr) && $arr = array(lang('no'), lang('yes'));
    $s = '';

    foreach ((array)$arr as $k => $v) {
        $add = $k == $checked ? ' checked="checked"' : '';
        $add .= $disabled == TRUE ? ' disabled' : '';
        $s .= "<label class=\"custom-input custom-radio\"><input type=\"radio\" name=\"$name\" value=\"$k\"$add /> $v</label> &nbsp; \r\n";
    }
    return $s;
}

function well_form_text($name, $value, $width = FALSE, $holdplacer = '')
{
    $style = '';
    if ($width !== FALSE) {
        is_numeric($width) AND $width .= 'px';
        $style = " style=\"width: $width\"";
    }
    $s = "<input type=\"text\" name=\"$name\" id=\"$name\" placeholder=\"$holdplacer\" value=\"$value\" class=\"form-control\"$style />";
    return $s;
}

function well_form_textarea($name, $value, $holdplacer = '', $width = FALSE, $height = FALSE)
{
    $style = '';
    if ($width !== FALSE) {
        is_numeric($width) AND $width .= 'px';
        is_numeric($height) AND $height .= 'px';
        $style = " style=\"width: $width; height: $height; \"";
    }
    $s = "<textarea name=\"$name\" id=\"$name\" placeholder=\"$holdplacer\" class=\"form-control\" $style>$value</textarea>";
    return $s;
}

// well_form_multi_checkbox('flag', array('1'=>'最新','2'=>'头条'), array('1','2'))
// name  选项内容  被选中选项(选项内容的键名)
function well_form_multi_checkbox($name, $arr, $checked = array())
{
    $s = '';
    foreach ($arr as $k => $v) {
        $ischecked = in_array($k, $checked) ? ' checked="checked"' : '';
        $_name = $name . '[' . $k . ']';
        $s .= "<label class=\"custom-input custom-checkbox pr-2 mr-1\"><input type=\"checkbox\" name=\"$_name\" value=\"$k\" $ischecked /> $v </label> ";
    }
    return $s;
}

function well_form_checkbox($name, $checked = 0, $txt = '')
{
    $ischecked = $checked ? ' checked="checked"' : '';
    $s = "<label class=\"custom-input custom-checkbox pr-1 mr-1\"><input type=\"checkbox\" name=\"$name\" value=\"1\" $ischecked /> $txt</label>";
    return $s;
}

function well_form_select($name, $arr, $checked = 0, $id = TRUE, $disabled = FALSE)
{
    if (empty($arr)) return '';
    $idadd = $id === TRUE ? "id=\"$name\"" : ($id ? "id=\"$id\"" : '');
    $add = $disabled == TRUE ? ' disabled="disabled"' : '';
    $s = "<select name=\"$name\" class=\"custom-select w-auto\" $idadd $add> \r\n";
    $s .= form_options($arr, $checked);
    $s .= "</select> \r\n";
    return $s;
}

// well_form_date('start', '2018-07-05') 为空则当前日期
function well_form_date($name, $value = 0, $width = FALSE)
{
    $style = '';
    if ($width !== FALSE) {
        is_numeric($width) AND $width .= 'px';
        $style = " style=\"width: $width\"";
    }
    $value = $value ? $value : date('Y-m-d');
    $s = "<input type=\"date\" name=\"$name\" id=\"$name\" class=\"form-control\" value=\"$value\" $style />";
    return $s;
}

// well_form_time('start', '18:00') 为空则当前时间
function well_form_time($name, $value = 0, $width = FALSE)
{
    $style = '';
    if ($width !== FALSE) {
        is_numeric($width) AND $width .= 'px';
        $style = " style=\"width: $width\"";
    }
    $value = $value ? $value : date('H:i');
    $s = "<input type=\"time\" name=\"$name\" id=\"$name\" class=\"form-control\" value=\"$value\" $style />";
    return $s;
}

// hook model_website_form_end.php

?>
