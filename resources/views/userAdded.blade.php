<html>
  <head></head>
  <body style="background: white; color: #333">
    <div style="margin: auto; width: 500px">
      <h1>Verify your email</h1>
      <p>Hello {{ $userDetails['firstname'] }} {{ $userDetails['lastname'] }}, your {{ strtolower($userDetails['user']) }} account has been added.
        Please click <a href="api/v1/{{ $userDetails['user'] }}/verify/{{ $userDetails['key'] }}">here</a> to verify your email and setup your account.</p>
    </div>
  </body>
</html>
