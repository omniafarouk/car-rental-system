const signform = {
    Fname: document.getElementById('fname'),
    Lname: document.getElementById('lname'),
    email: document.getElementById('email'),
    phone: document.getElementById('phone'),
    Id: document.getElementById('id'),
    address: document.getElementById('address'),
    license_Id: document.getElementById('licenseId'),
    license_exp_date: document.getElementById('exp_date'),
    password: document.getElementById('password'),
    confirmPass: document.getElementById('confirmPass'),
    submit: document.getElementById('button')
};
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

    const fields = Object.values(signform).filter(field => field !== signform.submit);
    for (let field of fields) {
        if (!field.value) {
            alert(`Please fill in the ${field.id} field.`);
            return;
        }
    }
    if(!ValidateEmail(document.getElementById('email').value)){
        alert("Invalid email.");
        return;  
    }
    if(isNaN(signform.Id.value) || signform.Id.value < 8){
        alert("Id is not valid , please try again.");
        document.getElementById('id').focus()
        document.getElementById('id').value ='';
        return;  
    }
    if (signform.password.value.length < 6) {
        alert("Your password must be at least 6 characters long.");
        document.getElementById('password').value ='';
        return;
    }
    if (signform.password.value !== signform.confirmPass.value) {
        alert("Passwords do not match. Please try again.");
        document.getElementById('confirmPass').focus();
        document.getElementById('confirmPass').value = '';
        return;
    }
    // Prepare the request
    const request = new XMLHttpRequest();
    const requestData = `email=${signform.email.value}&password=${signform.password.value}&lname=${signform.Lname.value}&fname=${signform.Fname.value}&phone=${signform.phone.value}&id=${signform.Id.value}&address=${signform.address.value}&licenseId=${signform.license_Id.value}&license_exp_date=${signform.license_exp_date.value}`;

    console.log(requestData);

    request.open('POST', 'signupSubmit.php');
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData);

    request.onload = () => {
        if (request.status >= 200 && request.status < 300) {
            try {
                const responseObject = JSON.parse(request.responseText);
                if (responseObject.ok) {
                    alert("Welcome " + signform.Fname.value +" "+ signform.Lname.value + "!");
                    localStorage.setItem('email', signform.email.value);  // Store the report type in localStorag
                    window.location.href = "./Dashboard.html";
                } else {
                    alert(responseObject.message || "An error occurred.");
                }
            } catch (e) {
                console.error('JSON parse error:', e, request.responseText);
                alert("Server response is invalid. Please try again.");
            }
        } else {
            console.error('HTTP error:', request.status, request.statusText);
            alert("Server error. Please try again later.");
        }
    };
    
});
