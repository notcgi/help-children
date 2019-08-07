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

    xhr.open("POST", "https://xn--c1accbmwfjbh2bd3o.xn--p1ai/user/sendConfirm");
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

    xhr.open("POST", "https://xn--c1accbmwfjbh2bd3o.xn--p1ai/account/sendPayoutRequest");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Accept", "*/*");
    xhr.setRequestHeader("Cache-Control", "no-cache");                    
    xhr.setRequestHeader("cache-control", "no-cache");

    xhr.send(data);
}

function openShare() {
    document.querySelector('#btnShare').style.display = 'none';
    document.querySelector('#link').style.display = 'block';
    document.querySelector('#share').style.display = 'flex';
}

function resetModal() {
    document.querySelector('#modal-register-content').style.display = 'block';
    document.querySelector('#modal-register-send').style.display = 'none';
    document.querySelector('#form').reset();
}

function modalRegistration() {
    let fund = document.querySelector('#fund').value;
    let firstName = document.querySelector('#inputFirstName').value;
    let lastName = document.querySelector('#inputLastName').value;
    let email = document.querySelector('#inputEmail').value;
    let phone = document.querySelector('#inputPhone').value;
    let check = document.querySelector('#inputCheck').value ? 1 : 0;
    
    let data = "email=" + email + "&phone=" + phone + "&firstName=" + firstName + "&lastName=" + lastName + "&check=" + check + "&fund=" + fund;

    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;

    xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {      
        if (xhr.responseText === 'true') {
            document.querySelector('#modal-register-content').style.display = 'none';
            document.querySelector('#modal-register-send').style.display = 'block';  
        }
    }
    });
    
    xhr.open("POST", "https://xn--c1accbmwfjbh2bd3o.xn--p1ai/user/registerFundMethod");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Accept", "*/*");
    xhr.setRequestHeader("Cache-Control", "no-cache");                    
    xhr.setRequestHeader("cache-control", "no-cache");

    xhr.send(data);
}

function sleep(milliseconds) {
    var start = new Date().getTime();
    for (var i = 0; i < 1e7; i++) {
      if ((new Date().getTime() - start) > milliseconds){
        break;
      }
    }
  }

function showCalendar() {
    document.querySelector('#buttonLater').style.display = 'none';
    document.querySelector('#blockWhen').style.display = 'block';    
}

function sendReminder() {
    let date = document.querySelector('#inputDate').value;
    if (!date) {
        alert('Сначала выберите дату!');
        return;
    }

    let email = document.querySelector('#email').value;    
    if (!email) {
        alert('Введите Email');
        return;
    }

    let name = document.querySelector('#name').value;
    if (!name) {
        alert('Введите имя');
        return;
    }
    
    document.querySelector('#blockDate').style.display = 'none';
    document.querySelector('#buttonWhen').textContent = 'Напоминание будет отправлено ' + date;


    let data = "email=" + email + "&name=" + name + "&date=" + date;

    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;

    xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {        
    }
    });

    xhr.open("POST", "https://xn--c1accbmwfjbh2bd3o.xn--p1ai/donate/sendReminder");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Accept", "*/*");
    xhr.setRequestHeader("Cache-Control", "no-cache");                    
    xhr.setRequestHeader("cache-control", "no-cache");

    xhr.send(data);
}