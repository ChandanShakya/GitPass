function validateMessage() {
    var message = document.getElementById("form4Example3").value;
    var validm = false;
    if (message === "") {
        document.getElementById("div3").innerHTML = "Please Fill the message.";
        document.getElementById("form4Example3").style.borderColor = "red";
        validm = false;
    } else {
        document.getElementById("div3").innerHTML = "";
        document.getElementById("form4Example3").style.borderColor = "green";
        validm = true;
    }
    return validm;
}

function validateUsername() {
    var name = document.getElementById("form4Example1").value;
    var validn = false;
    if (name === "") {
        document.getElementById("div1").innerHTML = "Please enter the username";
        document.getElementById("form4Example1").style.borderColor = "red";
        validn = false;
    } else {
        document.getElementById("div1").innerHTML = "";
        document.getElementById("form4Example1").style.borderColor = "green";
        validn = true;
    }
    return validn;
}

function validateEmail() {
    var email = document.getElementById("form4Example2").value;
    var valide = false;
    if (email === "") {
        document.getElementById("div2").innerHTML = "Please provide your E-mail address";
        document.getElementById("form4Example2").style.borderColor = "red";
        valide = false;
    } else {
        // Check valid email
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (re.test(email)) {
            document.getElementById("div2").innerHTML = "";
            document.getElementById("form4Example2").style.borderColor = "green";
            valide = true;
        } else {
            document.getElementById("div2").innerHTML = "E-mail is not valid";
            document.getElementById("form4Example2").style.borderColor = "red";
            valide = false;
        }
    }
    return valide;
}

function validate() {
    if (validateUsername() && validateEmail() && validateMessage()) {
        document.getElementById("send").disabled = false;
    } else {
        document.getElementById("send").disabled = true;
    }
}
