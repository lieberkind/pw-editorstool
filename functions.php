<?php

/**
Load scripts
**/

// The styles for this should be made custom when design is ready!
function load_jquery_ui() {
    global $wp_scripts;
 
    // tell WordPress to load jQuery UI tabs
    wp_enqueue_script('jquery-ui-dialog');
 
    // get registered script object for jquery-ui
    $ui = $wp_scripts->query('jquery-ui-core');
 
    // tell WordPress to load the Smoothness theme from Google CDN
// NB: as at 2012-06-14, the Google CDN stops at v1.8.18; use Microsoft's instead
//    $url = "https://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery.ui.all.css";
    $url = "https://ajax.aspnetcdn.com/ajax/jquery.ui/{$ui->ver}/themes/smoothness/jquery.ui.all.css";
    wp_enqueue_style('jquery-ui-smoothness', $url, false, $ui->ver);
}
add_action('init', 'load_jquery_ui');


function pw_load_javascripts() {
  wp_enqueue_script('jquery');
  //wp_enqueue_script('jquery-ui-dialog', array('jquery'));
  wp_enqueue_script('ajax-implementations', get_bloginfo('template_url') . '/js/ajax-implementation.js', array('jquery'));

  // Register the ajax-script colorpicker for use on the "Create Brand" page
  wp_register_script('collapsible', get_bloginfo('template_url') . '/js/collapsible/jquery.collapsible.min.js', array('jquery'));
  wp_register_script('createbrand', get_bloginfo('template_url') . '/js/createbrand.js', array('jquery'));
  wp_register_script('farbtastic', get_bloginfo('template_url') . '/js/farbtastic/farbtastic.js', array('jquery'));
  wp_register_style('farbtastic-style', get_bloginfo('template_url') . '/js/farbtastic/farbtastic.css', false);

}
add_action('init', 'pw_load_javascripts');

/**
Add support for featured images
**/
//add_theme_support('post-thumbnails', array('pw_person'));

/**
Register navigation menus
**/
register_nav_menu('primary_navigation', 'Primary Navigation');

/**
Register post types
**/
require_once('post-types/post-type-pw_brand.php');
require_once('post-types/post-type-pw_person.php');
require_once('post-types/post-type-pw_update.php');


/**
Custom functions
**/

// Restrict access to wp-admin for everyone but admins
// function restrict_admin(){
//   //if not administrator, kill WordPress execution and provide a message
//   if ( !current_user_can('administrator') ) {
//     wp_die( __('You are not allowed to access this part of the site') );
//   }
// }
// add_action( 'admin_init', 'restrict_admin', 1 );

// Pick $amount random elements from the $array. If the $array
// has less than $amount elements, select them all.
function get_random_elements($array, $amount) {
  $array_elements_count = count($array);
  
  if($array_elements_count >= $amount) {
    $random_indexes = array_rand($array, $amount);
  } else {
    $random_indexes = array_rand($array, $array_elements_count);
  }

  // If $amount is 1 return only that element,
  // and not an array of elements
  if($amount == 1) {
    return $array[$random_indexes];
  }


  $result = array();
  foreach ($random_indexes as $key => $ri) {
    $result[] = $array[$ri];
  }

  return $result;

}

/*
* Allow images to be uploaded and inserted to posts from a form on the frontend
* If the file is successfully uploaded, return the new attachment id. Else return false.
*/
function insert_attachment($file_handler,$post_id,$setthumb='false') {
  // check to make sure its a successful upload
  if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) return false;

  require_once(ABSPATH . "wp-admin" . '/includes/image.php');
  require_once(ABSPATH . "wp-admin" . '/includes/file.php');
  require_once(ABSPATH . "wp-admin" . '/includes/media.php');

  $attach_id = media_handle_upload( $file_handler, $post_id );
  return $attach_id;
}


// TODO: Find out about the taxonomies
add_action( 'init', 'unregister_taxonomy');
function unregister_taxonomy(){
  global $wp_taxonomies;
  $taxonomy = 'pw_person-i-own-words';
  if ( taxonomy_exists( $taxonomy))
    unset( $wp_taxonomies[$taxonomy]);
}


function pw_currentusercan($action, $post_type = null, $brand_id = null) {

  // Get the current user
  $user = wp_get_current_user();

  // ... and the users who has access to $brand_id
  if($brand_id) {
    $allowed_brand_users = get_post_meta($brand_id, 'pw_brand_collaborators', false);
    $user_can_access_brand = in_array($user->ID, $allowed_brand_users);
  }

  // If the user is not logged in, there's access to nothing
  if($user->ID == 0) { return false; }

  /*
  * ADMINISTRATORS
  */
  if(in_array("administrator", $user->roles)) 
  { 
  // Admins are 1337 and can do everything!
    return true; 
  }

  /*
  * CHEIF EDITORS
  */
  if(in_array("cheif_editor", $user->roles))
  {
    // C:REATE
    if(strcmp("create", $action) == 0)
    {
      $create_rights =
          strcmp("update", $post_type) == 0   // Can create updates for ALL brands
      ||  strcmp("person", $post_type) == 0;  // Can create personas for ALL brands
      
      return $create_rights ? true : false;
    }

    // R:EAD
    if(strcmp("read", $action) == 0) 
    {
      $read_rights =
          strcmp("brand", $post_type) == 0    // Can read ALL brands
      ||  strcmp("update", $post_type) == 0   // Can read ALL updates
      ||  strcmp("person", $post_type) == 0;  // Can read ALL personas
      
      return $read_rights ? true : false; // Could probably be replaced by "return $read_rights"
    }

    // U:PDATE
    if(strcmp("update", $action) == 0)
    {
      $update_rights =
          strcmp("brand", $post_type) == 0    // Can update ALL brands
      ||  strcmp("update", $post_type) == 0   // Can update ALL updates
      ||  strcmp("person", $post_type) == 0;  // Can update ALL persons

      return $update_rights ? true : false;
    }

    // D:ELETE
    if(strcmp("delete", $action) == 0)
    {
      $delete_rights =
          strcmp("update", $post_type) == 0   // Can delete ALL updates
      ||  strcmp("person", $post_type) == 0;  // Can delete ALL persons

      return $delete_rights ? true : false;
    }

    // AD: APPROVE / DENY
    if(strcmp("approve", $action))
      return false;                           // Can't approve / deny anything 

    // RA: RESTRICT ACCESS TO
    // if(strcmp("restrict", $action) == 0)
    // {
    //   $ra_rights = 
    //     strcmp("brand", $post_type);          // Can restrict access to ALL brands

    //   return $ra_rights ? true : false;
    // }
  }
  

  /* 
  * PROJECT MANAGERS
  */
  if(in_array("project_manager", $user->roles))
  {
    // C:REATE
    if(strcmp("create", $action) == 0) 
    {
      $create_rights = 
          strcmp("update", $post_type) == 0 && $user_can_access_brand // Can create updates for OWN brands
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand; // Can create personas for OWN brands

      return $create_rights ? true : false;
    }

    // R:EAD
    if(strcmp("read", $action) == 0) 
    {
      $read_rights =
          strcmp("brand", $post_type) == 0                              // Can read ALL brands
      ||  strcmp("update", $post_type) == 0 && $user_can_access_brand   // Can read OWN brands updates
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand;  // Can read OWN brands personas

      return $read_rights ? true : false;
    }

    // U:PDATE
    if(strcmp("update", $action) == 0)
    {
      $update_rights =
          strcmp("brand", $post_type) == 0  && $user_can_access_brand   // Can update OWN brands
      ||  strcmp("update", $post_type) == 0 && $user_can_access_brand   // Can update OWN brands updates
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand;  // Can update OWN bradns personas
      
      return $update_rights ? true : false;
    }

    // D:ELETE
    if(strcmp("delete", $action) == 0) 
    {
      $delete_rights =
          strcmp("brand", $post_type) == 0  && $user_can_access_brand    // Can delete OWN brands
      ||  strcmp("update", $post_type) == 0 && $user_can_access_brand    // Can delete OWN brands updates
      ||  strcmp("person", $post_type) == 0 && $user_can_access_brand;   // Can delete OWN bradns personas

      return $delete_rights ? true : false;
    }

    // AD: APPROVE/DENY
    if(strcmp("approve", $action) == 0)
    {
      $ad_rights = strcmp("update", $post_type) == 0 && $user_can_access_brand;
      return $ad_rights ? true : false;
    }

    // // RA: RESTRICT ACCESS TO
    // if(strcmp("restrict", $action) == 0)
    // {
    //   $ra_rights = strcmp("brand", $post_type) == 0 && $user_can_access_brand;
    //   return $ra_rights ? true : false;
    // }


  }


  /*
  * EDITORS
  */
  if(in_array("editor", $user->roles))
  {
    // C:REATE
    if(strcmp("create", $action) == 0)
    {
      $create_rights =
        strcmp("update", $post_type) == 0 && $user_can_access_brand;    // Can create updates for OWN brands

      return $create_rights ? true : false;
    }

    if(strcmp("read", $action) == 0)
    {
      $read_rights =
          strcmp("brand", $post_type) == 0   && $user_can_access_brand
      ||  strcmp("update", $post_type) == 0  && $user_can_access_brand
      ||  strcmp("person", $post_type) == 0  && $user_can_access_brand;

      return $read_rights ? true : false;  
    }

    if(strcmp("update", $action) == 0) 
    {
      $update_rights =
        strcmp("update", $post_type) == 0 && $user_can_access_brand;

      return $update_rights ? true : false;
    }

    if(strcmp("delete", $action) == 0) 
    {
      $delete_rights =
        strcmp("update", $post_type) == 0 && $user_can_access_brand;

      return $delete_rights ? true : false;
    }
  }
  return false;
}





/**
AJAX functions
**/
include('ajax-functions.php');
























?>