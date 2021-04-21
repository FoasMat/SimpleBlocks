<?php
/*
 * Plugin Name:       Simple Blocks
 * Plugin URI:        https://github.com/FoasMat/SimpleBlocks
 * Description:       A simple Wordpress plugin for editing chunks of content.
 * Version:           1.0.0
 * Author:            FoasMat Aleksey Vasiliev
 * Author URI:        https://github.com/FoasMat
 * Text Domain:       simple-blocks
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Copyright 2012     Aleksey Vasiliev (email : foasmat@ya.ru, telegram: foasmat)
*/

require_once(plugin_dir_path(__FILE__).'/control/simple-blocks-control.class.php');

// install hooks
function plugin_is_active() {
    global $wpdb;

    $sql_create_blocks = 'CREATE TABLE `sb_blocks` ( `Id` INT NOT NULL AUTO_INCREMENT, `SecondName` VARCHAR(64) NOT NULL, `Name` VARCHAR(64) NOT NULL , `Description` VARCHAR(256) NULL , `Code` VARCHAR(4096) NOT NULL , `ChangeDateTime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , `IsActive` BOOLEAN NOT NULL DEFAULT TRUE , `IsDeleted` BOOLEAN NOT NULL DEFAULT FALSE , `UserCreatorId` INT NOT NULL, `IdName` VARCHAR(32) NOT NULL , PRIMARY KEY (`Id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;';
    $wpdb->query($sql_create_blocks);
}
register_activation_hook(__FILE__, 'plugin_is_active');

// unintsll hooks
function plugin_is_deactivate() {
    global $wpdb;

    $wpdb->query('DROP TABLE `sb_blocks`');
}
register_deactivation_hook(__FILE__, 'plugin_is_deactivate');



// add to menu page

function add_to_menu() 
{        
    add_menu_page(
        'Simple blocks',
        'Simple blocks',
        'administrator',
        'simple_blocks_main',
        array('SimpleBlocksControl', 'Render_MainPage'),
        'dashicons-block-default',
        21
    );

    add_submenu_page(
        'simple_blocks_main', 
        'Add block', 
        'Add block',
        'administrator',
        'simple_blocks_main/add_block',
        array('SimpleBlocksControl', 'Render_EditOrAddPage'),
        1)
    ;
}
add_action('admin_menu', 'add_to_menu');

add_action('admin_print_styles', 'init_controlStyles');
add_action('admin_print_scripts', 'init_controlScripts');
add_shortcode('simple_block', 'SimpleBlock_ShortcodeRegister');


function init_controlStyles() {
    wp_enqueue_style('bootstrap');
}
function init_controlScripts() {
    wp_enqueue_scripts('jquery');
    wp_enqueue_scripts('bootstrap');
}

function SimpleBlock_ShortcodeRegister($attr, $content, $tag) {
    global $wpdb;
    $name = (isset($attr['name'])) ? $attr['name'] : '';
    
    if ($name != '') {
        $sql = 'SELECT * FROM sb_blocks WHERE `Name` = "'.$name.'" AND `IsActive` = 1 AND `IsDeleted` = 0';
        
        $data = $wpdb->get_results($sql);
        
        if (isset($data[0]))
            return $data[0]->Code;
    }
    
    return '';
}
function globalInit() {
    
}

function render_control_template($templateName, $dataToChange, $hasExistedPhp = false) {
    $filePath = plugin_dir_path(__FILE__).'control/templates/'.$templateName.'.tmp.php';
    if (!file_exists($filePath)) {
        return "Error: not found template ".$templateName.' on path: '.$filePath;
    }
    
    $contentFile = file_get_contents($filePath);
    $replacedContent = preg_replace_callback('/\{\{\S{1,32}\}\}/', 
        function ($str) use ($dataToChange)
        {
            
            $strFinded = "";
            if (isset($str[0])) {
                $strFinded = str_replace('{{','',$str[0]);
                $strFinded = str_replace('}}','',$strFinded);         
            }            
            if (isset($dataToChange[$strFinded]))
                return $dataToChange[$strFinded];
            else
                return "not found ".$strFinded;
        }, $contentFile);

    if ($hasExistedPhp)
        eval("?> $replacedContent <?php ");
    else 
        echo $replacedContent;
    
}

// function simple_block_main() {

//     require_once plugin_dir_path(__FILE__) . 'control/simple-blocks-control.class.php';
    
//     $control = new SimpleBlocksControl();
//     $control->Render_MainPage();
// }

