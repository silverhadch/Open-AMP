<?php
// Function to execute shell commands safely
function executeCommand($command) {
    // Allow only safe commands
    $allowedCommands = ['start', 'stop', 'restart'];
    $parts = explode(' ', $command);
    
    if (in_array($parts[0], $allowedCommands)) {
        // Execute the command via rcctl and return output
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
    <title>Open-AMP Control Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        .service, .projects {
            margin: 20px;
        }
        .output {
            white-space: pre-wrap;
            background: #f4f4f4;
            padding: 10px;
            border: 1px solid #ccc;
        }
        form {
            margin: 10px 0;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Open-AMP Control Panel</h1>

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

    <div class="projects">
        <h2>Virtual Hosts (Projects)</h2>
        <ul>
        <?php
            // Define the directory where projects are stored
            $project_dir = '/var/www/htdocs';

            // Scan the directory and list the folders (projects)
            $projects = array_filter(glob($project_dir . '/*'), 'is_dir');

            if (!empty($projects)) {
                foreach ($projects as $project) {
                    $project_name = basename($project);
                    echo "<li><a href=\"/$project_name\">$project_name</a></li>";
                }
            } else {
                echo "<li>No projects found in /var/www/htdocs</li>";
            }
        ?>
        </ul>
    </div>

</body>
</html>
