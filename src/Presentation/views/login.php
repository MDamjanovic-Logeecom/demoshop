<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="/src/Presentation/public/css/login.css?v=1.0">
</head>
<body>
<div class="container">
    <h1 style="font-size: 36px; text-align: center;">Welcome</h1>
    <p style="font-size: 18px; text-align: center; margin-bottom: 30px;">to demo shop administration!</p>

    <form action="/login" method="post">
        <div class="form-grid">
            <div class="left-side">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="remember_me" id="remember_me">
                    <label for="remember_me">Keep me logged in</label>
                </div>
            </div>
            <div class="right-side" style="display: flex; align-items: flex-end; justify-content: flex-end;">
                <button type="submit">Log In</button>
            </div>
        </div>
        <div class="error-message-container">
            <?php if (!empty($message)): ?>
                <span class="error-message"><?= htmlspecialchars($message) ?></span>
            <?php endif; ?>
        </div>
    </form>
</div>
<script src="/src/Presentation/public/js/message.js"></script>
</body>
</html>
