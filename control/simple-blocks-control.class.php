<?php

class SimpleBlocksControl 
{
    var $db;
    public function __construct() {
        //$db = 
    }

    // Get "simple_blocks_main"
    public static function Render_MainPage() {
        global $wpdb;
        
        $bloks_data = $wpdb->get_results('SELECT * FROM sb_blocks WHERE IsDeleted=false');
        if (count($bloks_data) == 0)
            echo render_control_template('nodata', 
            array(
                'alert_level' => 'primary',
                'alert_message' => 'Empty',
                'main_button_link' => '?page=simple_blocks_main/add_block',
                'main_button_title' => 'Add block'
            ));
            
        else {
            echo'
                
                <div class="container mt-3">
                <br>
                <div class="text-right">
                    <a class="btn btn-primary" href="?page=simple_blocks_main/add_block">Add block</a>
                </div>
                <div class="row">';
            foreach($bloks_data as $item) {
                
                $item = json_decode(json_encode($item), true);
                
                $item['action_text'] = 'Edit';
                $item['action_link'] = 'admin.php?page=simple_blocks_main/add_block&id='.$item['Id'];
                render_control_template('mainpage', $item);
                
            }
            echo '</div></div>';            
        }
    }

    // Get "simple_blocks_main/add_block"
    public static function Render_EditOrAddPage() {
        global $wpdb;
        $id = isset($_GET['id']) 
            ? intval($_GET['id'])
            : 0;
        
        if (isset($_POST['data']))
            self::EditOrAddPage();
        else {
            if ($id != 0) 
            {
                $block_data = $wpdb->get_results('SELECT * FROM sb_blocks WHERE `Id` = "'.$id.'"');
                $block_data = $block_data[0];
                //todo load block_data
                
                
                $data = array(
                    'title' => 'Edit block',
                    'item_id' => $id,
                    'item_body' => $block_data->Code,
                    'item_name' => $block_data->Name,
                    'item_secondname' => $block_data->SecondName,
                    'item_desc' => $block_data->Description,
                    'item_checked' => ($block_data->IsActive == 1) ? 'checked' : '',
                    'item_code' => 'Shortcode: [simple_block name=&#34;'.$block_data->Name.'&#34;]'
                );
                // load data on exist entity
                $content = render_control_template('edit_block', $data, true);

                //echo $content;
                
            } else {
                
                $data = array(
                    'title' => 'Create block', 
                    'item_id' => 0,
                    'item_body' => '',
                    'item_name' => '',
                    'item_secondname' => '',
                    'item_desc' => '',
                    'item_checked' => 'checked',
                    'item_code' => ''
                );
                $content = render_control_template('edit_block', $data, true);
                //echo $content;
                
            }
        }
    }

    static function GetRandomName($countChars) {
        $alphabet = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $countChars = intval($countChars);
        $name = '';
        if ($countChars < 3) $countChars = 3;
        for ($i =0; $i<$countChars; $i++) {
            $name .= $alphabet[rand(0,sizeof($alphabet)-1)];
        }
        return $name;
    }

    // Post "simple_blocks_main/add_block"
    static function EditOrAddPage() {
        global $wpdb;
        $secondName = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : "";
        $body = isset($_POST['body']) ? $_POST['body'] : "";
        $desc = isset($_POST['desc']) ? htmlspecialchars($_POST['desc']) : "";
        $isActive = isset($_POST['isActive']) ? intval($_POST['isActive']) : 0;
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $userId = get_current_user_id();
        $name = 'block_'.SimpleBlocksControl::GetRandomName(5);
         
        $isNew = ($id == 0) ? true : false;
        $sql = '';
        if ($isNew) 
        {
            $sql = 'INSERT INTO `sb_blocks` (`Name`, `SecondName`, `Description`, `Code`, `ChangeDateTime`, `IsActive`, `IsDeleted`, `UserCreatorId`) ';
            $sql .=' VALUES ("'.$name.'","'.$secondName.'", "'.$desc.'", "'.$body.'", CURRENT_TIMESTAMP, 1, 0, '.$userId.')';
        } else
        {
            $sql = 'UPDATE `sb_blocks` SET `SecondName` = "'.$secondName.'", `Description`="'.$desc.'", `Code`="'.$body.'", `ChangeDateTime`= CURRENT_TIMESTAMP, `IsActive` = '.$isActive.' WHERE `Id` = "'.$id.'"';
        }
        
        $wpdb->query($sql);

        SimpleBlocksControl::Render_MainPage();
    }


    

}