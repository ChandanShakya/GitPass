function validateUsername() {
    var username = document.getElementById("form4Example1").value;
    if (username.trim() === "") {
        document.getElementById("div1").innerHTML = "Username is required";
        document.getElementById("form4Example1").style.borderColor = "red";
    } else {
        document.getElementById("div1").innerHTML = "";
        document.getElementById("form4Example1").style.borderColor = "green";
    }
}

function validatePassword() {
    var password = document.getElementById("form4Example2").value;
    if (password.trim() === "") {
        document.getElementById("div2").innerHTML = "Password is required";
        document.getElementById("form4Example2").style.borderColor = "red";
    } else {
        document.getElementById("div2").innerHTML = "";
        document.getElementById("form4Example2").style.borderColor = "green";
    }
}
var submitButton = document.getElementById("send");

function buttonEnabler() {
    var username = document.getElementById("form4Example1").value;
    var password = document.getElementById("form4Example2").value;
    if (username.trim() !== "" && password.trim() !== "") {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }
}

// Disable submit button on page load
submitButton.disabled = true;