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
    officeID: document.getElementById('officeID')
};
function displayReport(data) {
    // Clear previous table if it exists
    const reportContainer = document.getElementById('results');
    reportContainer.innerHTML = ''; // Clear previous content

   const table = document.createElement('table');
   table.border = '1';

   // Add table headers dynamically
   const headers = Object.keys(data[0]);
   const headerRow = document.createElement('tr');
   headers.forEach(header => {
       const th = document.createElement('th');
       th.textContent = header;
       headerRow.appendChild(th);
   });
   table.appendChild(headerRow);

   // Add table rows dynamically
   data.forEach(row => {
       const tableRow = document.createElement('tr');
       headers.forEach(header => {
           const td = document.createElement('td');
           td.textContent = row[header];
           tableRow.appendChild(td);
       });
       table.appendChild(tableRow);
   });
   // reportContainer.appendChild(title);
   reportContainer.appendChild(table);
}
// Helper function to check if a field is empty
function isEmpty(field) {
    return field.value.trim() === ''; // Check the trimmed value
}

dashboard.submit.addEventListener('click', (e) => {
    console.log("JavaScript connected");
    e.preventDefault(); // Prevent default form submission

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
    request.open('GET', `search-car.php?${requestData}`, true); // Use GET request with query params
    console.log('GET', `search-car.php?${requestData}`);

    request.onload = function () {
        if (this.status >= 200 && this.status < 300) {
            try {
                const data = JSON.parse(this.responseText); // Parse JSON response
                const resultsContainer = document.getElementById("results");
                resultsContainer.innerHTML = ""; // Clear previous results
                console.log("sent to php");
                if (data.ok && data.cars && data.cars.length > 0) {
                    displayReport(data.cars); // Pass only the cars array
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