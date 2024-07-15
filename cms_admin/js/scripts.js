// Show the modal and set up 

function showDeleteModal(entityId, deleteUrl) {
    var modal = document.getElementById("deleteModal");
    var confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

    modal.style.display = "block";

    // Set up the confirm button 
    confirmDeleteBtn.onclick = function () {
        window.location.href = deleteUrl + "?delete=" + entityId;
    }

    // Get the button that closes the modal
    var span = document.querySelector('.close');
    span.onclick = function () {
        modal.style.display = "none";
    }

    // If user clicks on cancel button, close the modal
    document.getElementById("cancelBtn").onclick = function () {
        modal.style.display = "none";
    }

    //If the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

/* Description counter for article and service */
document.addEventListener('DOMContentLoaded', function () {
    // Function to update the description length counter
    function updateDescriptionCounter() {
        const descriptionInput = document.getElementById('description');
        const descriptionCounter = document.getElementById('descriptionCounter');
        if (descriptionInput && descriptionCounter) { // Ensure elements exist
            const maxChars = 180;
            const remainingChars = maxChars - descriptionInput.value.length;
            descriptionCounter.textContent = `${remainingChars} characters remaining`;
        }
    }

    /* Function to update tags length counter */
    function updateTagsCounter() {
        const tagsInput = document.getElementById('tags');
        const tagsCounter = document.getElementById('tagsCounter');
        if (tagsInput && tagsCounter) { // Ensure elements exist
            const maxChars = 84;
            const remainingChars = maxChars - tagsInput.value.length;
            tagsCounter.textContent = `${remainingChars} characters remaining`;
        }
    }

    // Initial update of counters
    updateDescriptionCounter();
    updateTagsCounter();

    // input event to the description input 
    const descriptionInput = document.getElementById('description');
    if (descriptionInput) { // Ensure element exists
        descriptionInput.addEventListener('input', updateDescriptionCounter);
    }

    // input event to the tags input 
    const tagsInput = document.getElementById('tags');
    if (tagsInput) { // Ensure element exists
        tagsInput.addEventListener('input', updateTagsCounter);
    }
});

// Helper function to validate email format
function validateEmail(email) {
    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return re.test(String(email).toLowerCase());
}

//summernote editor configuration
$('#summernote').summernote({

    tabsize: 2,
    height: 500,
    enterHtml: '<br>',
    dialogsInBody: true,
        callbacks: {
            onInit: function () {
                updateContentLength();
            },
            onKeyup: function () {
                updateContentLength();
            },
            onChange: function () {
                updateContentLength();
            },
            onPaste: function () {
                updateContentLength();
            }
        },
        clipboard: {
            matchVisual: false
        }
  });

/* Summernote editor sanitize and behavior adjustments */
function sanitizeHTML(html) {
    // Remove any <p> tags wrapping <h1>, <h2>, etc.
    html = html.replace(/<p>\s*(<h[1-6][^>]*>[^<]*<\/h[1-6]>)\s*<\/p>/g, '$1');

    // Ensure <h1>, <h2>, etc., are not within <p> tags
    html = html.replace(/<p>\s*(<h[1-6][^>]*>[^<]*<\/h[1-6]>)\s*<\/p>/g, '$1');

    // Handle specific heading classes
    html = html.replace(/<h1>/g, '<h1 class="primary-heading">');
    html = html.replace(/<h2>/g, '<h2 class="article-heading">');
    html = html.replace(/<h3>/g, '<h3 class="third-heading">');
    html = html.replace(/<h4>/g, '<h4 class="fourth-heading">');
    html = html.replace(/<h5>/g, '<h5 class="fifth-heading">');

    // Remove empty <p> tags
    html = html.replace(/<p><\/p>/g, '');

    // Remove any single </p> or <p> tags that are not closed
    html = html.replace(/<p>(\s|&nbsp;)*<\/p>/g, ''); // Remove empty <p> tags
    html = html.replace(/<p>\s*<\/p>/g, ''); // Remove empty <p> tags

    // Replace all <p> tags with <p class="paragraph">
    html = html.replace(/<p>/g, '<p class="paragraph">');

    return html;
}
 
// Function to update the content counter
function updateContentLength() {
    const content = $('#summernote').summernote('code');
    const plainText = $(content).text().trim();
    const contentLength = plainText.length;
    const contentLengthCounter = document.getElementById('contentLength');
    if (contentLengthCounter) {
        contentLengthCounter.textContent = `Content length: ${contentLength} characters`;
    }
}

/* Validate Article form */
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('articleForm') !== null) {
        document.getElementById('articleForm').addEventListener('submit', function (event) {
            // Clean inputs from previous errors
            document.getElementById('articleTitleError').textContent = "";
            document.getElementById('articleDescriptionError').textContent = "";
            document.getElementById('articleContentError').textContent = "";
            document.getElementById('articleAuthorError').textContent = "";
            document.getElementById('articleTagsError').textContent = "";
            document.getElementById('articlePublished_atError').textContent = "";
            document.getElementById('articleCat_idError').textContent = "";
            document.getElementById('articleImageError').textContent = "";
            document.getElementById('articleStatusError').textContent = "";

            // Initialize variables
            var title = document.getElementById('title').value.trim();
            var description = document.getElementById('description').value.trim();
            var content = $('#summernote').summernote('code').trim();
            var author = document.getElementById('author').value.trim();
            var tags = document.getElementById('tags').value.trim();
            var published_at = document.getElementById('published_at').value.trim();
            var cat_id = document.getElementById('cat_id').value.trim();
            var imageurl = document.getElementById('imageurl').files[0];
            var existingImage = document.getElementById('existingImage');
            var status = document.getElementById('status').value.trim();

            var validation = true;

            // Validate title
            if (title === "") {
                document.getElementById('articleTitleError').textContent = "Title is required";
                validation = false;
            }
            // Validate description
            if (description === "") {
                document.getElementById('articleDescriptionError').textContent = "Description is required";
                validation = false;
            }
            // Validate content
            if (content === "" || content === "<p><br></p>") { // This check is needed because sometimes summernote returns an empty paragraph
                document.getElementById('articleContentError').textContent = "Content is required";
                validation = false;
            }
            // Validate author
            if (author === "") {
                document.getElementById('articleAuthorError').textContent = "Author is required";
                validation = false;
            }
            // Validate tags
            if (tags === "") {
                document.getElementById('articleTagsError').textContent = "Tags are required";
                validation = false;
            }
            // Validate published date
            if (published_at === "") {
                document.getElementById('articlePublished_atError').textContent = "Date is required";
                validation = false;
            }
            // Validate category
            if (cat_id === "" || cat_id === "Select option") {
                document.getElementById('articleCat_idError').textContent = "Category is required";
                validation = false;
            }
            // Validate image
            if (imageurl === undefined && existingImage === null) {
                document.getElementById('articleImageError').textContent = "AAAImage is required";
                validation = false;
            } else if (imageurl !== undefined) {
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                var maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (!allowedTypes.includes(imageurl.type)) {
                    document.getElementById('articleImageError').textContent = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
                    validation = false;
                } else if (imageurl.size > maxSize) {
                    document.getElementById('articleImageError').textContent = "AAImage file size must be less than 10MB.";
                    validation = false;
                }
            }
            // Validate status
            if (status === "" || status === "Select option") {
                document.getElementById('articleStatusError').textContent = "Status is required";
                validation = false;
            }
            // Prevent submit if not valid
            if (!validation) {
                event.preventDefault();
                return;
            }

            // Sanitize the content before setting it to the textarea
            content = sanitizeHTML(content);
            document.getElementById('summernote').value = content;
        });
    }
});


/* Validate Service form */
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('serviceForm') !== null) {
        document.getElementById('serviceForm').addEventListener('submit', function (event) {
            /* clean inputs from previous errors */
            document.getElementById('serviceTitleError').textContent = "";
            document.getElementById('serviceDescriptionError').textContent = "";
            document.getElementById('serviceContentError').textContent = "";
            document.getElementById('serviceTagsError').textContent = "";
            document.getElementById('serviceImageError').textContent = "";
            document.getElementById('servicePublished_atError').textContent = "";

            /* Initialize variables */
            var title = document.getElementById('title').value.trim();
            var description = document.getElementById('description').value.trim();
            var content = document.getElementById('summernote').value.trim();
            var tags = document.getElementById('tags').value.trim();
            var imageurl = document.getElementById('imageurl').files[0];
            var published_at = document.getElementById('published_at').value.trim();
            var existingImage = document.getElementById('existingImage');

            var validation = true;
            /* Validate title */
            if (title === "") {
                document.getElementById('serviceTitleError').textContent = "AATitle is required";
                validation = false;
            }
            /* Validate description */
            if (description === "") {
                document.getElementById('serviceDescriptionError').textContent = "AADescription is required";
                validation = false;
            }
            /* Validate content */
            if (content === "" || content === "<p><br></p>") {  // This check is needed because sometimes Summernote returns an empty paragraph) 
                document.getElementById('serviceContentError').textContent = "AAContent is required";
                validation = false;
            }
            /* Validate tags */
            if (tags === "") {
                document.getElementById('serviceTagsError').textContent = "AATags are required";
                validation = false;
            }
            /* Validate image */
            if (imageurl === undefined && existingImage === null) {
                document.getElementById('serviceImageError').textContent = "AAImage is required";
                validation = false;
            } else if (imageurl !== undefined) {
                var allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                var maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if (!allowedTypes.includes(imageurl.type)) {
                    document.getElementById('serviceImageError').textContent = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
                    validation = false;
                } else if (imageurl.size > maxSize) {
                    document.getElementById('serviceImageError').textContent = "Image file size must be less than 10MB.";
                    validation = false;
                }
            }
            /* Validate published date */
            if (published_at === "") {
                document.getElementById('servicePublished_atError').textContent = "AADate is required";
                validation = false;
            }
            /* Prevent submit if not validation */
            if (!validation) {
                event.preventDefault();
                return;
            }

            // Sanitize the content before setting it to the textarea
            content = sanitizeHTML(content);
            document.getElementById('summernote').value = content;
        });
    }
});


/* User form */
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('userForm') !== null) {
        document.getElementById('userForm').addEventListener('submit', function (event) {
            // Clean previous error messages
            document.getElementById('usernameError').textContent = "";
            document.getElementById('firstNameError').textContent = "";
            document.getElementById('lastNameError').textContent = "";
            document.getElementById('emailError').textContent = "";
            document.getElementById('regDateError').textContent = "";
            document.getElementById('passwordError').textContent = "";
            document.getElementById('repeatPasswordError').textContent = "";

            // Get form values
            var username = document.getElementById('username').value.trim();
            var firstName = document.getElementById('first_name').value.trim();
            var lastName = document.getElementById('last_name').value.trim();
            var email = document.getElementById('email').value.trim();
            var date = document.getElementById('date').value.trim();
            var password = document.getElementById('password').value.trim();
            var repeatPassword = document.getElementById('repeat_password').value.trim();

            var validation = true;

            // Validate username
            if (username === '') {
                document.getElementById('usernameError').textContent = "ZZUsername is required";
                validation = false;
            }
            // Validate first name
            if (firstName === '') {
                document.getElementById('firstNameError').textContent = "ZZFirst name is required";
                validation = false;
            }
            // Validate last name
            if (lastName === '') {
                document.getElementById('lastNameError').textContent = "ZZLast name is required";
                validation = false;
            }
            // Validate email
            if (email === '') {
                document.getElementById('emailError').textContent = "ZZEmail is required";
                validation = false;
            } else if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = 'ZZInvalid email format';
                validation = false;
            }
            // Validate registration date
            if (date === '') {
                document.getElementById('regDateError').textContent = "ZZRegistration date is required";
                validation = false;
            }
            // Validate password when i create a new user
            if (document.getElementById('editUserId') === null) {
                if (password === '') {
                    document.getElementById('passwordError').textContent = "ZZPassword is required";
                    validation = false;
                    /* Validate length password */
                } else if (password.length < 8) {
                    document.getElementById('passwordError').textContent = "ZZPassword must be at least 8 characters";
                    validation = false;
                }
                /* Validate repeat password */
                if (repeatPassword === '') {
                    document.getElementById('repeatPasswordError').textContent = "ZZPlease repeat password";
                    validation = false;
                    /* Validate password match */
                } else if (password !== repeatPassword) {
                    document.getElementById('repeatPasswordError').textContent = "ZZPasswords do not match";
                    validation = false;
                }
            }

            // Prevent submission if validation fails
            if (!validation) {
                event.preventDefault();
            }
        });
    }
});

/* Validate profile form */
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('profileForm') !== null) {
        document.getElementById('profileForm').addEventListener('submit', function (event) {
            // Clean previous error messages
            document.getElementById('firstNameError').textContent = "";
            document.getElementById('lastNameError').textContent = "";
            document.getElementById('emailError').textContent = "";
            document.getElementById('passwordError').textContent = "";
            document.getElementById('repeatPasswordError').textContent = "";

            // Get form values
            var firstName = document.getElementById('first_name').value.trim();
            var lastName = document.getElementById('last_name').value.trim();
            var email = document.getElementById('email').value.trim();
            var password = document.getElementById('password').value.trim();
            var repeatPassword = document.getElementById('repeat_password').value.trim();

            var validation = true;

            // Validate first name
            if (firstName === '') {
                document.getElementById('firstNameError').textContent = "QQFirst name is required";
                validation = false;
            }
            // Validate last name
            if (lastName === '') {
                document.getElementById('lastNameError').textContent = "QQLast name is required";
                validation = false;
            }
            // Validate email
            if (email === '') {
                document.getElementById('emailError').textContent = "QQEmail is required";
                validation = false;
            } else if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = "QQInvalid email format";
                validation = false;
            }
            // Validate password
            if (password !== '') {
                if (password.length < 8) {
                    document.getElementById('passwordError').textContent = "QQPassword must be at least 8 characters";
                    validation = false;
                }
                if (password !== repeatPassword) {
                    document.getElementById('repeatPasswordError').textContent = "QQPasswords do not match";
                    validation = false;
                }
            }

            // Prevent submission if validation fails
            if (!validation) {
                event.preventDefault();
            }
        });
    }
});

/* Validate category form */
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('categoryForm') !== null) {
        document.getElementById('categoryForm').addEventListener('submit', function (event) {
            document.getElementById('categoryTitleError').textContent = "";
            var categoryTitle = document.getElementById('cat_title').value.trim();
            validation = true;
            /* Validate title */
            if (categoryTitle === "") {
                document.getElementById('categoryTitleError').textContent = "Insert category name !";
                validation = false;
            }
            /* Prevent submit if not validation */
            if (!validation) {
                event.preventDefault();
            }
        });
    }
});

/* Validate Update category form */
document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('updateCategoryForm') !== null) {
        document.getElementById('updateCategoryForm').addEventListener('submit', function (event) {
            document.getElementById('updateError').textContent = "";
            var categoryNewTitle = document.getElementById('new_title').value.trim();
            validation = true;
            /* Validate new title */
            if (categoryNewTitle === "") {
                document.getElementById('updateError').textContent = "Insert new category name !!!!";
                validation = false;
            }
            /* Prevent submit if not validation */
            if (!validation) {
                event.preventDefault();
            }
        });
    }

});