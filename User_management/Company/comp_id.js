// Generate the next company ID based on the highest existing ID in the database
function getNextCompanyID() {
    // Send an AJAX request to a PHP file to retrieve the next company ID
    // Update the "companyID" input field with the obtained ID
    // Example using jQuery:
    $.ajax({
        url: 'get_next_company_id.php',
        method: 'GET',
        success: function(response) {
            $('#companyID').val(response);
        },
        error: function() {
            alert('Failed to retrieve the next company ID.');
        }
    });
}

// Call the function when the page loads
$(document).ready(function() {
    getNextCompanyID();
});
