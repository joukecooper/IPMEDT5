document.getElementById("registerForm").addEventListener("submit", function(event) {
    event.preventDefault(); 

    const formData = new FormData(this);

    fetch("/registerUser", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (response.status === 201) {
            window.location.href = '/login';
        } else if (!response.ok) {
            return response.json().then(errorData => {
                if (errorData && errorData.error === "User already exists") {
                    alert("User already exists");
                } else {
                    alert("An error occurred. Please try again later.");
                }
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while processing your request. Please try again later.");
    });
});
