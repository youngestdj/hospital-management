<html>
  <head></head>
  <body style="background: white; color: #333">
    <div style="margin: auto; width: 500px">
      <h1>Your prescription</h1>
      <p>
        Hello {{ $user['firstname'] }} {{ $user['lastname'] }}, here is your doctor's prescription.
      </p>
      <p>
        {{ $history['prescription'] }}
      </p>
    </div>
  </body>
</html>
