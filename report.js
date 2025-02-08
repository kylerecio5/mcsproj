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
        window.location.href = "login.php"; // Redirect to the login page
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

    const transactionType = document.getElementById('transaction_dropdown');
    transactionType.addEventListener('change', () => {

        const val = transactionType.value;
        const tableBody = document.getElementById('tableId');

        tableBody.innerHTML = ``;
        if (val == 'Monthly Dues') {

            fetch(`fetch_reports.php?reportType=MonthlyDues`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data fetched successfully:', data);
                    var values = '';
                    values = `
                         <table class="resident-table">
                            <thead>
                                <tr>
                                    <th>TRANSACTION</th>
                                    <th>NAME</th>
                                    <th>MEMBER TYPE</th>
                                    <th>STREET LIGHT</th>
                                    <th>AMOUNT</th>
                                    <th>DATE</th>
                                    <th>MONTH</th>
                                </tr>
                             </thead>

                    <tbody>
                    `;

                    let totalDues = 0.00;
                    data.forEach(info => {
                        totalDues += parseFloat(info.Amount);
                        values = values + `
                            <tr>
                                <td>${info.TransactionType}</td>
                                    <td>${info.Name}</td>
                                    <td>${info.MemberType}</td>
                                    <td>${info.StreetLight}</td>
                                    <td>${info.Amount}</td>
                                    <td>${info.Date}</td>
                                    <td>${info.Month}</td>
                            </tr>
                        `;

                    });

                    let doubleNum = parseFloat(totalDues).toFixed(2);
                    let num = doubleNum;
                    let formattedNum = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(num);

                    values = values + `
                    </tbody>
                            
                    </table>
                        <table class="resident-table" style="width: 100%; border-collapse: collapse;"> 
                            <thead>
                                <tr>
                                    <th style= max-width: 50%; ">SUMMARY REPORT FOR MONTHLY DUES</th>
                                    <th style= max-width: 50%; "></th>
                                </tr>
                            </thead>
                                                    
                    <tbody>
                        <tr>
                            <td style="text-align: center; max-width: 50%">
                                <h5>TOTAL MONTHLY DUES COLLECTED:</h5>
                                    ₱${formattedNum}
                                        </td>
                            
                                        <!-- Add other rows similarly -->
                        </tbody>
                    </table>
                                        `;

                    tableBody.innerHTML = values;
                })
                .catch(error => console.log('Error fetching data:', error));


        } else if (val == 'Stickers') {
            fetch(`fetch_reports.php?reportType=Stickers`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data fetched successfully:', data);
                    var values = '';
                    values = `
                        <table class="resident-table">
                            <thead>
                                <tr>
                                    <th>TRANSACTION</th>
                                    <th>NAME</th>
                                    <th>PHONE NO.</th>
                                    <th>DATE</th>
                                    <th>VEHICLE TYPE</th>
                                    <th>PLATE NO.</th>
                                    <th>STICKER NO.</th>
                                    <th>AMOUNT</th>
                                </tr>
                            </thead>
                        <tbody>
                                            `;

                    let totalDues = 0.00;
                    let count = 0;

                    data.forEach(info => {
                        count++;
                        totalDues += parseFloat(info.Amount);
                        values = values + `
                            <tr>
                                <td>${info.TransactionType}</td>
                                <td>${info.ResidentName}</td>
                                <td>${info.PhoneNo}</td>
                                <td>${info.Date}</td>
                                <td>${info.VehicleType}</td>
                                <td>${info.PlateNum}</td>
                                <td>${info.StickerNum}</td>
                                <td>${info.Amount}</td>                        
                            </tr>
                        `;
                    });

                    let doubleNum = parseFloat(totalDues).toFixed(2);
                    let num = doubleNum;
                    let formattedNum = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(num);

                    values = values + `
                        </tbody>
                    </table>
                                        
                    <table class="resident-table" style="width: 100%; border-collapse: collapse;"> 
                        <thead>
                            <tr>
                                <th style= max-width: 50%; ">SUMMARY REPORT FOR STICKERS</th>
                                <th style= max-width: 50%; "></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center;max-width: 50%">
                                    <h5>TOTAL NUMBER OF TICKET SOLD</h5>${count}</td>
                                <td style="text-align: center; max-width: 50%">
                                    <h5>TOTAL AMOUNT:</h5>
                                        ₱${totalDues}</td>
                            </tr>
                                <!-- Add other rows similarly -->
                        </tbody>
                    </table>
                    `;

                    tableBody.innerHTML = values;
                })
                .catch(error => console.log('Error fetching data:', error));

        } else if (val == 'Reservations') {
            tableBody.innerHTML = `
                <table class="resident-table">
                    <thead>
                        <tr>
                            <th>TRANSACTION</th>
                            <th>NAME</th>
                            <th>PHONE NO.</th>
                            <th>RESERVATION TYPE</th>
                            <th>BOOKING NO.</th>
                            <th>CLIENT TYPE</th>
                            <th>RESERVATION DATE</th>
                            <th>AMOUNT</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Reservations</td>
                            <td>Vladimir D. Leyson</td>
                            <td>09876543212</td>
                            <td>Basketball Court</td>
                            <td>#001</td>
                            <td>Outsider</td>
                            <td>06/01/2020</td>
                            <td>₱1,000.00</td>
                            <td>PAID</td>
                        </tr>
                        <!-- Add other rows similarly -->
                    </tbody>
                </table>
                <table class="resident-table" style="width: 100%; border-collapse: collapse;"> 

                    <thead>
                        <tr>
                            <th style= max-width: 50%; ">SUMMARY REPORT FOR RESERVATIONS</th>
                            <th style= max-width: 50%; "></th>
                            <th style= max-width: 50%; "></th>
                            <th style= max-width: 50%; "></th>
                            <th style= max-width: 50%; "></th>
                            <th style= max-width: 50%; "></th>
                        </tr>
                    </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center; max-width: 50%">
                            <h5>TOTAL NUMBER OF RESERVATIONS:</h5></td>
                        <td style="text-align: center;max-width: 50%">
                            <h5>BASKETBALL COURT</h5>1</td>
                        <td style="text-align: center; max-width: 50%">
                            <h5>VOLLEYBALL COURT</h5> 0 </td>
                        <td style="text-align: center;max-width: 50%">
                            <h5>TENNIS COURT</h5>0</td>
                        <td style="text-align: center; max-width: 50%">
                            <h5>CLUBHOUSE</h5> 1 </td>
                        <td style="text-align: center;max-width: 50%">
                            <h5>SWIMMING POOL</h5>0</td>
                    </tr>

                    <tr>
                        <td style="text-align: center; max-width: 50%">
                            <h5>TOTAL NUMBER OF VOID:</h5></td>
                        <td style="text-align: center;max-width: 50%">
                            1</td>
                        <td style="text-align: center; max-width: 50%">
                            0 </td>
                        <td style="text-align: center;max-width: 50%">
                            0</td>
                        <td style="text-align: center; max-width: 50%">
                            1 </td>
                        <td style="text-align: center;max-width: 50%">
                        0</td>
                    </tr>

                    <tr>
                        <td style="text-align: center; max-width: 50%">
                            <h5>TOTAL NUMBER OF PAID:</h5></td>
                        <td style="text-align: center;max-width: 50%">
                            1</td>
                        <td style="text-align: center; max-width: 50%">
                            0 </td>
                        <td style="text-align: center;max-width: 50%">
                            0</td>
                        <td style="text-align: center; max-width: 50%">
                            1 </td>
                        <td style="text-align: center;max-width: 50%">
                            0</td>
                    </tr>

                    <tr style="text-align: center;">
                        <th style=max-width: 50%">
                            <h5>TOTAL AMOUNT COLLECTED:</th>
                        <th colspan="5"> </h5><h3 style="text-align: center; margin-left:-100px">₱1,000.00</h3></th>
                    </tr> 
                        <!-- Add other rows similarly -->
                    </tbody>
                </table>
                `;

        } else if (val == 'C Permit') {
            fetch(`fetch_reports.php?reportType=CPermit`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data fetched successfully:', data);
                    var values = '';
                    values = `
                        <table class="resident-table">
                            <thead>
                                <tr>
                                    <th>TRANSACTION</th>
                                    <th>NAME</th>
                                    <th>BUILDING TYPE NO.</th>
                                    <th>PERMIT NO.</th>
                                    <th>DATETYPE</th>
                                    <th>BLOCK</th>
                                    <th>LOT</th>
                                    <th>STREET</th>
                                    <th>AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody>
                           `;
                    let totalDues = 0.00;
                    let count = 0;

                    data.forEach(info => {
                        count++;
                        totalDues += parseFloat(info.Amount);
                        values = values + `
                            <tr>
                                <td>${info.TransactionType}</td>
                                    <td>${info.ResidentName}</td>
                                    <td>${info.BuildingType}</td>
                                    <td>${info.PermitNum}</td>
                                    <td>${info.PermitDate}</td>
                                    <td>${info.Block}</td>
                                    <td>${info.Lot}</td>
                                    <td>${info.Street}</td>
                                    <td>${info.Amount}</td>
                            </tr>
                        `;
                    });

                    let doubleNum = parseFloat(totalDues).toFixed(2);
                    let num = doubleNum;
                    let formattedNum = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(num);

                    values = values + `
                        </tbody>
                    </table>
                                                
                    <table class="resident-table" style="width: 100%; border-collapse: collapse;"> 
                        <thead>
                            <tr>
                                <th style= max-width: 50%; ">SUMMARY REPORT FOR C PERMIT</th>
                                <th style= max-width: 50%; "></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center; max-width: 50%">
                                    <h5>TOTAL ISSUED C PERMIT:</h5>
                                         ${count}
                                </td>
                                <td style="text-align: center;max-width: 50%">
                                    <h5>TOTAL AMOUNT COLLECTED:</h5>₱${formattedNum}</td>
                            </tr>
                                <!-- Add other rows similarly -->
                        </tbody>
                    </table>
                    `;

                    tableBody.innerHTML = values;
                })
                .catch(error => console.log('Error fetching data:', error));

        } else if (val == 'Parking') {
            fetch(`fetch_reports.php?reportType=Parking`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data fetched successfully:', data);
                    var values = '';

                    values = `
                        <table class="resident-table">
                            <thead>
                                <tr>
                                    <th>TRANSACTION</th>
                                    <th>NAME</th>
                                    <th>PHONE NO.</th>
                                    <th>PARKING TYPE</th>
                                    <th>PLATE NO.</th>
                                    <th>VEHICLE TYPE</th>
                                    <th>DATE</th>
                                    <th>AMOUNT</th>
                                </tr>
                            </thead>
                        <tbody>
                    `;

                    let totalDues = 0.00;
                    let count = 0;

                    data.forEach(info => {
                        count++;
                        totalDues += parseFloat(info.Amount);
                        values = values + `
                            <tr>
                                <td>${info.TransactionType}</td>
                                <td>${info.ResidentName}</td>
                                <td>${info.PhoneNo}</td>
                                <td>${info.ParkingType}</td>
                                <td>${info.PlateNum}</td>
                                <td>${info.VehicleType}</td>
                                <td>${info.Date}</td>
                                <td>${info.Amount}</td>
                            </tr>
                        `;

                    });

                    let doubleNum = parseFloat(totalDues).toFixed(2);
                    let num = doubleNum;
                    let formattedNum = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(num);

                    values = values + `
                        </tbody>
                    </table>
                                                    
                    <table class="resident-table" style="width: 100%; border-collapse: collapse;"> 
                        <thead>
                            <tr>
                                <th style= max-width: 50%; ">SUMMARY REPORT FOR C PERMIT</th>
                                <th style= max-width: 50%; "></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="text-align: center; max-width: 50%">
                                    <h5>TOTAL ISSUED C PERMIT:</h5>
                                        ${count}
                                </td>
                                <td style="text-align: center;max-width: 50%">
                                    <h5>TOTAL AMOUNT COLLECTED:</h5>₱${formattedNum}</td>
                            </tr>
                                <!-- Add other rows similarly -->
                        </tbody>
                    </table>
                                                                                        `
                    tableBody.innerHTML = values;
                })
                .catch(error => console.log('Error fetching data:', error));
        }
    });
});
