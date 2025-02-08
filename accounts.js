document.addEventListener("DOMContentLoaded", () => {
    // Elements for the logout popup
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

    // Elements for the "Add Account" popup
    const addButton = document.querySelector(".add-button");
    const addAccountPopup = document.getElementById("add-account-popup");
    const cancelAdd = document.getElementById("cancel-add");
    const addAccountForm = document.getElementById("add-account-form");

    // Elements for the "Edit Account" popup
    const editButton = document.querySelector(".edit-button");
    const editAccountPopup = document.getElementById("edit-account-popup");
    const cancelEdit = document.getElementById("cancel-edit");
    const editAccountForm = document.getElementById("edit-account-form");

    // Elements for the "Change Password" popup
    const changePassButton = document.querySelector(".change_pass");
    const changePassPopup = document.getElementById("change-password-popup");
    const cancelChangePass = document.getElementById("cancel-change-password");
    const changePassForm = document.getElementById("change-password-form");

    // Elements for the "Remove Account" popup
    const removeButtons = document.querySelectorAll(".remove");
    const removePopup = document.getElementById("remove-popup");
    const cancelRemove = document.getElementById("cancel-remove");
    const confirmRemove = document.getElementById("confirm-remove");

    // Elements for the "Change Password Self" popup
    const changePasswordSelfLink = document.getElementById("change_password_self");
    const changePasswordSelfPopup = document.getElementById("change-password-self-popup");
    const cancelChangePasswordSelf = document.getElementById("cancel-change-password-self");
    const changePasswordSelfForm = document.getElementById("change-password-self-form");

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

    // When clicking any of the remove buttons, show the remove popup
    removeButtons.forEach(button => {
        button.addEventListener("click", (e) => {
            e.preventDefault();
            console.log("Remove button clicked!");
            removePopup?.classList.remove("hidden");
        });
    });

    // Remove Account Popup: Cancel
    cancelRemove?.addEventListener("click", () => {
        console.log("Cancel remove clicked!");
        removePopup?.classList.add("hidden");
    });

    // Remove Account Popup: Confirm
    confirmRemove?.addEventListener("click", () => {
        console.log("Confirm remove clicked!");
        // Perform the removal logic (For now, we log it)
        alert("Account removed successfully!");
        removePopup?.classList.add("hidden");
    });


   
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            this.textContent = 'Hide'; // Change button text
        } else {
            passwordField.type = 'password';
            this.textContent = 'Show'; // Change button text
        }
    });


    // Add Account Popup: Open
    addButton?.addEventListener("click", () => {
        addAccountPopup?.classList.remove("hidden");
    });

    // Add Account Popup: Cancel
    cancelAdd?.addEventListener("click", () => {
        addAccountPopup?.classList.add("hidden");
    });

    // Add Account Popup: Form Submission
    addAccountForm?.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(addAccountForm);
        console.log("Form submitted:", Object.fromEntries(formData));
        addAccountPopup?.classList.add("hidden");
    });

    // Edit Account Popup: Open
    editButton?.addEventListener("click", () => {
        editAccountPopup?.classList.remove("hidden");
    });

    // Edit Account Popup: Cancel
    cancelEdit?.addEventListener("click", () => {
        editAccountPopup?.classList.add("hidden");
    });

    // Edit Account Popup: Form Submission
    editAccountForm?.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(editAccountForm);
        console.log("Form submitted:", Object.fromEntries(formData));
        editAccountPopup?.classList.add("hidden");
    });

    // Change Password Popup: Open
    changePassButton?.addEventListener("click", () => {
        changePassPopup?.classList.remove("hidden");
    });

    // Change Password Popup: Cancel
    cancelChangePass?.addEventListener("click", () => {
        changePassPopup?.classList.add("hidden");
    });


    // Change Password Self Popup: Open
    changePasswordSelfLink?.addEventListener("click", () => {
        changePasswordSelfPopup?.classList.remove("hidden");
    });

    // Change Password Self Popup: Cancel
    cancelChangePasswordSelf?.addEventListener("click", () => {
        changePasswordSelfPopup?.classList.add("hidden");
    });

    // Change Password Self Popup: Form Submission, do this for all pages
    changePasswordSelfForm?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData(changePasswordSelfForm);
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
                    changePasswordSelfPopup?.classList.add('hidden');
                    changePasswordSelfForm.reset(); // Clear the form fields
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
    

    // Toggle Password Visibility
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

    // Toggle Password Visibility (For Change Password)
    const passwordField = document.getElementById("new-password");
    const passwordToggle = document.getElementById("toggle-password");
    if (passwordField && passwordToggle) {
        passwordToggle.addEventListener("click", () => {
            togglePasswordVisibility(passwordField, passwordToggle);
        });
    }

    // Toggle Confirm Password Visibility (For Change Password)
    const confirmPasswordField = document.getElementById("confirm-password");
    const confirmPasswordToggle = document.getElementById("toggle-confirm-password");
    if (confirmPasswordField && confirmPasswordToggle) {
        confirmPasswordToggle.addEventListener("click", () => {
            togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
        });
    }

    // Toggle Password Visibility (For Change Password Self)
    const passwordSelfField = document.getElementById("new-password-self");
    const passwordSelfToggle = document.getElementById("toggle-password-self");
    if (passwordSelfField && passwordSelfToggle) {
        passwordSelfToggle.addEventListener("click", () => {
            togglePasswordVisibility(passwordSelfField, passwordSelfToggle);
        });
    }

    // Toggle Confirm Password Visibility (For Change Password Self)
    const confirmPasswordSelfField = document.getElementById("confirm-password-self");
    const confirmPasswordSelfToggle = document.getElementById("toggle-confirm-password-self");
    if (confirmPasswordSelfField && confirmPasswordSelfToggle) {
        confirmPasswordSelfToggle.addEventListener("click", () => {
            togglePasswordVisibility(confirmPasswordSelfField, confirmPasswordSelfToggle);
        });
    }
});
