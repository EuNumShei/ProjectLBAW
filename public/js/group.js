function openTab(event, tabId) {
    const tabs = document.querySelectorAll('.tab-link');
    tabs.forEach(tab => tab.classList.remove('active'));

    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => content.classList.remove('active-tab'));

    event.currentTarget.classList.add('active');

    const activeTab = document.getElementById(tabId);
    if (activeTab) {
        activeTab.classList.add('active-tab');
    } else {
        console.error(`A aba com ID "${tabId}" não foi encontrada.`);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('submit', function(event) {
        if (event.target.matches('.invite-form')) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const url = form.action;
            const method = form.method;
            const button = form.querySelector('button');
            const spinner = button.querySelector('.spinner');

            button.disabled = true;
            button.innerHTML = '<span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>';

            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, body: data };
                });
            })
            .then(({ status, body }) => {
                if (status === 201 && body.success) {
                    button.innerHTML = '<i class="fas fa-check"></i>';
                } else {
                    console.error('erro:', body.message || 'desconhecido');
                    button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Invite';
                    button.disabled = false;
                }
            })
            .catch(error => {
                alert('erro: ' + error.message);
                button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Invite';
                button.disabled = false;
            });
        }

        if (event.target.matches('.remove-member-form')) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const url = form.action;
            const method = form.method;
            const button = form.querySelector('button');
            const spinner = button.querySelector('.spinner');
        
            button.disabled = true;
            button.innerHTML = '<span class="spinner"><i class="fas fa-spinner fa-spin"></i></span>';
        
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                return response.json().then(data => {
                    return { status: response.status, body: data };
                });
            })
            .then(({ status, body }) => {
                if (status === 200 && body.kicked) {
                    form.closest('.member-item').remove();
                } else {
                    console.error('erro:', body.message || 'desconhecido');
                    button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Remove Member';
                    button.disabled = false;
                }
            })
            .catch(error => {
                alert('erro: ' + error.message);
                button.innerHTML = '<span class="spinner" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span> Remove Member';
                button.disabled = false;
            });
        }
    });
});