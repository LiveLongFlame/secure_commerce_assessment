<!--
    ============================================================
    Unlike v2, reCAPTCHA v3 has NO checkbox or puzzle for the 
    user to solve - it runs invisibly in the background and 
    returns a risk "score" 
    (0.0 = very likely a bot, 1.0 = very likely a human), which
    is checked on the server in process.php.
    ============================================================
-->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account - Alice's E-Commerce (reCAPTCHA v3)</title>
 
    <!-- STEP: Loads reCAPTCHA v3, bound to your Site Key via ?render= -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LfhN4otAAAAACqLEgyJ7ERYkocR2-5uQ3l3tAHW"></script>
 
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; }
        .card {
            max-width: 420px; margin: 60px auto; background:#fff;
            padding: 30px 35px; border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        h2 { text-align:center; margin-top:0; }
        label { display:block; margin-top:16px; font-weight:bold; font-size:14px; }
        input[type=text], input[type=password] {
            width:100%; padding:8px; margin-top:6px; box-sizing:border-box;
            border:1px solid #ccc; border-radius:4px;
        }
        .btn-row { display:flex; gap:10px; margin-top:22px; }
        button {
            flex:1; padding:10px; border:none; border-radius:4px;
            font-size:15px; cursor:pointer;
        }
        #submitBtn { background:#2d6cdf; color:#fff; }
        #submitBtn:hover { background:#1f52ab; }
        #resetBtn  { background:#e0e0e0; color:#333; }
        .error { color:#c0392b; font-size:13px; margin-top:6px; }
        .note  { font-size:12px; color:#888; margin-top:14px; }
    </style>
</head>
<body>
 
<div class="card">
    <h2>Create Account</h2>
 
    <!--
        Note there is NO g-recaptcha widget div here (that is the whole
        point of v3 - it is invisible).
    -->
    <form id="registerForm" action="process.php" method="post">
 
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>
 
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
 
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
 
        <div id="clientError" class="error"></div>
 
        <!-- This hidden field is filled in by JavaScript just before the
             form is submitted, with the token returned by grecaptcha.execute(). -->
        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
 
        <div class="btn-row">
            <button type="submit" id="submitBtn">Submit</button>
            <button type="reset" id="resetBtn">Reset</button>
        </div>
 
        <p class="note">Protected by reCAPTCHA v3 - no puzzle required.</p>
    </form>
</div>
 
<script>
    var SITE_KEY = "6LfhN4otAAAAACqLEgyJ7ERYkocR2-5uQ3l3tAHW";
 
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        // Stop the normal form submission first - we need to attach the
        // reCAPTCHA token to the form BEFORE it actually posts to process.php.
        e.preventDefault();
 
        var password = document.getElementById("password").value;
        var confirm  = document.getElementById("confirm_password").value;
        var errorBox = document.getElementById("clientError");
        var passwordRule = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,}$/;
 
        if (!passwordRule.test(password)) {
            errorBox.textContent = "Password must be at least 10 characters and include uppercase, lowercase and a number.";
            return;
        }
        if (password !== confirm) {
            errorBox.textContent = "Passwords do not match.";
            return;
        }
        errorBox.textContent = "";
 
        // STEP: Ask reCAPTCHA v3 to silently run its risk analysis for the
        // "create_account" action and hand back a one-time token.
        grecaptcha.ready(function () {
            grecaptcha.execute(SITE_KEY, { action: 'create_account' }).then(function (token) {
                document.getElementById('g-recaptcha-response').value = token;
                // Now that the token is attached, submit for real.
                document.getElementById('registerForm').submit();
            });
        });
    });
</script>
 
</body>
</html>
