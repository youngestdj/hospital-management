<html>
  <head></head>
  <body style="background: white; color: #333">
    <div style="margin: auto; width: 500px">
      <h1>Appointment Scheduled</h1>
      <p>
        Hello Dr. {{ $doctor['firstname'] }} {{ $doctor['lastname'] }}, a patient's appointment has been assigned to you. Here are the details: <br>
        <b>Patient's name: </b> {{ $patient['firstname'] }} {{ $patient['lastname'] }} <br>
        <b>Date:</b> {{ $date }}
      </p>
    </div>
  </body>
</html>
