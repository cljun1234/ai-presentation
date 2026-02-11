<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Presentation - AI Tools</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background: #f4f6f8; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px 20px; }
        .card { background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        h1 { margin-bottom: 20px; color: #333; }
        .input-group { margin-bottom: 30px; }
        input[type="text"] {
            width: 80%;
            padding: 15px;
            font-size: 18px;
            border: 2px solid #ddd;
            border-radius: 4px;
            outline: none;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus { border-color: #007bff; }
        button {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
        .back-link { display: block; margin-top: 20px; color: #666; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container">
        <div class="card">
            <h1>Create New Presentation</h1>
            <p>Enter a topic, and our AI will generate a slide deck for you.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="error">Error: <?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form method="POST" action="/generate" id="genForm">
                <div class="input-group">
                    <input type="text" name="topic" required placeholder="e.g. The Future of Renewable Energy" autofocus>
                </div>
                <button type="submit" onclick="this.innerHTML='Generating...'; this.disabled=true; document.getElementById('genForm').submit();">
                    Generate with AI
                </button>
            </form>

            <a href="/" class="back-link">← Back to Dashboard</a>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
