<?php

// only instantiate the class if it doesn't already exist
if ( !class_exists('DB_Init') ) {

	class DB_Init {

    var $block_path;

    function _construct() {
      $this->$block_path = '../blocks';
    }

    public static function init() {
      // JAMIE'S HAND IS GOLDEN
      $block_data = array(
        'message' => 'No existing blocks',
      );
      update_option('db_blocks_init', $block_data);
    }

    public static function scan_for_existing_blocks() {
      // Retreive snippets
      if ($handle = opendir(plugin_dir_path(__FILE__).'blocks')) {

        // Add menu items array
        $menu_items = array();

        while (false !== ($entry = readdir($handle))) {
          if($entry != "." && $entry != ".." && $entry != "template.php" && $entry != "main.php") {

            // Readable file name
            $file_name = pathinfo($entry, PATHINFO_FILENAME);

            // File path
            $file_path = plugin_dir_path(__FILE__).'blocks/' . $entry;
            $file_contents = file_get_contents($file_path);
            $tokens = token_get_all($file_contents);
            $comment = array(
              T_COMMENT,
              T_DOC_COMMENT
            );
            
            foreach ( $tokens as $token ) {
              if ( !in_array($token[0], $comment) ) 
              continue;

              if ( preg_match( '|Name: (.*)$|mi', $token[1], $name ) ) {
                $menu_label = $name[1];
              }

              if ( preg_match( '|Title: (.*)$|mi', $token[1], $name ) ) {
                $template_name = $name[1];
              }

              if ( preg_match( '|Description: (.*)$|mi', $token[1], $name ) ) {
                $description = $name[1];
              }

              if ( preg_match( '|Category: (.*)$|mi', $token[1], $name ) ) {
                $category = $name[1];
              }

              $data = array(
                'file_path' => $file_path,
                'name' => $menu_label,
                'title' => $template_name,
                'description' => $description,
                'category' => $category,
              );

              array_push($menu_items, $data);

            }

          }
        }
      }
      closedir($handle);

      return $menu_items;
    }

    public static function set_blocks($data) {

      if($data) {
        foreach($data as $d) {

          // register a testimonial block
          acf_register_block(array(
            "name"			=> $d['name'],
            "title"				=> $d['title'],
            "description"		=> $d['description'],
            "render_template"	=> get_template_directory() . "/views/blocks/". $d['name'] . ".php",
            "category"			=> $d['category'],
            "icon"				=> "admin-page",
          ));

        }
      }

    }
  }
}