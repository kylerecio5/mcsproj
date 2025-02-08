<!DOCTYPE html>
<html>
    <head>
        <title>Home Page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="index.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="index.css">
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
          <a id="homee" href="index.php">Home</a>
          <a href="javascript:void(0);" class="icon" onclick="myFunction()">
            <i class="fa fa-bars"></i>
          </a>
        </div>

        <h1>WELCOME TO CITATION HOMES</h1>
        <h2>MEYCAUAYAN CITY, BULACAN</h2>

        <div class="slideshow-wrapper">
            <div class="amenities-title">AMENITIES</div> <!-- Added title here -->
            <div class="slideshow-container">
                <!-- Full-width images with number and caption text -->
                <div class="mySlides fade">
                    <div class="numbertext"></div>
                    <img src="bc_pic.png" style="width:100%">
                    <div class="text">Basketball Court</div>
                </div>
                
                <div class="mySlides fade">
                    <div class="numbertext"></div>
                    <img src="ch_pic.png" style="width:100%">
                    <div class="text">Club House</div>
                </div>
                
                <div class="mySlides fade">
                    <div class="numbertext"></div>
                    <img src="tennis_pic.png" style="width:100%">
                    <div class="text">Tennis Court</div>
                </div>
                
                <div class="mySlides fade">
                    <div class="numbertext"></div>
                    <img src="vc_pic.png" style="width:100%">
                    <div class="text">Volleyball Court</div>
                </div>
                
                <div class="mySlides fade">
                    <div class="numbertext"></div>
                    <img src="sp_pic.png" style="width:100%">
                    <div class="text">Swimming Pool</div>
                </div>
                
                <!-- Next and previous buttons -->
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
            </div>
        </div>

        <div class="welcome-container"> <!-- New container for the heading and message -->
            <h2 class="welcome-heading">Welcome to Citation Homes</h2>
            <p class="welcome-message">We offer a range of top-notch amenities designed to enhance your lifestyle and provide endless enjoyment for you and your loved ones. Our community boasts a variety of recreational and leisure facilities, all available for rent at affordable prices. Discover what Citation Homes has to offer.</p>
        </div>

        <div class="container1">
            <div class="left-content1">
                <img id="cont2" src="bc_pic.png" width="400">
            </div>
            <div class="right-content1">
                <h2 id="title1">BASKETBALL COURT</h2>
                <p id="description1">Basketball court is available to rent and book for your summer league, summer training and events. Click book now to reserve your slot!</p>
                <div class="right_bgroup">
                    <button id="bl_button1">BOOK NOW!</button>
                    <a href="price.php" id="price-link"><p id="s-price">PHP 100.00 - PHP 150.00</p></a>
                </div>
            </div>
        </div>
        
        <div class="line"></div>
        
        <div class="container2">
            <div class="left-content2">
                <h2 id="title2">CLUBHOUSE</h2>
                <p id="description2">The clubhouse is available to rent for Zumba sessions, retreats, small events, and gatherings. Click book now to reserve your slot!</p>
                <div class="left_bgroup">
                    <button id="br_button2">BOOK NOW!</button>
                    <a href="price.php" id="price-link"><p id="s-price">PHP 5,000.00 - PHP 7,000.00</p></a>
                </div>
            </div>
            <div class="right-content2">
                <img id="cont2" src="ch_pic.png" width="400">
            </div>
        </div>
        
        <div class="line"></div>
        
        <div class="container1">
            <div class="left-content1">
                <img id="cont2" src="tennis_pic.png" width="400">
            </div>
            <div class="right-content1">
                <h2 id="title1">TENNIS COURT</h2>
                <p id="description1">The tennis court is available to rent for tennis tournament, summer training and camp. Click book now to reserve your slot!</p>
                <div class="right_bgroup">
                    <button id="bl_button3">BOOK NOW!</button>
                    <a href="price.php" id="price-link"><p id="s-price">PHP 100.00 - PHP 150.00</p></a>
                </div>
            </div>
        </div>
        
        <div class="line"></div>
        
        <div class="container2">
            <div class="left-content2">
                <h2 id="title2">VOLLEYBALL COURT</h2>
                <p id="description2">The volleyball court is available to rent for volleyball tournament, summer training and camp. Click book now to reserve your slot!</p>
                <div class="left_bgroup">
                    <button id="br_button4">BOOK NOW!</button>
                    <a href="price.php" id="price-link"><p id="s-price">PHP 100.00 - PHP 150.00</p></a>
                </div>
            </div>
            <div class="right-content2">
                <img id="cont2" src="vc_pic.png" width="400">
            </div>
        </div>
        
        <div class="line"></div>
        
        <div class="container1">
            <div class="left-content1">
                <img id="cont2" src="sp_pic.png" width="400">
            </div>
            <div class="right-content1">
                <h2 id="title1">SWIMMING POOL</h2>
                <p id="description1">The pool area is available to rent for summer parties, birthday parties and small events and gatherings. Click book now to reserve your slot!</p>
                <div class="right_bgroup">
                    <button id="bl_button5">BOOK NOW!</button>
                    <a href="price.php" id="price-link"><p id="s-price">PHP 6,000.00 - PHP 8,000.00</p></a>
                </div>
            </div>
        </div>
        

        <div class="line"></div>
        
        <h2 class="add-heading">ADDITIONALS</h2>

        <div class="additionals-container">
            <img id="add" src="additionals_pic.png" alt="additionals">
        </div>

        <div class="logo-container">
            <img id="logo" src="citation_logo_big.png" alt="logo">
        </div>

        <h2 class="add-heading">At Citation Homes</h2>

        <p id="at">We believe in creating a community that supports an active and social lifestyle. All our amenities are available for rent at affordable rates, making it easy for you to enjoy these fantastic facilities without breaking the bank. Come and experience the vibrant, welcoming atmosphere of Citation Homes, where you can make lasting memories with your loved ones.</p>

        <div class="footer">
            ©️ Citation Homes by FILINVEST 2024 All Rights Reserved <br>
            <a href="#">Privacy Policy</a> | <a href="#">Terms and Conditions</a> | 
            <a href="#">Follow us</a>
        </div>

    </body>
</html>