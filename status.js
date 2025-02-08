document.addEventListener("DOMContentLoaded", () => {
    const proofImage = document.getElementById("proof-image");
    const uploadBtn = document.querySelector(".upload-btn");
    const saveBtn = document.querySelector(".save-btn");
    const closeBtn = document.querySelector(".close-btn");
    let selectedFile = null; // Store selected file
    const placeholderImage = "cloud_uploadd.png";

    if (proofImage && uploadBtn && saveBtn && closeBtn) {
        // File upload handler
        uploadBtn.addEventListener("click", () => {
            const fileInput = document.createElement("input");
            fileInput.type = "file";
            fileInput.accept = "image/*";
            fileInput.addEventListener("change", () => {
                selectedFile = fileInput.files[0]; // Store the selected file
                if (selectedFile) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        proofImage.src = e.target.result;
                        proofImage.alt = "Uploaded Proof of Payment";
                        proofImage.style.display = "block";
                    };
                    reader.readAsDataURL(selectedFile);
                }
            });
            fileInput.click();
        });

        // Save proof handler (Upload file & Update DB)
        saveBtn.addEventListener("click", () => {
            if (!selectedFile) {
                alert("Please upload a proof of payment before saving.");
                return;
            }

            const formData = new FormData();
            formData.append("file", selectedFile);

            // Get the booking number from the HTML element
            const bookingNumberElement = document.getElementById("booking_number");
            const bookingNumber = bookingNumberElement.textContent.replace("BOOKING NUMBER: #", "").trim();

            formData.append("booking_no", bookingNumber);

            fetch("upload.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Proof of payment uploaded successfully!");
                } else {
                    alert("Error uploading proof of payment: " + data.message);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while uploading.");
            });
        });

        // Remove proof handler (reset to placeholder image)
        closeBtn.addEventListener("click", () => {
            proofImage.src = placeholderImage;
            proofImage.alt = "Upload Proof";
            selectedFile = null;
            alert("Proof of payment removed.");
        });
    } else {
        console.error("Missing required elements in the DOM.");
    }
});
