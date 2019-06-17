<?php
/*
	Plugin Name: DB Block Manager
	Plugin URI: http://donbranco.fi/
	Description: Plugin to simplify block management
	Text Domain: db-block-manager
	Author: Jere Hirvonen
	Author URI: http://donbranco.fi
	Version: 1.0.0
	Tested up to: 5.2
	License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // exit if accessed directly!
}

// require_once(plugin_dir_path(__FILE__).'ns-sidebar/ns-sidebar.php');

require_once(plugin_dir_path(__FILE__).'inc/admin-init.php');
require_once(plugin_dir_path(__FILE__).'inc/modals.php');
require_once(plugin_dir_path(__FILE__).'inc/ajax.php');

// TODO: rename this class
class DB_Block_Manager {
	
	var $path; 				// path to plugin dir
	var $wp_plugin_page; 	// url to plugin page on wp.org
	var $ns_plugin_page; 	// url to pro plugin page on ns.it
	var $ns_plugin_name; 	// friendly name of this plugin for re-use throughout
	var $ns_plugin_menu; 	// friendly menu title for re-use throughout
	var $ns_plugin_slug; 	// slug name of this plugin for re-use throughout
	var $ns_plugin_ref; 	// reference name of the plugin for re-use throughout
	
	function __construct(){		
		$this->path = plugin_dir_path( __FILE__ );
		// TODO: update to actual
		$this->wp_plugin_page = "http://wordpress.org/plugins/ns-wordpress-plugin-template";
		// TODO: update to link builder generated URL or other public page or redirect
		$this->ns_plugin_page = "http://neversettle.it/";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_name = "DB Block Manager";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_menu = "DB Block Manager";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_slug = "db-block-manager";
		// TODO: update this - used throughout plugin code and only have to update here
		$this->ns_plugin_ref = "db_block_manager";
		
		add_action( 'plugins_loaded', array($this, 'setup_plugin') );
		add_action( 'admin_notices', array($this,'admin_notices'), 11 );
		add_action( 'network_admin_notices', array($this, 'admin_notices'), 11 );		
		add_action( 'admin_init', array($this,'register_settings_fields') );		
		add_action( 'admin_menu', array($this,'register_settings_page'), 20 );
		add_action( 'admin_enqueue_scripts', array($this, 'admin_assets') );
		
		// TODO: uncomment this if you want to add custom JS 
		// add_action( 'admin_print_footer_scripts', array($this, 'add_javascript'), 100 );

		add_action('acf/init', array($this, 'init_block_files'));
		
		// TODO: uncomment this if you want to add custom actions to run on deactivation
		//register_deactivation_hook( __FILE__, array($this, 'deactivate_plugin_actions') );
		register_activation_hook(__FILE__, array($this, 'activate_plugin_actions'));
	}

	function deactivate_plugin_actions(){
		// TODO: add any deactivation actions here
	}

	function init_block_files() {

	// check function exists
	if( function_exists('acf_register_block') ) {
		$blocks = DB_Init::scan_for_existing_blocks();
		DB_Init::set_blocks($blocks);
	}

	}
	
	/*********************************
	 * NOTICES & LOCALIZATION
	 */
	function activate_plugin_actions() {
		DB_Init::init();

	}
	 
	 function setup_plugin(){
		 load_plugin_textdomain( $this->ns_plugin_slug, false, $this->path."lang/" ); 
	 }
	
	function admin_notices(){
		$message = '';	
		if ( $message != '' ) {
			echo "<div class='updated'><p>$message</p></div>";
		}
	}

	function admin_assets($page){
	 	wp_register_style( $this->ns_plugin_slug, plugins_url("css/main.css",__FILE__), false, '1.0.0' );
		wp_register_script( $this->ns_plugin_slug, plugins_url("js/admin.js",__FILE__), false, '1.0.0' );

		wp_register_script( 'popper.js', plugins_url("js/popper.min.js",__FILE__), false, '1.0.0' );

		wp_register_style( 'bootstrap.css', plugins_url("node_modules/bootstrap/dist/css/bootstrap.min.css",__FILE__), false, '1.0.0' );
		wp_register_script( 'bootstrap.js', plugins_url("node_modules/bootstrap/dist/js/bootstrap.min.js",__FILE__), false, '1.0.0' );

		global $pagenow;
		if(($pagenow == 'admin.php') && (get_current_screen()->base == 'toplevel_page_db-block-manager')) {

			wp_enqueue_script( 'popper.js' );

			wp_enqueue_style( 'bootstrap.css' );
			wp_enqueue_script( 'bootstrap.js' );
		}

		wp_enqueue_style( $this->ns_plugin_slug );
		wp_enqueue_script( $this->ns_plugin_slug );
	}
	
	/**********************************
	 * SETTINGS PAGE
	 */
	
	function register_settings_fields() {
		// TODO: might want to update / add additional sections and their names, if so update 'default' in add_settings_field too
		add_settings_section( 
			$this->ns_plugin_ref.'_set_section', 	// ID used to identify this section and with which to register options
			$this->ns_plugin_name, 					// Title to be displayed on the administration page
			false, 									// Callback used to render the description of the section
			$this->ns_plugin_ref 					// Page on which to add this section of options
		);
		// TODO: update labels etc.
		// TODO: for each field or field set repeat this
		add_settings_field( 
			$this->ns_plugin_ref.'_field1', 	// ID used to identify the field
			'Setting Name', 					// The label to the left of the option interface element
			array($this,'show_settings_field'), // The name of the function responsible for rendering the option interface
			$this->ns_plugin_ref, 				// The page on which this option will be displayed
			$this->ns_plugin_ref.'_set_section',// The name of the section to which this field belongs
			array( 								// args to pass to the callback function rendering the option interface
				'field_name' => $this->ns_plugin_ref.'_field1'
			)
		);
		register_setting( $this->ns_plugin_ref, $this->ns_plugin_ref.'_field1');
	}	

	function show_settings_field($args){
		$saved_value = get_option( $args['field_name'] );
		// initialize in case there are no existing options
		if ( empty($saved_value) ) {
			echo '<input type="text" name="' . $args['field_name'] . '" value="Setting Value" /><br/>';
		} else {
			echo '<input type="text" name="' . $args['field_name'] . '" value="'.$saved_value.'" /><br/>';
		}
	}

	function register_settings_page(){
		add_menu_page(
			__('DB Block Manager', 'db-block-manager'),
			'Block Manager',
			'manage_options',
			'db-block-manager',
			array($this, 'db_block_manager'),
			'',
			1
		);

		add_submenu_page(
			'db-block-manager',
			'Blocks',
			'All blocks',
			'manage_options',
			'edit.php?post_type=wp_block'
		);
	}

	function db_block_manager() {
		$blocks = DB_Init::scan_for_existing_blocks();
		// echo '<pre>';
		// print_r($blocks); 
		// echo '</pre>';
		$block_data = get_option('db_blocks_init');
		?>
		
		<div class="content-wrap">

			<section id="block-nav">
				<nav class="nav">
					<ul>
						<li><a href="#" data-toggle="modal" data-target="#modal-newblock">New block</a></li>
					</ul>
				</nav>
			</section>

			<div class="blocks-wrapper">
				<div class="block-list">
				<!-- Content -->

	<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Block Title</th>
      <th scope="col">Block name <i data-toggle="tooltip" data-placement="top" title="Create template for file [NAME].php">?</i></th>
      <th scope="col">Actions</th>
    </tr>
  </thead>
  <tbody>
			<?php
			$count = 0;
			foreach($blocks as $b) {
				$count++;
				?>
				<tr>
					<th scope="row"><?= $count; ?></th>
					<td><?= $b['title']; ?></td>
					<td><?= $b['name']; ?></td>
					<td><button type="button" class="btn btn-link db_delete_block" data-name="<?= $b['name']; ?>" action="<?= admin_url('admin-ajax.php'); ?>">Delete</button></td>
				</tr>
				<?php
			} ?>
  </tbody>
</table>

				</div>
			</div>

		</div>

		<?php
		// Hook to render modals
		do_action('render_modal_new_block'); ?>

		<?php
	}
	
	function show_settings_page(){
		?>
		<div class="wrap">
			
			<h2><?php $this->plugin_image( 'banner.png', __('ALT') ); ?></h2>
			
			<!-- BEGIN Left Column -->
			<div class="ns-col-left">
				<form method="POST" action="options.php" style="width: 100%;">
					<?php settings_fields($this->ns_plugin_ref); ?>
					<?php do_settings_sections($this->ns_plugin_ref); ?>
					<?php submit_button(); ?>
				</form>
			</div>
			<!-- END Left Column -->
						
			<!-- BEGIN Right Column -->			
			<div class="ns-col-right">
				<h3>Thanks for using <?php echo $this->ns_plugin_name; ?></h3>
				<?php ns_sidebar::widget( 'subscribe' ); ?>
				<?php ns_sidebar::widget( 'share', array('plugin_url'=>'http://neversettle.it/buy/wordpress-plugins/ns-fba-for-woocommerce/','plugin_desc'=>'Connect WordPress to Google Sheets!','text'=>'Would anyone else you know enjoy NS Google Sheets Connector?') ); ?>
				<?php ns_sidebar::widget( 'donate' ); ?>
				<?php ns_sidebar::widget( 'featured'); ?>
				<?php ns_sidebar::widget( 'links', array('ns-fba') ); ?>
				<?php ns_sidebar::widget( 'random'); ?>
				<?php ns_sidebar::widget( 'support' ); ?>
			</div>
			<!-- END Right Column -->
				
		</div>
		<?php
	}
	
	
	/*************************************
	 * FUNCTIONALITY
	 */
	
	// TODO: add additional necessary functions here
	
	/*************************************
	 * UITILITY
	 */
	 
	 function plugin_image( $filename, $alt='', $class='' ){
	 	echo "<img src='".plugins_url("/images/$filename",__FILE__)."' alt='$alt' class='$class' />";
	 }
	
}

new DB_Block_Manager();
