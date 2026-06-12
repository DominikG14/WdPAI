document.addEventListener('DOMContentLoaded', function() {
    // Pobranie parametrów URL w celu obsługi przekierowania po zalogowaniu
    const params = new URLSearchParams(window.location.search);
    const returnUrl = params.get('returnUrl');
    
    if (returnUrl) {
        const returnUrlInput = document.getElementById('returnUrl');
        if (returnUrlInput) {
            returnUrlInput.value = returnUrl;
        }
    }
});