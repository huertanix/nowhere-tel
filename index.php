<?php
  include 'Services/Twilio/Capability.php';
   
  // put your Twilio API credentials in a environment variable to prevent git commit leaks
  $accountSid = $_SERVER['TWILIO_ACCT_SID'];
  $authToken = $_SERVER['TWILIO_AUTH_TOKEN'];
  $appSid = $_SERVER['TWILIO_CLIENT_APP_SID'];
  
  $capability = new Services_Twilio_Capability($accountSid, $authToken);
  $capability->allowClientOutgoing($appSid);
  $capability_token = $capability->generateToken();
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Nowhere Tel</title>
    <meta name="description" content="Call Anywhere From Nowhere">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/main.css">
    <script src="js/vendor/modernizr-2.6.2.min.js"></script>
    <script src="js/vendor/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="//static.twilio.com/libs/twiliojs/1.1/twilio.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
      Twilio.Device.setup('<?php echo $capability_token; ?>');
      var connection = null;

      Twilio.Device.ready(function (device) {
        $('#status, #status-light').removeClass('badnews').addClass('goodnews');
        $('#status').text('Ready');
      });

      Twilio.Device.error(function (error) {
        $('#status').removeClass('goodnews').addClass('badnews');
        $('#status').text('Error: ' + error.message);
      });

      Twilio.Device.connect(function (conn) {
        $('#status').removeClass('badnews').addClass('goodnews');
        $('#status').text('Call established');
      });

      Twilio.Device.disconnect(function (conn) {
        $('#status').removeClass('goodnews').addClass('badnews');
        $("#status").text("Call ended");
      });

      Twilio.Device.cancel(function (conn) {
        $('#status').removeClass('goodnews').addClass('badnews');
        $("#status").text("Got hung up on");
      });

      function startCall() {
        var params = {"PhoneNumber": "+1" + $("#call_number").val()};
        connection = Twilio.Device.connect(params);
      }

      function endCall() {
        Twilio.Device.disconnectAll();
      }

      $('#call_number').keypress(function(ev) {
        var charCode = ev.which
        if (charCode > 31 && (charCode < 48 || charCode > 57))
          return false;

        return true;
      });

      $('#dialpad button').each ( function(index, el) {
        $(this).click( function() {
          var current_number = $('#call_number').val();
          var button_value = this.firstChild.nodeValue;

          if (connection) {
            connection.sendDigits(button_value);
          }

          if (/^\d+$/.test(button_value)) {
            current_number = current_number.toString() + button_value;
            $('#call_number').val(current_number);
          }
        });
      });
    });
    </script>
  </head>
  <body>
    <div class="container">
      <div class="logo-container">
        <img src="img/nowhere_tel_logo.png" alt="BFE" />
        <h1>Nowhere Tel</h1>
        <h2>Where you at? Behind 7 proxies!&#0153;</h2>
      </div>
      <p>
        <div class="status-indicator">
          <span id="status-light"></span><span id="status"></span>
        </div>
        <label for="call_number">Phone Number (US Only):</label>
        <input type="tel" id="call_number" name="call_number" maxlength="10" pattern="\d*" autofocus="autofocus" 
          placeholder="e.g. 9177465859" />
        <!--<button class="metal radial">✈</button>
        <button class="metal linear">0</button>
        <a href="http://simurai.com/post/9214147117/css3-brushed-metal" class="metal linear oval">i</a>-->
      </p>
      <div class="controls-container">
        <div id="dialpad">
          <button>1</button>
          <button>2</button>
          <button>3</button>
          <button>4</button>
          <button>5</button>
          <button>6</button>
          <button>7</button>
          <button>8</button>
          <button>9</button>
          <button>*</button>
          <button>0</button>
          <button>#</button>
        </div>
        <div class="dialcontrols">
          <button id="start_call" name="start_call" onclick="startCall();">Call</button>
          <button id="end_call" name="end_call" onclick="endCall();">Hangup</button>
        </div>
      </div>
      <p>
        <small><b>DISCLAIMER</b>: This is an experimental proof-of-concept and cannot guarantee anything, 
        including location anonymity. This is just the first step.</small>
      </p>
    </div>
  </body>
</html>
