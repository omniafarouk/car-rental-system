// References to dashboard form and elements
const dashboard = {
    search: document.getElementById('searchForm'),
    submit: document.querySelector('#searchForm button[type="submit"]') 
};

const reserv_const = {
    sdate: document.getElementById('sdate'),
    edate: document.getElementById('edate'),
    rdate: document.getElementById('rdate'),
    carId: document.getElementById('carId')

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
const email = localStorage.getItem('email');

dashboard.submit.addEventListener('click', (e) => {
    console.log("JavaScript connected");
    e.preventDefault(); // Prevent default form submission

    // Build search query parameters, filtering out empty fields
    const requestData = new URLSearchParams({
        sdate: reserv_const.sdate.value,
        edate: reserv_const.edate.value,
        rdate: reserv_const.rdate.value,
        carId : reserv_const.carId.value,
        email : email
    }).toString();

    // Create XMLHttpRequest for search
    const request = new XMLHttpRequest();
    request.open('GET', `return-reservation.php?${requestData}`, true); // Use GET request with query params
    console.log('GET', `return-reservation.php?${requestData}`);

    request.onload = function () {
        if (this.status >= 200 && this.status < 300) {
            try {
                const data = JSON.parse(this.responseText); // Parse JSON response
                const resultsContainer = document.getElementById("results");
                resultsContainer.innerHTML = ""; // Clear previous results
                console.log("sent to php");
                if (data.ok && data.reservations && data.reservations.length > 0) {
                data.reservations.forEach((reservation) => {
                    console.log("Results Occur");
                    resultsContainer.innerHTML += `
                    <div class="cars">
                        <h3>${reservation.car_id}</h3>
                        <p><strong>Reservation period:</strong> From ${reservation.start_date} To ${reservation.end_date}</p>
                        <p><strong>Reservation Date:</strong> ${reservation.reservation_date}</p>
                        <p><strong>Total payment :</strong> ${reservation.total_payment}</p>
                        <button class="return" onclick="returnReservation()">Return</button>
                    </div>`
                });
                }else {
                    resultsContainer.innerHTML += '<p class="reservations">No Reservations found matching your criteria.</p>';
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