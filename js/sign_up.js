document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("signupForm").addEventListener("submit", function (event) {
        event.preventDefault();

        let isValid = true;

        // Validate full name
        const fullname = document.getElementById("fullname").value.trim();
        const fullnameError = document.getElementById("fullnameError");
        if (fullname.length < 3) {
            fullnameError.textContent = "Full name must be at least 3 characters.";
            isValid = false;
        } else {
            fullnameError.textContent = "";
        }

        // Validate email
        const email = document.getElementById("email").value.trim();
        const emailError = document.getElementById("emailError");
        const emailRegex = /^\S+@\S+\.\S+$/;
        if (!emailRegex.test(email)) {
            emailError.textContent = "Please enter a valid email address.";
            isValid = false;
        } else {
            emailError.textContent = "";
        }

        // Validate password
        const password = document.getElementById("password").value.trim();
        const passwordError = document.getElementById("passwordError");
        if (password.length < 8) {
            passwordError.textContent = "Password must be at least 8 characters.";
            isValid = false;
        } else {
            passwordError.textContent = "";
        }

        // Validate confirm password
        const confirmPassword = document.getElementById("confirmpassword").value.trim();
        const confirmPasswordError = document.getElementById("confirmpasswordError");
        if (confirmPassword !== password) {
            confirmPasswordError.textContent = "Passwords do not match.";
            isValid = false;
        } else {
            confirmPasswordError.textContent = "";
        }

        if (isValid) {
            this.submit();
        }
    });
});