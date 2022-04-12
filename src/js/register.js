function validater() {
    var username = document.getElementById("form4Example1").value;
    var email = document.getElementById("form4Example2").value;
    var password = document.getElementById("form4Example3").value;
    var password2 = document.getElementById("form4Example4").value;

    var validu, valide, validp;

    if (username == "") {
        document.getElementById("div1").innerHTML = "Username is required";
        validu = false;
    } else {
        document.getElementById("div1").innerHTML = "";
        validu = true;
    }
    
    if (email == "") {
        document.getElementById("div2").innerHTML = "E-mail is required";
        valide = false;
    } else {
        // Check valid email
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (re.test(email)) {
        document.getElementById("div2").innerHTML = "";
        valide = true;
    } else {
        document.getElementById("div2").innerHTML = "E-mail is not valid";
        valide = false;
    }
    }
    
    if (password == "") {
        document.getElementById("div3").innerHTML = "Password is required";
        validp = false;
    } else {
        document.getElementById("div3").innerHTML = "";
        validp = true;
    }

    if (password2 == "") {
        document.getElementById("div4").innerHTML = "Confirmation Password is required";
        validp = false;
    } else {
        document.getElementById("div4").innerHTML = "";
        validp = true;
    }

    if(password != password2) {
        document.getElementById("div5").innerHTML = "Password and Confirmation Password are not the same";
        // increase bottom padding of div5
        document.getElementById("div5").style.paddingBottom = "1em";
        validp = false;
    } else {
        document.getElementById("div5").innerHTML = "";
        validp = true;
    }


    // Disable submit button until all field are filled
    if (validu && valide && validp) {
        document.getElementById("send").disabled = false;
    } else {
        document.getElementById("send").disabled = true;
    }
}