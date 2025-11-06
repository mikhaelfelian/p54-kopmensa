<?php
/**
 * Util Controller
 * 
 * Utility controller for running migrations and CLI commands without login
 * 
 * Created by Mikhael Felian Waskito
 * Created at 2025-01-XX
 */

namespace App\Controllers;

class Util extends BaseController
{
    /**
     * Run migrations without requiring login
     * 
     * Usage: /util/migrate
     * Optional parameters:
     * - ?group=default (database group)
     * - ?namespace=App (migration namespace)
     * - ?all=1 (run all namespaces)
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function migrate()
    {
        try {
            $migrate = \Config\Services::migrations();
            $group = $this->request->getGet('group') ?? 'default';
            $namespace = $this->request->getGet('namespace');
            $all = $this->request->getGet('all');
            
            $output = [];
            $output[] = "Starting migration...";
            $output[] = "Time: " . date('Y-m-d H:i:s');
            $output[] = str_repeat('-', 50);
            
            // Set namespace if provided
            if ($namespace) {
                $migrate->setNamespace($namespace);
            }
            
            // Run migrations
            if ($all == '1' || $all === '1') {
                // Run all namespaces
                $migrate->setNamespace(null);
                $result = $migrate->latest($group);
            } else {
                // Run specific namespace or default
                $result = $migrate->latest($group);
            }
            
            if ($result) {
                $output[] = "✓ Migrations completed successfully!";
                $output[] = str_repeat('-', 50);
                
                // Get migration history
                $history = $migrate->getHistory($group);
                if (!empty($history)) {
                    $output[] = "Migration History:";
                    foreach ($history as $migration) {
                        $migrationName = $migration->version . '_' . $migration->class;
                        $output[] = "  - " . $migrationName . " (Namespace: " . $migration->namespace . ", Batch: " . $migration->batch . ")";
                    }
                } else {
                    $output[] = "No migrations found or all migrations are up to date.";
                }
                
                return $this->response
                    ->setContentType('text/html')
                    ->setBody('<pre style="font-family: monospace; padding: 20px; background: #f5f5f5;">' . 
                              htmlspecialchars(implode("\n", $output)) . 
                              '</pre>');
            } else {
                $output[] = "✗ Migration failed!";
                $output[] = "Check the error messages above for details.";
                
                return $this->response
                    ->setContentType('text/html')
                    ->setStatusCode(500)
                    ->setBody('<pre style="font-family: monospace; padding: 20px; background: #f5f5f5; color: red;">' . 
                              htmlspecialchars(implode("\n", $output)) . 
                              '</pre>');
            }
        } catch (\Exception $e) {
            $error = [
                "Migration Error:",
                str_repeat('-', 50),
                "Message: " . $e->getMessage(),
                "File: " . $e->getFile(),
                "Line: " . $e->getLine(),
                str_repeat('-', 50),
                "Stack Trace:",
                $e->getTraceAsString()
            ];
            
            return $this->response
                ->setContentType('text/html')
                ->setStatusCode(500)
                ->setBody('<pre style="font-family: monospace; padding: 20px; background: #f5f5f5; color: red;">' . 
                          htmlspecialchars(implode("\n", $error)) . 
                          '</pre>');
        }
    }
    
    /**
     * Run CLI commands via web interface
     * 
     * Usage: /util/cli?command=migrate
     *        /util/cli?command=migrate:rollback
     *        /util/cli?command=db:table&params[]=users
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function cli()
    {
        try {
            $command = $this->request->getGet('command');
            
            if (empty($command)) {
                return $this->response
                    ->setContentType('text/html')
                    ->setStatusCode(400)
                    ->setBody('<pre style="font-family: monospace; padding: 20px; background: #f5f5f5; color: red;">' . 
                              htmlspecialchars("Error: Command parameter is required.\n\nUsage: /util/cli?command=migrate\n       /util/cli?command=migrate:rollback\n       /util/cli?command=db:table&params[]=users") . 
                              '</pre>');
            }
            
            // Get additional parameters
            $params = $this->request->getGet('params');
            if (!is_array($params)) {
                $params = $params ? [$params] : [];
            }
            
            $output = [];
            $output[] = "Running CLI Command: " . $command;
            $output[] = "Time: " . date('Y-m-d H:i:s');
            $output[] = str_repeat('-', 50);
            
            // Use Commands service to execute CLI commands
            $commands = service('commands');
            
            // Parse command (e.g., "migrate:rollback" -> ["migrate", "rollback"])
            $commandParts = explode(':', $command);
            $mainCommand = $commandParts[0];
            $subCommand = isset($commandParts[1]) ? $commandParts[1] : null;
            
            // Build params array
            $commandParams = [];
            if ($subCommand) {
                $commandParams[] = $subCommand;
            }
            $commandParams = array_merge($commandParams, $params);
            
            // Capture output using output buffering
            ob_start();
            
            // Set up CLI environment (simulate CLI context)
            // Some commands may check is_cli(), so we need to handle that
            $originalIsCli = defined('STDIN');
            
            // Execute command
            try {
                $exitCode = $commands->run($mainCommand, $commandParams);
            } catch (\Exception $cmdException) {
                $exitCode = EXIT_ERROR;
                $output[] = "Command Exception: " . $cmdException->getMessage();
            }
            
            // Get captured output
            $cliOutput = ob_get_clean();
            
            // Also try to get any CLI messages if available
            if (empty($cliOutput) && method_exists($commands, 'getLastOutput')) {
                $cliOutput = $commands->getLastOutput();
            }
            
            if ($exitCode === 0 || $exitCode === null || $exitCode === EXIT_SUCCESS) {
                $output[] = "✓ Command executed successfully!";
                $output[] = str_repeat('-', 50);
                if (!empty($cliOutput)) {
                    $output[] = "Output:";
                    $output[] = $cliOutput;
                } else {
                    $output[] = "Command completed with no output.";
                }
                
                return $this->response
                    ->setContentType('text/html')
                    ->setBody('<pre style="font-family: monospace; padding: 20px; background: #f5f5f5;">' . 
                              htmlspecialchars(implode("\n", $output)) . 
                              '</pre>');
            } else {
                $output[] = "✗ Command execution failed with exit code: " . ($exitCode ?? 'unknown');
                $output[] = str_repeat('-', 50);
                if (!empty($cliOutput)) {
                    $output[] = "Error Output:";
                    $output[] = $cliOutput;
                } else {
                    $output[] = "No error output available.";
                }
                
                return $this->response
                    ->setContentType('text/html')
                    ->setStatusCode(500)
                    ->setBody('<pre style="font-family: monospace; padding: 20px; background: #f5f5f5; color: red;">' . 
                              htmlspecialchars(implode("\n", $output)) . 
                              '</pre>');
            }
        } catch (\Exception $e) {
            $error = [
                "CLI Command Error:",
                str_repeat('-', 50),
                "Message: " . $e->getMessage(),
                "File: " . $e->getFile(),
                "Line: " . $e->getLine(),
                str_repeat('-', 50),
                "Stack Trace:",
                $e->getTraceAsString()
            ];
            
            return $this->response
                ->setContentType('text/html')
                ->setStatusCode(500)
                ->setBody('<pre style="font-family: monospace; padding: 20px; background: #f5f5f5; color: red;">' . 
                          htmlspecialchars(implode("\n", $error)) . 
                          '</pre>');
        }
    }
    
    /**
     * Index page - shows available utility functions
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    public function index()
    {
        $html = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>Util Controller - Utility Functions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }
        .section {
            margin: 30px 0;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #4CAF50;
        }
        .section h2 {
            color: #4CAF50;
            margin-top: 0;
        }
        .example {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
        .param {
            color: #a6e22e;
        }
        .note {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .note strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Util Controller</h1>
        <p>Utility controller for running migrations and CLI commands without login.</p>
        
        <div class="section">
            <h2>1. Run Migrations</h2>
            <p>Execute database migrations without requiring login.</p>
            
            <div class="example">
                <span class="param">GET</span> /util/migrate<br>
                <span class="param">GET</span> /util/migrate?group=default<br>
                <span class="param">GET</span> /util/migrate?namespace=App<br>
                <span class="param">GET</span> /util/migrate?all=1
            </div>
            
            <div class="note">
                <strong>Parameters:</strong><br>
                • <code>group</code> - Database group (optional)<br>
                • <code>namespace</code> - Migration namespace (optional)<br>
                • <code>all</code> - Set to 1 to run all namespaces (optional)
            </div>
        </div>
        
        <div class="section">
            <h2>2. Run CLI Commands</h2>
            <p>Execute CLI commands via web interface.</p>
            
            <div class="example">
                <span class="param">GET</span> /util/cli?command=migrate<br>
                <span class="param">GET</span> /util/cli?command=migrate:rollback<br>
                <span class="param">GET</span> /util/cli?command=db:table&params[]=users<br>
                <span class="param">GET</span> /util/cli?command=list
            </div>
            
            <div class="note">
                <strong>Parameters:</strong><br>
                • <code>command</code> - CLI command to run (required)<br>
                • <code>params[]</code> - Additional parameters as array (optional)
            </div>
        </div>
        
        <div class="note">
            <strong>⚠️ Security Note:</strong><br>
            This controller bypasses authentication. Make sure to restrict access to this controller
            in production environments (e.g., using IP whitelist, environment checks, or removing
            it from public routes).
        </div>
    </div>
</body>
</html>
HTML;
        
        return $this->response
            ->setContentType('text/html')
            ->setBody($html);
    }
}

