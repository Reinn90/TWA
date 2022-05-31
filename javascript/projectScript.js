// Nowell Reyes - 20658133 - Thursday 9am Online Tutorial  

// Initial form page validation 
function validateForm(theForm) {
    
    var invalid = false;

    // Check user has selected a staff
    if (requiredFieldEmpty(theForm.stafflist)){
        displayErrors(document.getElementById('staff-error-msg'));
        invalid = true;
    }

    // Check user entered a date
    if (requiredFieldEmpty(theForm.reviewDateCreation)){
        displayErrors(document.getElementById('date-error-msg'));
        invalid = true;
    }

    if(invalid) return false;
    return true;
}

// Generic function to check if a required input is empty
function requiredFieldEmpty(element) {

    if (!element.value.length) {
        return true;
    }
}

// Generic function to show error icon
function displayErrors(element) {
    element.innerHTML = "This field is required";
}

// check if the date input by user is correct
function validateNumber(element, msg) {
    var input = element.value;

    if (input.length != 0) {

        if (isNaN(input))  msg.innerHTML = "Not a number. Please try again."; 
        else {
            var regEx = /^[0-9]{4}$/g; //4 digit year regEx

            if (regEx.test(input)) {

                if (input < 2022 || input > 2030)
                    msg.innerHTML = "Please enter year between 2022 and 2030.";
                else
                    msg.innerHTML = ""; //correct date input
            }
        }

    } else msg.innerHTML = "Please enter year.";

}