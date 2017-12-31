document.addEventListener('DOMContentLoaded', function(){
    var contact_form=document.getElementById("contact_form");

    contact_form.addEventListener('submit', function(event){
        var isValid = validate_function();
        if (!isValid) {
            // this STOPS the event to proceed with it's revelant action
            // like it normally would (in this case that being a form submit)
            event.preventDefault();
        }
        // returning false doesn't really work across all browsers
        return isValid;
    });

    function validate_function(){

        var name=contact_form.querySelector('input[name="name"]');
        var phone=contact_form.querySelector('input[name="phone"]');
        var email=contact_form.querySelector('input[name="email"]');
        var birthday=contact_form.querySelector('input[name="birthday"]');

        var vali_name=validate_name(name);
        var vali_phone=validate_phone(phone);
        var vali_email=validate_email(email);
        var vali_birthday=validate_birthday(birthday);

        if (vali_name && vali_phone && vali_email && vali_birthday){
            return true;
        } else {
            return false;
        }
        
    }

    function validate_name(name){
        if (name.value.length>0){
            return true;
        }
        document.getElementById("val_name").innerHTML="Please enter name";
        return false;
    }

    function validate_phone(phone){
        var pattern=/^[789]\d{9}$/;
        if (pattern.test(phone.value)){
            return true;
        }
        document.getElementById("val_phone").innerHTML="Please enter valid 10-digit phone number (starting with 7,8 or 9)";
        return false;
    }

    function validate_email(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (email.value.length > 0 && !re.test(email.value)){
            document.getElementById("val_email").innerHTML="Please enter valid email";
            return false;
        } else {
            return true;
        }
        
    }

    function validate_birthday(birthday){
        if (!birthday.value) return true;

        // should never write like this. Issues with x-browser compat.
        // instead use a date parsing lib or implement manually
        // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/parse
        var parsedDate = new Date(birthday.value);
        if (parsedDate === "Invalid Date" || Date.parse(birthday.value) === NaN) {
            return false;
        } else {
            return true;
        }
    }
});