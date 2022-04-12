var validt, valids, validu, validp;


function validateTitle() {
    var title = document.getElementById("form4Example1").value;
    if (title == "") {
        document.getElementById("div1").innerHTML = "Title is required";
        validt = false;
    } else {
        document.getElementById("div1").innerHTML = "";
        validt = true;
    }
}
function validateSite(){
    var site = document.getElementById("form4Example2").value;
    if (site == "") {
        document.getElementById("div2").innerHTML = "Site is required";
        valids = false;
    } else {
        // Validate if the site is a valid URL
        var regex = /((?:(?:http?|ftp)[s]*:\/\/)?[a-z0-9-%\/\&=?\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?)/gi;
        if (!regex.test(site)) {
            document.getElementById("div2").innerHTML = "Please enter a valid URL";
            valids = false;
        } else {

        document.getElementById("div2").innerHTML = "";
        valids = true;
    }}
}
function validateUsername() {
    var username = document.getElementById("form4Example3").value;
    if (username == "") {
        document.getElementById("div3").innerHTML = "Username / Email is required";
        validu = false;
    } else {
        document.getElementById("div3").innerHTML = "";
        validu = true;
    }
}
function validatePassword() {
    var password = document.getElementById("form4Example4").value;
    if (password == "") {
        document.getElementById("div4").innerHTML = "Password is required";
        validp = false;
    } else {
        document.getElementById("div4").innerHTML = "";
        validp = true;
    }
}
function validateForm() {
    validateTitle();
    validateSite();
    validateUsername();
    validatePassword();
    if (validt && valids && validu && validp) {
        document.getElementById("send").disabled = false;
    } else {
        document.getElementById("send").disabled = true;
    }
}
