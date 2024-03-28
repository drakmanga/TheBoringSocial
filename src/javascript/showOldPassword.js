function showOldPassword() {
    var x = document.getElementById("oldPwd");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}