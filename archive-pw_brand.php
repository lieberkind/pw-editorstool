<?php
/*
Template Name: Update Archive
*/
?>

<?php

  $query_string = 
  'SELECT wp_posts.ID, wp_postmeta.meta_value
  FROM wp_posts, wp_postmeta 
  WHERE wp_posts.ID = wp_postmeta.post_id AND wp_posts.post_type = "pw_update" AND wp_postmeta.meta_key = "pw_update_update"';
  $updates = $wpdb->get_results($query_string);
  //print_r($updates);
?>

<?php get_header(); ?>
  <h2>Work-In-Progress</h2>

  <div class="wip-options">
    <p class="view-options">
      <a href="#">Feed view</a> | <a href="#">List view</a>
    </p>
      
    <input type="text" class="search-update">
    
    <form class="sort-options">
      <select>
        <option>Brand 1</option>
        <option>Brand 2</option>
        <option>Brand 3</option>
      </select>
      <select>
        <option>Category 1</option>
        <option>Category 2</option>
        <option>Category 3</option>
      </select>
      <input type="button" value="sort">
    </form>
  </div>

  <table>
    <thead>
      <tr>
        <td>ID</td>
        <td>Update</td>
        <td>Image</td>
        <td>Action</td>
      </tr>
    </thead>
    <tbody>
      <?php foreach($updates as $update): ?>
        <tr>
          <td><?= $update->ID ?></td><td><?= $update->meta_value?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php get_footer(); ?>




















