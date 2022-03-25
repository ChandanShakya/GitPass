// Validating username and password
// Check if empty


function validateLogin() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if (username == "") {
        document.getElementById("username_error").innerText = "Username is required";
        return false;
    } else if (password == "") {
        alert("Please enter your password");
        return false;
    }
    return true;
}
