const signform = {
    email: document.getElementById('email'),
    password: document.getElementById('password'),
    submit: document.getElementById('button')
};
function isEmpty(field) {
    return field.value.trim() === '';  // Used trim() to remove leading/trailing spaces
}
function ValidateEmail(uemail){
    var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    if(uemail.match(mailformat)){
      return true;
    }
    else{
      return false;
    }
  }
signform.submit.addEventListener('click', (e) => {
    e.preventDefault();

    if (isEmpty(signform.email)) {
        alert("Email is a required field!");
        return;
    }
    if (!ValidateEmail(signform.email.value)) {
        alert("Invalid Email!");
        return;
    }
    if (isEmpty(signform.password)) {
        alert("Password is a required field!");
        return;
    }

    const request = new XMLHttpRequest();
    const requestData = `email=${signform.email.value}&password=${signform.password.value}`;
    console.log("Request Data:", requestData);

    request.open('POST', 'loginSubmit.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);

    request.onload = () => {
        try {
            const responseObject = JSON.parse(request.responseText);
            if (responseObject.ok) {
                alert("Welcome " + (responseObject.messages || "user") + "!");
                // Get the selected report type value
                localStorage.setItem('email', signform.email.value);  // Store the report type in localStorag
                window.location.href = "./Dashboard.html";
            } else {
                alert(responseObject.messages);
            }
        } catch (e) {
            console.error('Could not parse JSON!', e);
            console.error('Server response:', request.responseText);
            alert("Server error. Please try again later.");
        }
    };

    request.onerror = () => {
        console.error("Request failed to send!");
    };

    request.onreadystatechange = () => {
        if (request.readyState === 4 && request.status !== 200) {
            console.error(`Request failed with status: ${request.status}`);
        }
    };
});
