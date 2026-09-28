<?php
declare(strict_types=1);

namespace App\Core;

class Response
{
    public static function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public static function render(string $viewPath, array $data = [], string $title = 'Mini Hospital - Pendaftaran'): void
    {
        extract($data);
        $viewsDir = __DIR__ . '/../Views/';
        $contentFile = $viewsDir . $viewPath . '.php';

        if (!file_exists($contentFile)) {
            http_response_code(500);
            echo "View file [{$viewPath}] not found.";
            exit;
        }

        ob_start();
        require $contentFile;
        $content = ob_get_clean();

        $layoutFile = $viewsDir . 'layout.php';
        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
        exit;
    }
}
