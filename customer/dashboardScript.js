// References to dashboard form and elements
const dashboard = {
    search: document.getElementById('searchForm'),
    submit: document.querySelector('#searchForm button[type="submit"]') // Correctly target the submit button
};

const car_const = {
    model: document.getElementById('model'),
    year: document.getElementById('year'),
    car_id: document.getElementById('car_id'),
    capacity: document.getElementById('capacity'),
    fuel: document.getElementById('fuel'),
    mileage: document.getElementById('mileage'),
    bodyType: document.getElementById('bodyType'),
    transmission: document.getElementById('transmission'),
    color: document.getElementById('color'),
    price: document.getElementById('price'),
    officeLocation: document.getElementById('officeLocation'),
    startDate: document.getElementById('startDate'),
    endDate: document.getElementById('endDate')
};

// Helper function to check if a field is empty
function isEmpty(field) {
    return field.value.trim() === ''; // Check the trimmed value
}

const email = localStorage.getItem('email');

dashboard.submit.addEventListener('click', (e) => {
    console.log("JavaScript connected");
    e.preventDefault(); // Prevent default form submission

    // Validate required fields
    if (isEmpty(car_const.startDate)) {
        alert("Start Date is a required field!");
        return;
    }
    if (isEmpty(car_const.endDate)) {
        alert("End Date is a required field!");
        return;
    }
    if (!isEmpty(car_const.endDate) && !isEmpty(car_const.startDate)) {
        if (car_const.endDate.value <= car_const.startDate.value) {
            alert("End Date is before Start Date");
            return;
        }
    }

    // Build search query parameters, filtering out empty fields
    const requestData = new URLSearchParams();

    Object.keys(car_const).forEach((key) => {
        const field = car_const[key];
        if (!isEmpty(field)) {
            requestData.append(key, field.value);
        }
    });

    // Create XMLHttpRequest for search
    const request = new XMLHttpRequest();
    request.open('GET', `search-car.php?${requestData.toString()}`, true); // Use GET request with query params
    console.log('GET', `search-car.php?${requestData.toString()}`);

    request.onload = function () {
        if (this.status >= 200 && this.status < 300) {
            try {
                const data = JSON.parse(this.responseText); // Parse JSON response
                const resultsContainer = document.getElementById("results");
                resultsContainer.innerHTML = ""; // Clear previous results
                console.log("Sent to PHP");
                if (data.ok && data.cars.length > 0) {
                    data.cars.forEach((car) => {
                        console.log("Results Occur");
                        resultsContainer.innerHTML += `
                            <div class="car">
                                <h3>${car.model} (${car.year}) (${car.color}) </h3>
                                <p><strong>Plate ID:</strong> ${car.plate_id}</p>
                                <p><strong>Price per Day:</strong> ${car.daily_rental_price}</p>
                                <p><strong>office Location:</strong> ${car.location}</p>
                                <p><strong>Additional Featuers:</strong> Mileage : ${car.mileage} , seating Capacity: ${car.seating_capacity}</p>
                                <p><strong> Additional Featuers:</strong> BodyType : ${car.body_type} , fuel_type: ${car.fuel_type}</p>
                                <button class="reserve" onclick="reserveCar('${car.plate_id}')">Reserve</button>
                            </div>
                        `;
                       // <p><strong> Additional Featuers:</strong>: ${car.addition_features}</p>
                    });
                } else {
                    resultsContainer.innerHTML += '<p class="car">No cars found matching your criteria.</p>';
                }
            } catch (error) {
                console.error("Error parsing search response:", error);
            }
        } else {
            console.error("Search request failed:", this.status, this.statusText);
        }
    };

    request.onerror = function () {
        console.error("Search request error:", this);
    };

    request.send(); // Send the search request
});


// Reserve a car
function reserveCar(car_id) {
    const password = prompt("Enter your password");
    console.log(password);
    console.log(email);
    if (!password) {
        alert("Password is required to reserve a car.");
        return;
    }
    // Prepare reservation data
    const reservationData = new URLSearchParams({
        password: password,
        car_id: car_id,
        startDate: car_const.startDate.value,
        endDate: car_const.endDate.value,
        email : email
    });
    // Create XMLHttpRequest for reservation
    const request = new XMLHttpRequest();
    request.open('POST', 'reserve-car.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    console.log('Post', `reserve-car.php?${reservationData}`);


    request.onload = function () {
        console.log("loaded");
        if (this.status >= 200 && this.status < 300) {
            try {
                console.log("request successful");
                const data = JSON.parse(this.responseText);
                alert(data.message);
            } catch (error) {
                console.error("Error parsing reservation response:", error);
            }
        } else {
            console.error("Reservation request failed:", this.status, this.statusText);
        }
    };

    request.onerror = function () {
        console.error("Reservation request error:", this);
    };

    request.send(reservationData.toString()); // Send the reservation request
}
