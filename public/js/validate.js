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

function sendEmailConfirmCode() {
    let inputEmail = document.querySelector('#email');
    let btnStatus = document.querySelector('#btnStatus');
    let btnSend = document.querySelector('#btnSend');

    btnStatus.textContent = 'Письмо отправлено.';
    btnSend.style.display = 'None';

    let email = inputEmail.value;
    var data = "email=" + email;

    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;

    xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {        
    }
    });

    xhr.open("POST", "https://test.xn--c1accbmwfjbh2bd3o.xn--p1ai/user/sendConfirm");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Accept", "*/*");
    xhr.setRequestHeader("Cache-Control", "no-cache");                    
    xhr.setRequestHeader("cache-control", "no-cache");

    xhr.send(data);
}

function sendPayoutRequest() {
    let btnSend = document.querySelector('#btnSendPayout');
    btnSend.blur();        
    btnSend.textContent = 'Заявка отправлена';
    btnSend.disabled = true;    
    btnSend.style.cursor = 'default';    
    btnSend.style.pointerEvents = 'none';
    btnSend.className = 'btn';

    let email = document.querySelector('#email').value;
    var data = "email=" + email;

    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;

    xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {        
    }
    });

    xhr.open("POST", "https://test.xn--c1accbmwfjbh2bd3o.xn--p1ai/account/sendPayoutRequest");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Accept", "*/*");
    xhr.setRequestHeader("Cache-Control", "no-cache");                    
    xhr.setRequestHeader("cache-control", "no-cache");

    xhr.send(data);
}