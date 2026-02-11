<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background: #f4f6f8; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h1 { margin-top: 0; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../layouts/header.php'; ?>

    <div class="container">
        <h1>Dashboard</h1>

        <div class="card">
            <h2>Welcome</h2>
            <p>You are logged in.</p>
            <?php if ($domain): ?>
                <p>Your configured domain: <strong><?php echo htmlspecialchars($domain['domain']); ?></strong></p>
            <?php else: ?>
                <p>No domain configured yet. Go to <a href="/settings">Settings</a>.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2>My Presentations</h2>
                <a href="/generate" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">+ Create New</a>
            </div>

            <?php if (count($presentations) > 0): ?>
                <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                    <thead>
                        <tr style="background: #f8f9fa; text-align: left;">
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Topic</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Created</th>
                            <th style="padding: 10px; border-bottom: 2px solid #dee2e6;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($presentations as $p): ?>
                            <tr>
                                <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><?php echo htmlspecialchars($p['topic']); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #dee2e6;"><?php echo htmlspecialchars($p['created_at']); ?></td>
                                <td style="padding: 10px; border-bottom: 1px solid #dee2e6;">
                                    <a href="/download/<?php echo htmlspecialchars($p['filename']); ?>" style="color: #28a745; text-decoration: none;">Download</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No presentations generated yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
