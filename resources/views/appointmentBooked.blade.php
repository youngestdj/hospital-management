<html>
  <head></head>
  <body style="background: white; color: #333">
    <div style="margin: auto; width: 500px">
      <h1>Appointment booked</h1>
      <p>
        Hello {{ $user['firstname'] }} {{ $user['lastname'] }}, your appointment has been booked. You will be assigned a doctor and you'll receive a mail with more details.
      </p>
    </div>
  </body>
</html>
