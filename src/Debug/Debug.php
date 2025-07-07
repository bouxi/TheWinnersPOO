<?php
namespace App\Debug;

class Debug
{
    private static array $logs = [];
    private static array $queries = [];

    // ➜ Ajouter un log texte
    public static function log($message): void
    {
        self::$logs[] = $message;
    }

    // ➜ Ajouter une requête SQL
    public static function addQuery($query): void
    {
        self::$queries[] = $query;
    }

    // ➜ Afficher la debugbar
    public static function render(): void
    {
        $time = round((microtime(true) - DEBUG_START) * 1000, 2);
        $memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        echo <<<HTML
        <style>
            .debug-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(0,0,0,0.85);
                color: #00ff00;
                padding: 10px;
                font-family: monospace;
                font-size: 12px;
                z-index: 9999;
                max-height: 300px;
                overflow-y: auto;
            }
            .debug-section { margin-bottom: 10px; }
            .debug-title { font-weight: bold; color: #ff9900; }
            .debug-toggle { cursor: pointer; color: #00ffff; }
        </style>

        <div class="debug-bar">
            <div class="debug-section">
                <span class="debug-title">⏱️ Temps d'exécution :</span> {$time} ms
            </div>
            <div class="debug-section">
                <span class="debug-title">💾 Mémoire utilisée :</span> {$memory} MB
            </div>
            <div class="debug-section">
                <span class="debug-title">🔗 Route actuelle :</span> {$_SERVER['REQUEST_URI']}
            </div>
            <div class="debug-section">
                <span class="debug-title debug-toggle" onclick="toggleDebug('logs')">📝 Logs</span>
                <div id="debug-logs" style="display:none;">
                    <pre style="white-space: pre-wrap;">HTML;
                        print_r(self::$logs);
                        echo <<<HTML
                    </pre>
                </div>
            </div>
            <div class="debug-section">
                <span class="debug-title debug-toggle" onclick="toggleDebug('queries')">🗄️ Requêtes SQL</span>
                <div id="debug-queries" style="display:none;">
                    <pre style="white-space: pre-wrap;">HTML;
                        print_r(self::$queries);
                        echo <<<HTML
                    </pre>
                </div>
            </div>
        </div>

        <script>
            function toggleDebug(id) {
                const element = document.getElementById('debug-' + id);
                element.style.display = (element.style.display === 'none') ? 'block' : 'none';
            }
        </script>
        HTML;
    }
}
