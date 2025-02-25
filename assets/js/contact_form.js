// In case the button is outside the form
$(document).ready(function () {

  var toggled = false;

  // Listen to submit event on the <form> itself.
  $('#contactForm').submit(function(e) {

    // Prevent form submission which refreshes page
    e.preventDefault();

    // Serialize data (might not add textarea)
    var formData = $(this).serialize();

    // Make AJAX request
    $.post("../contact_form.php", formData).complete(function() {
      console.log("Success");
    });

    // Show success message
    if (! toggled) {
      $('#form_success').toggle();
      toggled = true;
    }

  });

});

