
//"membersform" is the id of the form
$('#membersform').submit(function(e) {

  // the script which handles the form input.
    var url = '../membersform.php';

    $.ajax({
           type: 'POST',
           url: url,

           // Serialize the form's elements.
           data: $('#membersform').serialize(), 
           success: function(data)
           {
               $('.membersform').html(data);
           }
         });

    // Avoid executing the actual submit of the form.
    return false;
});

