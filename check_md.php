<!DOCTYPE html>
<html>

<head>
    <title>Check MD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="check_md.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="check_md.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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

    <h1>CHECK MONTHLY DUE</h1>

    <div class="gap">
        <div class="search">
            <div class="searchbox_background">
                <input type="text" placeholder="Enter resident number" class="searchbox">
                <input type="text" placeholder="Enter resident lastname" class="searchbox">
            </div>
            <div class="searchbtn_background">
                <button class="searchbtn">CHECK</button>
            </div>
        </div>
    </div>

    <div class="gap1">
        <div class="status_bg">
            <img id="logo" src="citation_logo_big.png" width="80">
            <!-- <h2 id="booking_number">Resident code: 9817</h2> -->
            <h2 id="b_status">Monthly Due</h2>

            <table class="resident-table">
                <thead>
                    <tr>
                        <th>Recident code</th>
                        <th>Name</th>
                        <th>Street Light</th>
                        <th>Monthly Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>9817</td>
                        <td>Allyssa D. Gallo</td>
                        <td>Yes</td>
                        <td>₱1,500.00</td>
                        <td>Compliant</td>
                    </tr>
                    <!-- Add other rows similarly -->
                </tbody>
            </table>
            <br>
            <br>
            <h2 id="b_status" style="text-align: middle">Payment History</h2>

            
            <div class="history-table">
                <table id="historyTable">
                    <thead>
                        <tr>
                            <th>MONTH</th>
                            <th>YEAR</th>
                            <th>AMOUNT PAID</th>
                            <th>PAYMENT DATE</th>
                        </tr>
                        <td>January</td>
                        <td>2025</td>
                        <td hidden></td>
                        <td>₱1,500.00</td>
                        <td>2025-02-07</td>

                    </thead>
                    <tbody id="history-table-body">
                    </tbody>
                </table>
            </div>
        </div>



    </div>


    <div class="footer">
        ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
        <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> |
        <a href="#">Follow us</a>
    </div>

</body>

</html>