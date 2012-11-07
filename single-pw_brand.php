<?php 
// First of all, get the brand ID.
$brand_id = $post->ID;

// If the current user doesn't have access to the brand, die.
if(!pw_currentusercan("read", "brand", $brand_id)) {
  die("You don't have access to this brand");
}

// Get the brand object
$brand_object = get_post($brand_id);
$brand_name = $brand_object->post_title;

// If the update form has been submitted, insert the update to the database
if($_POST['update-content']) {

  // Get the brand name
  $brand_name = $brand_object->post_title;

  // Create a new post object to be inserted to the database
  $new_update = array(
    'post_title'    => $brand_name . ': ' . $_POST['update-content'], // The title has the format: "<brandname>: <update-content>"
    'post_type'     => 'pw_update',
    'post_status'   => 'draft'
  );

  // Get the new update id
  $update_id = wp_insert_post($new_update);

  // Add meta data to the new update
  add_post_meta($update_id, 'pw_update_brand', $brand_id);
  add_post_meta($update_id, 'pw_update_update', $_POST['update-content']);
  add_post_meta($update_id, 'pw_update_link', $_POST['update-link']);

  // Upload the image. Update the meta box in the backend accordingly.
  print_r($_FILES);
  if($_FILES) {
    echo 'Entering file loop!';
    foreach ($_FILES as $file => $array) {
      $newupload = insert_attachment($file, $update_id);

      // If insert_attachment failed, it returns false.
      if($newupload) {
        add_post_meta($update_id, 'pw_update_image', $newupload);
      }
    }
  }

  die('I was called!');
  //echo 'I was called!';
} 
?>

<?php
$update_type = $_GET['update-type'];



// Generate the update type reminder text
switch ($update_type) {
  case 'brand':
    $update_type_reminder = 'HUSK: Brand updates skal indeholde en reference til ' . $brand_object->post_title . 's produkt<br>og generere likes/shares.';
    break;
  case 'social':
    $update_type_reminder = 'Skal generere kommentarer.';
    break;
  case 'traffic':
    $update_type_reminder = 'Skal generere link clicks.';
    break;
  default:
    $update_type_reminder = '';
    break;
}



// Fetch all brand meta and the brand logo
$brand_meta = get_post_custom($brand_id);
$brand_logo = rwmb_meta('pw_brand_logo', array('type' => 'image'), $brand_id);

// Fetch a random person that belongs to the brand
// As this always returns 1 element, it's safe to use [0] at the end
// of the get_posts function call.
$person = get_posts(array(
  'numberposts' => 1,
  'orderby'     => 'rand',
  'meta_key'    => 'pw_person_brand',
  'meta_value'  => $brand_id,
  'post_type'   => 'pw_person',
))[0];

// Fetch the photo of person
$person_photo = reset(rwmb_meta('pw_person_image', array('type' => 'plupload_image'), $person->ID));



// Fetch all the example updates from the person
$person_updates = rwmb_meta('pw_person_update', array(), $person->ID);

// Pick 3 random indexes from the person updates array
$random_person_updates = get_random_elements($person_updates, 3);

// Fetch all the inspiration words from the person
$person_inspiration_words = wp_get_object_terms($person->ID, 'pw_person-inspiration-words', array('fields' => 'names'));

// Pick 3 random indexes from the person inspiration words array
$random_inspiration_words = get_random_elements($person_inspiration_words, 4);

// Fetch person inspiration images
$person_images = rwmb_meta('pw_person_images', array('type' => 'image'), $person->ID);

// Pick a random person image
$random_person_image = get_random_elements($person_images, 1);


?>

<?php get_header(); ?>
<?php wp_enqueue_script('collapsible'); ?>

<script type="text/javascript">
  function getClickBehavior(id) {
    document.getElementById(id).click();
  }

  jQuery(document).ready(function() {
    var $button = jQuery("#update-upload-image-button")
    $button.change(function() {

      var button_val = $button.val();

      // Matches, for example, "C:\fakepath\"
      var filename = button_val.replace(/[A-z][:\\\/]*[A-z]+[\\\/]/, "");
      
      jQuery("#brand-file-upload-container .file-path").html(filename);


    });

    jQuery('.collapsible').collapsible({
      speed: "medium"
    });


  });
</script>

<div class="update-container">
  <header class="update-brand-header">
    <div class="update-brand-info"> <!-- This should probably have a more describing class name -->
      <h2 class="page-title">Generator</h2>
      <p class="brand-name">Brand: <?= $brand_object->post_title ?></p>
      <p class="update-type-reminder"><?= $update_type_reminder; ?></p>
    </div>

    <!-- <div class="brand-dna"> -->
      
      <!-- 
        This is not the optimal solution. Should probably be changed
        so that the user can't upload multiple images and make mistakes. -->
<!--         <div class="brand-logo-wrapper">
          <div class="brand-logo-container">
            <?php foreach ($brand_logo as $bl): ?>
              <img src="<?= $bl['url']; ?>" class="brand-logo" />
            <?php endforeach; ?>
          </div>
        </div> -->

<!--       <p class="brand-dna-1"><?= $brand_meta['pw_brand_dna-1-title'][0]; ?></p>
      <p class="brand-dna-2"><?= $brand_meta['pw_brand_dna-2-title'][0]; ?></p>
      <p class="brand-dna-3"><?= $brand_meta['pw_brand_dna-3-title'][0]; ?></p>
      <p class="brand-dna-4"><?= $brand_meta['pw_brand_dna-4-title'][0]; ?></p>


    </div> -->

  </header>
  <div class="collapsible-wrapper">
    <h3 class="collapsible" id="section1">Brand DNA<div class="arrow"></div><h3>
    <div class="container">
      <div class="content">
        <div class="dna-pure">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
        <div class="dna-simple">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
        <div class="dna-reward">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
        <div class="dna-topic">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="update-form-container">
    <div class="update-inspiration">
      <!-- <img src="">  Fetch an image -->
      <!-- <img src="<?= $random_person_image['url']; ?>" class="inspiration-image"> -->
      <img src="<?= bloginfo('template_url'); ?>/img/test.png" class="inspiration-image">

      <?php foreach ($random_inspiration_words as $key => $riw): ?>
        <span class="inspiration-word-<?= $key + 1; ?>"><?= $riw; ?></span>
      <?php endforeach; ?>
    </div>

    <h3 class="update-form-title">Skriv ny update</h3> <!-- Denne overskrift skal ikke være her... LOL! JO DEN SKAL!-->

    <!-- This can probably be hacked by writing your own form... -->
    <?php if(pw_currentusercan("create", "update", $brand_id)): ?>
      <form class="update-form" method="POST" name="new_update" id="new_update" enctype="multipart/form-data" target="upload_target">
        <input type="hidden" name="brand-id" value="<?= $brand_id; ?>">
        <input type="hidden" name="brand-name" value="<?= $brand_name; ?>">
        <input type="hidden" name="update-type" value="<?= $update_type; ?>">
        <textarea name="update-content" class="update-content" placeholder="Skriv statusopdatering her..."></textarea>
        <input placeholder="Evt. link" type="text" name="update-link" class="update-link">
        <div class="upload-button-container" id="brand-file-upload-container">
          <button class="button" onclick="getClickBehavior('update-upload-image-button')">Billedupload</button>
          <span class="file-path"></span>
        </div>
        <input type="file" name="update-image" class="update-upload-image" id="update-upload-image-button">
        <input type="submit" value="Gem update" class="button submit-update">

        <!-- This iframe shoud be hidden with CSS -->
        <iframe src="" id="upload_target" name="upload_target"></iframe>
      </form>
    <?php endif; ?>
  </div>

  <!-- <input type="button" onclick="postUpdate('<?= bloginfo('url'); ?>', '#new_update')" value="Call Ajax!"> -->
  <!-- <div id="ajax-response"></div> -->


  <div class="collapsible-wrapper">
    <h3 class="collapsible" id="section1">Brand DNA<div class="arrow"></div><h3>
    <div class="container">
      <div class="content">
        <div class="dna-pure">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
        <div class="dna-simple">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
        <div class="dna-reward">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
        <div class="dna-topic">
          <h3 class="dna-heading">Pure</h3>
          <ul class="dna-topic-list">
            <li>Ord</li>
            <li>Ord</li>
            <li>Ord</li>
          </ul>
        </div>
      </div>
    </div>
  </div>



</div>


<div class="brand-persona">
  <header class="persona-information">
    <h3 class="persona-title">Persona</h3>
    <p class="person-name">Mød <?= $person->post_title; ?> - han skal finde din update interessant</p>
    <img src="<?= $person_photo['url']; ?>" class="person-image" /> <!-- Fetch the persons image -->
  </header>

  <div class="person-updates">
    <?php foreach($random_person_updates as $rpu): ?>
      <div class="person-update">
        <img src="<?= $person_photo['url']; ?>" class="person-image" /> <!-- Fetch the persons image -->
        <p class="update-person-name"><?= $person->post_title; ?></p>
        <p class="person-update-text"><?= $rpu; ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php get_footer(); ?> 






