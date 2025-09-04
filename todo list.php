<?php
// Simple database connection
$conn = new mysqli("localhost", "root", "", "todolist");

// Check if connection works
if ($conn->connect_error) {
    die("Database connection failed. Make sure XAMPP MySQL is running!");
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['add_task'])) {
        // Add new task
        $task = $_POST['task_text'];
        if (!empty($task)) {
            $stmt = $conn->prepare("INSERT INTO tasks (task_text) VALUES (?)");
            $stmt->bind_param("s", $task);
            $stmt->execute();
        }
    }
    
    if (isset($_POST['toggle_task'])) {
        // Toggle completion
        $id = $_POST['task_id'];
        $completed = $_POST['completed'] == 1 ? 0 : 1;
        $stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
        $stmt->bind_param("ii", $completed, $id);
        $stmt->execute();
    }
    
    if (isset($_POST['edit_task'])) {
        // Edit task
        $id = $_POST['task_id'];
        $new_text = $_POST['new_text'];
        if (!empty($new_text)) {
            $stmt = $conn->prepare("UPDATE tasks SET task_text = ? WHERE id = ?");
            $stmt->bind_param("si", $new_text, $id);
            $stmt->execute();
        }
    }
    
    if (isset($_POST['delete_task'])) {
        // Delete task
        $id = $_POST['task_id'];
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
    
    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Get all tasks
$result = $conn->query("SELECT * FROM tasks ORDER BY id DESC");
$tasks = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>
        
        <!-- Add Task Form -->
        <form method="POST" class="input-section">
            <input type="text" name="task_text" class="task-input" placeholder="Add a new task..." required>
            <button type="submit" name="add_task" class="add-btn">Add</button>
        </form>
        
        <!-- Task List -->
        <div class="task-list">
            <?php if (empty($tasks)): ?>
                <div class="empty-state">No tasks yet. Add one above!</div>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="task-item">
                        <span class="task-text <?php echo $task['completed'] ? 'completed' : ''; ?>">
                            <?php echo htmlspecialchars($task['task_text']); ?>
                        </span>
                        
                        <div class="task-actions">
                            <!-- Complete/Incomplete Button -->
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="completed" value="<?php echo $task['completed']; ?>">
                                <button type="submit" name="toggle_task" class="action-btn complete-btn" title="<?php echo $task['completed'] ? 'Mark as incomplete' : 'Mark as complete'; ?>">
                                    <?php echo $task['completed'] ? '↶' : '✓'; ?>
                                </button>
                            </form>
                            
                            <!-- Edit Button -->
                            <button class="action-btn edit-btn" onclick="toggleEdit(<?php echo $task['id']; ?>)">✎</button>
                            
                            <!-- Delete Button -->
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this task?')">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="delete_task" class="action-btn delete-btn">✕</button>
                            </form>
                        </div>
                        
                        <!-- Edit Form (Hidden by default) -->
                        <div id="edit-<?php echo $task['id']; ?>" class="edit-form">
                            <form method="POST">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <input type="text" name="new_text" class="edit-input" value="<?php echo htmlspecialchars($task['task_text']); ?>" required>
                                <div class="edit-buttons">
                                    <button type="submit" name="edit_task" class="save-btn">Save</button>
                                    <button type="button" class="cancel-btn" onclick="toggleEdit(<?php echo $task['id']; ?>)">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
    
</body>
</html>
