<?php

function addUpdateTerm() {
  $term_name = $_POST['term_name'];

  $response = wp_insert_term($term_name, 'pw_update-update-categories');

  echo json_encode($response);

  die();
}
add_action('wp_ajax_addUpdateTerm', 'addUpdateTerm');


function setUpdateTerms() {
  $update_id = $_POST['update_id'];
  $new_terms = $_POST['update_terms'];

  $response = wp_set_post_terms($update_id, $new_terms, 'pw_update-update-categories', false);

  echo json_encode($response);

  die();
}
add_action('wp_ajax_setUpdateTerms', 'setUpdateTerms');

function getUpdateTerms() {

  // Fetch the updates ID
  $update_id = $_POST['update_id'];

  // Fetch all terms from update categories
  $terms = get_terms('pw_update-update-categories', array('hide_empty' => 0));

  // Fetch the updates term ids
  $update_terms = wp_get_post_terms($update_id, 'pw_update-update-categories', array('fields' => 'ids'));

  foreach ($terms as $key => $term) {
    if(in_array($term->term_id, $update_terms)) {
      $terms[$key]->checked = true;
    } else {
      $temrs[$key]->checked = false;
    }
  }

  echo json_encode($terms);

  die();
}
add_action('wp_ajax_getUpdateTerms', 'getUpdateTerms');

function editUpdate() {
  $update = array();
  $update['ID']             = $_POST['update_id'];
  $update['update_content'] = $_POST['update_content'];
  $update['update_link']    = $_POST['update_link'];

  //$update_response = wp_update_post($update);

  $update_response = array();

  $update_response['is_content_updated']  = update_post_meta($update['ID'], 'pw_update_update', $update['update_content']);
  $update_response['updated_content']     = $update['update_content'];
  $update_response['is_link_updated']     = update_post_meta($update['ID'], 'pw_update_link', $update['update_link']);
  $update_response['updated_link']        = $update['update_link'];

  echo json_encode($update_response);

  die();
}
add_action('wp_ajax_editUpdate', 'editUpdate');

function deleteUpdate() {

  $result = wp_delete_post($_POST['update_id']);
  echo $_POST['update_id'];
  die();
}
add_action('wp_ajax_deleteUpdate', 'deleteUpdate');

function updateToPending() {
  $update_id = $_POST['update_id'];
 
  $update = array();
  $update['ID']           = $update_id;
  $update['post_status']  = 'pending';

  $update_response = wp_update_post($update);

  $response = array();

  if($update_response == $update_id) {
    $response['status'] = 1; 
    $response['msg']    = "Post was successfully sent to review";
  } else {
    $response['status'] = 0;
    $response['msg']    = "Something went wrong. Try again!";
  }

  echo json_encode($response);

  die();
}
add_action('wp_ajax_updateToPending', 'updateToPending');


function postUpdate() {
  
  // First of all, get the brand ID.
  $brand_id = $_POST['brand-id'];

  // Get the brand object
  $brand_object = get_post($brand_id);

  // If the update form has been submitted, insert the update to the database
  if($_POST['update-content']) {

    // Get the brand name
    $brand_name = $brand_object->post_title;

    // Create a new post object to be inserted to the database
    $new_update = array(
      'post_title'    => $_POST['update-content'], // The title has the format: "<brandname>: <update-content>"
      'post_type'     => 'pw_update',
      'post_status'   => 'publish' // Should maybe be draft or pending instead
    );

    // Get the new update id
    $update_id = wp_insert_post($new_update);

    // Add meta data to the new update
    add_post_meta($update_id, 'pw_update_brand', $brand_id);
    add_post_meta($update_id, 'pw_update_update', $_POST['update-content']);
    add_post_meta($update_id, 'pw_update_link', $_POST['update-link']);

    die('Update published!');
  }
}
add_action('wp_ajax_postUpdate', 'postUpdate');


function sortUpdates() {
  $brand_id = $_POST['brand-id'];

  // Update category ID
  $uc_id = $_POST['update-category'];

  if(strcmp($uc_id, 'all') == 0) {
    $term_id = '';
  } else {
    $term_id = $uc_id;
  }


  $update_args = array(
    'post_type'   => 'pw_update',
    'meta_key'    => 'pw_update_brand',
    'meta_value'  => $brand_id,
    'post_status' => 'draft',
    'tax_query'   => array(
      array(
        'taxonomy'  => 'pw_update-update-categories',
        'field'     => 'id',
        'terms'     => $term_id
      )
    )
  );

  $updates = new WP_Query($update_args);

  $updates = get_posts(array(
    'numberposts' => -1,
    'category'    => '',
    'post_type'   => 'pw_update',
    'order'       => 'ASC',
    'post_status' => 'draft',
    'meta_key'    => 'pw_update_brand',
    'meta_value'  => $brand_id 
  ));

  foreach ($updates as $key => $update) {
    // Get the attached image, if there is one.
    $attachment_id = get_post_meta($update->ID, 'pw_update_image', true);
    $attachment_url = wp_get_attachment_url($attachment_id);


    // Set the updates content
    $updates[$key]->update_content = get_post_meta($update->ID, 'pw_update_update', true);

    // Set the updates image
    $updates[$key]->attachment_url = $attachment_url;
  }

  echo json_encode($updates);
  die(); // Very important!
}

add_action('wp_ajax_sortUpdates', 'sortUpdates');

?>