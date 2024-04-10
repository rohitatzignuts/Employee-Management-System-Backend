<!DOCTYPE html>
<html>
<body>
    <body>
        <div class="container mt-5">
            <div class="card">
                <div class="card-header">
                    <h1 class="text-center">This is a Welcome Email from " {{ $mailData['company_name'] }} "</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Here are your login credentials:</p>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Email:</strong> {{ $mailData['email'] }}</li>
                        <li class="list-group-item"><strong>Password:</strong> {{ $mailData['password'] }}</li>
                    </ul>
                </div>
                <div>
                    <a href="http://localhost:5173/login" class="card-link">Login..</a>
                </div>
            </div>
        </div>
    </body>
</body>

</html>
