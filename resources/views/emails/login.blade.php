<!DOCTYPE html>
<html>
<head>
    <title>Login Creds</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        li strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login Credentials</h1>
        <p>Here are your login credentials:</p>
        <ul>
            <li><strong>Email:</strong> {{ $mailData['cmp_admin_email'] }}</li>
            <li><strong>Password:</strong> {{ $mailData['cmp_admin_password'] }}</li>
        </ul>
    </div>
</body>
</html>
