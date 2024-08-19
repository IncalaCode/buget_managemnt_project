var sp = location.pathname.split("/")

if (sp.includes('g_manager.php')) {
    document.getElementById('budgetDuration').min = new Date().toISOString().split('T')[0];
}


function loadDocx(url) {
    fetch(url)
        .then(response => response.arrayBuffer())
        .then(arrayBuffer => mammoth.convertToHtml({ arrayBuffer: arrayBuffer }))
        .then(result => {
            document.getElementById('viewer').innerHTML = result.value;
        })
        .catch(err => console.error(err));
}

function createButtons() {
    const buttonContainer = document.getElementById('buttonContainer');

    proposals.forEach(proposal => {
        const buttonDiv = document.createElement('div');
        buttonDiv.className = 'row-button';

        const button = document.createElement('button');
        button.className = 'btn btn-primary btn-block text-left';
        button.innerHTML = `Proposal Name: ${proposal.name}<br>Sender: ${proposal.sender}`;
        button.onclick = () => loadDocx(proposal.url);

        buttonDiv.appendChild(button);
        buttonContainer.appendChild(buttonDiv);
    });
}





const navLinks = document.querySelectorAll('.nav-link');
const contentDivs = document.querySelectorAll('.content');
const sidebar = document.querySelector('.sidebar');
const toggleButton = document.getElementById('toggleButton')

// Function to navigate to the target slide
function navigateToSlide(targetId) {
    // Check if the target element exists
    const targetElement = document.getElementById(targetId);
    if (!targetElement) {
        console.error(`Element with ID '${targetId}' not found.`);
        return;
    }

    // Remove 'active' class from all links and hide all content divs
    navLinks.forEach(link => link.classList.remove('active'));
    contentDivs.forEach(div => {
        div.style.display = 'none';
        div.classList.remove('active'); // Remove 'active' class from all content divs
    });

    // Show the corresponding content div and add 'active' class
    targetElement.style.display = 'block';
    targetElement.classList.add('active');

    // Add 'active' class to the link corresponding to the targetId
    navLinks.forEach(link => {
        if (link.getAttribute('data-bs-target') && link.getAttribute('data-bs-target').substring(1) === targetId) {
            link.classList.add('active');
        }
    });
}

// Add click event listeners to the navigation links
navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('data-bs-target').substring(1);
        navigateToSlide(targetId);
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
}) || 0;


