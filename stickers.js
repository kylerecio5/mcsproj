document.addEventListener("DOMContentLoaded", () => {
    // Select necessary elements
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

   

    const addButton = document.querySelector(".add-button");
    const addStickerPopup = document.getElementById("add-sticker-popup");
    const cancelAddStickerButton = document.getElementById("cancel-add-sticker");

    const editButtons = document.querySelectorAll(".edit");
    const editStickerPopup = document.getElementById("edit-sticker-popup");
    const cancelEditStickerButton = document.getElementById("cancel-edit-sticker");

    const addStickerForm = document.getElementById("add-sticker-form");
    const editForm = document.getElementById("edit-sticker-form");

    console.log("Script is running!");

    // Handle Logout Popup
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

    // Change Password Popup
    const changePasswordLink = document.getElementById("change_password");
    const changePasswordPopup = document.getElementById("change-password-self-popup");
    const cancelChangePasswordButton = document.getElementById("cancel-change-password-self");
    const changePasswordForm = document.getElementById("change-password-self-form");
    const newPasswordField = document.getElementById("new-password-self");
    const confirmPasswordField = document.getElementById("confirm-password-self");
    const newPasswordToggle = document.getElementById("toggle-password-self");
    const confirmPasswordToggle = document.getElementById("toggle-confirm-password-self");

    changePasswordLink?.addEventListener("click", (e) => {
        e.preventDefault();
        changePasswordPopup?.classList.remove("hidden");
    });

    cancelChangePasswordButton?.addEventListener("click", () => {
        changePasswordPopup?.classList.add("hidden");
    });

    newPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(newPasswordField, newPasswordToggle);
    });

    confirmPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
    });

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
    // Show Add Sticker Popup
    addButton?.addEventListener("click", () => {
        document.querySelector('#add-sticker-form').reset();  // Reset all form fields
        addStickerPopup?.classList.remove("hidden");
    });

    cancelAddStickerButton?.addEventListener("click", () => {
        addStickerPopup?.classList.add("hidden");
    });

    // Add Sticker Form Submission (AJAX)
    addStickerForm?.addEventListener("submit", function (e) {
        e.preventDefault();  // Prevent default form submission
        const formData = new FormData(addStickerForm);

        fetch('add_sticker.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                addStickerPopup?.classList.add("hidden");
                addStickerForm.reset();  // Reset form fields after successful submission
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert("An error occurred. Please try again.");
        });
    });
// Show Edit Sticker Popup and Populate Data
    editButtons.forEach(button => {
        button.addEventListener("click", function () {
            const stickerID = this.getAttribute('data-id');
            const firstName = this.getAttribute('data-first-name');
            const middleName = this.getAttribute('data-middle-name');
            const lastName = this.getAttribute('data-last-name');
            const phone = this.getAttribute('data-phone');
            const date = this.getAttribute('data-date');
            const vehicleType = this.getAttribute('data-vehicle-type');
            const plateNum = this.getAttribute('data-plate-num');
            const stickerNum = this.getAttribute('data-sticker-num');
            const amount = this.getAttribute('data-amount');

            
            // Log stickerID to check if it is being correctly fetched
            console.log("Sticker ID:", stickerID);
            // Populate the fields in the Edit Sticker popup
            document.querySelector('#edit-sticker-popup input[name="sticker_id"]').value = stickerID;  // Ensure sticker_id is populated
            document.querySelector('#edit-sticker-popup input[name="first_name"]').value = firstName;
            document.querySelector('#edit-sticker-popup input[name="middle_name"]').value = middleName;
            document.querySelector('#edit-sticker-popup input[name="last_name"]').value = lastName;
            document.querySelector('#edit-sticker-popup input[name="phone_number"]').value = phone;
            document.querySelector('#edit-sticker-popup input[name="sticker_number"]').value = stickerNum;
            document.querySelector('#edit-sticker-popup select[name="vehicle_type"]').value = vehicleType;
            document.querySelector('#edit-sticker-popup input[name="plate_number"]').value = plateNum;
            document.querySelector('#edit-sticker-popup input[name="amount"]').value = amount;
            document.querySelector('#edit-sticker-popup input[name="date"]').value = date;  // Added this line to populate the date

            // Show the Edit Sticker popup
            editStickerPopup?.classList.remove("hidden");
        });
    });


    // Hide Edit Sticker Popup
    cancelEditStickerButton?.addEventListener("click", () => {
        editStickerPopup?.classList.add("hidden");
    });

    // Edit Sticker Form Submission (AJAX)
    editForm?.addEventListener("submit", function (e) {
        e.preventDefault();  // Prevent default form submission
        const formData = new FormData(editForm);

        fetch('edit_sticker.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                editStickerPopup?.classList.add("hidden");
                location.reload();  // Optionally refresh the page or update the UI
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert("An error occurred. Please try again.");
        });
    });

    // Toggle Password Visibility Function
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

    // Remove Sticker (AJAX)
    document.querySelectorAll('.remove').forEach(button => {
        button.addEventListener('click', (e) => {
            const stickerId = e.target.getAttribute('data-id');
            if (confirm("Are you sure you want to remove this sticker?")) {
                fetch('remove_sticker.php', {
                    method: 'POST',
                    body: JSON.stringify({ stickerId: stickerId }),  // Send the sticker ID to the server
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Sticker removed successfully.");
                        location.reload();  // Reload the page to reflect changes
                    } else {
                        alert("An error occurred. Please try again.");
                    }
                })
                .catch(error => {
                    alert("Error: " + error);
                });
            }
        });
    });
});
