var username = document.forms['form']['username'];
var password = document.forms['form']['password'];
var error_username = document.getElementById('error_username');
var error_password = document.getElementById('error_password');
username.addEventListener('textInput', username_verify);
password.addEventListener('textInput', password_verify);
function validated() {
    if (username.value.test('[a-zAZ]')) {
        username.style.border = "1px,solid,red";
        error_username.style.display = "block";
        username.focus();
        return false;
    }
    if (password.value.length < 8) {
        password.style.border = "1px,solid,red";
        error_password.style.display = "block";
        error_password.focus();
        return false;
    }
}

function username_verify() {
    if (username.value.test('[a-zAZ]')) {
        username.style.border = "1px,solid,silver";
        error_username.style.display = "none";
        return true;
    }
}
function password_verify() {
    if (password.value.length >= 5) {
        password.style.border = "1px,solid,silver";
        error_password.style.display = "none";
        return true;
    }
}