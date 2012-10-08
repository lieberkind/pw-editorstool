<?php 
  if($_POST['brand'] && $_POST['update-type']) {
    $link = get_permalink($_POST['brand']);
    $mod_link = add_query_arg('update-type', $_POST['update-type'], $link);
    wp_redirect($mod_link);
  }
?>

<?php 
  if($_POST['update-content']) {

    $brand = get_post($_POST['brand']);
    $brand_name = $brand->post_title;


    $new_update = array(
      'post_title'    => $brand_name . ': ' . $_POST['update-content'], // Find an appropriate title. Example - "<Brand>: <update>"
      'post_type'     => 'pw_update',
      'post_status'   => 'publish'
    );

    // Get the new update id
    $update_id = wp_insert_post($new_update);

    // Add meta data to the new update
    add_post_meta($update_id, 'pw_update_brand', $_POST['brand']);
    add_post_meta($update_id, 'pw_update_update', $_POST['update-content']);
    add_post_meta($update_id, 'pw_update_link', $_POST['update-link']);

    // Upload the image. Update the meta box in the backend accordingly.
    if($_FILES) {
      foreach ($_FILES as $file => $array) {
        $newupload = insert_attachment($file, $update_id);
        add_post_meta($update_id, 'pw_update_image', $newupload);
      }
    }

    echo 'I was called!';
  } 
?>

<?php get_header(); ?>

<?php
  // Are there security issues to be aware of when doing this?
  // Should probably be done like this: http://voodoopress.com/how-to-post-from-your-front-end-with-no-plugin/
  if($_POST['brand'] && $_POST['update-type']) {
    //include('search-results.php');
  } else {
    include('session-start.php');
  }
?>

<?php get_footer(); ?>