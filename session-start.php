<h1>Søg efter opdateringsinspiration</h1>

<?php 
  // Fetch all the brand information for use in
  // the option field, in alphabetical order
  $brand_posts = get_posts(array(
    'numberposts' => -1,
    'post_type'   => 'pw_brand',
    'orderby'     => 'title',
    'order'       => 'ASC'
    )
  );

  // Get the current user id
  $current_user = wp_get_current_user();

  //$u = new WP_User($current_user_id);

  // if(in_array("administrator", $u->roles)) { echo 'I\'m sysadm, bitch!'; }

  if(count($brand_posts) > 0) {
    foreach($brand_posts as $key => $brand) {
      if(pw_currentusercan("read", "brand", $brand->ID)) {
        echo "User can read: " . $brand->post_title . "<br>";
        $brands[$brand->ID] = $brand->post_title;
      }
    }
  }
?>

<form method="POST">
  <select name="brand">
    <?php foreach($brands as $key => $brand): ?>
      <option value="<?= $key; ?>"><?= $brand; ?></option>
    <?php endforeach; ?>
  </select>

  <select name="update-type">
    <option value="brand">Brand Update</option>
    <option value="social">Social Update</option>
    <option value="traffic">Traffic Update</option>
  </select>

  <input type="submit" value="Start session!">
</form>