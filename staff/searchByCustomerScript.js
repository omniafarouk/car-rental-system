// References to dashboard form and elements
const dashboard = {
    search: document.getElementById('searchForm'),
    submit: document.querySelector('#searchForm button[type="submit"]') // Correctly target the submit button
};

const customer_const = {
    Fname: document.getElementById('fname'),
    Lname: document.getElementById('lname'),
    email: document.getElementById('email'),
    phone: document.getElementById('phone'),
    Id: document.getElementById('id'),
    address: document.getElementById('address'),
    license_Id: document.getElementById('licenseId'),
    license_exp_date: document.getElementById('exp_date'),
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

    // Create XMLHttpRequest for search
    const request = new XMLHttpRequest();
    const requestData = new URLSearchParams({
        Fname: customer_const.Fname.value,
        Lname: customer_const.Lname.value,
        email: customer_const.email.value,
        phone: customer_const.phone.value,
        address : customer_const.address.value,
        license_exp_date : customer_const.license_exp_date.value,
        license_Id : customer_const.license_Id.value,
        Id : customer_const.Id.value
    }).toString();

    request.open('GET', `search-customer.php?${requestData}`, true); // Use GET request with query params
    console.log('GET', `search-customer.php?${requestData}`);

    request.onload = function () {
        if (this.status >= 200 && this.status < 300) {
            try {
                const data = JSON.parse(this.responseText); // Parse JSON response
                const resultsContainer = document.getElementById("results");
                resultsContainer.innerHTML = ""; // Clear previous results
                console.log("sent to php");
                if (data.ok && data.customers && data.customers.length > 0) {
                    console.log(data.customers);
                    displayReport(data.customers); // Pass only the cars array
                } else {
                    resultsContainer.innerHTML += '<p class="car">No customers with these specifications. !!</p>';
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