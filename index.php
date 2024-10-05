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

// Function to check the status of a service
function checkServiceStatus($service) {
    $status = shell_exec("rcctl check $service 2>&1");
    return nl2br(htmlspecialchars($status));
}

// Detect installed PHP version
$phpVersion = detectPhpVersion();
$phpDaemon = $phpVersion ? "php" . str_replace('.', '', $phpVersion) . "_fpm" : null;

$apacheStatus = checkServiceStatus('apache2');
$mysqlStatus = checkServiceStatus('mysqld');
$phpStatus = $phpDaemon ? checkServiceStatus($phpDaemon) : 'No PHP version detected';
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

        .output {
            background-color: #f4f4f4;
            border-left: 5px solid #007BFF;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
            color: #333;
            white-space: pre-wrap;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        ul li {
            margin: 8px 0;
        }

        a {
            text-decoration: none;
            color: #007BFF;
            transition: color 0.3s;
        }

        a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Open-AMP Control Panel</h1>

        <!-- Service Status Card -->
        <div class="card">
            <h2>Service Status</h2>
            <div class="output">
                <strong>Apache Status:</strong><br>
                <?php echo $apacheStatus; ?>
            </div>
            <div class="output">
                <strong>MySQL Status:</strong><br>
                <?php echo $mysqlStatus; ?>
            </div>
            <div class="output">
                <strong>PHP-FPM Status:</strong><br>
                <?php echo $phpStatus; ?>
            </div>
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
