<?php

add_action('init', 'register_pw_person');
add_action('init', 'register_pw_person_taxonomies');
add_action('admin_init', 'register_pw_person_metaboxes');

function register_pw_person() {
  $labels = array(
    'name'                => _x('Persons', 'post type general name'),
    'singular_name'       => _x('Person', 'post type singular name'),
    'add_new'             => _x('Add New', 'person'),
    'add_new_item'        => __('Add New Person'),
    'edit_item'           => __('Edit Person'),
    'new_item'            => __('New Person'),
    'view_item'           => __('View Person'),
    'search_items'        => __('Search Person'),
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

  register_post_type('pw_person', $args);
}


function register_pw_person_taxonomies() {

  $i_do_labels = array(
    'name'                => _x('I Do Words', 'taxonomy general name'),
    'singular_name'       => _x('I Do Word', 'taxonomy singular name'),
    'search_items'        => __('Search I Do Words'),
    'popular_items'       => __('Popular I Do Words'),
    'all_items'           => __('All I Do Words'),
    'edit_item'           => __('Edit I Do Word'),
    'update_item'         => __('Update I Do Word'),
    'add_new_item'        => __('Add New I Do Word'),
    'new_item_name'       => __('New I Do Word')  
  );
  
  $i_do_args = array(
    'labels'            => $i_do_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  );
  
  register_taxonomy('pw_person-i-do-words', 'pw_person', $i_do_args);

  $i_say_labels = array(
    'name'                => _x('I Say Words', 'taxonomy general name'),
    'singular_name'       => _x('I Say Word', 'taxonomy singular name'),
    'search_items'        => __('Search I Say Words'),
    'popular_items'       => __('Popular I Say Words'),
    'all_items'           => __('All I Say Words'),
    'edit_item'           => __('Edit I Say Word'),
    'update_item'         => __('Update I Say Word'),
    'add_new_item'        => __('Add New I Say Word'),
    'new_item_name'       => __('New I Say Word')  
  );
  
  $i_say_args = array(
    'labels'            => $i_say_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  );

  register_taxonomy('pw_person-i-say-words', 'pw_person', $i_say_args);

  $i_think_labels = array(
    'name'                => _x('I Think Words', 'taxonomy general name'),
    'singular_name'       => _x('I Think Word', 'taxonomy singular name'),
    'search_items'        => __('Search I Think Words'),
    'popular_items'       => __('Popular I Think Words'),
    'all_items'           => __('All I Think Words'),
    'edit_item'           => __('Edit I Think Word'),
    'update_item'         => __('Update I Think Word'),
    'add_new_item'        => __('Add New I Think Word'),
    'new_item_name'       => __('New I Think Word')  
  );
  
  $i_say_args = array(
    'labels'            => $i_think_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  ); 

  //register_taxonomy('pw_person-i-think-words', 'pw_person', $i_think_args);

  $i_own_labels = array(
    'name'                => _x('I Own Words', 'taxonomy general name'),
    'singular_name'       => _x('I Own Word', 'taxonomy singular name'),
    'search_items'        => __('Search I Own Words'),
    'popular_items'       => __('Popular I Own Words'),
    'all_items'           => __('All I Own Words'),
    'edit_item'           => __('Edit I Own Word'),
    'update_item'         => __('Update I Own Word'),
    'add_new_item'        => __('Add New I Own Word'),
    'new_item_name'       => __('New I Own Word')  
  );
  
  $i_own_args = array(
    'labels'            => $i_own_labels,
    'public'            => true,
    'show_ui'           => true,
    'show_tagcloud'     => true,
    'hierarchical'      => false,
    '_builtin'          => false
  ); 

  register_taxonomy('pw_person-i-own-words', 'pw_person', $i_own_args);


}


function register_pw_person_metaboxes() {

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
  
  $prefix = 'pw_person_';

  $meta_boxes[] = array(
    'id'      => 'brand',
    'title'   => 'Persona Info',
    'pages'   => array('pw_person'),
    'context' => 'normal',

    'fields'  => array(
      array(
        'name'    => 'Brand',
        'desc'    => 'Select the brand this person belongs to',
        'id'      => $prefix . 'brand',
        'type'    => 'select',
        'options' => $brands
      ),
      array(
        'name'              => 'Image',
        'desc'              => 'A photo of this person',
        'id'                => $prefix . 'image',
        'type'              => 'plupload_image',
        'max_file_uploads'  => 1
      )
    )
  );

  $meta_boxes[] = array(
    'id'      => 'person',
    'title'   => 'Persona Updates',
    'pages'   => array('pw_person'),
    'context' => 'normal',

    'fields'  => array(
      array(
        'name'  => 'Update',
        'desc'  => 'An update this persona could have made. Pres "+" to add more.',
        'id'    => $prefix . 'update',
        'type'  => 'textarea',
        'rows'  => 2,
        'clone' => true,
      )
    )
  );

  $meta_boxes[] = array(
    'id'      => 'images',
    'title'   => 'Persona Images',
    'pages'   => array('pw_person'),
    'context' => 'normal',

    'fields'  => array(
      array(
        'name'  => 'Images',
        'id'    => $prefix . 'images',
        'type'  => 'plupload_image',
      )
    )
  );

  foreach ($meta_boxes as $key => $meta_box) {
    new RW_Meta_Box($meta_box); 
  }
}




?>