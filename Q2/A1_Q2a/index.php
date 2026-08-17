<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - Alice's E-Commerce (reCAPTCHA v2)</title>

    <!-- STEP: Loads Google's reCAPTCHA v2 JS library.
         This is what draws the "I'm not a robot" widget below. -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; }
        .card {
            max-width: 420px; margin: 60px auto; background:#fff;
            padding: 30px 35px; border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        h2 { text-align:center; margin-top:0; }
        label { display:block; margin-top:16px; font-weight:bold; font-size:14px; }
        input[type=email], input[type=password] {
            width:100%; padding:8px; margin-top:6px; box-sizing:border-box;
            border:1px solid #ccc; border-radius:4px;
        }
        small { color:#666; }
        .g-recaptcha { margin-top:20px; }
        button {
            margin-top:22px; width:100%; padding:10px; background:#2d6cdf;
            color:#fff; border:none; border-radius:4px; font-size:15px; cursor:pointer;
        }
        button:hover { background:#1f52ab; }
        .error { color:#c0392b; font-size:13px; margin-top:6px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Create Account</h2>

    <!--
        The form posts to process.php (server side), which:
          1) re-validates everything (never trust the browser alone),
          2) verifies the reCAPTCHA v2 token with Google, and
          3) only then "creates" the account.
    -->
    <form action="process.php" method="post" onsubmit="return clientSideValidate();">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <small>Minimum 10 characters. Must include uppercase, lowercase, and number(s).</small>

        <label for="confirm_password">Re-type Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <div id="clientError" class="error"></div>

        <!-- STEP: This is the reCAPTCHA v2 widget itself. -->
        <div class="g-recaptcha" data-sitekey="6LdAIIotAAAAAN7-PYKSJWr7q_lpOMQnSzAiFNDo"></div>

        <button type="submit">Create Account</button>
    </form>
</div>

<script>
    function clientSideValidate() {
        var password = document.getElementById("password").value;
        var confirm  = document.getElementById("confirm_password").value;
        var errorBox = document.getElementById("clientError");
        var passwordRule = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,}$/;

        if (!passwordRule.test(password)) {
            errorBox.textContent = "Password must be at least 10 characters and include uppercase, lowercase and a number.";
            return false;
        }
        if (password !== confirm) {
            errorBox.textContent = "Passwords do not match.";
            return false;
        }
        errorBox.textContent = "";
        return true;
    }
</script>

</body>
</html>