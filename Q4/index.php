<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <div>
        <h1> Q4. Login Page</h1>
        <form action="loginCheck.php" method="post">
            <div class="form-group">
                <label for="email">Email address:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <br>
            <div class="form-group">
                <label for="">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <br>
            <div class="form-group">
                <input type="submit" id="btnLogin" name="btnLogin" value="login">
            </div>
            <br>
            
        </form>
    </div>
</body>
</html>