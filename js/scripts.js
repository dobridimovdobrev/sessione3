// Helper function to validate email format
function validateEmail(email) {
  var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
  return re.test(String(email).toLowerCase());
}

// Event listener for contact form submission
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('contactForm') !== null) {
    document.getElementById('contactForm').addEventListener('submit', function (event) {
      // Clear previous errors
      document.getElementById('contactSubjectError').textContent = '';
      document.getElementById('contactNameError').textContent = '';
      document.getElementById('contactEmailError').textContent = '';
      document.getElementById('contactMessageError').textContent = '';

      // Get input values
      var subject = document.getElementById('contactSubject').value.trim();
      var name = document.getElementById('contactName').value.trim();
      var email = document.getElementById('contactEmail').value.trim();
      var message = document.getElementById('contactMessage').value.trim();

      var validation = true;

      // Validate subject
      if (subject === '') {
        document.getElementById('contactSubjectError').textContent = 'Subject is required';
        validation = false;
      }

      // Validate name
      if (name === '') {
        document.getElementById('contactNameError').textContent = 'Name is required';
        validation = false;
      }

      // Validate email
      if (email === '') {
        document.getElementById('contactEmailError').textContent = 'Email is required';
        validation = false;
      } else if (!validateEmail(email)) {
        document.getElementById('contactEmailError').textContent = 'Invalid email format';
        validation = false;
      }

      // Validate message
      if (message === '') {
        document.getElementById('contactMessageError').textContent = 'Message is required';
        validation = false;
      }

      // Prevent form submission if validation fails
      if (!validation) {
        event.preventDefault();
      }
    });
  }
});

// Newsletter form validation function
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('newsletterForm') !== null) {
    document.getElementById('newsletterForm').addEventListener('submit', function (event) {
      // Clear previous errors in the inputs
      document.getElementById('newsletterNameError').textContent = '';
      document.getElementById('newsletterEmailError').textContent = '';
      document.getElementById('newsletterOriginError').textContent = '';

      // Get input values from the user
      var newsletterName = document.getElementById('newsletterName').value.trim();
      var newsletterEmail = document.getElementById('newsletterEmail').value.trim();
      var newsletterOrigin = document.getElementById('newsletterOrigin').value;

      var validation = true; // Assume validation passes initially

      // Validate name
      if (newsletterName === '') {
        document.getElementById('newsletterNameError').textContent = 'Name is required';
        validation = false;
      }

      // Validate email
      if (newsletterEmail === '') {
        document.getElementById('newsletterEmailError').textContent = 'Email is required';
        validation = false;
      } else if (!validateEmail(newsletterEmail)) {
        document.getElementById('newsletterEmailError').textContent = 'Invalid email format';
        validation = false;
      }

      // Validate origin
      if (newsletterOrigin === '') {
        document.getElementById('newsletterOriginError').textContent = 'Please select an option';
        validation = false;
      }
      /* Prevent submission if no validation */
      if (!validation) {
        event.preventDefault();
      }
    });
  }
});


/* Validate Registration form */
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('registerForm') !== null) {
    document.getElementById('registerForm').addEventListener('submit', function (event) {

      /* Clean previous errors */
      document.getElementById('registrationUsernameError').textContent = "";
      document.getElementById('registrationFirstNameError').textContent = "";
      document.getElementById('registrationLastNameError').textContent = "";
      document.getElementById('registrationEmailError').textContent = "";
      document.getElementById('registrationPasswordError').textContent = "";
      document.getElementById('registrationRepeatPasswordError').textContent = "";
      document.getElementById('registrationTermsError').textContent = "";
      /* Get value from user */

      var userName = document.getElementById('username').value;
      var firstName = document.getElementById('first_name').value;
      var lastName = document.getElementById('last_name').value;
      var email = document.getElementById('email').value;
      var password = document.getElementById('password').value;
      var repeatPassword = document.getElementById('repeat_password').value;
      var terms = document.getElementById('terms');
      var validation = true;
      /* Validate username */
      if (userName.trim() === '') {
        document.getElementById('registrationUsernameError').textContent = "Username is required";
        validation = false;
      }
      /* Validate firstname */
      if (firstName.trim() === '') {
        document.getElementById('registrationFirstNameError').textContent = "First name is required";
        validation = false;
      }
      /* Validate lastname */
      if (lastName.trim() === '') {
        document.getElementById('registrationLastNameError').textContent = "Last name is required";
        validation = false;
      }
      /* Validate email */
      if (email.trim() === '') {
        document.getElementById('registrationEmailError').textContent = "Email is required";
        validation = false;
        /* Validate email format */
      } else if (!validateEmail(email)) {
        document.getElementById('registrationEmailError').textContent = 'Invalid email format';
        validation = false;
      }
      /* Validate password */
      if (password.trim() === '') {
        document.getElementById('registrationPasswordError').textContent = "Password is required";
        validation = false;
        /* Validate length password */
      } else if (password.length < 8) {
        document.getElementById('registrationPasswordError').textContent = "Password must be at least 8 characters";
        validation = false;
      }
      /* Validate repeat password */
      if (repeatPassword.trim() === '') {
        document.getElementById('registrationRepeatPasswordError').textContent = "Please repeat password";
        validation = false;
        /* Validate if password match with repeat password */
      } else if (password !== repeatPassword) {
        document.getElementById('registrationRepeatPasswordError').textContent = "Passwords do not match";
        validation = false;
      }
      /* Validate checked terms and conditions checkbox */
      if(!terms.checked){
        document.getElementById('registrationTermsError').textContent = "You must agree to the terms and conditions.";
        validation = false;
      }
      /* Prevent submission if no validation */
      if (!validation) {
        event.preventDefault();
      }

    });
  }
});

/* Login validation */

document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('loginForm') !== null) {
    document.getElementById('loginForm').addEventListener('submit', function (event) {

      /* Clean previous errors */
      document.getElementById('loginUsernameError').textContent = "";
      document.getElementById('loginPasswordError').textContent = "";

      /* Get values from user */
      var username = document.getElementById('username').value;
      var password = document.getElementById('password').value;

      var validation = true;
      /* Validate username */
      if (username.trim() === '') {
        document.getElementById('loginUsernameError').textContent = "Username is required;"
        validation = false;
      }
      /* Validate Password */
      if (password.trim() === '') {
        document.getElementById('loginPasswordError').textContent = "Password is required";
        validation = false;
      }
      /* Prevent submission if no validation */
      if (!validation) {
        event.preventDefault();
      }

    });
  }
});
