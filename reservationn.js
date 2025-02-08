document.addEventListener("DOMContentLoaded", () => {
    // Select necessary elements
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

    const changePasswordLink = document.getElementById("change_password");
    const changePasswordPopup = document.getElementById("change-password-self-popup");
    const cancelChangePasswordButton = document.getElementById("cancel-change-password-self");
    const newPasswordField = document.getElementById("new-password-self");
    const confirmPasswordField = document.getElementById("confirm-password-self");
    const newPasswordToggle = document.getElementById("toggle-password-self");
    const confirmPasswordToggle = document.getElementById("toggle-confirm-password-self");

    const updateButtons = document.querySelectorAll(".update"); // Select all update buttons
    const updatePopup = document.getElementById("update-reservation-popup"); // Update popup element
    const closeUpdatePopupButton = document.getElementById("close-update-popup");

    console.log("Script is running!");

    // Add event listener to open the logout popup
    logoutLink?.addEventListener("click", (e) => {
        e.preventDefault();
        console.log("Logout link clicked!");
        logoutPopup?.classList.remove("hidden");
    });

    // Add event listener to cancel the logout
    cancelLogout?.addEventListener("click", () => {
        console.log("Cancel logout clicked!");
        logoutPopup?.classList.add("hidden");
    });

    // Add event listener to confirm the logout
    confirmLogout?.addEventListener("click", () => {
        console.log("Confirm logout clicked!");
        window.location.href = "login.html"; // Redirect to the login page
    });

    // Show the Change Password popup
    changePasswordLink?.addEventListener("click", (e) => {
        e.preventDefault();
        changePasswordPopup?.classList.remove("hidden");
    });

    // Hide the Change Password popup when Cancel is clicked
    cancelChangePasswordButton?.addEventListener("click", () => {
        changePasswordPopup?.classList.add("hidden");
    });

    // Toggle visibility for new password field
    newPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(newPasswordField, newPasswordToggle);
    });

    // Toggle visibility for confirm password field
    confirmPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
    });

    // Toggle password visibility function
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

    // Open Update Reservation popup when "Update" button is clicked
    updateButtons.forEach(button => {
        button.addEventListener("click", (e) => {
            const reservationId = e.target.getAttribute('data-id');
            console.log(`Update Reservation for ID: ${reservationId}`);

            // Check if the popup is present
            if (updatePopup) {
                console.log("Popup found! Removing 'hidden' class to show it.");
                updatePopup.classList.remove("hidden"); // Show the popup

                // Log to ensure visibility toggle is triggered
                console.log("Popup is now visible!");
            } else {
                console.log("Popup element not found! Check the HTML structure.");
            }
        });
    });

    // Close the Update Reservation popup
    closeUpdatePopupButton?.addEventListener("click", () => {
        if (updatePopup) {
            console.log("Closing Update popup.");
            updatePopup.classList.add("hidden"); // Hide the popup
        }
    });

});
