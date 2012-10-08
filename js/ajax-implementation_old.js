/*
* Add en update category term
*/
function addUpdateTerm(term_name, dialog) {
  term_data = 'action=addUpdateTerm&term_name=' + term_name;

  jQuery.ajax({
    type    : 'POST',
    url     : 'wp-admin/admin-ajax.php',
    data    : term_data,
    success : function(res) {

      var response = JSON.parse(res);

      var html = '<input type="checkbox" name="update-category[]" value="' + response.term_id + '" checked>' + term_name + '<br>';

      jQuery('form', dialog).append(html);
    }
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
* Get update categories
*/
function getUpdateTerms(update_id, dialog) {

  // Data to send to the server
  update_data = 'action=getUpdateTerms&update_id=' + update_id; 

  jQuery.ajax({
    type    : 'POST',
    url     : 'wp-admin/admin-ajax.php',
    data    : update_data,
    success : function(res) {
      response = JSON.parse(res);

      //alert(res);

      // The HTML string to build
      var html = '<input type="hidden" name="update_id" value="' + update_id + '">';

      for(obj in response) {
        var checked = '';
        
        if(response[obj].checked) {
          checked = 'checked';
        }

        html += '<input type="checkbox" name="update-category[]" value="' + response[obj].term_id +'" ' + checked + '>' + response[obj].name + '<br>';
      }

      jQuery('form', dialog).html(html);

      dialog.dialog('open');
    }
  });
}



/*
* Edit an update
*/
function editUpdate(site_url, update_id, update_content, update_link, dialog) {
  
  // Data to send to the server
  update_data = 'action=editUpdate&update_id=' + update_id + '&update_content=' + update_content + '&update_link=' + update_link;

  jQuery.ajax({
    type      : 'POST',
    url       : site_url + '/wp-admin/admin-ajax.php',
    data      : update_data,
    success   : function(res) {
      var response = JSON.parse(res);

      if(response.is_content_updated || response.is_link_updated) {
        dialog.dialog('close');
        jQuery("td#" + update_id).siblings(".update-content").text(response.updated_content);
        jQuery("td#" + update_id).siblings(".update-extra").children(".update-link").val(response.updated_link);
      } else {
        dialog.dialog('close');
        alert('Something went wrong. Please try again.');
      }
    }
  });
}


/*
* Delete an update
*/
function deleteUpdate(update_id, dialog) {

  jQuery.ajax({
    type: 'POST',
    url: 'http://localhost:8888/Apps/Patchwork%20-%20Redaktorvarktoj' + '/wp-admin/admin-ajax.php',
    data: 'action=deleteUpdate&update_id=' + update_id,
    success: function(data) {

      var res = JSON.parse(data);

      //alert(res);

      // This is so damn ugly.
      if(res > 0) {
        // Close the dialog and remove the update from the list
        dialog.dialog('close');
        jQuery("td#" + update_id).parents("tr").fadeOut();
      } else {
        alert('Something went wrong, please try again.');
        dialog.dialog('close');
      }

    }
  })
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