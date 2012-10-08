/*
* "AJAX object"
* Can make an ajax call to the WordPress database
*/
function AjaxObject(options) {
  this.options = options;

  //this.action           = action;
  //this.post_data        = post_data;
  //this.success_callback = success_callback;
}

AjaxObject.prototype.post_data_string = function() {
  
  var post_data         = this.options.post_data;
  
  var post_data_string  = 'action=' + this.options.action;

  for(param in post_data) {
    post_data_string += "&" + param + "=" + post_data[param];
  }

  return post_data_string;
}

AjaxObject.prototype.call_ajax = function() {
  var success = this.options.success;
  var post_data_string = this.post_data_string();

  jQuery.ajax({
    type    : 'POST',
    url     : 'wp-admin/admin-ajax.php',
    data    : post_data_string,
    success : function(res) { success(res); }
  });
}


/*
* Update an updates category terms
*/
function setUpdateTerms(update_id, terms, dialog) {
  update_data = 'action=setUpdateTerms&update_id=' + update_id + '&update_terms=' + terms;

  jQuery.ajax({
    type    : 'POST',
    url     : 'wp-admin/admin-ajax.php',
    data    : update_data,
    success : function(res) {
      jQuery(dialog).dialog('close');
    }
  });
}

/*
* Update an updates status from "draft" to "pending"
*/
function updateToPending(siteUrl, element) {

  var update_id = jQuery(element).parents("td").attr("id");

  jQuery.ajax({
    type: 'POST',
    url     : siteUrl + '/wp-admin/admin-ajax.php',
    data: 'action=updateToPending&update_id=' + update_id,
    success: function(data) {

      var response = JSON.parse(data);

      // Alert the response message
      alert(response.msg);

      // If the update was successfully updated (lol), remove ther tr element that holds it
      if(response.status == 1) {
        jQuery(element).parents("tr").remove();
      }
    }
  });

}

/*
* Post an update to the WordPress database
*/ 
function postUpdate(siteUrl, formElm) {
  // The action to be called is encoded in this as well. This could probably be made prettier!
  var update_data = 'action=postUpdate&' + jQuery(formElm).serialize();
  console.log(update_data);

  console.log(siteUrl + '/wp-admin/admin-ajax.php');

  jQuery.ajax({
    type    : 'POST',
    url     : siteUrl + '/wp-admin/admin-ajax.php',
    data    : update_data,
    success : function(data) {
      alert(data);
    }
  });
}

/*
* Sort updates.
*/
function sortUpdates(siteUrl, formElm) {
  var update_data = 'action=sortUpdates&' + jQuery(formElm).serialize();

  jQuery.ajax({
    type    : 'POST',
    url     : siteUrl + '/wp-admin/admin-ajax.php',
    data    : update_data,
    dataType: 'json',
    success : function(data) {

      console.log(data); 

      var updates = data;

      jQuery("#update-list").empty();

      // Populate the table with new stuff
      var tableContents = '';
      tableContents += '<tr><th>ID</th><th>Update</th><th>Image?</th><th>Actions</th></tr>';
      for(i in updates) {
        tableContents += '<tr>'
        + '<td>' + updates[i].ID + '</td>' 
        + '<td>' + updates[i].update_content + '</td>';
        
        if(updates[i].attachment_url) {
          tableContents += '<td>I have an image!</td>';
        }

        tableContents += '</tr>'; 
      }

      jQuery("#update-list").append(tableContents);
    }
  });
}