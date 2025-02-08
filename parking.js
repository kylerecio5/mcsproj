document.addEventListener("DOMContentLoaded", () => {
    // Elements for Logout Popup
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

    // Elements for Change Password Popup
    const changePasswordLink = document.getElementById("change_password");
    const changePasswordPopup = document.getElementById("change-password-self-popup");
    const cancelChangePasswordButton = document.getElementById("cancel-change-password-self");
    const changePasswordForm = document.getElementById("change-password-self-form");
    const newPasswordField = document.getElementById("new-password-self");
    const confirmPasswordField = document.getElementById("confirm-password-self");
    const newPasswordToggle = document.getElementById("toggle-password-self");
    const confirmPasswordToggle = document.getElementById("toggle-confirm-password-self");

    // Elements for Add Parking Popup
    const addButton = document.querySelector(".add-button"); // The ADD + button for parking
    const addParkingPopup = document.getElementById("add-parking-popup"); // The popup for parking
    const cancelAddParkingButton = document.getElementById("cancel-add-parking"); // The Cancel button inside the popup
    const form = document.getElementById("add-parking-form"); // The form for adding parking

    console.log("Script is running!");

    // Logout Popup: Open
    logoutLink?.addEventListener("click", (e) => {
        e.preventDefault();
        console.log("Logout link clicked!");
        logoutPopup?.classList.remove("hidden");
    });

    // Logout Popup: Cancel
    cancelLogout?.addEventListener("click", () => {
        console.log("Cancel logout clicked!");
        logoutPopup?.classList.add("hidden");
    });

    // Logout Popup: Confirm
    confirmLogout?.addEventListener("click", () => {
        console.log("Confirm logout clicked!");
        window.location.href = "login.php"; // Redirect to the login page
    });

    // Change Password Popup: Open
    changePasswordLink?.addEventListener("click", (e) => {
        e.preventDefault();
        changePasswordPopup?.classList.remove("hidden");
    });

    // Change Password Popup: Cancel
    cancelChangePasswordButton?.addEventListener("click", () => {
        changePasswordPopup?.classList.add("hidden");
    });

    // Change Password Popup: Form Submission
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
                    changePasswordForm.reset(); // Clear the form fields
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

    // Toggle Password Visibility Functions
    const togglePasswordVisibility = (passwordField, passwordToggle) => {
        if (passwordField.type === "password") {
            passwordField.type = "text";
            passwordToggle.classList.remove("fa-eye");
            passwordToggle.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            passwordToggle.classList.remove("fa-eye-slash");
            passwordToggle.classList.add("fa-eye");
        }
    };

    // Toggle visibility for new password field
    newPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(newPasswordField, newPasswordToggle);
    });

    // Toggle visibility for confirm password field
    confirmPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
    });

    // Add Parking Popup: Open
    addButton?.addEventListener("click", () => {
        addParkingPopup?.classList.remove("hidden");
    });

    // Add Parking Popup: Cancel
    cancelAddParkingButton?.addEventListener("click", () => {
        addParkingPopup?.classList.add("hidden");
    });

    // Add Parking Form Submission
    form?.addEventListener("submit", (e) => {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(form); // Collect all form data

        // Use Fetch API for AJAX
        fetch("add_parking.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) // Parse the JSON response
        .then(data => {
            if (data.success) {
                alert("Parking added successfully!");
                addParkingPopup.classList.add("hidden");  // Hide popup after success
                form.reset();  // Reset form fields
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Something went wrong. Please try again.");
        });
    });
});
