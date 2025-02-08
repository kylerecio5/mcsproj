function togglePasswordVisibility() {
    const passwordField = document.getElementById("password");
    const passwordToggle = document.querySelector(".toggle-password i");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        passwordToggle.classList.remove("fa-eye");
        passwordToggle.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password";
        passwordToggle.classList.remove("fa-eye-slash");
        passwordToggle.classList.add("fa-eye");
    }
}