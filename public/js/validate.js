function validate_registration() {    
    document.getElementById('phone').style.display = 'none';
    document.getElementById('pass').style.display = 'none';
    document.getElementById('confirm').style.display = 'none';

    let isValid = true;
    let fields = document.getElementsByClassName('registration-form-input');

    let phoneNumber = fields[3];
    let password = fields[5];
    let confirm = fields[6];
    
    if (phoneNumber.value.length < 10 || phoneNumber.value.length > 13) {        
        document.getElementById('phone').style.display = 'block';
        isValid = false;
    }

    let passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,30}$/;
    if(!password.value.match(passw)) {
        document.getElementById('pass').style.display = 'block';
        isValid = false;
    }
    else
    if (password.value !== confirm.value) {
        document.getElementById('confirm').style.display = 'block';    
        isValid = false;
    }
    
    return isValid;
}

function validate_reset() {
    document.getElementById('pass').style.display = 'none';
    document.getElementById('confirm').style.display = 'none';

    let isValid = true;
    let fields = document.getElementsByClassName('registration-form-input');
    
    let password = fields[0];
    let confirm = fields[1];

    let passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,30}$/;
    if(!password.value.match(passw)) {
        document.getElementById('pass').style.display = 'block';
        isValid = false;
    }
    else
    if (password.value !== confirm.value) {
        document.getElementById('confirm').style.display = 'block';    
        isValid = false;
    }
    
    return isValid;
}