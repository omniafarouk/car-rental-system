const dashboard = {
    register: document.getElementById('register')
};
const register_popup = {
    close: document.getElementById('close'),
    model: document.getElementById('model'),
    year: document.getElementById('year'),
    plateID: document.getElementById('plateID'),
    capacity: document.getElementById('capacity'),
    fuel: document.getElementById('fuel'),
    mileage: document.getElementById('mileage'),
    bodyType: document.getElementById('bodyType'),
    transmission: document.getElementById('transmission'),
    color: document.getElementById('color'),
    price: document.getElementById('price'),
    officeID: document.getElementById('officeID'),
    submit: document.getElementById('submit')
};

function isValidCarPlate(plate) {
    const regex = /^(?:[A-Za-z]{3} \d{4}|[A-Za-z]{3} \d{4} [Tt]| \d{4} [A-Za-z]{3})$/;
    return regex.test(plate);
}
dashboard.register.addEventListener('click', (e) => {
    e.preventDefault(); 
    document.querySelector(".popup").style.display = "flex";
});
register_popup.close.addEventListener('click',(e)=> {
    document.querySelector(".popup").style.display = "none";
});
register_popup.submit.addEventListener('click',(e)=> {
    e.preventDefault();
    const fields = Object.values(register_popup).filter(field => field !== register_popup.close);
    for (let field of fields) {
        if (!field.value) {
            alert(`Please fill in the ${field.id} field.`);
            return;
        }
    }
    if(register_popup.year.value.length != 4){
        alert("Please Enter a Valid Year!"); 
    }else if(!isValidCarPlate(register_popup.plateID.value)){
        alert("Please Enter a Valid Plate Id!");
    }else{
        const request = new XMLHttpRequest();
        const requestData = `model=${register_popup.model.value}&plateId=${register_popup.plateID.value}&year=${register_popup.year.value}&capacity=${register_popup.capacity.value}&fuel=${register_popup.fuel.value}
        &mileage=${register_popup.mileage.value}&bodyType=${register_popup.bodyType.value}&transmission=${register_popup.transmission.value}&color=${register_popup.color.value}&price=${register_popup.price.value}&officeId=${register_popup.officeID.value}`;
        
        request.open('POST', 'dashboard-php.php'); // Open the request before setting headers
        request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        request.send(requestData); // Send the request

    }

});