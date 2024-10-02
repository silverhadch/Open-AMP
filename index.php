<?php
// Function to execute shell commands safely
function executeCommand($command) {
    // Make sure the command is safe to execute
    $allowedCommands = ['start', 'stop', 'restart'];
    $parts = explode(' ', $command);
    
    if (in_array($parts[0], $allowedCommands)) {
        $output = shell_exec("rcctl $command 2>&1");
        return nl2br(htmlspecialchars($output));
    } else {
        return "Invalid command.";
    }
}

// Handle service control requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service'], $_POST['action'])) {
    $service = htmlspecialchars($_POST['service']);
    $action = htmlspecialchars($_POST['action']);
    $output = executeCommand("$action $service");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localhost Control Panel</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { text-align: center; }
        .service { margin: 20px; }
        .output { white-space: pre-wrap; background: #f4f4f4; padding: 10px; border: 1px solid #ccc; }
        form { margin: 10px 0; }
    </style>
</head>
<body>
    <h1>Localhost Control Panel</h1>
    
    <div class="service">
        <h2>Control Services</h2>
        <form method="post">
            <label for="service">Service Name:</label>
            <input type="text" id="service" name="service" required>
            <select name="action">
                <option value="start">Start</option>
                <option value="stop">Stop</option>
                <option value="restart">Restart</option>
            </select>
            <button type="submit">Execute</button>
        </form>
        <?php if (isset($output)): ?>
            <div class="output"><?php echo $output; ?></div>
        <?php endif; ?>
    </div>

    <div class="service">
        <h2>Access phpMyAdmin</h2>
        <a href="http://localhost/phpMyAdmin">Click here to access phpMyAdmin</a>
    </div>
</body>
</html>
