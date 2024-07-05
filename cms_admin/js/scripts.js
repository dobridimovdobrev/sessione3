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

// Function to update the content counter
function updateContentLength() {
    const content = $('#summernote').summernote('code');

    // trim spaces
    const plainText = $(content).text().trim();

    // Count the characters 
    const contentLength = plainText.length;

    // Update the content counter
    const contentLengthCounter = document.getElementById('contentLength');
    if (contentLengthCounter) { // Ensure element exists
        contentLengthCounter.textContent = `Content length: ${contentLength} characters`;
    }
}

/* Styles and functionality for summernote editor from their documentationAA */
$(document).ready(function () {
    $('#summernote').summernote({
        height: 500, // set editor height
        callbacks: {
            onInit: function () {
                // Initial update of content length counter
                updateContentLength();
            },
            onKeyup: function () {
                // Update content length on key up
                updateContentLength();
            },
            onChange: function () {
                // Update content length on change
                updateContentLength();
            }
        },
        minHeight: null, // set minimum height of editor
        maxHeight: null, // set maximum height of editor
        focus: true,
        toolbar: [
            ['style', ['style']], // Add this line for heading options
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']],
            ['misc', ['undo', 'redo']]
        ]
    });
});

// Helper function to validate email format
function validateEmail(email) {
    var re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return re.test(String(email).toLowerCase());
}

/* Validate article form */

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('articleForm') !== null) {
        document.getElementById('articleForm').addEventListener('submit', function (event) {
            /* clean inputs from previous errors */
            document.getElementById('articleTitleError').textContent = "";
            document.getElementById('articleDescriptionError').textContent = "";
            document.getElementById('articleContentError').textContent = "";
            document.getElementById('articleAuthorError').textContent = "";
            document.getElementById('articleTagsError').textContent = "";
            document.getElementById('articlePublished_atError').textContent = "";
            document.getElementById('articleCat_idError').textContent = "";
            document.getElementById('articleImageError').textContent = "";
            document.getElementById('articleStatusError').textContent = "";

            /* Initialize variables */
            var title = document.getElementById('title').value.trim();
            var description = document.getElementById('description').value.trim();
            var content = document.getElementById('summernote').value.trim();
            var author = document.getElementById('author').value.trim();
            var tags = document.getElementById('tags').value.trim();
            var published_at = document.getElementById('published_at').value.trim();
            var cat_id = document.getElementById('cat_id').value.trim();
            var imageurl = document.getElementById('imageurl').value.trim();
            var status = document.getElementById('status').value.trim();

            var validation = true;

            /* Validate title */
            if (title === "") {
                document.getElementById('articleTitleError').textContent = "AATitle is required";
                validation = false;
            }
            /* Validate description */
            if (description === "") {
                document.getElementById('articleDescriptionError').textContent = "ADescription is required";
                validation = false;
            }
            /* Validate content */
            if (content === "") {
                document.getElementById('articleContentError').textContent = "AContent is required";
                validation = false;
            }
            /* Validate author */
            if (author === "") {
                document.getElementById('articleAuthorError').textContent = "AAuthor is required";
                validation = false;
            }
            /* Validate tags */
            if (tags === "") {
                document.getElementById('articleTagsError').textContent = "AATags are required";
                validation = false;
            }
            /* Validate published date */
            if (published_at === "") {
                document.getElementById('articlePublished_atError').textContent = "AADate is required";
                validation = false;
            }
            /* Validate category */
            if (cat_id === "" || cat_id === "Select option") {
                document.getElementById('articleCat_idError').textContent = "AACategory is required";
                validation = false;
            }
            /* Validate image */
            if (imageurl === "") {
                document.getElementById('articleImageError').textContent = "AAImage is required";
                validation = false;
            }
            /* Validate status */
            if (status === "" || status === "Select option") {
                document.getElementById('articleStatusError').textContent = "AAStatus is required";
                validation = false;
            }
            /* Prevent submit if not validation */
            if (!validation) {
                event.preventDefault();
            }
        });
    }
});



/* Validate service form */
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
            var imageurl = document.getElementById('imageurl').value.trim();
            var published_at = document.getElementById('published_at').value.trim();


            var validation = true;
            /* Validate title */
            if (title === "") {
                document.getElementById('serviceTitleError').textContent = "SsTitle is required";
                validation = false;
            }
            /* Validate description */
            if (description === "") {
                document.getElementById('serviceDescriptionError').textContent = "SsDescription is required";
                validation = false;
            }
            /* Validate content */
            if (content === "" || content === "<p><br></p>") {  // This check is needed because sometimes Summernote returns an empty paragraph) {
                document.getElementById('serviceContentError').textContent = "SsContent is required";
                validation = false;
            }
            /* Validate tags */
            if (tags === "") {
                document.getElementById('serviceTagsError').textContent = "SsTags are required";
                validation = false;
            }
            /* Validate image */
            if (imageurl === 0) {
                document.getElementById('serviceImageError').textContent = "SsImage is required";
                validation = false;
            }
            /* Validate published date */
            if (published_at === "") {
                document.getElementById('servicePublished_atError').textContent = "SsDate is required";
                validation = false;
            }
            /* Prevent submit if not validation */
            if (!validation) {
                event.preventDefault();
            }
        });
    }
});


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