<?php 
/*
Plugin Name: WP Spry Menu
Plugin URI: http://takien.com
Description: Automatically create spry dropdown menu for Wordpress Category
Author: takien
Version: 1.5.2
Author URI: http://takien.com
*/

/* WP Spry Menu */
/**
 * Create Spry Dropdown Menu of Wordpress categories.
 *
*/


if(!defined('ABSPATH')) die();

if (!class_exists('WPSpryMenu')) {
	class WPSpryMenu {
		
		var $plugin_version = '1.5.2';
		var $plugin_domain  = 'wp-spry-menu';
		var $plugin_slug    = 'wp-spry-menu';
		var $option_group   = 'wp_spry_menu_settings';
		
		function WPSpryMenu(){
			$this->__construct();
		}
		
		function __construct(){
			register_activation_hook(__FILE__,array(&$this,'wp_spry_menu_install'));
			add_action('init', 		 array(&$this,'wp_spry_menu_init'));
			add_shortcode('wp_spry_menu',  array(&$this,'wp_spry_menu_short_code'));
			add_action('admin_enqueue_scripts', array(&$this,'wp_spry_menu_style_and_script'));
			add_action('wp_enqueue_scripts', array(&$this,'wp_spry_menu_style_and_script'));
			add_action('easy_option_'.$this->plugin_slug.'_before_form', array(&$this,'before_form'));
			add_action('easy_option_'.$this->plugin_slug.'_after_form', array(&$this,'after_form'));
			add_filter('widget_text', 'do_shortcode');
		}
		
		function wp_spry_menu_install() {
			/*
			 * Old setting, make sure old settings (ver <= 1.0.3) are imported and removed after it.
			 */ 
			$oldsetting = Array(
				'direction',
				'home_text',
				'depth',
				'exclude',
				'orderby',
				'hide_empty',
				'child_of',
				'cattitle',
				'styleedit',
				'parent_border_width',
				'parent_border_style',
				'parent_border_color',
				'child_border_width',
				'child_border_style',
				'child_border_color',
				'bgcolor',
				'txcolor',
				'bghover',
				'txhover',
				'theme'
			);

			$new_setting = Array();
			//assume this two options are owned by wp spry menu exclusively.
			if( get_option('parent_border_width') AND get_option('child_border_width') ) {
				
				foreach($oldsetting as $olds) {
					if(get_option($olds)) {
						$new_setting[$olds] = get_option($olds);
					}
				}
				//import to new setting 
				if(update_option( $this->option_group,$new_setting )) {
				
					//delete old setting
					foreach($oldsetting as $olds) {
						delete_option($olds);
					}
				}
			}
		}
		
		function wp_spry_menu_init() {
			$this->options();
		}
		
		function options($return = false) {
			require_once(dirname(__FILE__).'/inc/options/takien-easy-options.php');
			
			$option = new WPSpryMenuOptions;
			
			$option->option_group  	      = $this->option_group;;
			$option->option_menu_name     = 'Spry Menu';
			$option->option_menu_slug     = $this->plugin_slug;
			$option->option_menu_location = 'add_theme_page';
			$option->option_icon_big = plugins_url( '/inc/options/images/icon-large.png', __FILE__ );
			$option->option_icon_small    = plugins_url( '/inc/options/images/icon-small.png', __FILE__ );
			$option->option_add_tab       = false;
			$option->option_menu_position = 100;
			
			if($return) {
				return $option->option($return);
			}
			
			$fields	= Array(
				Array(
					'name'         => 'direction',
					'label'        => __('Direction',$this->plugin_domain),
					'type'         => 'select',
					'description'  => __('Choose menu direction',$this->plugin_domain),
					'value'        => $option->option('direction'),
					'values'       => Array(
						'verticalr'  => __('Vertical Drop Right',$this->plugin_domain),
						'verticall'  => __('Vertical Drop Left',$this->plugin_domain),
						'horizontal' => __('Horizontal',$this->plugin_domain)
						)
				),
				Array(
					'name'         => 'home_text',
					'label'        => __('Home Text',$this->plugin_domain),
					'type'         => 'text',
					'description'  => __('Text for Home link, eg. Home',$this->plugin_domain),
					'value'        => $option->option('home_text')
				),
				Array(
					'name'         => 'depth',
					'label'        => __('Depth',$this->plugin_domain),
					'type'         => 'text',
					'description'  => __('Depth of Child Category will be displayed, default 3 levels',$this->plugin_domain),
					'value'        => $option->option('depth')
				),
				Array(
					'name'         => 'exclude',
					'label'        => __('Exclude',$this->plugin_domain),
					'type'         => 'text',
					'description'  => __('ID of excluded Categories, separate it by comma',$this->plugin_domain),
					'value'        => $option->option('exclude')
				),
			Array(
				'name'         => 'orderby',
				'label'        => __('Order by',$this->plugin_domain),
				'type'         => 'select',
				'description'  => __('Order by Name, ID or slug',$this->plugin_domain),
				'value'        => $option->option('orderby'),
				'values'       => Array(
					'name'       => __('Name',$this->plugin_domain),
					'ID'         => __('ID',$this->plugin_domain),
					'slug'       => __('Slug',$this->plugin_domain),
					'count'      => __('Count',$this->plugin_domain),
					'term_group' => __('Term Group',$this->plugin_domain),
				)
			),
			Array(
				'name'         => 'hide_empty',
				'label'        => __('Hide empty',$this->plugin_domain),
				'type'         => 'checkbox',
				'description'  => __('Hide empty',$this->plugin_domain),
				'value'        => $option->option('hide_empty')
			),	
			Array(
				'name'         => 'child_of',
				'label'        => __('Child of',$this->plugin_domain),
				'type'         => 'text',
				'description'  => __('Use category ID',$this->plugin_domain),
				'value'        => $option->option('child_of')
			),
			Array(
				'name'         => 'cattitle',
				'label'        => __('Category title',$this->plugin_domain),
				'type'         => 'text',
				'description'  => __('Link title when hover',$this->plugin_domain),
				'value'        => $option->option('cattitle')
			),
			Array(
				'name'         => 'theme',
				'label'        => __('Theme',$this->plugin_domain),
				'type'         => 'select',
				'description'  => __('Menu theme',$this->plugin_domain),
				'value'        => $option->option('theme'),
				'values'       => array_combine($this->themes(),$this->themes()),
			),
			);
			$option->option_fields = $fields;	
			
		}
		function themes(){
			/**
			 * delete theme cache on setting updated.
			 */

			if( isset($_GET['page']) AND ( 'wp-spry-menu' == $_GET['page']) AND isset($_GET['settings-updated']) ) {
				delete_transient('wp_spry_menu_themes');
			}
			
			if(!function_exists('scandir')){
				return Array('default');
			}
			if ( false === ( $themes = get_transient( 'wp_spry_menu_themes' ) )) {
				
				$dir = plugin_dir_path(__FILE__).'themes/';
				$dirs = scandir($dir);
				foreach($dirs as $d){
					if( (substr($d,0,1) !=='.') AND (is_dir($dir.$d)) ) {
						$themes[] = $d;
					}
				}
				set_transient( 'wp_spry_menu_themes', $themes , 86400 );
			}
			return $themes;
		}
		
		function before_form() { ?>
			
			<div style="width:49%;float:left">
				<h3>Global setting</h3>
				<p>This global setting will be used if you do not pass argument to the function call or short code call.</p>
				<p>Note: Theme CAN NOT be overriden by function/shortcode parameter</p>
			<?php
		}
		function after_form() { ?>
			</div>
			<div style="width:49%;float:left">
			<h3>Preview</h3>
			<div id="spry-preview">
				<?php if ( function_exists('wp_spry_menu') ) wp_spry_menu();?>
			</div>
			<h3>Installation</h3>
			<p>
			Paste the following code to your Wordpress Theme file, eg. somewhere inside your theme file where you want to place dropdown menu.</p>
			<ul>
				<li>Use <strong>Function call</strong> to paste into theme file, or</li>
				<li>Use <strong>Shortcode call</strong> to paste into post/page or <strong>Text Widget</strong>.</li>
			</ul>
			
					
			<table style="width:100%" class="wp-list-table widefat">
				<tr>
					<th style="width:150px">Parameter</th><th>Function Call</th><th style="width:20px">or</th><th>Shortcode Call</th>
				</tr>
				<tr>
					<td>Default</td>
					<td>
						<textarea><?php echo "<?php if ( function_exists('wp_spry_menu') ) wp_spry_menu();?>" ?></textarea>
						</td><td></td><td>
						<textarea>[wp_spry_menu /]</textarea>
					</td>
				</tr>
				<tr>
					<td>Direction Horizontal</td>
					<td>
						<textarea><?php echo "<?php if ( function_exists('wp_spry_menu') ) wp_spry_menu('direction=horizontal');?>" ?></textarea>
						</td><td></td><td>
						<textarea>[wp_spry_menu direction="horizontal" /]</textarea>
					</td>
				</tr>
				<tr>
					<td>Direction Vertical drop to right</td>
					<td>
						<textarea><?php echo "<?php if ( function_exists('wp_spry_menu') ) wp_spry_menu('direction=verticalr');?>" ?></textarea>
						</td><td></td><td>
						<textarea>[wp_spry_menu direction="verticalr" /]</textarea>
					</td>
				</tr>
				<tr>
					<td>Direction Vertical drop to left</td>
					<td>
						<textarea><?php echo "<?php if ( function_exists('wp_spry_menu') ) wp_spry_menu('direction=verticall');?>" ?></textarea>
						</td><td></td><td>
						<textarea>[wp_spry_menu direction="verticall" /]</textarea>
					</td>
				</tr>
				<tr>
					<td>Direction Horizontal, exclude category ID 1</td>
					<td>
						<textarea><?php echo "<?php if ( function_exists('wp_spry_menu') ) wp_spry_menu('direction=horizontal&exclude=1');?>" ?></textarea>
						</td><td></td><td>
						<textarea>[wp_spry_menu direction="horizontal" exclude="1" /]</textarea>
					</td>
				</tr>
			</table>
			</div>
			<?php
		}
		
		function wp_spry_menu($args = '') {	
			$defaults = array(
				//default category arg
				'show_option_all'    => '',
				'orderby'            => $this->options('orderby'),
				'order'              => 'ASC',
				'show_last_update'   => 0,
				'sort_column'        => 'name',
				'style'              => 'list',
				'show_count'         => 0,
				'title_li'           => '',
				'hide_empty'         => $this->options('hide_empty'),
				'use_desc_for_title' => 1,
				'child_of'           => $this->options('child_of'),
				'feed'               => '',
				'feed_type'          => '',
				'feed_image'         => '',
				'exclude'            => $this->options('exclude'),
				'current_category'   => 0,
				'hierarchical'       => true,
				'title_li'           => __( '' ),
				'echo'               => 1,
				'depth'              => $this->options('depth'),
				//for menu
				'direction'          => $this->options('direction'),
				'home_text'          => $this->options('home_text')
			);
			
			$r = wp_parse_args( $args, $defaults );
			
			
			extract( $r );
			
			$categories = get_categories( $r );
			
			$output = '';
			
			$classname = 'MenuBarVertical';
			if( 'horizontal' == $direction ) {
				$classname = 'MenuBarHorizontal';
			}
			
			if ( empty( $categories ) ) {
				if ( 'list' == $style )
				$output .= 'The category you selected have no child category, please select another.';
				else
				$output .= __( "No categories" );
			} else {
				global $wp_query;
				
				if ( empty( $r['current_category'] ) && is_category() )
				$r['current_category'] = $wp_query->get_queried_object_id();
				
				$output .= walk_category_tree( $categories, $depth, $r );
				
				$output = preg_replace ('/<li(.*?)>(.*?)View all posts filed under(.*?)<\/a>/', '<li>$2'. $this->options('cattitle') .'$3</a>', $output);
				$output = preg_replace ('/\<a href=(.*?)\n<ul class=\\\'children\\\'>/', '<a class="MenuBarItemSubmenu" href=$1<ul class=\'children\'>', $output);
				
			}
			
			if ($this->options('home_text') !== '')
			$home = '<li><a href="' . site_url('/') . '" title="'. $this->options('home_text') .'">'. $this->options('home_text'). '</a></li>';
			else 
			$home = '';	

			$output = apply_filters( 'wp_spry_menu', $output );
			$menu_id = 'wp_spry_menu_'.uniqid();
			$output = '<ul id="'.$menu_id.'" class="'.$classname.'">' .$home . $output . '</ul><div class="clear clearfix" style="clear:both"></div>';
			$output .= '<script type="text/javascript">
			<!--
			var wp_spry_menu = new Spry.Widget.MenuBar("'.$menu_id.'");
			//-->
			</script>
			';
			if ( $echo )
			echo $output;
			else
			return $output;
		}
		
		function wp_spry_menu_short_code ( $atts = '') {
			return wp_spry_menu( $atts );
		}
		
		function wp_spry_menu_style_and_script() {
			//global
			wp_enqueue_style('wp_spry_menu_style', plugins_url('SpryAssets/global.css', __FILE__),false,$this->plugin_version);
			
			if( isset($_GET['page']) AND ( 'wp-spry-menu' == $_GET['page'] )) {
				wp_enqueue_style('wp_spry_menu_admin_style', plugins_url('SpryAssets/admin-style.css', __FILE__),false,$this->plugin_version);
			}
			
			//vertical
			if($this->options('direction') == 'verticall') {
				wp_enqueue_style( 'wp_spry_menu_style_vertical', plugins_url('SpryAssets/SpryMenuBarVerticall.css', __FILE__),false,$this->plugin_version );
			}
			//themes
			wp_enqueue_style( 'wp_spry_menu_style_theme', plugins_url('themes/'.$this->options('theme').'/style.css', __FILE__),false,$this->plugin_version );
			
			//js
			wp_enqueue_script( 'wp_spry_menu_script',plugins_url( 'SpryAssets/SpryMenuBar.js' , __FILE__ ),false,$this->plugin_version,false );
		}
		
		}
}

if (class_exists('WPSpryMenu')) {
	$wp_spry_menu = new WPSpryMenu();
	
	function wp_spry_menu($args='') {
		global $wp_spry_menu;
		$wp_spry_menu->wp_spry_menu($args);
	}
}