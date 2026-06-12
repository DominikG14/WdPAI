$(document).ready(function() {
    // Odczyt konfiguracji
    const config = JSON.parse(document.getElementById('admin-config').textContent);
    const currentUserId = config.currentUserId;

    $('#search-input').on('input', function() {
        const searchValue = $(this).val();

        $.ajax({
            url: '/admin/users/search',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ search: searchValue }),
            dataType: 'json',
            success: function(users) {
                const $tableBody = $('#users-table-body');
                $tableBody.empty();

                if (users.length === 0) {
                    $tableBody.append('<tr><td colspan="4" style="text-align:center; color: #666; font-style: italic; padding: 20px;">Nie znaleziono dopasowań.</td></tr>');
                    return;
                }

                users.forEach(function(user) {
                    let actionButton = (parseInt(user.id) === parseInt(currentUserId)) 
                        ? '<span class="badge-self">To Ty</span>' 
                        : `<a href="/admin/users/delete/${user.id}" class="btn-delete" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')"><i class="fa-solid fa-trash"></i> Usuń konto</a>`;

                    const row = `
                        <tr>
                            <td>${user.id}</td>
                            <td><strong>${$('<div>').text(user.username).html()}</strong></td>
                            <td>${$('<div>').text(user.email).html()}</td>
                            <td>
                                <a href="/admin/users/progress/${user.id}" class="btn-progress"><i class="fa-solid fa-eye"></i> Postępy</a>
                                ${actionButton}
                            </td>
                        </tr>
                    `;
                    $tableBody.append(row);
                });
            }
        });
    });
});