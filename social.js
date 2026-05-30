function checkUser() {
    let username = document.getElementById("username").value.trim();
    if (username === "") {
        alert("Please enter a username!");
        return;
    }

    fetch("detect_fake.php?username=" + username)
        .then(response => response.json())
        .then(data => {
            let resultDiv = document.getElementById("result");
            resultDiv.innerHTML = "";

            if (data.error) {
                resultDiv.innerHTML = `<p style="color: red;">❌ ${data.error}</p>`;
            } else {
                let statusMessage = data.is_fake ? 
                    `<p style="color: red; font-weight: bold;">🚨 This account is likely fake! 🚨</p>` : 
                    `<p style="color: green; font-weight: bold;">✅ This account appears genuine. ✅</p>`;

                resultDiv.innerHTML = `
                    <img class="user-photo" src="${data.photo_link}" alt="User Photo">
                    <h3>${data.username}</h3>
                    <p><strong>Followers:</strong> ${data.followers}</p>
                    <p><strong>Following:</strong> ${data.following}</p>
                    <p><strong>Profile Info:</strong> ${data.profile_info || "No information provided."}</p>
                    ${statusMessage}
                `;
            }
        })
        .catch(error => console.error("Error fetching data:", error));
}
