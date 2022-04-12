function validater() {
    var username = document.getElementById("form4Example1").value;
    var password = document.getElementById("form4Example2").value;
    var validu, validp;

    if (username == "") {
        document.getElementById("div1").innerHTML = "Username is required";
        document.getElementById("form4Example1").style.borderColor = "red";
        validu = false;
    } else {
        document.getElementById("div1").innerHTML = "";
        document.getElementById("form4Example1").style.borderColor = "green";
        validu = true;
    }
    if (password == "") {
        document.getElementById("div2").innerHTML = "Password is required";
        document.getElementById("form4Example2").style.borderColor = "red";
        validp = false;
    } else {
        document.getElementById("div2").innerHTML = "";
        document.getElementById("form4Example2").style.borderColor = "green";
        validp = true;
    }
    // Disable submit button until all field are filled
    if (validu && validp) {
        document.getElementById("send").disabled = false;
    } else {
        document.getElementById("send").disabled = true;
    }
}