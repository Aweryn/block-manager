<?php 
function create_new_block() {

  $title = $_POST['inputTitle'];
  $name = $_POST['inputName'];
  $desc = $_POST['inputDesc'];
  $cat = $_POST['inputCat'];

  $data = array(
    'title' => $title,
    'name' => $name,
    'description' => $desc,
    'category' => array($cat),
    'message' => '',
    'error' => '',
  );

  $block_name = sanitize_title($name);
  $block_content = '
  <?php
  /**
   * name: '.$name.'
   * title: '.$title.'
   * description: '.$desc.'
   * category: '.$cat.'
   */
   ?>';

  // Create file
  $dir = WP_PLUGIN_DIR.'/db-block-manager/inc/blocks';

  if (!file_exists('blocks')) {
      mkdir('blocks', 0777, true);
  }

  $block_file = $dir . '/' . $block_name . '.block.php';

  //unlink($block_file);

  if(file_exists($block_file)) {
    $data['error'] = __('File already exists');
  } else {
    file_put_contents($block_file, $block_content, FILE_APPEND | 'LOCK_EX' );
  }

  return $data;
}
add_action('wp_ajax_create_new_block', 'create_new_block');

add_action('wp_ajax_block_delete', 'block_delete');

function block_delete() {

  $block_name = $_POST['block_name'];

  // Create file
  $dir = WP_PLUGIN_DIR.'/db-block-manager/inc/blocks';

  if (!file_exists('blocks')) {
      mkdir('blocks', 0777, true);
  }

  $block_file = $dir . '/' . $block_name . '.block.php';

  unlink($block_file);

  return;
}
?>