function myFunction() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
      x.className += " responsive";
    } else {
      x.className = "topnav";
    }
}

window.onload = function() {
  let slideIndex = 1;
  showSlides(slideIndex);

  // Next/previous controls
  document.querySelector(".prev").onclick = function() {
      plusSlides(-1);
  };
  document.querySelector(".next").onclick = function() {
      plusSlides(1);
  };

  function plusSlides(n) {
      showSlides(slideIndex += n);
  }

  function showSlides(n) {
      let i;
      let slides = document.getElementsByClassName("mySlides");
      if (n > slides.length) { slideIndex = 1; }
      if (n < 1) { slideIndex = slides.length; }
      for (i = 0; i < slides.length; i++) {
          slides[i].style.display = "none"; // Hide all slides
      }
      slides[slideIndex - 1].style.display = "block"; // Show the current slide
  }

  // Auto scroll function
  setInterval(function() {
      plusSlides(1); // Move to the next slide
  }, 3000); // Change slide every 3 seconds

  // Add event listeners for all "BOOK NOW!" buttons, including book_btn
  const bookButtons = document.querySelectorAll("button[id^='bl_button'], button[id^='br_button'], #book_btn");
  bookButtons.forEach(button => {
      button.addEventListener("click", function() {
          window.location.href = "book.php";
      });
  });
};
