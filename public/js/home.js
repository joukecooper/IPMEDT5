function FeedBirdButtonPressed() {
    fetch("/getFeedNow")
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error('Failed to fetch feeding status');
        }
    })
    .then(data => {
        const isFeeding = data.message;
        if (isFeeding) {
            alert("Already feeding");
        } else {
            fetch("/setFeedNow?feed_now=1")
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Failed to start feeding');
                }
            })
            .then(data => {
                alert("Feeding started successfully");
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred while starting feeding. Please try again later.");
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while fetching feeding status. Please try again later.");
    });
}


function DecreaseFoodButtonPressed() {
    const baseUrl = window.location.origin;
    const url = `${baseUrl}/decreaseFood`;

    fetch(url)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })

    UpdateAmountOfFood();

}

function IncreaseFoodButtonPressed() {
    const baseUrl = window.location.origin;
    const url = `${baseUrl}/increaseFood`;

    fetch(url)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        return response.json();
    })

    UpdateAmountOfFood();
}

function UpdateAmountOfFood() {
    const baseUrl = window.location.origin;
    const url = `${baseUrl}/amountOfFood`;

    fetch(url)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        return response.json();
    })
    .then(data => {
        var amount_of_food_text = document.getElementById('amount_of_food');

        if (amount_of_food_text) {
            amount_of_food_text.textContent = data.doubleValue.toString() + ' gram';
    }})
    .catch(error => {
        console.error('There was a problem with your fetch operation:', error);
    });
}

document.getElementById('feed_bird').addEventListener('click', FeedBirdButtonPressed);
document.getElementById('decrease_food').addEventListener('click', DecreaseFoodButtonPressed);
document.getElementById('increase_food').addEventListener('click', IncreaseFoodButtonPressed);

UpdateAmountOfFood()