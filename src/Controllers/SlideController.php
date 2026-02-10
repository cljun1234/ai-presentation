<?php

class SlideController {

    public function showGenerator() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        require_once __DIR__ . '/../../views/pages/generator.php';
    }

    public function processGenerator() {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $topic = $_POST['topic'] ?? '';

        if (empty($topic)) {
            header('Location: /generate?error=missing_topic');
            exit;
        }

        try {
            $generator = new \Services\SlideGenerator();
            $filename = $generator->generate($topic);

            header('Location: /generate?success=' . urlencode($filename));
            exit;

        } catch (\Exception $e) {
            header('Location: /generate?error=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function download($filename) {
         if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $filename = basename($filename);
        $filepath = __DIR__ . '/../../storage/generated/' . $filename;

        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            readfile($filepath);
            exit;
        } else {
            http_response_code(404);
            echo "File not found.";
        }
    }
}
