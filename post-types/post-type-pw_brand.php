<?php

add_action('init', 'register_pw_brand');
add_action('admin_init', 'register_pw_brand_metaboxes');


function register_pw_brand() {
  $labels = array(
    'name'                => _x('Brands', 'post type general name'),
    'singular_name'       => _x('Brand', 'post type singular name'),
    'add_new'             => _x('Add New', 'brand'),
    'add_new_item'        => __('Add New Brand'),
    'edit_item'           => __('Edit Brand'),
    'new_item'            => __('New Brand'),
    'view_item'           => __('View Brand'),
    'search_items'        => __('Search Brand'),
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
    'supports'            => array('title')
  );

  register_post_type('pw_brand', $args);
}

function register_pw_brand_metaboxes() {
  $prefix = 'pw_brand_';

  $users = get_users();

  $user_names = array();
  foreach ($users as $key => $user) {
    $user_names[$user->ID] = $user->display_name;
  }
  
  $meta_boxes[] = array(
    'id'      => 'brand-info',
    'title'   => 'Brand Information',
    'pages'   => array('pw_brand'),
    'context' => 'normal',

    'fields'  => array(
      array(
        'name'  => 'Brand logo',
        'id'    => $prefix . 'logo',
        'type'  => 'image'
      ),
      array(
        'name'  => 'Facebook Page',
        'id'    => $prefix . 'fb-page',
        'type'  => 'text',
        'size'  => 60 // Why is this here?
      )
    )
  );

  $meta_boxes[] = array(
    'id'      => 'brand-collaborators',
    'title'   => 'Brand Collaborators',
    'pages'   => array('pw_brand'),
    'context' => 'normal',

    'fields' => array(
      array(
        'name'    => 'Brand Collaborators',
        'desc'    => 'Who has access to post updates for the brand?',
        'id'      => $prefix . 'collaborators',
        'type'    => 'checkbox_list',
        'options' => $user_names
      )
    )
  );

  $meta_boxes[] = array(
    'id'      => 'dna-options',
    'title'   => 'Brand Information',
    'pages'   => array('pw_brand'),
    'context' => 'normal',

    'fields'  => array(
      // DNA 1
      array(
        'name'  => 'DNA 1 Title',
        'id'    => $prefix . 'dna-1-title',
        'type'  => 'text',
        'size'  => 60
      ),
      array(
        'name'  => 'DNA 1 Description',
        'desc'  => 'A longer description for DNA 1',
        'id'    => $prefix . 'dna-1-description',
        'type'  => 'textarea',
        'rows'  => 3
      ),

      // DNA 2
      array(
        'name'  => 'DNA 2 Title',
        'id'    => $prefix . 'dna-2-title',
        'type'  => 'text',
        'size'  => 60
      ),
      array(
        'name'  => 'DNA 2 Description',
        'desc'  => 'A longer description for DNA 2',
        'id'    => $prefix . 'dna-2-description',
        'type'  => 'textarea',
        'rows'  => 3
      ),

      // DNA 3
      array(
        'name'  => 'DNA 3 Title',
        'id'    => $prefix . 'dna-3-title',
        'type'  => 'text',
        'size'  => 60
      ),
      array(
        'name'  => 'DNA 3 Description',
        'desc'  => 'A longer description for DNA 3',
        'id'    => $prefix . 'dna-3-description',
        'type'  => 'textarea',
        'rows'  => 3
      ),

      // DNA 4
      array(
        'name'  => 'DNA 3 Title',
        'id'    => $prefix . 'dna-4-title',
        'type'  => 'text',
        'size'  => 60
      ),
      array(
        'name'  => 'DNA 3 Description',
        'desc'  => 'A longer description for DNA 4',
        'id'    => $prefix . 'dna-4-description',
        'type'  => 'textarea',
        'rows'  => 3
      ),
    )
  );

  foreach ($meta_boxes as $meta_box) {
    new RW_Meta_Box($meta_box);
  }
}

?>