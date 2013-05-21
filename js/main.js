$(document).ready(function() {
  var connection = null;

  Twilio.Device.ready(function (device) {
    $('#status, #status-light').removeClass('badnews').addClass('goodnews');
    $('#status').text('Ready');
  });

  Twilio.Device.error(function (error) {
    $('#status, #status-light').removeClass('goodnews').addClass('badnews');
    $('#status').text('Error: ' + error.message);
  });

  Twilio.Device.connect(function (conn) {
    $('#status, #status-light').removeClass('badnews').addClass('goodnews');
    $('#status').text('Call established');
  });

  Twilio.Device.disconnect(function (conn) {
    $('#status, #status-light').removeClass('goodnews').addClass('badnews');
    $("#status").text("Call ended");
  });

  Twilio.Device.cancel(function (conn) {
    $('#status, #status-light').removeClass('goodnews').addClass('badnews');
    $("#status").text("Got hung up on");
  });

  function startCall() {
    var params = {"PhoneNumber": "+1" + $("#call_number").val()};
    connection = Twilio.Device.connect(params);
  }

  function endCall() {
    Twilio.Device.disconnectAll();
  }

  $('#start_call').click(startCall);
  $('#end_call').click(endCall);

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