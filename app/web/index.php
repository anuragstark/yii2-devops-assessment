<?php
// app/web/index.php - Entry point for Yii2 application

// Define application environment
defined('YII_DEBUG') or define('YII_DEBUG', getenv('YII_DEBUG') ?: false);
defined('YII_ENV') or define('YII_ENV', getenv('YII_ENV') ?: 'prod');

// Include Yii2 framework (if using composer)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';
} else {
    // Minimal implementation without full Yii2 framework for demo
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Yii2 DevOps Demo Application</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .container {
                background: white;
                border-radius: 10px;
                padding: 40px;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
                text-align: center;
                max-width: 600px;
                width: 90%;
            }
            
            .header {
                color: #333;
                margin-bottom: 30px;
            }
            
            .status-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
                margin: 30px 0;
            }
            
            .status-card {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                border-left: 4px solid #007bff;
            }
            
            .status-card h3 {
                margin: 0 0 10px 0;
                color: #007bff;
            }
            
            .status-card p {
                margin: 5px 0;
                color: #666;
            }
            
            .success {
                border-left-color: #28a745;
            }
            
            .success h3 {
                color: #28a745;
            }
            
            .footer {
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #eee;
                color: #666;
                font-size: 14px;
            }
            
            .badge {
                display: inline-block;
                padding: 4px 8px;
                background: #007bff;
                color: white;
                border-radius: 4px;
                font-size: 12px;
                margin: 0 5px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>🚀 Yii2 DevOps Assessment</h1>
                <p>Deployed via Docker Swarm + NGINX + CI/CD + Ansible</p>
            </div>
            
            <div class="status-grid">
                <div class="status-card success">
                    <h3>✅ Application Status</h3>
                    <p><strong>Status:</strong> Running</p>
                    <p><strong>Environment:</strong> <?php echo YII_ENV; ?></p>
                    <p><strong>Debug Mode:</strong> <?php echo YII_DEBUG ? 'ON' : 'OFF'; ?></p>
                </div>
                
                <div class="status-card success">
                    <h3>🐳 Docker Info</h3>
                    <p><strong>Container:</strong> PHP 8.1 + Apache</p>
                    <p><strong>Deployment:</strong> Docker Swarm</p>
                    <p><strong>Replicas:</strong> 2</p>
                </div>
                
                <div class="status-card success">
                    <h3>🌐 Proxy Info</h3>
                    <p><strong>Reverse Proxy:</strong> NGINX</p>
                    <p><strong>Load Balancer:</strong> Upstream</p>
                    <p><strong>Port:</strong> 80 → 8080</p>
                </div>
                
                <div class="status-card success">
                    <h3>⚙️ Server Info</h3>
                    <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
                    <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                    <p><strong>Host:</strong> <?php echo gethostname(); ?></p>
                </div>
            </div>
            
            <div class="status-card">
                <h3>📊 System Health</h3>
                <p><strong>Timestamp:</strong> <?php echo date('Y-m-d H:i:s T'); ?></p>
                <p><strong>Uptime:</strong> <?php echo getUptime(); ?></p>
                <p><strong>Memory Usage:</strong> <?php echo getMemoryUsage(); ?></p>
            </div>
            
            <div class="footer">
                <p>
                    <span class="badge">Docker Swarm</span>
                    <span class="badge">NGINX</span>
                    <span class="badge">GitHub Actions</span>
                    <span class="badge">Ansible</span>
                    <span class="badge">AWS EC2</span>
                </p>
                <p>DevOps Assessment - Yii2 Application Deployment</p>
            </div>
        </div>
    </body>
    </html>
    
    <?php
}

// Helper functions
function getUptime() {
    if (file_exists('/proc/uptime')) {
        $uptime = file_get_contents('/proc/uptime');
        $uptime = floatval($uptime);
        $days = floor($uptime / 86400);
        $hours = floor(($uptime % 86400) / 3600);
        $minutes = floor(($uptime % 3600) / 60);
        return sprintf('%dd %dh %dm', $days, $hours, $minutes);
    }
    return 'N/A';
}

function getMemoryUsage() {
    $memory = memory_get_usage(true);
    $unit = ['B', 'KB', 'MB', 'GB'];
    $index = 0;
    
    while ($memory >= 1024 && $index < count($unit) - 1) {
        $memory /= 1024;
        $index++;
    }
    
    return round($memory, 2) . ' ' . $unit[$index];
}
?>