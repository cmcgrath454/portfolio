function submitForm(btn) {
    btn.disabled = true;
    grecaptcha.ready(function () {
        grecaptcha.execute("6Lc-QdIkAAAAANrnQ84mJJs5Cp0hqXfsg8pI443U").then(function (token) {
            document.getElementById("token-response").value = token;
            document.getElementById('contact-form').submit();
        });
    });
}
