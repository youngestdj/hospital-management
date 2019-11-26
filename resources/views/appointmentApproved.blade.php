<html>
  <head></head>
  <body style="background: white; color: #333">
    <div style="margin: auto; width: 500px">
      <h1>Appointment approved</h1>
      <p>
        Hello {{ $appointment['firstname'] }} {{ $appointment['lastname'] }}, your appointment has been approved.  {{ array_key_exists('date', $appointment) ? 'Your appointment as been rescheduled to '. $appointment['date']: null }}
      </p>
    </div>
  </body>
</html>
