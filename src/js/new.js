function validateTitle() {
    var title = document.getElementById("form4Example1").value;
    if (title == "") {
        document.getElementById("div1").innerHTML = "Title is required";
        return false;
    } else {
        document.getElementById("div1").innerHTML = "";
        return true;
    }
}
function validateSite(){
    var site = document.getElementById("form4Example2").value;
    if (site == "") {
        document.getElementById("div2").innerHTML = "Site is required";
        return false;
    } else {
        // Validate if the site is a valid URL
        var regex = /((?:(?:http?|ftp)[s]*:\/\/)?[a-z0-9-%\/\&=?\.]+\.[a-z]{2,4}\/?([^\s<>\#%"\,\{\}\\|\\\^\[\]`]+)?)/gi;
        if (!regex.test(site)) {
            document.getElementById("div2").innerHTML = "Please enter a valid URL";
            return false;
        } else {

        document.getElementById("div2").innerHTML = "";
        return true;
    }}
}
function validateUsername() {
    var username = document.getElementById("form4Example3").value;
    if (username == "") {
        document.getElementById("div3").innerHTML = "Username / Email is required";
        return false;
    } else {
        document.getElementById("div3").innerHTML = "";
        return true;
    }
}
function validatePassword() {
    var password = document.getElementById("form4Example4").value;
    if (password == "") {
        document.getElementById("div4").innerHTML = "Password is required";
        return false;
    } else {
        document.getElementById("div4").innerHTML = "";
        return true;
    }
}
function validateForm() {
    if (validateTitle() && validateSite() && validateUsername() && validatePassword()) {
        document.getElementById("send").disabled = false;
    } else {
        document.getElementById("send").disabled = true;
    }
}
