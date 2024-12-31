document.addEventListener("DOMContentLoaded", function () {
  document.querySelector("form").addEventListener("submit", function (event) {
    event.preventDefault();

    let isValid = true;

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

    if (isValid) {
      this.submit();
    }
  });
});
