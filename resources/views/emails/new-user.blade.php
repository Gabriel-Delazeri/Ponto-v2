<!DOCTYPE html>
<h1>Welcome {{ $user->first_name }}</h1>

<h2>Your Credentials</h2>

<p>First Name: {{ $user->first_name }}</p>
<p>Surname: {{ $user->surname }}</p>
<p>Email: {{ $user->email }}</p>
<p>Password: {{ $password }}</p>
</html>
