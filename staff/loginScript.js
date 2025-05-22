const signform = {
    id: document.getElementById('id'),
    password: document.getElementById('password'),
    submit: document.getElementById('button')
};
function isEmpty(field) {
    return field.value.trim() === '';  // Used trim() to remove leading/trailing spaces
}
signform.submit.addEventListener('click', (e) => {
    e.preventDefault();

    if(isEmpty(signform.id)){
        alert("ID is a required field!");
        return;
    }
    if(isEmpty(signform.password)){
        alert("Password is a required field!");
        return;
    }

    const request = new XMLHttpRequest();

    const requestData = `id=${signform.id.value}&password=${signform.password.value}`;
    console.log(requestData); // Log request data to verify

    request.open('POST', 'staffSubmit.php'); // Open the request before setting headers
    request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    request.send(requestData); // Send the request

    // Set up the onload callback after opening the request
    request.onload = () => {
        let responseObject = null;

        try{
            responseObject = JSON.parse(request.responseText);
        } catch(e){
            console.error('Could not parse JSON!');
            return;
        }
        if(responseObject){
            if(responseObject.ok){
               alert("Welcome " + (responseObject.messages) + "!");
            }else{
                alert("Invalid ID or Password.");
            }
        }
    };
    
});