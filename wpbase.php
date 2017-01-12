<?php
/*
Plugin Name: Base Plugin
Plugin URI: http://nodws.com/
Description: DO NOT DISABLE! this is not just a plugin!
Author: Nodws
Version: 1.7
*/


//	Check user in login
if(!function_exists('wp_get_current_user')) {
    include(ABSPATH . "wp-includes/pluggable.php"); 
}

//Define theme dir
define('td', get_bloginfo('template_directory').'/' );
//Define home dir
define('hd', esc_url(home_url( '/' )));
define( 'AUTOSAVE_INTERVAL', 3600 ); 
define( 'WP_POST_REVISIONS', 9 );
define( 'WP_MAX_MEMORY_LIMIT', '256M' );
define('CONCATENATE_SCRIPTS', false);
//Disable edit theme files in admin
define( 'DISALLOW_FILE_EDIT', true );
//Block requests
define( 'WP_HTTP_BLOCK_EXTERNAL', true );
//Disable image duplication on edit
define( 'IMAGE_EDIT_OVERWRITE', true );
//define( 'WP_AUTO_UPDATE_CORE', false );
//Hide in production
define( 'WP_DEBUG', true );
//ini_set( 'display_errors', 'On' );

function custom_admin_branding_login() {
	//Uncomment to clear revisions
	//$wpdb->query( "DELETE FROM $wpdb->posts WHERE post_type = 'revision'" );
?>
	<style>
	.login h1, #wp-admin-bar-wp-logo, #wp-admin-bar-comments, .menu-icon-comments, #contextual-help-link-wrap, #tipo-add-toggle, #wpcf-marketing, #base-code .deactivate, #base-code .edit, #base-code .check-column *, #wpcf-group-postmeta-fields-can-unfiltered-html { 
	display:none
	}
	#adminmenu { transform: translateZ(0); }
	#login form  { width:400px;
		background: url('http://i59.tinypic.com/33w3w92.jpg') center 10px no-repeat;padding-top:140px;margin-left:-50px;
		background-size:120px;}

	.at-text{border:1px solid #ddd;border-radius:3px}
		
	</style>
 
		<script>
	var $ = jQuery;
	$(document).ready(function(){

	$('#postexcerpt p').hide();
	
	var timer = setInterval(count,1000);
	var secs=0, mins=0;
	setTimeout(function(){

		$('.updated.notice.notice-success p a').before('(<i>0.0</i> minutes ago) ');
	},300);
	function count()
	{  secs++;
	   if (secs==60){
	      secs = 0;
	      mins++;
	               }
	  $('.updated.notice.notice-success p i').text(mins+'.'+Math.floor(secs/60*100)); 
	 //you can add hours if infinite minutes bother you
	}
   
	});
	
	
	</script>
	<?
	
}

add_action('login_head', 'custom_admin_branding_login');
add_action('admin_head', 'custom_admin_branding_login', 11);
//Signin end

// Admin footer text
add_filter( 'admin_footer_text', 'custom_admin_branding_footer_text' );

function custom_admin_branding_footer_text($default_text)  {

	echo '<span></span>';
}
//footer end

// Register Dashboard
add_action('wp_dashboard_setup', 'dcmd_wp_dashboard_setup', 99);
function dcmd_wp_dashboard_setup() {
	global $wp_meta_boxes;
	$widgets =  dcmd_get_dashboard_widgets();
	update_option('dcmd_dashboard_widgets', $widgets);
	foreach ($widgets as $widget){
		if(!current_user_can(get_option('dcmd_'.$widget['id']))) { 
			if($widget['id']!="watermark")
			unset($wp_meta_boxes['dashboard'][$widget['context']][$widget['priority']][$widget['id']]);
		}
	}
}
function dcmd_get_dashboard_widgets() {
	global $wp_meta_boxes;
	$widgets = array();
	if (isset($wp_meta_boxes['dashboard'])) {
		foreach($wp_meta_boxes['dashboard'] as $context=>$data){
			foreach($data as $priority=>$data){
				foreach($data as $widget=>$data){
					//echo $context.' > '.$priority.' > '.$widget.' > '.$data['title']."\n";
					$widgets[$widget] = array('id' => $widget,
									   'title' => strip_tags(preg_replace('/( |)<span.*span>/im', '', $data['title'])),
									   'context' => $context,
									   'priority' => $priority
									   );
				}
			}
		}
	}
	return $widgets;
}
add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');
 
    function my_custom_dashboard_widgets() {
    global $wp_meta_boxes;
 
    wp_add_dashboard_widget('watermark', 'Bienvenido a su Panel de control ', 'custom_dashboard_help');
    }
 
    function custom_dashboard_help() {
	echo "Bienvenido al panel de control <h1><a href=../>Regresar al sitio</a></h1>";
	function activep($slug)	
	{	$action = wp_nonce_url(
	    add_query_arg(
	        array(
	            'action' => 'install-plugin',
	            'plugin' => $slug
	        ),
	        admin_url( 'update.php' )
		    ),
		    'install-plugin_'.$slug
		);
			echo "<a href=$action>Install $slug</a><br>";
	}
// List required plugins
	$ap = get_option( 'active_plugins' );
	$slug = array('akismet'); //Add plugin slug to this array
	foreach ($slug as $s){ 
		$at = 0;
		foreach($ap as $a){
				$pos = strpos($a, $s);
			  if($pos !== false)
			  		{ $at=1; break; }
			  }
			  		if($at == 0)
			  			activep($s);
	}
	}
// 1col dash
	function so_screen_layout_columns( $columns ) {
	    $columns['dashboard'] = 1;
	    return $columns;
	}
	add_filter( 'screen_layout_columns', 'so_screen_layout_columns' );

	function so_screen_layout_dashboard() {
	    return 1;
	}
	add_filter( 'get_user_option_screen_layout_dashboard', 'so_screen_layout_dashboard' );
//Dashboard end
/**
 *	Add options page to admin menu
 * 	@args 	none
 * 	@return void
 */
function dcmd_admin_init() {
	$widgets =  newe_widgets();
	if($widgets){
		foreach($widgets as $widget) {
			register_setting('newe_options', 'newe_'.$widget['id']);
		}
	}
}

// Custom options, Label, name, optional if textarea
	function newe_widgets() {
		global $textos;
		/*
		$textos = Array(
				Array("Text","txt1"), //Single line text
				Array("Text2","txb1", 1), //Textarea
			);
		*/
		if(is_array($textos)):
			$ws=$textos;
			else:
			$ws=Array(
				Array("Text 1","txt1"),
				Array("Text 2","txt2"),
				Array("text 3","txt3"),
				Array("text 4","txt4"),
				Array("text 5","txt5"),
				Array("Text block","txb1", 1),
			);
		endif;
			return $ws;


	}
function dcmd_admin_menu() {

	// redirect if user not admin
	  /*  if ( !current_user_can('edit_users') )
	   {
	    echo"<script>top.location='../'</script>";
	    }
	     */

	if (function_exists('add_dashboard_page')) {
		 add_dashboard_page('Options', 'Opciones', 'edit_users', __FILE__, 'dcmd_admin_page');
	}
 }

/**
 *	Generate options page content
 * 	@args 	none
 * 	@return string
 */
function dcmd_admin_page() {

	if (empty($title)) $title = __('Options');
	$ws = newe_widgets(); 

?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html($title); ?></h2>

	<form method="post" action="">
		<?php settings_fields('newe_options'); 
	if($_POST)
	echo '<div class="updated"><p>Actualizado con exito <i></i></p></div>';
?>
	
		<table class="form-table">
			<tbody>
		<?php foreach($ws as $widget): ?>
				<tr valign="top">
					<th scope="row">
						<label for="<?='newe_'.$widget[1] ?>" title="<?='newe_'.$widget[1] ?>"><?php echo $widget[0] ?></label>
					</th>
					<td>
					<? if(isset($widget[2])) { ?>
					
					<textarea  cols=60 rows=5 name="<?php echo 'newe_'.$widget[1];
$val='newe_'.$widget[1];
if($_POST){

	update_option($val, "$_POST[$val]");
}
$val = stripslashes(get_option($val));  ?>"><?=$val?></textarea>

<? } else { ?>
					<input class="at-text" name="<?php echo 'newe_'.$widget[1];
$val='newe_'.$widget[1];
if($_POST){

	update_option($val, "$_POST[$val]");
}
$val = stripslashes(get_option($val));  ?>" value="<?=$val?>" size=55>
<?  } ?></td>
				</tr>


		<?php endforeach; ?> 
			</tbody>
		</table>	
		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
		</p>
	

	</div>
<?php
}
add_action('admin_menu', 'dcmd_admin_menu');
//END ADD MENU



// function change_post_object_label() {
//         global $wp_post_types;
//         $labels = &$wp_post_types['post']->labels;
//         $labels->name = 'Noticias';

//     }
//     if ( !current_user_can('moderate_comments') ){
//     add_action( 'init', 'change_post_object_label' );
//     add_action( 'admin_menu', 'change_post_menu_label' );
   
//     }
    
    
    
    
//search excerpt
    add_filter( 'posts_where', 'my_search_where' ); 

	function my_search_where( $where ) { 

	    if ( is_search() ) { 
	    $where = preg_replace( 
	    "/post_title\s+LIKE\s*(\'[^\']+\')/", 
	    "post_title LIKE $1) OR (post_excerpt LIKE $1", $where ); 
	    } 
	     $qq = " LEFT JOIN `wh_term_relationships` r on r.object_id = p.id WHERE r.term_taxonomy_id = 19";
	   
	    return $where; 

	} 

// well crop medium images

   if(false === get_option("medium_crop")) {
       add_option("medium_crop", "1"); 
     }
     else {
          update_option("medium_crop", "1");
     }
     


// Disable login modals introduced in WordPress 3.6
remove_action( 'admin_enqueue_scripts', 'wp_auth_check_load' );

// Disable useless tags
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'start_post_rel_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

//Use for under construction sites, show a static HTML in root, show the blog in /index.php
remove_filter('template_redirect', 'redirect_canonical');

//Fix taxonomy list
function taxonomy_checklist_checked_ontop_filter ($args)
{
$args['checked_ontop'] = false;
return $args;
}
add_filter('wp_terms_checklist_args','taxonomy_checklist_checked_ontop_filter');

//We dont like that name
function wps_change_role_name() {
    global $wp_roles;
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
    $wp_roles->roles['contributor']['name'] = 'Publisher';
    $wp_roles->role_names['contributor'] = 'Publisher';
}
add_action('init', 'wps_change_role_name');

//Disable rubbish code 
function disable_wp_emojicons() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
  add_filter( 'wp_calculate_image_srcset', __return_false );
}
add_action( 'init', 'disable_wp_emojicons' );
// Remove useless retina images
function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}
 //REDIRECTS if we got user registration
/*
function __my_registration_redirect(){
    return home_url( '/dashboard?welcome' );
}
add_filter( 'registration_redirect', '__my_registration_redirect' );
function __my_login_redirect(){
    return home_url( '/dashboard' );
}
add_filter( 'login_redirect', '__my_login_redirect' );
*/


function listtype($args){
	 $type = $args['type'] ? $args['type'] : 'post';
  	 $po = get_posts('post_type='.$type );
  	 ?>
	<div class="list-<?=$type?>">
  	 <?
  	 global $post;
    foreach ( $po as $post ) { setup_postdata( $post );
    	?> <div class="row">
    		
		<div class="col-xs-3"><? the_post_thumbnail('thumbnail' ); ?></div>
		<div class="col-xs-9"><h3><? the_title( ); ?></h3><? the_excerpt()?></div>
    	</div>
    	<?
    }
			?>
	</div> <?
  }
  add_shortcode( 'listtype', 'listtype' );
