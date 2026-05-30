document.getElementById('registerForm').addEventListener('submit', function (e) {
    const username = document.querySelector('input[name="userId"]').value.trim();
    const email = document.querySelector('input[name="email"]').value.trim();
    const contact = document.querySelector('input[name="contact"]').value.trim();
    const password = document.querySelector('input[name="password"]').value.trim();

    if (!username || !email || !contact || !password) {
        alert("All fields are required.");
        e.preventDefault();
        return;
    }

    if (!validateEmail(email)) {
        alert("Please enter a valid email address.");
        e.preventDefault();
        return;
    }

    if (!validateContact(contact)) {
        alert("Contact number must be 10 digits.");
        e.preventDefault();
        return;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters long.");
        e.preventDefault();
        return;
    }
});

// Helper Functions
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}

function validateContact(contact) {
    const re = /^\d{10}$/;
    return re.test(contact);
}
