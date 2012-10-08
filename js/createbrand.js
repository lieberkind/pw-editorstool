var CREATEBRAND = (function($) {
  var API = {};

  API.createBrand = function() {
    var brandData = 'action=createBrand&' + $('#create-brand-form').serialize();

    $.ajax({
      type    : 'POST',
      url     : 'wp-admin/admin-ajax.php',
      data    : brandData,
      success : function(res) {
        alert();
      }
    });
  };

  return API;
})(jQuery);

jQuery(document).ready(function() {
  jQuery("#create-form").click(function() {
    CREATEBRAND.createBrand();
    return false;
  });
});