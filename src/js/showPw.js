function showPw(id) {
    const field = document.getElementById(id);
    const eye = document.getElementById(`${id}-eye`);
    if (field.type == "text") {
        field.type = "password";
        eye.classList.remove("fa-eye-slash");
        eye.classList.add("fa-eye");
    } else {
        field.type = "text";
        eye.classList.add("fa-eye-slash");
        eye.classList.remove("fa-eye");
    }
}