<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['form_data'] = [
        'first-name' => $_POST['first-name'],
        'last-name' => $_POST['last-name'],
        'middle-name' => $_POST['middle-name'],
        'phone-number' => $_POST['phone-number'],
        'amenities' => $_POST['amenities'],
        'client-type' => $_POST['client-type'],
        'selected-date' => $_POST['selected-date'], // Separate field for date
        'selected-times' => $_POST['selected-times'], // Separate field for times
        'table' => $_POST['table'],
        'chair' => $_POST['chair'],
        'karaoke' => $_POST['karaoke'],
        'note' => $_POST['note'],
        'total-amount' => $_POST['total-amount'],
    ];

    header('Location: booking_details.php');
    exit;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Book</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="book.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="book.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
    // Function to handle showing/hiding the additional options
    function handleAmenitiesChange(form) {
        console.log("handleAmenitiesChange triggered");

        var amenities = form.querySelector("#amenities").value;  // Use the specific form's amenities select
        var tableLabel = form.querySelector("label[for='table']");
        var tableSelect = form.querySelector("#table");
        var chairLabel = form.querySelector("label[for='chair']");
        var chairSelect = form.querySelector("#chair");
        var karaokeLabel = form.querySelector("label[for='karaoke']");
        var karaokeSelect = form.querySelector("#karaoke");

        // Hide Table, Chair, Karaoke if sports courts (Basketball, Volleyball, Tennis) are selected
        if (amenities === "basketball-court" || amenities === "tennis-court" || amenities === "volleyball-court") {
            tableLabel.style.display = "none";
            tableSelect.style.display = "none";
            chairLabel.style.display = "none";
            chairSelect.style.display = "none";
            karaokeLabel.style.display = "none";
            karaokeSelect.style.display = "none";
        } else if (amenities === "clubhouse" || amenities === "swimming-pool") {
            // Show Table, Chair, Karaoke if Clubhouse or Swimming Pool are selected
            tableLabel.style.display = "block";
            tableSelect.style.display = "block";
            chairLabel.style.display = "block";
            chairSelect.style.display = "block";
            karaokeLabel.style.display = "block";
            karaokeSelect.style.display = "block";
        } else {
            // Default state (if nothing is selected, show all)
            tableLabel.style.display = "block";
            tableSelect.style.display = "block";
            chairLabel.style.display = "block";
            chairSelect.style.display = "block";
            karaokeLabel.style.display = "block";
            karaokeSelect.style.display = "block";
        }
    }

    // Function to add a new booking form with its own pad
    function addBookingForm() {
    console.log("addBookingForm triggered");

    var newPad = document.createElement("div");
    newPad.classList.add("pad");

    var formBackground = document.createElement("div");
    formBackground.classList.add("form_background");

    var form = document.querySelector(".booking-form");
    var newForm = form.cloneNode(true);

    // Reset input fields
    newForm.querySelectorAll("input, select, textarea").forEach(function (input) {
        input.value = "";
    });

    // Remove unnecessary fields
    let fieldsToRemove = [
        "label[for='first-name']", "#first-name",
        "label[for='middle-name']", "#middle-name",
        "label[for='last-name']", "#last-name",
        "label[for='phone-number']", "#phone-number",
        "label[for='client-type']", "#client-type",
        "label[for='inline-calendar']", "#inline-calendar",
        "label[for='select-date']", "#select-date"
    ];

    fieldsToRemove.forEach(selector => {
        let element = newForm.querySelector(selector);
        if (element) {
            element.remove();
        }
    });

    formBackground.appendChild(newForm);
    newPad.appendChild(formBackground);
    document.getElementById("additional-booking-forms-container").appendChild(newPad);

    const selectedDateHiddenInput = newForm.querySelector("#selected-date-hidden");
    const selectedTimesHiddenInput = newForm.querySelector("#selected-times-hidden");
    const tableSelect = newForm.querySelector("#table");
    const chairSelect = newForm.querySelector("#chair");
    const karaokeSelect = newForm.querySelector("#karaoke");
    const amenitiesDropdown = newForm.querySelector("#amenities");
    const selectedDateInput = newForm.querySelector("#select-date");

    const prices = {
        "swimming-pool": { "morning": { homeowner: 5000, guest: 6000 }, "night": { homeowner: 6000, guest: 7000 } },
        "clubhouse": { "morning": { homeowner: 5000, guest: 6000 }, "night": { homeowner: 6000, guest: 7000 } },
        "basketball-court": { "day": 100, "night": 150 },
        "volleyball-court": { "day": 100, "night": 150 },
        "tennis-court": { "day": 100, "night": 150 },
    };

    const additionalPrices = {
        "chair": 6, // Price per chair
        "table": 25, // Price per table
        "karaoke": 600, // Price per karaoke unit
    };

    let selectedTimes = new Set();
    let totalAmountForAllBookings = 0;

    function updateBreakdown() {
        const breakdownContainer = newForm.querySelector("#breakdown-container");
        const totalAmountElement = newForm.querySelector("#total-amount");

        const clientTypeElement = newForm.querySelector("#client-type");
        const selectedDateElement = newForm.querySelector("#select-date");
        const amenitiesDropdownElement = newForm.querySelector("#amenities");

        if (!clientTypeElement || !selectedDateElement || !amenitiesDropdownElement) {
            console.warn("Missing form elements in updateBreakdown");
            return;
        }

        const clientType = clientTypeElement ? clientTypeElement.value : "resident";
        const selectedDate = selectedDateElement.value;
        const amenity = amenitiesDropdownElement.value;

        breakdownContainer.innerHTML = ""; // Reset breakdown container for new form
        let total = 0;

        // Add selected date
        if (selectedDate) {
            breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Date:</span><span>${selectedDate}</span></div>`;
        }

        // Add selected time slots and prices for amenities
        selectedTimes.forEach((time) => {
            let price = 0;
            if (prices[amenity]) {
                if (["swimming-pool", "clubhouse"].includes(amenity)) {
                    const period = time.includes("Morning") ? "morning" : "night";
                    price = prices[amenity][period][clientType === "resident" ? "homeowner" : "guest"];
                } else {
                    const isNight = time.includes("PM") && parseInt(time) >= 6;
                    price = isNight ? prices[amenity].night : prices[amenity].day;
                }
            }

            total += price;
            breakdownContainer.innerHTML += `<div class="breakdown-item"><span>${time} (${amenity})</span><span>₱${price.toFixed(2)}</span></div>`;
        });

        // Add additional items (Chairs, Tables, Karaoke)
        const chairCount = chairSelect.value;
        const tableCount = tableSelect.value;
        const karaokeCount = karaokeSelect.value;

        if (chairCount > 0) {
            const chairTotal = chairCount * additionalPrices["chair"];
            total += chairTotal;
            breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Chairs (${chairCount} pcs)</span><span>₱${chairTotal.toFixed(2)}</span></div>`;
        }

        if (tableCount > 0) {
            const tableTotal = tableCount * additionalPrices["table"];
            total += tableTotal;
            breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Tables (${tableCount} pcs)</span><span>₱${tableTotal.toFixed(2)}</span></div>`;
        }

        if (karaokeCount > 0) {
            const karaokeTotal = karaokeCount * additionalPrices["karaoke"];
            total += karaokeTotal;
            breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Karaoke (${karaokeCount} pcs)</span><span>₱${karaokeTotal.toFixed(2)}</span></div>`;
        }

        // Show total amount for this booking
        totalAmountElement.textContent = `₱${total.toFixed(2)}`;

        // Update the grand total for all bookings
        totalAmountForAllBookings += total;
        document.querySelector("#grand-total-amount").textContent = `₱${totalAmountForAllBookings.toFixed(2)}`;
    }

    function updateHiddenInputs() {
        if (!selectedDateInput) return;
        const selectedDate = selectedDateInput.value;
        const timesArray = Array.from(selectedTimes).join(", ");
        const totalAmount = newForm.querySelector("#total-amount")?.textContent.replace('₱', '').trim() || "0";

        if (selectedDateHiddenInput) selectedDateHiddenInput.value = selectedDate;
        if (selectedTimesHiddenInput) selectedTimesHiddenInput.value = timesArray;
        newForm.querySelector("#total-amount-hidden").value = totalAmount;
    }

    function populateTimeOptions() {
        var timeOptionsContainer = newForm.querySelector("#time-options");
        if (!timeOptionsContainer) return;
        
        selectedTimes.clear();
        timeOptionsContainer.innerHTML = "";
        var amenity = amenitiesDropdown.value;
        let times = [];

        if (["swimming-pool", "clubhouse"].includes(amenity)) {
            times = ["8:00 AM - 5:00 PM (Morning)", "6:00 PM - 11:59 PM (Night)"];
        } else if (["basketball-court", "volleyball-court", "tennis-court"].includes(amenity)) {
            for (let i = 6; i <= 23; i++) {
                times.push(`${i}:00 ${i < 12 ? "AM" : "PM"}`);
            }
        }

        times.forEach((time) => {
            const timeButton = document.createElement("button");
            timeButton.type = "button";
            timeButton.classList.add("time-btn");
            timeButton.textContent = time;

            timeButton.addEventListener("click", () => {
                selectedTimes.clear();
                selectedTimes.add(time);
                updateBreakdown();
                updateHiddenInputs();
                newForm.querySelectorAll(".time-btn").forEach((btn) => btn.classList.remove("selected"));
                timeButton.classList.add("selected");
            });

            timeOptionsContainer.appendChild(timeButton);
        });
    }

    // Ensure the elements exist before adding event listeners
    if (amenitiesDropdown) {
        amenitiesDropdown.addEventListener("change", () => { 
            populateTimeOptions(); 
            updateBreakdown(); 
            updateHiddenInputs(); 
        });
    } else {
        console.warn("Amenities dropdown not found.");
    }

    if (tableSelect) tableSelect.addEventListener("change", updateBreakdown);
    if (chairSelect) chairSelect.addEventListener("change", updateBreakdown);
    if (karaokeSelect) karaokeSelect.addEventListener("change", updateBreakdown);
    if (selectedDateInput) selectedDateInput.addEventListener("change", updateBreakdown);

    populateTimeOptions();
}
    // Ensure that the first form also has the amenities change handler applied when the page loads
    window.onload = function() {
        // Apply the handler to the first form
        var firstForm = document.querySelector(".booking-form");
        var amenitiesSelect = firstForm.querySelector("#amenities");

        // Attach the event listener to the first form's amenities select
        amenitiesSelect.addEventListener("change", function() {
            handleAmenitiesChange(firstForm);  // Apply to the first form
        });

        // Trigger the function to hide or show the table, chair, and karaoke options for the first form
        handleAmenitiesChange(firstForm);  // Ensure this is applied to the first form as well
    };

    document.addEventListener("DOMContentLoaded", () => {
    // Initialize DOM elements
    const amenitiesDropdown = document.getElementById("amenities");
    const timeOptionsContainer = document.getElementById("time-options");
    const selectedTimes = new Set(); // Store selected times
    const selectedDateInput = document.getElementById("inline-calendar");
    const selectedDateHiddenInput = document.getElementById("selected-date-hidden");
    const selectedTimesHiddenInput = document.getElementById("selected-times-hidden");

    // Add event listeners to tables, chairs, and karaoke dropdowns
    const tableSelect = document.getElementById("table");
    const chairSelect = document.getElementById("chair");
    const karaokeSelect = document.getElementById("karaoke");

    // Prices for time slots based on amenities
    const prices = {
        "swimming-pool": { "morning": { homeowner: 5000, guest: 6000 }, "night": { homeowner: 6000, guest: 7000 } },
        "clubhouse": { "morning": { homeowner: 5000, guest: 6000 }, "night": { homeowner: 6000, guest: 7000 } },
        "basketball-court": { "day": 100, "night": 150 },
        "volleyball-court": { "day": 100, "night": 150 },
        "tennis-court": { "day": 100, "night": 150 },
    };

    // Function to update the breakdown display and total cost
    const updateBreakdown = () => {
        const breakdownContainer = document.getElementById("breakdown-container");
        const totalAmountElement = document.getElementById("total-amount");
        const clientType = document.getElementById("client-type").value; // "resident" or "guest"
        const selectedDate = selectedDateInput.value;

        breakdownContainer.innerHTML = ""; // Clear the breakdown
        let total = 0;

        // Add selected date
        if (selectedDate) {
            breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Date:</span><span>${selectedDate}</span></div>`;
        }

        // Add selected times with pricing
        selectedTimes.forEach((time) => {
            const amenity = amenitiesDropdown.value;
            let price = 0;

            if (["swimming-pool", "clubhouse"].includes(amenity)) {
                const period = time.includes("Morning") ? "morning" : "night";
                price = prices[amenity][period][clientType === "resident" ? "homeowner" : "guest"];
            } else if (["basketball-court", "volleyball-court", "tennis-court"].includes(amenity)) {
                const isNight = time.includes("PM") && parseInt(time) >= 6;
                price = isNight ? prices[amenity].night : prices[amenity].day;
            }

            total += price;
            breakdownContainer.innerHTML += `<div class="breakdown-item"><span>${time} (${amenity})</span><span>₱${price.toFixed(2)}</span></div>`;
        });

        // Now add the chair, table, and karaoke prices
        const chairCount = parseInt(chairSelect.value) || 0;
        const tableCount = parseInt(tableSelect.value) || 0;
        const karaokeCount = parseInt(karaokeSelect.value) || 0;

        const chairPrice = chairCount * 6;
        const tablePrice = tableCount * 25;
        const karaokePrice = karaokeCount * 600;

        if (chairCount) breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Chairs (${chairCount} pcs)</span><span>₱${chairPrice.toFixed(2)}</span></div>`;
        if (tableCount) breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Tables (${tableCount} pcs)</span><span>₱${tablePrice.toFixed(2)}</span></div>`;
        if (karaokeCount) breakdownContainer.innerHTML += `<div class="breakdown-item"><span>Karaoke (${karaokeCount} pcs)</span><span>₱${karaokePrice.toFixed(2)}</span></div>`;

        total += chairPrice + tablePrice + karaokePrice;

        // Update the total amount display
        totalAmountElement.textContent = `₱${total.toFixed(2)}`;
    };

    // Function to update hidden inputs for form submission
    const updateHiddenInputs = () => {
        const selectedDate = selectedDateInput.value;
        const timesArray = Array.from(selectedTimes).join(", ");
        const totalAmount = document.getElementById("total-amount").textContent.replace('₱', '').trim();

        selectedDateHiddenInput.value = selectedDate;
        selectedTimesHiddenInput.value = timesArray;
        document.getElementById("total-amount-hidden").value = totalAmount;
    };

    // Event listeners for amenities dropdown
    amenitiesDropdown.addEventListener("change", () => {
        populateTimeOptions(amenitiesDropdown.value);
        updateBreakdown();  // Trigger recalculation on amenities change
        updateHiddenInputs();
    });

    // Add event listeners to tables, chairs, and karaoke dropdowns
    tableSelect.addEventListener("change", () => {
        updateBreakdown();
        updateHiddenInputs();
    });

    chairSelect.addEventListener("change", () => {
        updateBreakdown();
        updateHiddenInputs();
    });

    karaokeSelect.addEventListener("change", () => {
        updateBreakdown();
        updateHiddenInputs();
    });

    // Re-run calculations whenever the date is selected
    selectedDateInput.addEventListener("change", () => {
        updateBreakdown();
        updateHiddenInputs();
    });

    // Ensure time selection updates everything correctly
    const populateTimeOptions = (amenity) => {
        timeOptionsContainer.innerHTML = ""; // Clear existing buttons
        selectedTimes.clear(); // Reset selected times

        let times = [];
        if (["swimming-pool", "clubhouse"].includes(amenity)) {
            times = ["8:00 AM - 5:00 PM (Morning)", "6:00 PM - 11:59 PM (Night)"];
        } else if (["basketball-court", "volleyball-court", "tennis-court"].includes(amenity)) {
            for (let i = 6; i <= 23; i++) {
                times.push(`${i}:00 ${i < 12 ? "AM" : "PM"}`);
            }
        }

        let currentlySelectedButton = null;

        times.forEach((time) => {
            const timeButton = document.createElement("button");
            timeButton.type = "button";
            timeButton.classList.add("time-btn");
            timeButton.textContent = time;

            timeButton.addEventListener("click", () => {
                selectedTimes.clear();  // Remove all selected times from the set
                selectedTimes.add(time);

                updateBreakdown();
                updateHiddenInputs();

                document.querySelectorAll(".time-btn").forEach((btn) => {
                    btn.classList.remove("selected");
                });
                timeButton.classList.add("selected");
            });

            timeOptionsContainer.appendChild(timeButton);
        });
    };

    // Initialize Flatpickr for the date picker
    flatpickr("#inline-calendar", {
        enableTime: false,
        dateFormat: "Y-m-d",
        minDate: "today",
        onChange: () => {
            updateBreakdown();
            updateHiddenInputs();
        },
    });
});
</script>
</head>

<body>
    <div class="topnav" id="myTopnav">
        <a href="index.html" class="active">
            <img src="citation_logo.png" alt="Home" class="logo-icon">
        </a>
        <a href="check_md.php">Check MD</a>
        <a href="status.php">Status</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact Us</a>
        <a href="book.php">Book</a>
        <a href="price.php">Price List</a>
        <a href="index.php">Home</a>
        <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
        </a>
    </div>

    <h1>BOOK NOW!</h1>
<div class="pad">
    <div class="form_background">
        <form class="booking-form" method="POST" action="book.php" autocomplete="off">
            <h2>BOOKING FORM</h2>
            <!-- First Layer (3 columns) -->
            <div class="form-content">
                <div class="form-left">
                    <label for="first-name">First name:</label>
                    <input type="text" id="first-name" name="first-name" placeholder="Juan" required autocomplete="off"> 
                    <label for="middle-name">Middle name:</label>
                    <input type="text" id="middle-name" name="middle-name" placeholder="Middle Name" required autocomplete="off">
                    <label for="last-name">Last name:</label>
                    <input type="text" id="last-name" name="last-name" placeholder="Dela Cruz" required autocomplete="off">
                    <label for="phone-number">Phone number:</label>
                    <input type="tel" id="phone-number" name="phone-number" placeholder="+63" required autocomplete="off">
                    <label for="amenities">Amenities:</label>
                    <select id="amenities" name="amenities">
                        <option value="0">Select</option>
                        <option value="basketball-court">Basketball Court</option>
                        <option value="swimming-pool">Swimming Pool</option>
                        <option value="tennis-court">Tennis Court</option>
                        <option value="volleyball-court">Volleyball Court</option>
                        <option value="clubhouse">Clubhouse</option>
                    </select>
                    <label for="client-type">Client type:</label>
                    <select id="client-type" name="client-type">
                        <option value="0">Select</option>
                        <option value="resident">Resident</option>
                        <option value="guest">Guest</option>
                    </select>
                    <!-- Resident Code Section -->
                    <div id="resident-code-section" style="display: none;">
                        <label for="resident-code">Input resident code:</label>
                        <input type="text" id="resident-code" name="resident-code" placeholder="Resident Code" autocomplete="off">
                        <div id="resident-code-status"></div> <!-- This will show if the code exists or not -->
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var clientTypeSelect = document.getElementById('client-type');
                        var residentCodeSection = document.getElementById('resident-code-section');
                        var residentCodeInput = document.getElementById('resident-code');
                        var statusDiv = document.getElementById('resident-code-status');
                        var submitButton = document.querySelector('.final-book-btn');
                        
                        // Show/Hide "Resident Code" field based on "Client Type" selection
                        clientTypeSelect.addEventListener('change', function() {
                            var clientType = this.value;

                            // Reset all states when switching to "Guest"
                            if (clientType === 'guest') {
                                residentCodeSection.style.display = 'none';
                                statusDiv.innerHTML = ''; // Clear any previous status messages
                                submitButton.disabled = false; // Enable the submit button
                            } else if (clientType === 'resident') {
                                residentCodeSection.style.display = 'block';
                            }
                        });

                        // Live search for resident code
                        residentCodeInput.addEventListener('input', function() {
                            var residentCode = this.value.trim();

                            // Clear previous status message
                            statusDiv.innerHTML = '';

                            if (residentCode.length > 0) {
                                // Perform AJAX request to check if the resident code exists
                                fetch('check_resident_code.php', {
                                    method: 'POST',
                                    body: new URLSearchParams({
                                        'resident_code': residentCode
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.exists) {
                                        statusDiv.innerHTML = '<span style="color: green;">Resident code exists!</span>';
                                        submitButton.disabled = false; // Enable form submission
                                    } else {
                                        statusDiv.innerHTML = '<span style="color: red;">Resident code does not exist.</span>';
                                        submitButton.disabled = true; // Disable form submission
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    statusDiv.innerHTML = '<span style="color: red;">Error checking resident code.</span>';
                                    submitButton.disabled = true; // Disable form submission on error
                                });
                            }
                        });

                        // Prevent form submission if resident code does not exist
                        submitButton.addEventListener('click', function(event) {
                            if (submitButton.disabled) {
                                event.preventDefault();
                                statusDiv.innerHTML = '<span style="color: red;">Please enter a valid resident code to proceed with booking.</span>';
                            }
                        });
                    });
                    </script>


                </div>
                <div class="form-middle">
                    <div class="calendar-container">
                        <label for="select-date">Select date:</label>
                        <input type="text" id="inline-calendar" readonly>
                    </div>
                </div>
                <div class="form-right">
                    <label for="select-time">Select time:</label>
                    <div class="time-options" id="time-options">
                        <!-- Time slots will be dynamically populated based on the selected amenity -->
                    </div>
                </div>
                <!-- Add these hidden inputs inside your form -->
                <input type="hidden" name="selected-date" id="selected-date-hidden">
                <input type="hidden" name="selected-times" id="selected-times-hidden">
                <input type="hidden" name="total-amount" id="total-amount-hidden">

                <script>
                    // Assuming you're using Flatpickr for the calendar
                    flatpickr("#inline-calendar", {
                        dateFormat: "Y-m-d", // You can adjust the format as per your requirement
                        onChange: function(selectedDates, dateStr, instance) {
                            updateDateTime();
                        }
                    });

                    // Assuming you have time options in your `#time-options` div
                    document.querySelectorAll('.time-option').forEach(function(timeOption) {
                        timeOption.addEventListener('click', function() {
                            updateDateTime();
                        });
                    });

                    // Function to combine date and time and update the hidden field
                    function updateDateTime() {
                        var date = document.getElementById('inline-calendar').value;
                        var time = document.querySelector('.time-option.selected') ? document.querySelector('.time-option.selected').textContent : ''; 

                        if (date && time) {
                            var datetime = date + ' ' + time;
                            document.getElementById('datetime-hidden').value = datetime;
                        }
                    }
                </script>
            </div>
            <h3 id="additionals_title">ADDITIONALS</h3>
            <div class="form-left-bottom">
                <div class="left-column">
                    <label for="table">Table:</label>
                    <select id="table" name="table">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <label for="chair">Chair:</label>
                    <select id="chair" name="chair">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    <label for="karaoke">Karaoke:</label>
                    <select id="karaoke" name="karaoke">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <div class="right-column">
                    <label for="note">Note:</label>
                    <textarea id="note" name="note" placeholder="Type here..."></textarea>
                </div>
            </div>

            <div class="form-bottom">
                <button type="button" class="add-booking-btn" onclick="addBookingForm()">ADD BOOKING</button>
            </div>

            
    </div>
</div>
<div id="additional-booking-forms-container"></div>

<div class="pad2">
    <div class="total_background">
        <h3>TOTAL AMOUNT BREAKDOWN:</h3>
        <div id="breakdown-container">
            <!-- Items will be dynamically added here -->
        </div>
        <div class="total-row">
            <span>Total:</span>
            <span id="total-amount">₱0.00</span>
        </div>
        
        <div class="form-bottom1">
            <button type="reset" class="reset-btn">RESET</button>
            <!-- MOVED THE "BOOK" BUTTON HERE INSIDE FORM -->
            <button type="submit" class="final-book-btn">BOOK</button>
        </div>
    </div>
</div>
</form>

    <div class="footer">
        ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
        <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> | 
        <a href="#">Follow us</a>
    </div>

</body>
</html>
