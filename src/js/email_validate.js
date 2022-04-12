function validate() {
    var name = document.getElementById("form4Example1").value;
    var email = document.getElementById("form4Example2").value;
    var message = document.getElementById("form4Example3").value;
    var validn, valide, validm;

    if (name == "") {
        document.getElementById("div1").innerHTML = "Username is required";
        document.getElementById("form4Example1").style.borderColor = "red";
        validn = false;
    } else {
        document.getElementById("div1").innerHTML = "";
        document.getElementById("form4Example1").style.borderColor = "green";
        validn = true;
    }
    if (email == "") {
        document.getElementById("div2").innerHTML = "E-mail is required";
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

    if (message == "") {
        document.getElementById("div3").innerHTML = "Message is required";
        document.getElementById("form4Example3").style.borderColor = "red";
        validm = false;
    } else {
        document.getElementById("div3").innerHTML = "";
        document.getElementById("form4Example3").style.borderColor = "green";
        validm = true;
    }
    // Disable submit button until all field are filled
    if (validn && valide && validm) {
        document.getElementById("send").disabled = false;
    } else {
        document.getElementById("send").disabled = true;
    }
}