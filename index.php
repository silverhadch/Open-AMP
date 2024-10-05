<?php
// Function to detect the latest PHP version
function detectPhpVersion() {
    $phpDirs = array_filter(glob('/etc/php-*'), 'is_dir');
    $phpVersions = [];

    foreach ($phpDirs as $dir) {
        if (preg_match('/php-(\d+\.\d+)/', basename($dir), $matches)) {
            $phpVersions[] = $matches[1];
        }
    }

    if (!empty($phpVersions)) {
        // Sort and return the latest version
        usort($phpVersions, 'version_compare');
        return end($phpVersions);
    }
    return null;
}

// Function to execute shell commands safely
function executeCommand($command) {
    // Allow only safe commands
    $allowedServices = ['apache2', 'mysqld', 'php_fpm'];
    $allowedActions = ['start', 'stop', 'restart'];

    $parts = explode(' ', $command);

    if (in_array($parts[0], $allowedActions) && in_array($parts[1], $allowedServices)) {
        // Execute the command via rcctl and return output
        $output = shell_exec("rcctl $command 2>&1");
        return nl2br(htmlspecialchars($output));
    } else {
        return "Invalid command.";
    }
}

// Detect installed PHP version
$phpVersion = detectPhpVersion();
$phpDaemon = $phpVersion ? "php" . str_replace('.', '', $phpVersion) . "_fpm" : null;

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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }

        form {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        select, button {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        button {
            background-color: #007BFF;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .output {
            background-color: #f4f4f4;
            border-left: 5px solid #007BFF;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
            color: #333;
            white-space: pre-wrap;
        }

        a {
            text-decoration: none;
            color: #007BFF;
            transition: color 0.3s;
        }

        a:hover {
            color: #0056b3;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin: 8px 0;
        }

        .link-button {
            display: inline-block;
            padding: 10px 15px;
            border: 1px solid #007BFF;
            border-radius: 5px;
            background-color: #fff;
            color: #007BFF;
            transition: background-color 0.3s, color 0.3s;
            text-align: center;
        }

        .link-button:hover {
            background-color: #007BFF;
            color: #fff;
        }

        .responsive {
            display: flex;
            flex-direction: column;
        }

        @media (min-width: 768px) {
            form {
                flex-direction: row;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Open-AMP Control Panel</h1>

        <!-- Service Control Card -->
        <div class="card">
            <h2>Control Services</h2>
            <form method="post">
                <div class="responsive">
                    <label for="service">Service:</label>
                    <select name="service" id="service" required>
                        <option value="apache2">Apache (apache2)</option>
                        <option value="mysqld">MySQL (mysqld)</option>
                        <?php if ($phpDaemon): ?>
                            <option value="<?php echo $phpDaemon; ?>">PHP <?php echo $phpVersion; ?> FPM (<?php echo $phpDaemon; ?>)</option>
                        <?php else: ?>
                            <option disabled>No PHP version detected</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="responsive">
                    <label for="action">Action:</label>
                    <select name="action" id="action" required>
                        <option value="start">Start</option>
                        <option value="stop">Stop</option>
                        <option value="restart">Restart</option>
                    </select>
                </div>
                <button type="submit">Execute</button>
            </form>
            <?php if (isset($output)): ?>
                <div class="output"><?php echo $output; ?></div>
            <?php endif; ?>
        </div>

        <!-- phpMyAdmin Access Card -->
        <div class="card">
            <h2>Access phpMyAdmin</h2>
            <a href="http://localhost/phpMyAdmin" class="link-button">Click here to access phpMyAdmin</a>
        </div>

        <!-- Virtual Hosts Card -->
        <div class="card">
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
    </div>

</body>
</html>
