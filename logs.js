document.addEventListener("DOMContentLoaded", () => {
    // Select necessary elements
    const logoutLink = document.getElementById("logout");
    const logoutPopup = document.getElementById("logout-popup");
    const cancelLogout = document.getElementById("cancel-logout");
    const confirmLogout = document.getElementById("confirm-logout");

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

});
