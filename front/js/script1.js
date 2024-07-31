
const navLinks = document.querySelectorAll('.nav-link');
const contentDivs = document.querySelectorAll('.content');
const sidebar = document.querySelector('.sidebar');


navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();

        // Remove active class from all links
        navLinks.forEach(l => l.classList.remove('active'));

        // Add active class to clicked link
        this.classList.add('active');

        // Hide all content divs
        contentDivs.forEach(div => div.style.display = 'none');

        // Show the corresponding content div
        const targetId = this.getAttribute('data-bs-target').substring(1);
        document.getElementById(targetId).style.display = 'block';
    });
});

toggleButton.addEventListener('click', function () {
    sidebar.classList.toggle('collapsed');
    const icon = toggleButton.querySelector('i');
    icon.classList.toggle('bi-chevron-left');
    icon.classList.toggle('bi-chevron-right');
});

function checkFormValidity() {
    const formElements = form.querySelectorAll('input[required], select[required]');
    const allFilled = Array.from(formElements).every(element => element.value.trim() !== '');
    submitBtn.disabled = !allFilled || confirm_password.value !== password.value;
}


confirm_password.addEventListener('input', (e) => {

    confirm_password.addEventListener('input', () => {
        if (confirm_password.value !== password.value) {
            password_message.textContent = "* Confirm password and password don't match";
            confirm_password.classList.add('border-danger');
            confirm_password.classList.remove('border-success');
            password.classList.add('border-danger');
            password.classList.remove('border-success');
            submitBtn.disabled = true; // Disable submit button if passwords don't match
        } else {
            password_message.textContent = "";
            confirm_password.classList.add('border-success');
            confirm_password.classList.remove('border-danger');
            password.classList.add('border-success');
            password.classList.remove('border-danger');
            checkFormValidity(); // Check form validity when passwords match
        }
    })
});