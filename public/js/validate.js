function validate_registration() {    
    document.getElementById('firstNameError').style.display = 'none';
    document.getElementById('emailError').style.display = 'none';
    document.getElementById('phone').style.display = 'none';
    document.getElementById('pass').style.display = 'none';
    document.getElementById('confirm').style.display = 'none';

    let isValid = true;
    let fields = document.getElementsByClassName('registration-form-input');

    let name = fields[1];
    let phoneNumber = fields[3];
    let email = fields[4];
    let password = fields[5];
    let confirm = fields[6];
        
    if (name.value === '') {
        document.getElementById('firstNameError').style.display = 'block';
        isValid = false;
    }
    // phoneNumber.valuereplace(/^8/,'+7');
    // phoneNumber.valuereplace(/^9/,'+79');
    if (phoneNumber.value.replace(/\D/gi, '').length < 10 || phoneNumber.value.replace(/\D/gi, '').length > 13 || a.indexOf('+7')!=0) {        
        document.getElementById('phone').style.display = 'block';
        isValid = false;
    }
    
    let emailPattern = ".+@.+\..+";              
    if (!email.value.match(emailPattern)) {        
        document.getElementById('emailError').style.display = 'block';
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
    
    return isValid && checkEmailExisting('registration_form_email');
}

function validate_reset() {
    document.getElementById('pass').style.display = 'none';
    document.getElementById('confirm').style.display = 'none';

    let isValid = true;
    let fields = document.getElementsByClassName('input-rounded');
    
    let password = fields[0];
    let confirm = fields[1];    

    let passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,30}$/;
    if(!password.value || !password.value.match(passw)) {
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

function validate_login() {
    document.getElementById('loginError').style.display = 'none';
    document.getElementById('passwordError').style.display = 'none';

    let isValid = true;
    let fields = document.getElementsByClassName('input-rounded');
    
    let login = fields[0].value;
    let password = fields[1].value;

    if (login === "") {
        document.getElementById('loginError').style.display = 'block';
        isValid = false;
    }

    if (password === "") {
        document.getElementById('passwordError').style.display = 'block';
        isValid = false;
    }
    
    return isValid;
}

function sendEmailConfirmCode() {
    let inputEmail = document.querySelector('#inputEmail');
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

    document.getElementById('firstNameError').style.display = 'none';    
    document.getElementById('emailError').style.display = 'none';
    document.getElementById('emailExist').style.display = 'none';
    document.getElementById('phoneExist').style.display = 'none';
    document.getElementById('phone').style.display = 'none';
}

function validate_modal_registration() {
    let firstNameInput = document.querySelector('#inputFirstName').value;    
    let emailInput = document.querySelector('#inputEmail').value;
    let phoneInput = document.querySelector('#inputPhone').value;

    document.getElementById('firstNameError').style.display = 'none';    
    document.getElementById('emailError').style.display = 'none';    
    document.getElementById('phone').style.display = 'none';

    let isValid = true;    
    
    if (firstNameInput === '') {
        document.getElementById('firstNameError').style.display = 'block';
        isValid = false;
    }

    if (phoneInput.length < 10 || phoneInput.length > 13|| phoneInput.indexOf('+7')!=0) {        
        document.getElementById('phone').style.display = 'block';
        isValid = false;
    }

    let emailPattern = ".+@.+\..+";              
    if (!emailInput.match(emailPattern)) {        
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
    }

    return isValid;
}

function modalRegistration() {
    if (!validate_modal_registration())
        return;

    let fund = document.querySelector('#fund').value;
    let firstName = document.querySelector('#inputFirstName').value;
    let lastName = document.querySelector('#inputLastName').value;
    let email = document.querySelector('#inputEmail').value;
    let phone = document.querySelector('#inputPhone').value;
    let check = document.querySelector('#inputCheck').checked ? 1 : 0;
    
    let data = "email=" + email + "&phone=" + phone + "&firstName=" + firstName + "&lastName=" + lastName + "&check=" + check + "&fund=" + fund;

    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;

    xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {      
        if (xhr.responseText === 'true') {
            document.querySelector('#modal-register-content').style.display = 'none';
            document.querySelector('#modal-register-send').style.display = 'block';  
        }
        else if (xhr.responseText === 'false (email)') {
            document.querySelector('#emailExist').style.display = 'block';
        }
        else if (xhr.responseText === 'false (phone)') {
            document.querySelector('#phoneExist').style.display = 'block';
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

    let lastName = document.querySelector('#lastName').value;
    let phone = document.querySelector('#phone').value;
    
    document.querySelector('#blockDate').style.display = 'none';
    document.querySelector('#buttonWhen').textContent = 'Напоминание будет отправлено ' + date;


    let data = "email=" + email + "&name=" + name + "&date=" + date + "&lastName=" + lastName + "&phone=" + phone;

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

function checkPhone() {
    ph = document.getElementsByClassName('input-type-phone')[0].value;
    document.getElementsByClassName('input-type-phone')[0].value=ph.replace(/\D/gi, '')
    document.getElementsByClassName('input-type-phone')[0].value=ph.replace(/^8/,'+7');
    document.getElementsByClassName('input-type-phone')[0].value=ph.replace(/^9/,'+79');
    document.getElementsByClassName('input-type-phone')[0].value=ph.replace(/^7/,'+7');
    let flag = document.getElementsByClassName('selected-flag')[0].title;
    let code = flag.split(': ')[1];    
    let phone = document.getElementsByClassName('input-type-phone')[0].value;

    if (phone.length < code.length)
        document.getElementsByClassName('input-type-phone')[0].value = code;        
    if (phone === '')
        document.getElementsByClassName('input-type-phone')[0].value = code; 
}

function validate_recovery() {    
    let emailInput = document.querySelector('#inputEmail').value;    
    
    document.getElementById('emailError').style.display = 'none';        
    let isValid = true;    

    let emailPattern = ".+@.+\..+";              
    if (!emailInput.match(emailPattern)) {        
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
    }

    return isValid;
}

function validate_myAccount() {    
    let firstNameInput = document.querySelector('#inputFirstName').value;    
    let emailInput = document.querySelector('#inputEmail').value;
    let phoneInput = document.querySelector('#inputPhone').value.replace(/\D/gi, '');
    let passwordInput = document.querySelector('#inputPassword').value;
    let newPassowrdInput = document.querySelector('#inputNewPassword');
    let confirmPassowrdInput = document.querySelector('#inputConfirmPassword');

    document.getElementById('firstNameError').style.display = 'none';    
    document.getElementById('emailError').style.display = 'none';
    document.getElementById('emailExist').style.display = 'none';
    document.getElementById('phoneExist').style.display = 'none';
    document.getElementById('phoneError').style.display = 'none';
    document.getElementById('passwordError').style.display = 'none';
    document.getElementById('newPasswordError').style.display = 'none';
    document.getElementById('confirmPasswordError').style.display = 'none';

    let isValid = true;    
    
    if (firstNameInput === '') {
        document.getElementById('firstNameError').style.display = 'block';
        isValid = false;
    }

    if (phoneInput.length < 10 || phoneInput.length > 13) {        
        document.getElementById('phoneError').style.display = 'block';
        isValid = false;
    }

    let emailPattern = ".+@.+\..+";              
    if (!emailInput.match(emailPattern)) {        
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
    }

    if (passwordInput === '') {        
        document.getElementById('passwordError').style.display = 'block';
        isValid = false;
    }    

    let password = newPassowrdInput;
    let confirm = confirmPassowrdInput;    

    let passw = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,30}$/;
    if (password.value) {
        if(!password.value.match(passw)) {
            document.getElementById('newPasswordError').style.display = 'block';
            isValid = false;
        }
        else
        if (password.value !== confirm.value) {
            document.getElementById('confirmPasswordError').style.display = 'block';    
            isValid = false;
        }
    }

    return isValid && checkEmailExisting('inputEmail');        
}

function checkEmailExisting(element) {
    let emailInput = document.getElementById(element).value;  
    let phoneInput = document.getElementsByClassName('input-type-phone')[0].value;

    document.getElementById('emailExist').style.display = 'none';    
    
    var data = "email=" + emailInput  + "&phone=" + phoneInput;

    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;    
    xhr.open("POST", "https://xn--c1accbmwfjbh2bd3o.xn--p1ai/checkEmail", false);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Accept", "*/*");
    xhr.setRequestHeader("Cache-Control", "no-cache");                    
    xhr.setRequestHeader("cache-control", "no-cache");
        
    xhr.send(data);

    if (xhr.responseText === 'exist') {
        document.getElementById('emailExist').style.display = 'block';
        return false;
    }
    if (xhr.responseText === 'phone') {
        document.getElementById('phoneExist').style.display = 'block';
        return false;
    }
    return true;
}

function hideError(element) {
    document.getElementById(element).style.display = 'none';
}

function validate_donate() {
    let isValid = true;
    document.getElementById('checkboxError').style.display = 'none';
    let checkBoxInput = document.getElementById('checkboxInput').checked;
    if (!checkBoxInput) {
        document.getElementById('checkboxError').style.display = 'block';
        isValid = false;
    }

    let firstNameInput = document.querySelector('#name').value;    
    let emailInput = document.querySelector('#email').value;
    let phoneInput = document.querySelector('#phone').value.replace(/\D/gi, '');

    document.getElementById('firstNameError').style.display = 'none';    
    document.getElementById('emailError').style.display = 'none';    
    document.getElementById('phoneError').style.display = 'none';    
    
    if (firstNameInput === '') {
        document.getElementById('firstNameError').style.display = 'block';
        isValid = false;
    }

    if (phoneInput.length < 10 || phoneInput.length > 13) {        
        document.getElementById('phoneError').style.display = 'block';
        isValid = false;
    }

    let emailPattern = ".+@.+\..+";              
    if (!emailInput.match(emailPattern)) {        
        document.getElementById('emailError').style.display = 'block';
        isValid = false;
    }
    
        return isValid;    
}