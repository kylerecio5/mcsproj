document.addEventListener("DOMContentLoaded", () => {
    // Select necessary elements
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

   
    const changePasswordLink = document.getElementById("change_password");
    const changePasswordPopup = document.getElementById("change-password-self-popup");
    const cancelChangePasswordButton = document.getElementById("cancel-change-password-self");
    const changePasswordSelfForm = document.getElementById("change-password-self-form");
    const newPasswordField = document.getElementById("new-password-self");
    const confirmPasswordField = document.getElementById("confirm-password-self");
    const newPasswordToggle = document.getElementById("toggle-password-self");
    const confirmPasswordToggle = document.getElementById("toggle-confirm-password-self");


    const addButton = document.querySelector('.add-button'); // Select the ADD button
    const addPopup = document.getElementById('add-permit-popup'); // Select the popup
    const cancelButton = document.getElementById('cancel-add-permit'); // Select the CANCEL button in the popup
    const addPermitForm = document.getElementById("add-permit-form"); // Select the form

    console.log("Script is running!");

    // Show the popup when the "ADD" button is clicked
    addButton.addEventListener('click', () => {
        addPopup.classList.remove('hidden'); // Remove the "hidden" class to display the popup
    });

    // Hide the popup when the "CANCEL" button is clicked
    cancelButton.addEventListener('click', () => {
        addPopup.classList.add('hidden'); // Add the "hidden" class to hide the popup
    });

    const tableBody = document.querySelector(".resident-table tbody"); 
    // After saving the permit in c_permit.js
    addPermitForm.addEventListener('submit', (e) => {
        e.preventDefault(); // Prevent form from submitting the traditional way

        // Gather form data dynamically from the form fields
        const firstName = document.getElementById('first-name').value;
        const middleName = document.getElementById('middle-name').value;
        const lastName = document.getElementById('last-name').value;
        const phoneNumber = document.getElementById('phone-number').value;
        const buildingType = document.getElementById('building-type').value;
        const permitNumber = document.getElementById('permit-number').value;
        const block = document.getElementById('block').value;
        const lot = document.getElementById('lot').value;
        const street = document.getElementById('street').value;
        const amount = document.getElementById('amount').value;

        // Create new FormData object and append form data
        const formData = new FormData();
        formData.append('first-name', firstName);
        formData.append('middle-name', middleName);
        formData.append('last-name', lastName);
        formData.append('phone-number', phoneNumber);
        formData.append('building-type', buildingType);
        formData.append('permit-number', permitNumber);
        formData.append('block', block);
        formData.append('lot', lot);
        formData.append('street', street);
        formData.append('amount', amount);

        // Send data to the PHP script using POST request
        fetch('save_permit.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Parse the JSON response
        .then(data => {
            if (data.success) {
                console.log("Permit saved successfully.");

                // Add the new permit to the table
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>${firstName} ${middleName} ${lastName}</td>
                    <td>${phoneNumber}</td>
                    <td>${buildingType}</td>
                    <td>${permitNumber}</td>
                    <td>${new Date().toLocaleDateString()}</td> <!-- Replace with actual Permit Date -->
                    <td>${block}</td>
                    <td>${lot}</td>
                    <td>${street}</td>
                    <td>₱${parseFloat(amount).toFixed(2)}</td>
                    <td>
                        <button class="edit" data-id="${data.permit_id}" data-name="${firstName} ${middleName} ${lastName}" data-phone="${phoneNumber}" data-building-type="${buildingType}" data-permit-num="${permitNumber}" data-block="${block}" data-lot="${lot}" data-street="${street}" data-amount="${amount}">EDIT</button>
                        <button class="remove" data-id="${data.permit_id}">REMOVE</button>
                    </td>
                `;
                tableBody.appendChild(newRow); // Append the new row to the table

                addPopup.classList.add('hidden'); // Hide the popup after successful submission
            } else {
                console.log("Error:", data.message);
                // Optional: Display the error message to the user
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    

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
        window.location.href = "login.php"; // Redirect to the login page
    });

   // Change Password Popup Handlers
    changePasswordLink?.addEventListener("click", (e) => {
        e.preventDefault();
        changePasswordPopup?.classList.remove("hidden");
    });

    cancelChangePasswordButton?.addEventListener("click", () => {
        changePasswordPopup?.classList.add("hidden");
    });

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
                    headers: { 'Content-Type': 'application/json' },
                });
                const result = await response.json();
                if (result.success) {
                    alert('Password changed successfully!');
                    changePasswordPopup?.classList.add('hidden');
                    changePasswordSelfForm.reset();
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
            passwordToggle.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            passwordField.type = "password";
            passwordToggle.classList.replace("fa-eye-slash", "fa-eye");
        }
    }

    newPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(newPasswordField, newPasswordToggle);
    });

    confirmPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(confirmPasswordField, confirmPasswordToggle);
    });

});

