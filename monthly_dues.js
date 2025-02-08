document.addEventListener("DOMContentLoaded", () => {

    const addButton = document.querySelector(".add-button"); 
    const addDuesPopup = document.getElementById("add-dues-popup");
    const confirmAddDues = document.getElementById("confirm-add-dues");
    const cancelAddDues = document.getElementById("cancel-add-dues");
    const addDuesForm = document.getElementById("add-dues-form");
    const yearSort = document.querySelector(".yearSort");

    yearSort?.addEventListener("change", () => {

        const selectedYear = yearSort.value;
    // Log or use the selected value
        console.log(`Selected year: ${selectedYear}`);
        window.location.href  = `monthly_dues.php?year=${selectedYear}`;

    
    });

    // Show Add Dues Popup
    addButton?.addEventListener("click", () => {
        addDuesPopup?.classList.remove("hidden");
    });

     // Submit Add Dues Popup on Confirm
     confirmAddDues?.addEventListener("submit", (event) => {
        event.preventDefault(); // Prevent default form submission behavior
        window.location.href = window.location.href; // Reload the current page
    });

    // Hide Add Dues Popup on Cancel
    cancelAddDues?.addEventListener("click", () => {
        addDuesPopup?.classList.add("hidden");
    });

    // Form Submit logic
    addDuesForm?.addEventListener("submit", () => {
        alert("Monthly Dues Added Successfully!");
        addDuesPopup?.classList.add("hidden");
        addDuesForm.reset(); // Reset the form fields
    });


      // Get references to the elements
      const streetLightSelect = document.getElementById('street-light');
      const monthlyAmountInput = document.getElementById('monthly-amount');
  
      // Add an event listener to the street-light select dropdown
      streetLightSelect.addEventListener('change', function () {
          // Update the monthly amount based on the selected street light value
          if (streetLightSelect.value === 'Yes') {
              monthlyAmountInput.value = 1500;
          } else if (streetLightSelect.value === 'No') {
              monthlyAmountInput.value = 1000;
          }
      });

           // Get references to the elements
           const editstreetLightSelect = document.getElementById('edit_street-light');
           const editmonthlyAmountInput = document.getElementById('edit_monthly-amount');
       
           // Add an event listener to the street-light select dropdown
           editstreetLightSelect.addEventListener('change', function () {
               // Update the monthly amount based on the selected street light value
               if (editstreetLightSelect.value === 'Yes') {
                   editmonthlyAmountInput.value = 1500;
               } else if (editstreetLightSelect.value === 'No') {
                   editmonthlyAmountInput.value = 1000;
               }
           });

        const queryString = window.location.search;
        // Create a URLSearchParams object
        const urlParams = new URLSearchParams(queryString);

        // Get the value of the 'year' parameter
        const year = urlParams.get('year');
    // Function to fetch and display monthly dues data
    function fetchMonthlyDues() {

                // Get the query string from the current URL
        

        // Check and use the parameter
        if (year) {
            console.log(`Year parameter: ${year}`);
        } else {
            console.log("Year parameter is not present in the URL.");

            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('year', getCurrentYear()); // Set or update the 'year' parameter

            // Redirect to the same page with the updated URL
            window.location.href = currentUrl.toString();

        }
        //const yearFilter = document.getElementById("sort-by");
        document.getElementById("sort-by").value = year;
        

        const addPaymentModal = document.getElementById("add-payment-modal");
        const editPaymentModal = document.getElementById("edit-dues-popup");

        fetch('fetch_monthly_dues.php?year='+year)
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('monthly-dues-table-body');
                tableBody.innerHTML = ''; // Clear any existing data in the table
                data.forEach(dues => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td>${dues.residentcode}</td>
                <td>${dues.Resident}</td>
                <td>${dues.StreetLight}</td>
                <td>${dues.Amount}</td>
                <td>${dues.Status}</td>
                <td>
                    <button class="view-history-btn" data-id="${dues.ID}"  name="historyBtn">PAYMENT HISTORY</button>
                    <button class="show-add-payment-btn" data-resident="${dues.ID}" data-mdues="${dues.Amount}" data-exclude="${dues.Paid_Months}">ADD PAYMENT</button>
                    <button class="edit" data-id="${dues.ID}" data-duesId="${dues.Dues_ID}" data-resident="${dues.ID}"  data-streetlight="${dues.StreetLight}" data-mdues="${dues.Amount}">EDIT</button>
                    <button class="remove remove_duesBtn" data-duesId="${dues.Dues_ID}" name="remove_duesBtn" id="remove_duesBtn">ARCHIVE</button>
                </td>
                `;
                    tableBody.appendChild(row);

                    // Handle Add Payment modal
                    const addPaymentButtons = document.querySelectorAll(".show-add-payment-btn");
                    const confirmPaymentBtn = document.getElementById("confirm-payment-btn"); 
                    const cancelPaymentBtn = document.getElementById("cancel-payment-btn");

                    // Add click event listener to each "ADD PAYMENT" button
                    addPaymentButtons.forEach(button => {
                        button.addEventListener("click", () => {
                            console.log("Add Payment button clicked!");
                            addPaymentModal.classList.remove("hidden");

                            const selectElement = document.getElementById("payment-month");

                            const residentId = event.target.getAttribute('data-resident');
                            const mdues = event.target.getAttribute('data-mdues');
                            const exlude = event.target.getAttribute('data-exclude');
                            const listList = exlude.split("|");

                            Array.from(selectElement.options).forEach(option => {
                                option.hidden = false; // Show option
                            });

                        

                            Array.from(selectElement.options).forEach(option => {
                                if (listList.includes(option.value)) {
                                    option.hidden = true; // Hide the option
                                }
                            });

                            console.log("listList " + listList);
                            console.log("mdues " + mdues);
                            document.getElementById("add_payment_residentID").value = residentId;
                            document.getElementById("add_amount").value = mdues;
                        });
                    });

 // Confirm Modal when Confirm is clicked
 confirmPaymentBtn?.addEventListener("click", (event) => {
    event.preventDefault(); // Prevent form submission (and redirect)

    // Reload the page with the 'year' parameter
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('year', selectedYear); // Set or update the 'year' parameter

    // Redirect to the same page with the updated URL
    window.location.href = currentUrl.toString();
});

                    // Close Modal when Cancel is clicked
                    cancelPaymentBtn?.addEventListener("click", () => {
                        console.log("Cancel Add Payment clicked!");
                        addPaymentModal.classList.add("hidden");
                    });


                    // const remove_duesBtn = document.querySelectorAll(".remove_duesBtn");
                    // remove_duesBtn.forEach(button => {
                    //     button.addEventListener("click", () => {
                    //         const duesId = event.target.getAttribute('data-duesId');
                    //         console.log(duesId);
                    //         const idToRemove = duesId; // Or get this from user input
                    //         Swal.fire({
                    //             title: "Are you sure?",
                    //             text: "You won't be able to revert this!",
                    //             icon: "warning",
                    //             showCancelButton: true,
                    //             confirmButtonColor: "#3085d6",
                    //             cancelButtonColor: "#d33",
                    //             confirmButtonText: "Yes, remove it!"
                    //         }).then((result) => {
                    //             if (result.isConfirmed) {
                    //                 fetch(`remove_dues.php?idToRemove=${idToRemove}`)
                    //                     .then(response => {
                    //                         if (!response.ok) {
                    //                             throw new Error(`HTTP error! Status: ${response.status}`);
                    //                         }
                    //                         return response;
                    //                     })
                    //                     .then(data => {
                    //                         location.reload();
                    //                     })
                    //                     .catch(error => console.log('Error fetching data:', error));
                    //             }
                    //         });
                    //     });
                    // });


                    const remove_duesBtn = document.querySelectorAll(".remove_duesBtn");
                    const removePopup = document.getElementById("remove-popup");
                    const confirmRemoveBtn = document.getElementById("confirm-remove");
                    const cancelRemoveBtn = document.getElementById("cancel-remove");
                    const dataDuesId = document.getElementById("data-duesId");
                    let duesIdToRemove = null;

                    // Show the popup
                    remove_duesBtn.forEach(button => {
                        button.addEventListener("click", (event) => {
                            duesIdToRemove = event.target.getAttribute('data-duesId');
                            console.log(duesIdToRemove);
                            //dataDuesId.textContent = duesIdToRemove; // Display the duesId in the popup
                            removePopup.classList.remove("hidden"); // Show the popup
                        });
                    });

                    // Handle the "Confirm" button
                    confirmRemoveBtn.addEventListener("click", () => {
                        // Make the fetch request to remove the dues
                        fetch(`remove_dues.php?idToRemove=${duesIdToRemove}`)
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`HTTP error! Status: ${response.status}`);
                                }
                                return response.json(); // Or appropriate data handling
                            })
                            .then(data => {
                                location.reload(); // Reload the page after removal
                            })
                            .catch(error => console.log('Error fetching data:', error));

                        // Hide the popup after confirming
                        removePopup.classList.add("hidden");
                    });

                    // Handle the "Cancel" button
                    cancelRemoveBtn.addEventListener("click", () => {
                        removePopup.classList.add("hidden"); // Hide the popup if cancelled
                    });



                    // Handle View modal
                    const viewHistoryButton = document.querySelectorAll(".view-history-btn"); // Button to open history popup
                    const historyPopup = document.getElementById("history-popup");
                    const closeHistoryButton = document.getElementById("close-history");

                    // View History Popup
                    viewHistoryButton.forEach(button => {
                        button.addEventListener("click", () => {
                            console.log("View History button clicked!");
                            historyPopup.classList.remove("hidden");
                            const residentId = event.target.getAttribute('data-id');
                            console.log(`Button clicked for data-id: ${residentId}`);
                            const userId = residentId; // Or get this from user input

                            fetch(`fetch_dues_history.php?user=${userId}&year=${year}`)
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! Status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Data fetched successfully:', data);
                                    const tableBodyH = document.getElementById('history-table-body');
                                    tableBodyH.innerHTML = ''; // Clear any existing data in the table

                                    let html = ``;

                                    data.forEach(info => {
                                       // const rowH = document.createElement('tr');

                                        html = html+`<tr>

                                        
                                        <td>${info.Month}</td>
                                        <td>${info.Year}</td> 
                                        <td hidden>${info.Amount_Due}</td>
                                        <td>${info.Amount_Paid}</td>
                                        <td>${info.Payment_Date}</td> 
                                        <td hidden>
                                        
                                       <form action="remove_history.php" method="POST">
                                         <input type="hidden" name="idToRemove" value="${info.History_ID}">
                                         `;
                                         if(info.Amount_Paid > 0){
                                            html = html+`<button class="remove remove_historyBtn" id="remove_historyBtn" onclick="alert('History has been successfully removed');" type="submit">REMOVE</button>`
                                         }
                                          html = html+`
                                              </form>
                                        </td>
                                        
                                        
                                        </tr>
                                        
                                        `;
                                        // Update this part with the correct properties from your data
                                        
                                    });

                                    tableBodyH.innerHTML = html;
                                        // Append the row to the table body
                                       // tableBodyH.appendChild(rowH);
                                })
                                .catch(error => console.log('Error fetching data:', error));
                        });
                    });

                    // Close History Popup
                    closeHistoryButton.addEventListener("click", () => {
                        console.log("Cancel View History clicked!");
                        historyPopup.classList.add("hidden");
                    });

                    // Edit Montly Dues 
                    const EditButton = document.querySelector(".edit");
                    const EditDuesPopup = document.getElementById("edit-dues-popup");
                    const cancelEditDues = document.getElementById("cancel-edit-dues");
                    const EditDuesForm = document.getElementById("edit-dues-form");

                    // Show Edit Dues Popup
                    EditButton.addEventListener("click", () => {
                        EditDuesPopup?.classList.remove("hidden");
                    });
                    // Hide Edit Dues Popup on Cancel
                    cancelEditDues?.addEventListener("click", () => {
                        EditDuesPopup?.classList.add("hidden");
                    });
                    // Form Submit logic
                    EditDuesForm?.addEventListener("submit", () => {
                        alert("Monthly Dues Edited Successfully!");
                        EditDuesPopup?.classList.add("hidden");
                        EditDuesForm.reset(); // Reset the form fields
                    });

                    const EditPaymentButtons = document.querySelectorAll(".edit");

                    // Add click event listener to each "ADD PAYMENT" button
                    EditPaymentButtons.forEach(button => {
                        button.addEventListener("click", (event) => { // Explicitly pass the event object
                            console.log("Edit Payment button clicked!");

                            // Get the 'data-id' attribute from the clicked button
                            const dataDuesID = event.target.getAttribute('data-duesId');
                            const dataId = event.target.getAttribute('data-id');
                            const dataResident = event.target.getAttribute('data-resident');
                            const dataStreetlight = event.target.getAttribute('data-streetlight');
                            const dataAmount = event.target.getAttribute('data-mdues');
                            console.log(`Button clicked for data-id: ${dataId}`);
                            console.log(`Button clicked for data-id: ${dataDuesID}`);
                            console.log(`Button clicked for data-id: ${dataResident}`);
                            console.log(`Button clicked for data-id: ${dataStreetlight}`);
                            console.log(`Button clicked for data-id: ${dataAmount}`);

                            // Show the edit payment modal
                            editPaymentModal.classList.remove("hidden");

                            // Set the value of the input element
                            document.getElementById("idToEdit").value = dataDuesID;
                            //document.getElementById("idToEdit").value = dataId;
                            document.getElementById("edit_resident").value = dataResident;
                            document.getElementById("edit_street-light").value = dataStreetlight;
                            document.getElementById("edit_monthly-amount").value = dataAmount;
                        });
                    });

                });
            })
            .catch(error => console.error('Error fetching data:', error));

    }

    window.onload = fetchMonthlyDues;

    // // Handle Form Submission
    // const paymentForm = document.querySelector('.payment-form');
    // paymentForm.addEventListener('submit', (event) => {
    //     event.preventDefault(); // Prevent form submission
    //     const paymentDate = document.getElementById('payment-date').value;
    //     const paymentMonth = document.getElementById('payment-month').value;
    //     document.getElementById('add-payment-modal').classList.add('hidden');
    // });

    // Logout Popup
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

    // Show logout popup when logout link is clicked
    logoutLink?.addEventListener("click", (e) => {
        e.preventDefault();
        console.log("Logout link clicked!");
        logoutPopup?.classList.remove("hidden");
    });

    // Cancel logout action
    cancelLogout?.addEventListener("click", () => {
        console.log("Cancel logout clicked!");
        logoutPopup?.classList.add("hidden");
    });

    // Confirm logout action
    confirmLogout?.addEventListener("click", () => {
        console.log("Confirm logout clicked!");
        window.location.href = "login.php"; // Redirect to login page
    });

    // Change Password Popup
    const changePasswordLink = document.getElementById("change_password");
    const changePasswordPopup = document.getElementById("change-password-self-popup");
    const cancelChangePasswordButton = document.getElementById("cancel-change-password-self");
    const newPasswordField = document.getElementById("new-password-self");
    const confirmPasswordField = document.getElementById("confirm-password-self");
    const newPasswordToggle = document.getElementById("toggle-password-self");
    const confirmPasswordToggle = document.getElementById("toggle-confirm-password-self");

    // Show change password popup
    changePasswordLink?.addEventListener("click", (e) => {
        e.preventDefault();
        changePasswordPopup?.classList.remove("hidden");
    });

    // Cancel password change action
    cancelChangePasswordButton?.addEventListener("click", () => {
        changePasswordPopup?.classList.add("hidden");
    });

    // Toggle password visibility
    newPasswordToggle?.addEventListener("click", () => {
        togglePasswordVisibility(newPasswordField, newPasswordToggle);
    });

    // Toggle confirm password visibility
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

    function getCurrentYear(){

        const currentYear = new Date().getFullYear();
        console.log(currentYear);
        return currentYear;

    }

});

