<?php
  include 'Services/Twilio/Capability.php';
   
  // put your Twilio API credentials here
  $accountSid = getenv('TWILIO_ACCT_SID');
  $authToken = getenv('TWILIO_AUTH_TOKEN');
  $appSid = getenv('TWILIO_CLIENT_APP_SID');
  
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

      Twilio.Device.setup('<?php echo $capability_token; ?>');

      Twilio.Device.ready(function (device) {
        $('#log').text('Ready');
      });

      Twilio.Device.error(function (error) {
        $('#log').text('Error: ' + error.message);
      });

      Twilio.Device.connect(function (conn) {
        $('#log').text('Successfully established call');
        $('#toggle_call').text = "Hangup";
      });

      Twilio.Device.disconnect(function (conn) {
        $("#log").text("Call ended");
        $('#toggle_call').text = "Call";
      });

      Twilio.Device.cancel(function(conn) {
        $("#log").text("Call ended on the other end");
      });

      function togglecall() {
        if (Twilio.Device.status() === 'busy') {
          Twilio.Device.disconnectAll();
        }
        else {
          params = {"PhoneNumber": $("#call_number").val()};
          Twilio.Device.connect(params);
        }
      }
    </script>
  </head>
  <body>
    <div class="container">
      <!--<div class="logo-container">
        <img src="img/tor.png" alt="" style="display:inline;" />
      </div>-->
      <h1>A Tel Called Nowhere</h1>
      <p>We're all Reyjavikians now.</p>
      <p><label for="call_number">Phone Number:</label>
      <input type="tel" id="call_number" name="call_number" maxlength="10" autofocus="autofocus" placeholder="e.g. 917-746-5859" />
      <button id="toggle_call" name="toggle_call" onclick="togglecall();">Call</button></p>
      <!--<ul>
        <li>1</li>
        <li>2</li>
        <li>3</li>
        <li>4</li>
        <li>5</li>
        <li>6</li>
        <li>7</li>
        <li>8</li>
        <li>9</li>
        <li>*</li>
        <li>0</li>
        <li>#</li>
      <ul>-->
      <div id="log"></div>
      <small><a href="https://www.flickr.com/photos/didmyself/6377889005/">Photo</a> by Daniel Kulinski (CC BY-NC-SA 2.0)</small>
    </div>
  </body>
</html>
