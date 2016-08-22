'use strict';
var cur_call = null;
var verto;
var chatting_with = false;

var callbacks = {
 onMessage: function(verto, dialog, msg, data) {
  console.error("msg ", msg);
  console.error("data ", data);
  switch (msg) {
   case $.verto.enum.message.pvtEvent:
    if (data.pvtData) {
     console.error("data.pvtData ", data.pvtData);
     switch (data.pvtData.action) {
      case "conference-liveArray-join":
       chatting_with = data.pvtData.chatID;
       $("#content").hide();
       $("#ask").hide();
       $("#br").hide();
       $("#backbtn").hide();
       $("#ext").hide();
       $("#extbtn").hide();
       $("#callbtn").hide();
       $("#cidname").hide();
       $("#hupbtn").show();
       $("#chatwin").show();
       $("#chatmsg").show();
       $("#chatsend").show();
       $("#webcam").show();
       $("#video1").show();
       break;
      case "conference-liveArray-part":
       $("#content").show();
       $("#ask").show();
       $("#ext").show();
       $("#extbtn").show();
       $("#cidname").hide();
       $("#callbtn").hide();
       $("#hupbtn").hide();
       $("#chatwin").hide();
       $("#chatmsg").hide();
       $("#chatsend").hide();
       $("#backbtn").hide();
       $("#webcam").hide();
       $("#video1").hide();
       cur_call = null;
       break;
     }
    }
    break;
   case $.verto.enum.message.info:
    var body = data.body;
    var from = data.from_msg_name || data.from;

    if (body.slice(-1) !== "\n") {
     body += "\n";
    }
    $('#chatwin')
     .append(from + ': ')
     .append(body)
     $('#chatwin').animate({"scrollTop": $('#chatwin')[0].scrollHeight}, "fast");
    break;
   default:
    break;
  }
 },
 onEvent: function(v, e) {
  console.error("GOT EVENT", e);
 },
 onDialogState: function(d) {
  if (!cur_call) {
   cur_call = d;
  }
  switch (d.state) {
   case $.verto.enum.state.hangup:
    $("#content").show();
    $("#ask").show();
    $("#ext").show();
    $("#extbtn").show();
    $("#cidname").hide();
    $("#callbtn").hide();
    $("#hupbtn").hide();
    $("#chatwin").hide();
    $("#chatmsg").hide();
    $("#chatsend").hide();
    $("#backbtn").hide();
    $("#webcam").hide();
    $("#video1").hide();
    cur_call = null;
    console.error("HANGUP");
    break;

   case $.verto.enum.state.destroy:
    $("#content").show();
    $("#ask").show();
    $("#ext").show();
    $("#extbtn").show();
    $("#cidname").hide();
    $("#callbtn").hide();
    $("#hupbtn").hide();
    $("#chatwin").hide();
    $("#chatmsg").hide();
    $("#chatsend").hide();
    $("#backbtn").hide();
    $("#webcam").hide();
    $("#video1").hide();
    cur_call = null;
    console.error("DESTROY");
    break;
   case $.verto.enum.state.active:
    $("#content").hide();
    $("#ask").hide();
    $("#br").hide();
    $("#backbtn").hide();
    $("#ext").hide();
    $("#extbtn").hide();
    $("#callbtn").hide();
    $("#cidname").hide();
    $("#hupbtn").show();
    $("#chatwin").show();
    $("#chatmsg").show();
    $("#chatsend").show();
    $("#webcam").show();
    $("#video1").show();
    console.error("ACTIVE");
    break;
   default:
    break;
  }
 },
};

function docall() {
 if (cur_call) {
  return;
 }
 cur_call = verto.newCall({
  destination_number: $("#ext").val(),
  caller_id_name: $("#cidname").val(),
  caller_id_number: $("#cidnumber").val(),
  useVideo: true,
  useStereo: false,
  useCamera: $("#usecamera").find(":selected").val(),
  useMic: $("#usemic").find(":selected").val()
 });
}

$("#callbtn").click(function() {
 if($("#cidname").val() ){
  docall();
 }
});

$("#hupbtn").click(function() {
 verto.hangup();
 cur_call = null;
});

$("#extbtn").click(function() {
 if($("#ext").val()){
  $("#ext").hide();
  $("#extbtn").hide();
  $("#cidname").show();
  $("#callbtn").show();
  $("#backbtn").show();
  $("#cidname").focus();
  $("#br").show();
 }
});

$("#backbtn").click(function() {
 $("#ext").show();
 $("#extbtn").show();
 $("#cidname").hide();
 $("#callbtn").hide();
 $("#hupbtn").hide();
 $("#chatwin").hide();
 $("#chatmsg").hide();
 $("#chatsend").hide();
 $("#backbtn").hide();
 cur_call = null;

});

function setupChat() {
 $("#chatwin").html("");

 $("#chatsend").click(function() {
  if (!cur_call && chatting_with) {
   return;
  }
  cur_call.message({to: chatting_with,
   body: $("#chatmsg").val(),
   from_msg_name: cur_call.params.caller_id_name,
   from_msg_number: cur_call.params.caller_id_number
  });
  $("#chatmsg").val("");
 });

 $("#chatmsg").keyup(function (event) {
  if (event.keyCode == 13 && !event.shiftKey) {
   $( "#chatsend" ).trigger( "click" );
  }
 });
}

function init() {
 cur_call = null;

 verto = new $.verto({
  login: $("#login").val() + "@" + $("#hostName").val(),
  passwd: $("#passwd").val(),
  socketUrl: $("#wsURL").val(),
  tag: "webcam",
  iceServers: true
 },callbacks);

 $("#ext").keyup(function (event) {
  if (event.keyCode == 13 && !event.shiftKey) {
   $( "#extbtn" ).trigger( "click" );
  }
 });

 $("#cidname").keyup(function (event) {
  if (event.keyCode == 13 && !event.shiftKey) {
   $( "#callbtn" ).trigger( "click" );
  }
 });

 $(document).keypress(function(event) {
  var key = String.fromCharCode(event.keyCode || event.charCode);
  var i = parseInt(key);

  if (key === "#" || key === "*" || key === "0" || (i > 0 && i <= 9)) {
   cur_call.dtmf(key);
  }
 });

 setupChat();
}

$(window).load(function() {
 cur_call = null;
 $("#conference").show();
 $("#backbtn").hide();
 $("#cidname").hide();
 $("#callbtn").hide();
 $("#hupbtn").hide();
 $("#chatwin").hide();
 $("#chatmsg").hide();
 $("#chatsend").hide();
 $("#webcam").hide();
 $("#video1").hide();
 init();
});

