document.addEventListener("DOMContentLoaded", () => {
    // Logout popup functionality
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

    // Add Resident popup functionality
    const addButton = document.querySelector(".add-button");
    const addResidentPopup = document.getElementById("add-resident-popup");
    const cancelAddButton = document.getElementById("cancel-add");
    const addResidentForm = document.getElementById("add-resident-form");

    // Change Password popup functionality
    const changePasswordLink = document.getElementById("change_password");
    const changePasswordPopup = document.getElementById("change-password-self-popup");
    const cancelChangePasswordButton = document.getElementById("cancel-change-password-self");
    const changePasswordForm = document.getElementById("change-password-self-form");

    // Password fields and toggle icons
    const newPasswordField = document.getElementById("new-password-self");
    const confirmPasswordField = document.getElementById("confirm-password-self");
    const newPasswordToggle = document.getElementById("toggle-password-self");
    const confirmPasswordToggle = document.getElementById("toggle-confirm-password-self");

    // Logout popup events
    logoutLink?.addEventListener("click", (e) => {
        e.preventDefault();
        logoutPopup?.classList.remove("hidden");
    });

    cancelLogout?.addEventListener("click", () => {
        logoutPopup?.classList.add("hidden");
    });

    confirmLogout?.addEventListener("click", () => {
        window.location.href = "login.php"; // Redirect to the login page
    });

    // Add Resident popup events
    addButton?.addEventListener("click", () => {
        addResidentPopup?.classList.remove("hidden");
    });

    cancelAddButton?.addEventListener("click", () => {
        addResidentPopup?.classList.add("hidden");
    });

    addResidentForm?.addEventListener("submit", (e) => {
        e.preventDefault();
        console.log("Form submitted");
        // Handle form submission logic here
        addResidentPopup?.classList.add("hidden");
    });

    // Change Password popup events
    changePasswordLink?.addEventListener("click", (e) => {
        e.preventDefault();
        changePasswordPopup?.classList.remove("hidden");
    });

    cancelChangePasswordButton?.addEventListener("click", () => {
        changePasswordPopup?.classList.add("hidden");
    });

    // Change Password form submission
    changePasswordForm?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(changePasswordForm);
        const newPassword = formData.get("new_password");
        const confirmPassword = formData.get("confirm_password");

        if (newPassword === confirmPassword) {
            try {
                const response = await fetch('change_password_self.php', {
                    method: 'POST',
                    body: JSON.stringify({ new_password: newPassword }),
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                const result = await response.json();
                if (result.success) {
                    alert('Password changed successfully!');
                    changePasswordPopup?.classList.add('hidden');
                    changePasswordForm.reset();
                } else {
                    alert(result.message || 'Failed to change password.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An unexpected error occurred. Please try again.');
            }
        } else {
            alert("Passwords do not match. Please try again.");
        }
    });

    // Password visibility toggle functionality
    newPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(newPasswordField, newPasswordToggle);
    });

    confirmPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
    });

    function togglePasswordVisibility(passwordField, passwordToggle) {
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
});
