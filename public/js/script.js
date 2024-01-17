
function submitForm(event) {
    event.preventDefault();

    fetch('/insert', {
    method: 'POST',
    body: new FormData(document.forms.insertForm),
})
    .then(response => {
    if (response.ok) {
    // Reload the page after a successful response
    location.reload();
} else {
    // Handle error response
    return response.json();
}
})
    .then(data => handleResponse(data))
    .catch(error => console.error('Error:', error));
}

    function handleResponse(data) {
    const errorMessageElement = document.getElementById('errorMessage');

    if (data.validationMessage) {
    errorMessageElement.style.display = 'block';
    errorMessageElement.innerHTML = data.validationMessage;
} else {
    errorMessageElement.style.display = 'none';
}
}

