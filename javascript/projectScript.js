// Nowell Reyes - 20658133 - Thursday 9am Online Tutorial  

// Initial form page validation 
function validateForm(theForm) {

    var staffErrorMsg = document.getElementById('staff-error-msg');
    var dateErrorMsg = document.getElementById('date-error-msg');

    // Check if input fields are left empty, if so, throw DOM error notifications
    if (requiredFieldEmpty(theForm.stafflist)) displayErrors(staffErrorMsg);
    if (requiredFieldEmpty(theForm.reviewDateCreation)) displayErrors(dateErrorMsg);


    // if there are no error message showing
    if ((staffErrorMsg.innerHTML == "") && (dateErrorMsg.innerHTML == "")) {
        return true;
    }
    return false;
}

// Secondary 'ratings' form validation

function validateRatingsForm(theForm) {

    // If there are error messages due to incorrect input, do not let form submit
    if (!(document.getElementById('job-error-msg').innerHTML == "")) return false;
    if (!(document.getElementById('workQ-error-msg').innerHTML == "")) return false;
    if (!(document.getElementById('init-error-msg').innerHTML == "")) return false;
    if (!(document.getElementById('comms-error-msg').innerHTML == "")) return false;
    if (!(document.getElementById('depend-error-msg').innerHTML == "")) return false;

    // otherwise, inputs are not required
    return true;
}

// validate the ratings system on top of the maxlength setting
function validateRatings(element, msg) {

    var input = element.value;

    if (isNaN(input)) msg.innerHTML = "Numbers only please";
    else {
        var regEx = /^[1-5]$/g; // 1 digit between 1-5

        if (!regEx.test(input)) msg.innerHTML = "Pick a number between 1-5.";
        else msg.innerHTML = "";
    }

}



// Generic function to check if a required input is empty
// From my Prac Set 1 
function requiredFieldEmpty(element) {

    if (!element.value.length) {
        return true;
    }
}

// Generic function to show error icon
function displayErrors(element) {
    element.innerHTML = "This field is required";
}

// Clear error message on select box when staff has been selected
// From my prac set 1
function clearErrorMsg(element, msg) {
    var input = element.value;

    if (input.length != 0) {
        msg.innerHTML = "";
    }
}

// check if the date input by user is correct
function validateNumber(element, msg) {
    var input = element.value;

    if (input.length != 0) {

        if (isNaN(input)) msg.innerHTML = "Not a number. Please try again.";
        else {
            var regEx = /^[0-9]{4}$/g; // regEx to match 2020-2039

            if (regEx.test(input)) {

                if (input < 2022 || input > 2030)
                    msg.innerHTML = "Please enter year between 2022 and 2030.";
                else
                    msg.innerHTML = ""; //correct date input
            }
        }

    } else msg.innerHTML = "Please enter year.";

}