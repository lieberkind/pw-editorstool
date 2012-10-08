<?php
/*
Template Name: Work in Progress
*/
?>


<?php get_header(); ?>

<script type="text/javascript">

/* LIVE SEARCH */
jQuery(document).ready(function() {

  jQuery("#update-search").keyup(function() {

    var filter = jQuery(this).val(), count = 0;

    var filter_regexp = new RegExp(filter, "i");

    jQuery("#update-list .update-id").each(function() {

      // If the list item does not contain the text phrase fade it out
      if (jQuery(this).text().search(filter_regexp) < 0) {
        if(jQuery(this).siblings(".update-content").text().search(filter_regexp) < 0) {
          jQuery(this).parents("tr").hide();
        } else {
          jQuery(this).parents("tr").show();
        }
      } else {
        jQuery(this).parents("tr").show();
      }

    });

  })
});
</script>

<script type="text/javascript">
/*
* Add an update category
*/
function addUpdateTerm(e) {

  // Prevent the #-url to cause a to jump to top of page
  e.preventDefault();

  // Fetch the new update category name from the input field
  var new_category_name = jQuery("input[name=new_category]", "#edit-update-categories").val();

  // Make an ajax object
  var call = new AjaxObject({
    action    : "addUpdateTerm",
    post_data : { 
      term_name : new_category_name 
    }, 
    success   : function(res) {
      var response  = JSON.parse(res);
      var html      = '<input type="checkbox" name="update-category[]" value="' + response.term_id + '" checked>' + new_category_name + '<br>';
      jQuery('form', '#edit-update-categories').append(html);
    }
  });

  // Invoke the call
  call.call_ajax();
}


/*
* Gets all update category terms and builds a category list with the
* correct categories checked
*/
function getUpdateTerms(e) {
  e.preventDefault();

  // Fetch the update ID and the dialog to open
  var uid     = jQuery(this).parents('td').attr('id');
  var dialog  = jQuery("#edit-update-categories");

  // Make the call object
  var call = new AjaxObject({
    action    : "getUpdateTerms",
    post_data : {
      update_id : uid
    },
    success   : function(res) {
      response = JSON.parse(res);

      // The HTML string to build
      var html = '<input type="hidden" name="update_id" value="' + uid + '">';

      // Build the category list, and check the categories that the update is posted in
      for(obj in response) {
        var checked = '';
        
        if(response[obj].checked)
          checked = 'checked';

        html += '<input type="checkbox" name="update-category[]" value="' + response[obj].term_id +'" ' + checked + '>' + response[obj].name + '<br>';
      }

      jQuery('form', dialog).html(html);
      dialog.dialog('open');
    }
  });

  // Invoke the call
  call.call_ajax();
}

/*
* CATEGORY DIALOG
*/
var CATEGORYDIALOG = (function($) {
  var _updateID,
      _dialog
  ;

  // Get update category terms
  function _getUpdateTerms() {
    var call = new AjaxObject({
      action    : "getUpdateTerms",
      post_data : { update_id : _updateID },
      success   : function(res) { _successFunc(JSON.parse(res)); }
    });
    call.call_ajax();
  }

  // The function that runs on ajax success
  function _successFunc(data) {
    // The HTML string to build
    var html = '<input type="hidden" name="update_id" value="' + _updateID + '">';

    // Build the category list, and check the categories that the update is posted in
    for(obj in data) {
      var checked = '';

      if(data[obj].checked)
        checked = 'checked';

      html += '<input type="checkbox" name="update-category[]" value="' + data[obj].term_id +'" ' + checked + '>' + data[obj].name + '<br>';
    }

    $('form', _dialog).html(html);
  }

  var API = {};

  API.init = function(dialog) {
    _dialog = $(dialog);
  }

  API.open = function(e) {
    e.preventDefault();
    _updateID = $(this).parents('td').attr('id');

    _getUpdateTerms();

    _dialog.dialog('open');
  }

  API.insertTerm = function(e) {
    e.preventDefault();
  }

  return API;
}(jQuery));




/*
* DELETE DIALOG
*/ 
var DELETEDIALOG = (function() {

  // Private members
  var _dialog; 
  var _updateID;
  
  // Private functions
  function _deleteUpdate() {
    var call = new AjaxObject({
      action    : "deleteUpdate",
      post_data : {
        update_id : _updateID
      },
      success   : function(res) { _successFunc(res); }
    });
    call.call_ajax();
  }

  function _successFunc(res) {
    var response = JSON.parse(res);

    if(response > 0) {
      _dialog.dialog('close');
      jQuery("td#" + _updateID).parents("tr").fadeOut();
    } else {
      alert('Something went wrong, please try again.');
      _dialog.dialog('close');
    }
  }


  // Public API
  var API = {};

  API.init = function() {

    console.log("Init was called...");

    _dialog = jQuery("#delete-update-dialog");

    _dialog.dialog({
      autoOpen : false,
      title    : "Delete update?",
      modal    : true,
      buttons  : {
        "Cancel" : function() { _dialog.dialog('close')},
        "Delete" : function() { _deleteUpdate(); }
      }
    });
  };

  API.open = function(e) {
    e.preventDefault();

    _updateID = jQuery(this).parents('td').attr('id');
    
    _dialog
      .find('.delete-update-message')
      .html('Are you sure you want to delete the update with ID ' + _updateID)
    ;
    
    _dialog.dialog('open');
  };

  return API;

} ());



/*
* EDIT DIALOG
*/
var EDITDIALOG = (function() {
  var _updateID,
    _updateContent,
    _updateLink,
    _dialog
  ;

  function _editUpdate() {
    var _updateContent  = jQuery('textarea[name=update-content]', _dialog).val();
    var _updateLink     = jQuery('input[name=update-link]', _dialog).val();

    var call = new AjaxObject({
      action    : "editUpdate",
      post_data : {
        update_id       : _updateID,
        update_content  : _updateContent, // These have to be different
        update_link     : _updateLink     // These have to be different - fetch from form
      },
      success   : function(res) { _successFunc(res) }
    });

    call.call_ajax();
  }

  function _successFunc(res) {
    var response = JSON.parse(res);

    if(response.is_content_updated || response.is_link_updated) {
      _dialog.dialog('close');
      jQuery("td#" + _updateID).siblings(".update-content").text(response.updated_content);
      jQuery("td#" + _updateID).siblings(".update-extra").children(".update-link").val(response.updated_link);
    } else {
      _dialog.dialog('close');
      alert('Something went wrong. Please try again.');
    }
  }

  var API = {};

  API.init = function() {
    _dialog = jQuery("#edit-update-dialog");
    _dialog
      .dialog({
        autoOpen  : false,
        modal     : true,
        title     : "Edit update",
        buttons   : {
            "Save"    : function() { _editUpdate(); },
            "Cancel"  : function() { _dialog.dialog('close'); }
        }
      })
    ;
  }

  API.open = function(e) {
      e.preventDefault();

      _updateID       = jQuery(this).parents('td').attr('id');
      _updateContent  = jQuery(this).parent().siblings('.update-content').text();
      _updateLink     = jQuery(this).parent().siblings('.update-extra').children('.update-link').attr('value');

      var form_html = "";
      form_html += '<input type="hidden" name="update-id" value="' + _updateID + '">';
      form_html += '<textarea name="update-content">' + _updateContent + '</textarea>';
      form_html += '<input type="text" name="update-link" value="' + _updateLink + '">';

      _dialog.find('form').html(form_html);
      _dialog.dialog('open');
  }

  return API;


} ());




jQuery(document).ready(function() {

  // Initialize modules
  DELETEDIALOG.init();
  EDITDIALOG.init();
  CATEGORYDIALOG.init("#edit-update-categories");

  // Bind events
  jQuery("#add-new-category").click(addUpdateTerm);
  jQuery(".edit-update-categories").click(CATEGORYDIALOG.open);
  jQuery(".delete-update").click(DELETEDIALOG.open);
  jQuery(".edit-update").click(EDITDIALOG.open);




  jQuery('#edit-update-categories').dialog({
    autoOpen: false,
    modal: true,
    title: "Edit update categories",
    buttons: {
      Save : function() {

        var update_id = jQuery("input[name=update_id]", this).attr('value');
        var new_terms = '';

        jQuery("input:checked", this).each(function() {
          new_terms += jQuery(this).attr('value') + ",";
        });

        setUpdateTerms(update_id, new_terms, this);
      }
    }
  });

});

</script>

<?php 

// Find out how to use this shit!

// $update_args2 = array(
//   'post_type'   => 'pw_update',
//   'meta_key'    => 'pw_update_brand',
//   'meta_value'  => 24,
//   'post_status' => 'draft',
//   'tax_query'   => array(
//     array(
//       'taxonomy'  => 'pw_update_update-categories',
//       'field'     => 'id',
//       'terms'     => 23
//     )
//   )
// );

  // $updates2 = new WP_Query($update_args2);


  // echo '<pre>';
  // print_r($updates2);
  // echo '</pre>';

?>

<?php 

  // Get updates with related content
  $query_string =
  'SELECT 
    posts.ID AS ID,
    posts.post_author AS update_author,
    postmeta.meta_value AS update_content,
    postmeta_2.meta_value AS update_link,
    postmeta_3.meta_value AS update_brand_id
  FROM
    wp_posts AS posts, 
    wp_postmeta AS postmeta,
    wp_postmeta AS postmeta_2,
    wp_postmeta AS postmeta_3
  WHERE posts.ID = postmeta.post_id
    AND posts.post_type = "pw_update"
    AND posts.ID = postmeta_2.post_id
    AND postmeta_2.meta_key = "pw_update_link"
    AND posts.ID = postmeta_3.post_id
    AND postmeta_3.meta_key = "pw_update_brand"
    AND posts.post_status = "draft"
    AND postmeta.meta_key = "pw_update_update"';
  
  $updates = $wpdb->get_results($query_string);


  // foreach ($updates as $key => $update) {
  //   $terms = wp_get_post_terms($update->ID, 'pw_update-update-categories');
  //   // echo $key;
  //   //print_r($terms);

  //   $updates[$key]->terms = $terms;
  // }

  // echo '<pre>';
  // print_r($updates);
  // echo '</pre>';

  // Fetch all the brands for sorting options
  $brands = get_posts(array(
    'post_type'   => 'pw_brand',
    'numberposts' => -1
  ));

  // Fetch all the update categories
  $update_cats = get_terms('pw_update-update-categories', array(
    'hide_empty' => 0
  ));

  //print_r($update_cats);
?>

<header class="work-in-progress-header">
  <h2>Work in Progress</h2> 
  <a href="#">Feed view</a>
  <a href="#">List view</a>
  <input type="text" class="update-search" id="update-search">
  <form class="update-sorting-options" id="update-sorting-options">
    <select class="update-brand-select" name="brand-id">
      <?php foreach($brands as $key => $brand): ?>
        <?php if(pw_currentusercan("read", "update", $brand->ID)): ?>
          <option value="<?= $brand->ID; ?>"><?= $brand->post_title; ?></option>
        <?php endif; ?>
      <?php endforeach; ?>
    </select>
    <select class="update-category-select" name="update-category">
      <option value="all">Get all categories</option>
      <?php foreach($update_cats as $key => $update_cat): ?>
        <option value="<?= $update_cat->term_id; ?>"><?= $update_cat->name; ?></option>
      <?php endforeach; ?>
    </select>
  </form>
  <input type="button" value="Sort" onclick="sortUpdates('<?= bloginfo('url'); ?>', '#update-sorting-options')">
</header>

<section class="work-in-progress-updates">
  <table class="update-list list-view" id="update-list">
    <tr>
      <th>ID</th>
      <th>Update</th>
      <th>Image? (extras)</th>
      <th>Actions</th>
      <td>Brand</td>
      <td>Author</td>
    </tr>

    <?php foreach($updates as $key => $update): ?>
      <?php
        // If the user can't access the brand, jump over this iteration
        if(!pw_currentusercan("read", "update", $update->update_brand_id)) {
          continue;
        }

        // Get the attached image, if there is one.
        $attachment_id = get_post_meta($update->ID, 'pw_update_image', true);
        $attachment_url = wp_get_attachment_url($attachment_id);
      ?>

      <tr>
        <td class="update-id searchable"><?= $update->ID; ?></td>
        <td class="update-content searchable"><?= $update->update_content; ?></td>
        <td class="update-extra">
          <input type="hidden" value="<?= $update->update_link; ?>" class="update-link">
          <?php if($attachment_url): ?>
            <a href="<?= $attachment_url; ?>">
              <img src="<?= $attachment_url; ?>" width="50px" height="75px">
            </a>
          <?php endif ?>
        </td>
        <td id="<?= $update->ID; ?>">
          <a href="#" class="delete-update">Delete</a>
          <a href="#" class="edit-update">Edit</a>
          <a href="#" class="edit-update-categories">Category</a>
          <a href="#" class="update-to-pending" onclick="updateToPending('<?= bloginfo('url'); ?>', this)">Send</a>
        </td>
        <td><?= get_the_title($update->update_brand_id); ?></td>
        <td><?= the_author_meta('user_nicename', $update->update_author); ?></td>
      </tr>      
    <?php endforeach; ?>
  </table>



  <!-- 
    The display should be "none" by default to prevent a brief flash.
    Whether this should be set in the CSS is a good question 
  -->
  <div id="edit-update-dialog" style="display: none">
    <form>
<!--       <input type="hidden" name="update-id">
      <textarea name="update-content"></textarea>
      <input type="text" name="update-link">  -->
    </form>
  </div>


  <!-- The dialog that opens when the Category action button is pressed -->
  <div id="edit-update-categories" style="display: none">
    <form>
    </form>
    <input type="text" name="new_category">
    <a href="#" id="add-new-category">Add new category</a>
  </div>

  <div id="delete-update-dialog" style="display: none">
    <p class="delete-update-message"></p>
  </div>








</section>
<?php get_footer(); ?>
