
        function toggleEdit(taskId) {
            const editForm = document.getElementById('edit-' + taskId);
            if (editForm.style.display === 'none' || editForm.style.display === '') {
                editForm.style.display = 'block';
            } else {
                editForm.style.display = 'none';
            }
        }

        // Save to localStorage as backup
        function saveToLocalStorage() {
            const tasks = [];
            document.querySelectorAll('.task-text').forEach(task => {
                tasks.push({
                    text: task.textContent.trim(),
                    completed: task.classList.contains('completed')
                });
            });
            localStorage.setItem('todolist_backup', JSON.stringify(tasks));
        }

        // Save backup when page loads
        window.onload = function() {
            saveToLocalStorage();
        };
    
