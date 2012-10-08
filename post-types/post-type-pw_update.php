<?php

add_action('init', 'register_pw_update');
add_action('init', 'register_pw_update_taxonomies');
add_action('admin_init', 'register_pw_update_metaboxes');

// Register the the "Update" posttype
function register_pw_update() {

  $labels = array(
    'name'                => _x('Updates', 'post type general name'),
    'singular_name'       => _x('Update', 'post type singular name'),
    'add_new'             => _x('Add New', 'update'),
    'add_new_item'        => __('Add New Update'),
    'edit_item'           => __('Edit Update'),
    'new_item'            => __('New Update'),
    'view_item'           => __('View Update'),
    'search_items'        => __('Search Update'),
    'not_found'           =>  __('Nothing found'),
    'not_found_in_trash'  => __('Nothing found in Trash'),
    'parent_item_colon'   => ''
  );

  $args = array( 
    '_builtin'            => false,
    'labels'              => $labels,
    'public'              => true,
    'publicly_queryable'  => true,
    'show_ui'             => true,
    'query_var'           => true,
    'menu_position'       => 5,
    'capability_type'     => 'post',
    'hierarchical'        => false,
    'supports'            => array('title', 'thumbnail')
  );

  register_post_type('pw_update', $args);
}

// Register the "Update" taxonomies
function register_pw_update_taxonomies() {

  $labels = array(
    'name'                => _x('Update Categories', 'taxonomy general name'),
    'singular_name'       => _x('Update Category', 'taxonomy singular name'),
    'search_items'        => __('Search Update Categories'),
    'popular_items'       => __('Popular Update Categories'),
    'all_items'           => __('All Update Categories'),
    'edit_item'           => __('Edit Update Categories'),
    'update_item'         => __('Update Update Categories'),
    'add_new_item'        => __('Add New Update Category'),
    'new_item_name'       => __('New Update Category')  
  );
  
  $args = array(
    'labels'            => $labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'query_var'         => 'update_categories',
    'hierarchical'      => true,
    '_builtin'          => false
  );
  
  register_taxonomy('pw_update-update-categories', 'pw_update', $args);
}

// Register the "Update" meta boxes
function register_pw_update_metaboxes() {
  
  // Fetch all the brand information for use in
  // the option field, in alphabetical order
  $brand_posts = get_posts(array(
    'numberposts' => -1,
    'post_type'   => 'pw_brand',
    'orderby'     => 'title',
    'order'       => 'ASC'
    )
  );

  if(count($brand_posts) > 0) {
    foreach($brand_posts as $key => $brand) {
      $brands[$brand->ID] = $brand->post_title;
    }
  }

  $prefix = 'pw_update_';

  $meta_box = array(
    'id'      => 'update',
    'title'   => 'Update Options',
    'pages'   => array('pw_update'),
    'context' => 'normal',

    'fields'  => array(
      array(
        'name'    => 'Brand',
        'desc'    => 'Select the brand that the update belongs to',
        'id'      => $prefix . 'brand',
        'type'    => 'select',
        'options' => $brands
      ),
      array(
        'name'  => 'Update',
        'desc'  => 'A brand update',
        'id'    => $prefix . 'update',
        'type'  => 'textarea',
        'rows'  => 2,
      ),
      array(
        'name'              => 'Update Image',
        'desc'              => 'An image that belongs to the update',
        'id'                => $prefix . 'image',
        'type'              => 'plupload_image',
        'max_file_uploads'  => 1
      ),
      array(
        'name'  => 'Update Link',
        'desc'  => 'Remember <code>http://</code> in front of the url',
        'id'    => $prefix . 'link',
        'type'  => 'text',
        'size'  => 60
      )
    )
  );

  new RW_Meta_Box($meta_box);  
}













?>