
//"membersform" is the id of the form
$("#membersform").submit(function() {

    var url = "../membersform.php"; // the script where you handle the form input.

    $.ajax({
           type: "POST",
           url: url,
           // serialize your form's elements.
           data: $("#membersform").serialize(), 
           success: function(data)
           {
               // ".membersform" is the class of your form wrapper
               $('.membersform').html(data);
           }
         });
    // avoid executing the actual submit of the form.
    return false;
});

