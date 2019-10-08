{% extends 'base_client_wrapper.twig' %}

{% block title %}БФ “Помогите Детям” | Онлайн помощь{% endblock %}
{% block desciption %}БФ “Помогите Детям” | Онлайн помощь{% endblock %}
{% block keywords %}Онлайн помощь{% endblock %}

{% block content %}
    <!--
    {%- for error in formErrors %}
        {{ error }}
    {% endfor -%}
    -->
    <main>
        <section class="section hero-large">
            <div class="container">
                <h1 class="hero-large__title">Форма онлайн помощи</h1>
                <p class="hero-large__subtitle">помочь прямо сейчас за 3 шага</p>
            </div>
        </section>
        <div class="bg-purple-line"></div>
        <section class="section online-help section-with-bghearts">
            <div class="container container--small">
                <div class="online-help-wrapper">
                    <form method="POST" action="{{ path('donate') }}">
                        <input type="hidden" name="child_id" value="{{ form.child_id ?: '' }}">
                        <div class="ohf-row">
                            <div class="ohf-row__col ohf-col-label">
                                <div class="ohf-col-label__number">1</div>
                                <div class="ohf-col-label__notice">
                                    <div class="ohf-col-label__notice-title">Выберите способ оплаты</div>
                                    <p class="ohf-col-label__notice-subtitle">Выберите удобный вариант оплаты</p>
                                </div>
                            </div>
                            <div class="ohf-row__col ohf-col-input">
                                {% include 'components/payment_methods.twig' %}
                            </div>
                        </div>
                        <div class="ohf-row showVISA"
                             style="display:{{ 'visa' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row__col ohf-col-label">
                                <div class="ohf-col-label__number">2</div>
                                <div class="ohf-col-label__notice">
                                    <div class="ohf-col-label__notice-title">Кто наш благотворитель?</div>
                                    <p class="ohf-col-label__notice-subtitle">Мы хотим знать кого благодарить :)</p>
                                </div>
                            </div>
                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row">
                                    <div class="form-group ohf-col-input__row-item">
                                        <input id='name' class="input-rounded input-fullwidth" type="text" name="name"
                                               placeholder="Введите ваше имя*" value="{{ form.name }}" onclick="hideError('firstNameError');">
                                        <div id="firstNameError" class="form-error-tooltip" style="display: none; position: relative; left: 0; top: -1px; width: 265px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Введите имя</p>
                                        </div>
                                    </div>
                                    <div class="form-group ohf-col-input__row-item ohf-col-input__row-item__notfirst">
                                        <input id='lastName' class="input-rounded input-fullwidth" type="text" name="surname"
                                               placeholder="Введите вашу фамилию" value="{{ form.surname }}">
                                    </div>
                                </div>
                                <div class="ohf-col-input__row">
                                    <div class="form-group ohf-col-input__row-item">
                                        <input id='phone' class="input-rounded input-fullwidth input-type-phone" type="text" name="phone"
                                               placeholder="Введите номер телефона*" data-type="phone"
                                               value="{{ form.phone }}" onclick="hideError('phoneError');checkPhone();" onkeyup="checkPhone();">
                                        <div id="phoneError" class="form-error-tooltip" style="display: none; position: relative; left: 0; top: -1px; width: 265px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Введите правильный номер</p>
                                        </div>
                                    </div>
                                    <div class="form-group ohf-col-input__row-item ohf-col-input__row-item__notfirst">
                                        <input id='email' class="input-rounded input-fullwidth" type="email" name="email"
                                               placeholder="Введите E-mail*" value="{{ form.email }}" onclick="hideError('emailError');">
                                        <div id="emailError" class="form-error-tooltip" style="display: none; width: 265px; position: relative; left: 0; top: -1px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Введите правильный email</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ohf-row showSMS"
                             style="display:{{ 'sms' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row__col ohf-col-label">
                                <div class="ohf-col-label__number">2</div>
                                <div class="ohf-col-label__notice">
                                    <div class="ohf-col-label__notice-title">Отправьте SMS</div>
                                    <p class="ohf-col-label__notice-subtitle">с помощью телефона</p>
                                </div>
                            </div>
                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row">
                                    <p class='little-paragraph'>
                                        Отправьте SMS на номер <strong style="background-color: #faedd8; padding: 10px; border-radius: 20px;">3443</strong>
                                        с сообщением <strong style="background-color: #faedd8; border-radius: 20px; padding: 10px;">ФОНДУ 400</strong>,<br>
                                        где 400 - сумма пожертвования.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="ohf-row showVISA" style="display:{{ 'visa' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row__col ohf-col-label">
                                <div class="ohf-col-label__number">3</div>
                                <div class="ohf-col-label__notice">
                                    <div class="ohf-col-label__notice-title">Сумма пожертвования</div>
                                    <p class="ohf-col-label__notice-subtitle">Выберите тип оплаты</p>
                                </div>
                            </div>
                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row" style='flex-direction:column'>
                                    <div class="donate-sum-fieldgroup toggle-fieldgroup" style="overflow: hidden">
                                        <label class="donate-sum-fieldgroup__label toggle-field">
                                            <input class="toggle-field__input" type="radio" name="radioSum" value="300" checked>
                                            <span class="toggle-field__text">300 Р</span>
                                        </label>
                                        <label class="donate-sum-fieldgroup__label toggle-field">
                                            <input class="toggle-field__input" type="radio" name="radioSum" value="500">
                                            <span class="toggle-field__text">500 Р</span>
                                        </label>
                                        <label class="donate-sum-fieldgroup__label toggle-field">
                                            <input class="toggle-field__input" type="radio" name="radioSum" value="700">
                                            <span class="toggle-field__text">700 Р</span>
                                        </label>
                                        <label class="donate-sum-fieldgroup__label">
                                            <input class="donate-text-field"
                                                   type="number"
                                                   name="textSum"
                                                   min="50"
                                                   max="1000000"
                                                   maxlength="7"
                                                   minlength="2"
                                                   value=""
                                                   placeholder="Другая сумма">
                                            <input type="hidden"
                                                   name="sum"
                                                   min="50"
                                                   max="1000000"
                                                   maxlength="7"
                                                   minlength="2"
                                                   value="{{ form.sum ?: 0 }}">
                                        </label>
                                    </div>
                                    <button id='buttonLater' class='btn btn-link' style='margin: 0 auto;' type='button' onclick='showCalendar();'>Совершить пожертвование позже</button>
                                    <div id='blockWhen' style='display:none; margin: 0 auto;'>
                                        <button id='buttonWhen' class='btn btn-link' style='margin: 0 auto; pointer-events:none;' type='button' disabled>Когда вам напомнить о пожертвовании?</button>
                                        <div id='blockDate' style="text-align:center">
                                            <input id='inputDate' class='input-rounded' type='text' placeholder='Выберите дату' value="{{ "now"|date("d.m.Y") }}" style="min-width: 180px;" readonly>
                                            <button class="btn btn-dark referrals__invite-button"
                                                    style="background:none;color:black;padding-left:1rem;padding-right:1rem"
                                                    type="button"
                                                    onClick="sendReminder()">Отправить</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="ohf-col-input__row ohf-col-input__row--start">
                                    <div class="input-radio payment-count-field">
                                        <label class="input-radio__label">
                                            <input class="input-radio__input" type="radio" name="recurent" value="true"
                                                    {{ form.recurent ? 'checked' }}>
                                            <span class="input-radio__radiomark"></span>
                                            <span class="input-radio__label">Ежемесячная оплата</span>
                                        </label>
                                    </div>
                                    <div class="input-radio payment-count-field">
                                        <label class="input-radio__label">
                                            <input class="input-radio__input" type="radio" name="recurent" value=""
                                                    {{ not form.recurent ? 'checked' }}>
                                            <span class="input-radio__radiomark"></span>
                                            <span class="input-radio__label">Единоразовая оплата</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {% if not app.user %}
                            <div class="ohf-row showVISA"
                                 style="display:{{ 'visa' == form['payment-type'] ? 'flex' : 'none' }}">
                                <div class="ohf-row__col ohf-col-label">
                                    <div class="ohf-col-label__number">4</div>
                                    <div class="ohf-col-label__notice">
                                        <div class="ohf-col-label__notice-title">Кто вас пригласил?</div>
                                        <p class="ohf-col-label__notice-subtitle">Не обязательно</p>
                                    </div>
                                </div>
                                <div class="ohf-row__col ohf-col-input">
                                    <div class="ohf-col-input__row">
                                        <input class="input-rounded input-fullwidth" type="text" name="ref-code"
                                               placeholder="Введите код приглашения" value="{{ form['ref-code'] }}"
                                                {% if not (form['ref-code'] is empty) %} disabled {% endif %}>
                                    </div>
                                </div>
                            </div>
                        {% endif %}
                        <div class="ohf-row showVISA"
                             style="display:{{ 'visa' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row__col ohf-col-label"></div>
                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row ohf-col-input__row--start">
                                    <div class="input-checkbox payment-count-field">
                                        <label class="input-checkbox__label">
                                            <input class="input-checkbox__input"
                                                   type="checkbox"
                                                   id="checkboxInput"
                                                   name="agree"
                                                   value="true"
                                                   onclick="hideError('checkboxError');"
                                                   checked>
                                            <span class="input-checkbox__checkmark"></span>
                                            <span class="input-checkbox__label">Я согласен с&nbsp;
                                                <a class="link--purple" href="/docs/Public_offer.pdf" target="_blank">договором оферты</a>
                                            </span>
                                        </label>
                                        <div id="checkboxError" class="form-error-tooltip" style="display: none; position: relative; left: 0; top: -1px; width: 300px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Необходимо согласие</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ohf-col-input__row ohf-col-input__row--start ohf-col-buttons">
                                    <button class="btn btn-dark btn--big online-help-form__submit js-online-help-button-submit" type="submit" onclick="ym(53180137, 'reachGoal', 'pozhertvovat'); return validate_donate();">
                                        Пожертвовать
                                    </button>
                                    <div class="donate-form-payment-systems__cards-onDonatPage" style="width: 300px; margin: 0;">
                                        <div>Оплата с карт</div>
                                        <div id="paymentByVisa" class="toggle-field__text" style="opacity: 0.7; border-right: none; padding-left: 0; text-align: center; font-size: 1.2rem; color: #707070 !important; text-transform: none;">
                                            <img class="toggle-field__text-image" src="http://test.pomogitedetyam.ru/Apple_Pay.png" width="50"><img class="toggle-field__text-image" src="http://test.pomogitedetyam.ru/Google_Pay.png" width="50"><img class="toggle-field__text-image" src="/images/mastercard.png" width="30"><img class="toggle-field__text-image" src="/images/visa.png" width="40">
                                        </div>
                                    </div>

                                    {# <span class="online-help-form__or">или</span>
                                    <button class="btn btn-black btn--big btn-withicon online-help-form__applepay js-online-help-button-applepay"
                                        type="button">
                                        <span class="icon icon-apple"></span> <span>Оплатить</span>
                                    </button> #}
                                </div>
                                <div
                                        class="donate-form-payment-systems__notice online-help-form-payment-systems__notice">
                                    <div class="icon-wrapper"><span class="icon icon-lock"></span></div>
                                    <span style="padding-top:5px">Все сделки защищены SSL, PSI DSS, <br>
                                        Персональные данные конфиденциальны.
                                    </span>
                                </div>
                            </div>
                        </div>



                        <div id="requisitesContent"
                             class="ohf-row"
                             style="display:{{ 'requisite-services' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="contacts-details">
                                {% include 'components/requisites.twig' %}
                            </div>
                        </div>
                        <div id="smsContent"
                             class="ohf-row"
                             style="padding:3rem 7rem;display:{{ 'sms' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="sms-details">
                                <div class="contacts-details__row-value">

                                    <p>Чтобы Ваша поддержка была регулярной, отправьте SMS на <strong>3443</strong> текстом: <strong>ФОНДУ 400 ПОДПИСКА</strong> и Вам ежемесячно будет приходить SMS-напоминание с просьбой подтвердить данный платеж. Чтобы отписаться от регулярного пожертвования, отправьте SMS на <strong>3443</strong> с текстом <strong>ФОНДУ СТОП</strong>.<br></p>

                                    <p>Допустимый размер пожертвования — от 10 до 5 000 рублей. Стоимость отправки SMS на <strong>3443</strong> – бесплатно. Комиссия с абонента - 0%. Услуга доступна для абонентов МТС, Билайн, Мегафон, Теле2.<br><a href="http://info.gmmobile.ru">Техническая поддержка сервиса осуществляется компанией GmMobile.ru</a></p>
                                    <hr>
                                    <p><strong>Информация для абонентов:</strong></p>
                                    <img src="/images/operators.png" alt="Операторы сотовой связи">
                                    <p>
                                        МТС Договор оферты: <a href="http://static.mts.ru/uploadmsk/contents/1655/soglashenie_easy_pay.pdf">Ссылка</a><br>
                                        ТЕЛЕ2 Договор оферты: <a href="https://market.tele2.ru/offer/">Ссылка</a><br>
                                        БИЛАЙН Договор оферты: <a href="https://static.beeline.ru/upload/dpcupload/images/help/Docs/Limity_pri_oplate_tovarov.pdf">Ссылка</a><br>
                                        МЕГАФОН Комиссия с абонента — 0%. Договор оферты: <a href="http://moscow.megafon.ru/popups/oferta_m_payment.html">Ссылка</a>
                                    </p>
                                </div>
                            </div>
                        </div>




                         <div id="uniteller"
                             style="display:{{ 'requisite-services' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row" 
                             style="display:{{ 'visa' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row__col ohf-col-label">
                                <div class="ohf-col-label__number">2</div>
                                <div class="ohf-col-label__notice">
                                    <div class="ohf-col-label__notice-title">Кто наш благотворитель?</div>
                                    <p class="ohf-col-label__notice-subtitle">Мы хотим знать кого благодарить :)</p>
                                </div>
                            </div>
                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row">
                                    <div class="form-group ohf-col-input__row-item">
                                        <input id='name' class="input-rounded input-fullwidth" type="text" name="name"
                                               placeholder="Введите ваше имя*" value="{{ form.name }}" onclick="hideError('firstNameError');">
                                        <div id="firstNameError" class="form-error-tooltip" style="display: none; position: relative; left: 0; top: -1px; width: 265px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Введите имя</p>
                                        </div>
                                    </div>
                                    <div class="form-group ohf-col-input__row-item ohf-col-input__row-item__notfirst">
                                        <input id='lastName' class="input-rounded input-fullwidth" type="text" name="surname"
                                               placeholder="Введите вашу фамилию" value="{{ form.surname }}">
                                    </div>
                                </div>
                                <div class="ohf-col-input__row">
                                    <div class="form-group ohf-col-input__row-item">
                                        <input id='phone' class="input-rounded input-fullwidth input-type-phone" type="text" name="phone"
                                               placeholder="Введите номер телефона*" data-type="phone"
                                               value="{{ form.phone }}" onclick="hideError('phoneError');checkPhone();" onkeyup="checkPhone();">
                                        <div id="phoneError" class="form-error-tooltip" style="display: none; position: relative; left: 0; top: -1px; width: 265px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Введите правильный номер</p>
                                        </div>
                                    </div>
                                    <div class="form-group ohf-col-input__row-item ohf-col-input__row-item__notfirst">
                                        <input id='email' class="input-rounded input-fullwidth" type="email" name="email"
                                               placeholder="Введите E-mail*" value="{{ form.email }}" onclick="hideError('emailError');">
                                        <div id="emailError" class="form-error-tooltip" style="display: none; width: 265px; position: relative; left: 0; top: -1px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Введите правильный email</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><div id="uniteller"
                             style="display:{{ 'requisite-services' == form['payment-type'] ? 'flex' : 'none' }}">
                        <div class="ohf-row " >
                            <div class="ohf-row__col ohf-col-label">
                                <div class="ohf-col-label__number">3</div>
                                <div class="ohf-col-label__notice">
                                    <div class="ohf-col-label__notice-title">Сумма пожертвования</div>
                                    <p class="ohf-col-label__notice-subtitle">Выберите тип оплаты</p>
                                </div>

                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row" style='flex-direction:column'>
                                    <div class="donate-sum-fieldgroup toggle-fieldgroup" style="overflow: hidden">
                                        TEST
                                    </div>
                                    
                                </div>


                            </div>
                        </div>

<!--
<div class="ohf-row " style="display:{{ 'eq' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row__col ohf-col-label">
                                <div class="ohf-col-label__number">3</div>
                                <div class="ohf-col-label__notice">
                                    <div class="ohf-col-label__notice-title">Сумма пожертвования</div>
                                    <p class="ohf-col-label__notice-subtitle">Выберите тип оплаты</p>
                                </div>
                            </div>
                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row" style='flex-direction:column'>
                                    <div class="donate-sum-fieldgroup toggle-fieldgroup" style="overflow: hidden">
                                        <label class="donate-sum-fieldgroup__label toggle-field">
                                            <input class="toggle-field__input" type="radio" name="radioSum" value="300" checked>
                                            <span class="toggle-field__text">300 Р</span>
                                        </label>
                                        <label class="donate-sum-fieldgroup__label toggle-field">
                                            <input class="toggle-field__input" type="radio" name="radioSum" value="500">
                                            <span class="toggle-field__text">500 Р</span>
                                        </label>
                                        <label class="donate-sum-fieldgroup__label toggle-field">
                                            <input class="toggle-field__input" type="radio" name="radioSum" value="700">
                                            <span class="toggle-field__text">700 Р</span>
                                        </label>
                                        <label class="donate-sum-fieldgroup__label">
                                            <input class="donate-text-field"
                                                   type="number"
                                                   name="textSum"
                                                   min="50"
                                                   max="1000000"
                                                   maxlength="7"
                                                   minlength="2"
                                                   value=""
                                                   placeholder="Другая сумма">
                                            <input type="hidden"
                                                   name="sum"
                                                   min="50"
                                                   max="1000000"
                                                   maxlength="7"
                                                   minlength="2"
                                                   value="{{ form.sum ?: 0 }}">
                                        </label>
                                    </div>
                                    
                                </div>
                                
                            </div>
                        </div>
                        {% if not app.user %}
                            <div class="ohf-row"
                                 style="display:{{ 'eq' == form['payment-type'] ? 'flex' : 'none' }}">
                                <div class="ohf-row__col ohf-col-label">
                                    <div class="ohf-col-label__number">4</div>
                                    <div class="ohf-col-label__notice">
                                        <div class="ohf-col-label__notice-title">Кто вас пригласил?</div>
                                        <p class="ohf-col-label__notice-subtitle">Не обязательно</p>
                                    </div>
                                </div>
                                <div class="ohf-row__col ohf-col-input">
                                    <div class="ohf-col-input__row">
                                        <input class="input-rounded input-fullwidth" type="text" name="ref-code"
                                               placeholder="Введите код приглашения" value="{{ form['ref-code'] }}"
                                                {% if not (form['ref-code'] is empty) %} disabled {% endif %}>
                                    </div>
                                </div>
                            </div>
                        {% endif %}
                        <div class="ohf-row"
                             style="display:{{ 'eq' == form['payment-type'] ? 'flex' : 'none' }}">
                            <div class="ohf-row__col ohf-col-label"></div>
                            <div class="ohf-row__col ohf-col-input">
                                <div class="ohf-col-input__row ohf-col-input__row--start">
                                    <div class="input-checkbox payment-count-field">
                                        <label class="input-checkbox__label">
                                            <input class="input-checkbox__input"
                                                   type="checkbox"
                                                   id="checkboxInput"
                                                   name="agree"
                                                   value="true"
                                                   onclick="hideError('checkboxError');"
                                                   checked>
                                            <span class="input-checkbox__checkmark"></span>
                                            <span class="input-checkbox__label">Я согласен с&nbsp;
                                                <a class="link--purple" href="/docs/Public_offer.pdf" target="_blank">договором оферты</a>
                                            </span>
                                        </label>
                                        <div id="checkboxError" class="form-error-tooltip" style="display: none; position: relative; left: 0; top: -1px; width: 300px">
                                            <div class="form-error-tooltip__arrow"></div>
                                            <p class="form-error-tooltip__notice">Необходимо согласие</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="ohf-col-input__row ohf-col-input__row--start ohf-col-buttons">
                                    <button class="btn btn-dark btn--big online-help-form__submit js-online-help-button-submit" type="submit" onclick="ym(53180137, 'reachGoal', 'pozhertvovat'); return validate_donate();">
                                        Пожертвовать
                                    </button>
                                    <div class="donate-form-payment-systems__cards-onDonatPage" style="width: 300px; margin: 0;">
                                        <div>Оплата с карт</div>
                                        <div id="paymentByVisa" class="toggle-field__text" style="opacity: 0.7; border-right: none; padding-left: 0; text-align: center; font-size: 1.2rem; color: #707070 !important; text-transform: none;">
                                            <img class="toggle-field__text-image" src="http://test.pomogitedetyam.ru/Apple_Pay.png" width="50"><img class="toggle-field__text-image" src="http://test.pomogitedetyam.ru/Google_Pay.png" width="50"><img class="toggle-field__text-image" src="/images/mastercard.png" width="30"><img class="toggle-field__text-image" src="/images/visa.png" width="40">
                                        </div>
                                    </div>

                                    {# <span class="online-help-form__or">или</span>
                                    <button class="btn btn-black btn--big btn-withicon online-help-form__applepay js-online-help-button-applepay"
                                        type="button">
                                        <span class="icon icon-apple"></span> <span>Оплатить</span>
                                    </button> #}
                                </div>
                                <div
                                        class="donate-form-payment-systems__notice online-help-form-payment-systems__notice">
                                    <div class="icon-wrapper"><span class="icon icon-lock"></span></div>
                                    <span style="padding-top:5px">Все сделки защищены SSL, PSI DSS, <br>
                                        Персональные данные конфиденциальны.
                                    </span>
                                </div>
                            </div>
                        </div>



-->








</div> 























                    </form>
                </div>
            </div>
        </section>
        <section class="section help-notice" id="section-help-notice">
            <div class="container container--small">
                <div class="help-notice-description">
                    <div class="help-notice-description-col help-notice-description-col-left">
                        <p class="help-notice-description-text">
                            <b>Уважаемый благотворитель!</b><br>
                            Вы можете произвести пожертвование с помощью предложенных методов оплат
                            через платежный сервис компании CloudPayments. После подтверждения Вы
                            будете перенаправлены на защищенную платежную страницу CloudPayments,
                            где необходимо будет ввести данные для оплаты. После успешной оплаты
                            на указанную в форме оплаты электронную почту будет направлен электронный
                            чек с информацией об успешном совершении пожертвования.
                            <br><br>
                            <b>Гарантии безопасности</b><br>
                            Безопасность процессинга CloudPayments подтверждена сертификатом стандарта
                            безопасности данных индустрии платежных карт PCI DSS. Надежность сервиса
                            обеспечивается интеллектуальной системой мониторинга мошеннических операций,
                            а также применением 3D Secure - современной технологией безопасности
                            интернет-платежей. Данные Вашей карты вводятся на специальной защищенной
                            платежной странице.
                            <br><br>
                            Передача информации в процессинговую компанию CloudPayments происходит
                            с применением технологии шифрования TLS. Дальнейшая передача информации
                            осуществляется по закрытым банковским каналам, имеющим наивысший уровень
                            надежности. CloudPayments не передает данные Вашей карты магазину и иным
                            третьим лицам!
                            <br>
                            Если Ваша карта поддерживает технологию 3D Secure, для осуществления
                            платежа, Вам необходимо будет пройти дополнительную проверку пользователя
                            в банке-эмитенте (банк, который выпустил Вашу карту). Для этого Вы будете
                            направлены на страницу банка, выдавшего карту. Вид проверки зависит от
                            банка. Как правило, это дополнительный пароль, который отправляется
                            в SMS, карта переменных кодов, либо другие способы.
                        </p>
                    </div>
                    <div class="help-notice-description-col help-notice-description-col-right">
                        <p class="help-notice-description-text">
                            <b>Если у Вас возникли вопросы</b> по совершенному платежу, <br>
                            Вы можете обратиться в службу технической поддержки процессингового центра
                            CloudPayments:
                            <br><br><b>support@cloudpayments.ru</b>
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <script>
            function ready() {
                var foopicker = new FooPicker({
                    id: 'inputDate',
                    dateFormat: 'dd.MM.yyyy',
                });
            }
            document.addEventListener("DOMContentLoaded", ready);
        </script>
    </main>
    <script type="text/javascript">
        const form = document.forms[0],
            sum = form.elements['sum'],
            radioSums = Array.from(form.elements['radioSum']),
            textSum = form.elements['textSum'],
            formBlocks = Array.from(document.querySelectorAll('.showVISA')),
            requisites = document.getElementById('requisitesContent'),
            smsBlock = document.querySelector('.showSMS'),
            sms = document.getElementById('smsContent'),
            uniteller = document.getElementById('uniteller'),
            setPaymentMethod = e => {
                if ('visa' === e) {
                    requisites.style.display = 'none';
                    sms.style.display = 'none';
                    smsBlock.style.display = 'none';
                    uniteller.style.display = 'none';

                    for (const el of formBlocks) {
                        el.style.display = 'flex';
                    }
                }
                else if ('sms' === e) {
                    requisites.style.display = 'none';
                    sms.style.display = 'block';
                    smsBlock.style.display = 'flex';
                    uniteller.style.display = 'none';

                    for (const el of formBlocks) {
                        el.style.display = 'none';
                    }
                }
                else if ('eq' === e) {
                    requisites.style.display = 'none';
                    sms.style.display = 'none';
                    smsBlock.style.display = 'none';
                    uniteller.style.display = 'flex';

                    for (const el of formBlocks) {
                        el.style.display = 'none';
                    }
                }
                else {
                    requisites.style.display = 'block';
                    sms.style.display = 'none';
                    smsBlock.style.display = 'none';
                    uniteller.style.display = 'none';

                    for (const el of formBlocks) {
                        el.style.display = 'none';
                    }
                }
            },
            radioSumChangeHandler = e => {
                textSum.classList.remove('checked');
                textSum.value = '';
                sum.value = e.currentTarget.value;

            },
            textSumHandler = e => {
                radioSums.forEach(el => el.checked = false);
                e.currentTarget.classList.add('checked');
                sum.value = e.currentTarget.value;
            },
            radioHandler = e => setPaymentMethod(e.target.value);

        setPaymentMethod('{{ form['payment-type'] }}');

        switch (0 | sum.value) {
            case 300: radioSums[0].checked = true; break;
            case 500: radioSums[1].checked = true; break;
            case 700: radioSums[2].checked = true; break;
            default:
                textSum.classList.add('checked');
                textSum.value = sum.value;
        }

        textSum.addEventListener('change', textSumHandler);
        textSum.addEventListener('click', textSumHandler);

        for (const el of radioSums) {
            el.addEventListener('change', radioSumChangeHandler);
        }

        for (const el of Array.from(form.elements['payment-type'])) {
            el.addEventListener('change', radioHandler);
        }
    </script>
{% endblock %}
