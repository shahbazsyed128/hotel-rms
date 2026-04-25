"use strict";

function signupcustomer() {

    var name     = $('#user_name').val().trim();
    var email    = $('#user_email').val().trim();
    var phone    = $('#phone').val().trim();
    var pass     = $('#u_pass').val().trim();
    var address  = $('textarea[name="address"]').val().trim();

    if (name == '' || email == '' || phone == '' || pass == '') {
        alert("Please fill all required fields!");
        return false;
    }

    var btn = $("#signupBtn");
    btn.html("Registering...").prop("disabled", true);

    $.ajax({
        type: "POST",
        url: basicinfo.baseurl + 'hungry/userregister',
        data: {
            user_name: name,
            email: email,
            phone: phone,
            u_pass2: pass,
            address: address,
            csrf_test_name: basicinfo.csrftokeng
        },
        success: function(response) {
            response = response.trim();

            if (response == "200") {
                alert("Registration successful!");
            } 
            else if (response == "404") {
                $('#error-details').html("Something went wrong!");
            	$('#ajax-error-box').show();
            } 
            else {
				
				$('#error-details').html(response); 
				$('#ajax-error-box').show(); 
				
				$('html, body').animate({ scrollTop: 0 }, 'slow');
            }
        },
        error: function() {
            alert("Server error! Please try again.");
        },
        complete: function() {
            btn.html("Register Now").prop("disabled", false);
        }
    });
}