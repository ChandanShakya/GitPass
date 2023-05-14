function validater() {
    var submitButton = document.getElementById("send");
    if(validateUsername() && validateEmail() && validatePassword() && validateConfirmationPassword()){
        submitButton.disabled = false;
    }else{
        submitButton.disabled = true;
    }
}

function validateUsername() {
    var username = document.getElementById("form4Example1").value.trim();
    if (username === "") {
        document.getElementById("div1").innerHTML = "Please enter the username";
        return false;
    } else {
        document.getElementById("div1").innerHTML = "";
        return true;
    }
}

function validateEmail() {
    var email = document.getElementById("form4Example2").value.trim();
    if (email === "") {
        document.getElementById("div2").innerHTML = "Please provide your E-mail address";
        return false;
    } else {
        // Check valid email
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (re.test(email)) {
            document.getElementById("div2").innerHTML = "";
            return true;
        } else {
            document.getElementById("div2").innerHTML = "E-mail is not valid";
            return false;
        }
    }
}

function validatePassword() {
    var password = document.getElementById("form4Example3").value.trim();
    if (password === "") {
        document.getElementById("div3").innerHTML = "Password is required";
        return false;
    } else {
        document.getElementById("div3").innerHTML = "";
        return true;
    }
}

function validateConfirmationPassword() {
    var password = document.getElementById("form4Example3").value.trim();
    var password2 = document.getElementById("form4Example4").value.trim();
    if (password2 === "") {
        document.getElementById("div4").innerHTML = "Confirmation Password is required";
        return false;
    } else if (password2 !== password) {
        document.getElementById("div4").innerHTML = "";
        document.getElementById("div5").innerHTML = "Password and Confirmation Password are not the same";
        // increase bottom padding of div5
        document.getElementById("div5").style.paddingBottom = "1em";
        return false;
    } else {
        document.getElementById("div4").innerHTML = "";
        document.getElementById("div5").innerHTML = "";
        return true;
    }
}
