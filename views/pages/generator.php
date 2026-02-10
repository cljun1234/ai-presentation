<!DOCTYPE html>
<html>
<head>
    <title>AI Slide Generator</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .error { color: red; }
        .success { color: green; }
        .container { max-width: 600px; margin: 0 auto; }
        input[type="text"] { width: 100%; padding: 10px; margin: 10px 0; }
        button { padding: 10px 20px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Presentation</h1>

        <?php if (isset($_GET['error'])): ?>
            <p class="error">Error: <?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <p class="success">Success! Your presentation is ready.</p>
            <p><a href="/download/<?php echo htmlspecialchars($_GET['success']); ?>">Download PPTX</a></p>
        <?php endif; ?>

        <form method="POST" action="/generate">
            <label>Topic:</label>
            <input type="text" name="topic" required placeholder="e.g. Future of AI">
            <button type="submit">Generate</button>
        </form>

        <p><a href="/">Back to Dashboard</a></p>
    </div>
</body>
</html>
