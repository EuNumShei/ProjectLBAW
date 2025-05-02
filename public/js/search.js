document.addEventListener('DOMContentLoaded', function() {
    console.log("domloaded");
    document.querySelectorAll('.search-option-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            const group = button.getAttribute('data-group');
            document.querySelectorAll('.search-option-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            document.getElementById('group-input').value = group;
            updateResults(group);
        });
    });

    function updateResults(group) {
        console.log('Update results for group:', group);
    }
});