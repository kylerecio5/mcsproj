<!DOCTYPE html>
<html>
    <head>
        <title>Price</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="price.css">
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
    
        <div class="image-container">
            <img id="price" src="get_image.php" alt="Price List">
        </div>    
        
        <div class="btn_container">
            <button id="book_btn">BOOK NOW</button>
        </div>

        <script>
window.onload = function() {
    // Load the latest image when the page loads
    document.getElementById("price").src = "get_image.php?" + new Date().getTime(); 

    // Add event listener for the "BOOK NOW" button
    const bookBtn = document.getElementById("book_btn");
    if (bookBtn) {
        bookBtn.addEventListener("click", function() {
            window.location.href = "book.php"; // Redirect to the booking page
        });
    }
};
</script>

<div class="footer">
    ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
    <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> | 
    <a href="#">Follow us</a>
</div>

    </body>
    
</html>