document.getElementById("loginForm").addEventListener("submit", function(event) {
    event.preventDefault(); 

    const formData = new FormData(this);

    fetch("/loginUser", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (response.status === 201) {
            window.location.href = '/home';
        } else if (!response.ok) {
            return response.json().then(errorData => {
                if (errorData && errorData.error === "User does not exist") {
                    alert("User does not exist");
                } else if (errorData && errorData.error === "Incorrect password") {
                    alert("Incorrect password");
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
