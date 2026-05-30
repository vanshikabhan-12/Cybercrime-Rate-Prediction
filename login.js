document.getElementById('loginForm').addEventListener('submit', function (e) {
    const username = document.querySelector('input[name="userId"]').value.trim();
    const password = document.querySelector('input[name="password"]').value.trim();

    if (!username || !password) {
        alert("Both fields are required.");
        e.preventDefault();
        return;
    }
});
