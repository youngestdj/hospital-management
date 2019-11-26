<html>
  <head></head>
  <body style="background: white; color: #333">
    <div style="margin: auto; width: 500px">
      <h1>Account verified successfully!</h1>
      <p>Hello {{ $user['firstname'] }} {{ $user['lastname'] }}, your {{ strtolower($user['user']) }} account has been verified successfully.
    </div>
  </body>
</html>
