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
       $("#cidname").hide();
       $("#hupbtn").hide();
       $("#chatwin").hide();
       $("#chatmsg").hide();
       $("#chatsend").hide();
       $("#backbtn").show();
       $("#webcam").hide();
       $("#video1").hide();
    $("#login").hide();
    $("#loginbtn").hide();
    $("#callbtn").show();
    $("#ext").show();
  $("#ext").focus();
       cur_call = null;
chatting_with = false;
       break;
     }
    }
    break;

   case $.verto.enum.message.display:
       console.error("display");
	//dialog.handleDisplay();
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
   case $.verto.enum.state.ringing:
    console.error("RINGING");

    $("#webcam").show();
    $("#video1").show();
//setTimeout(function(){ 
                    cur_call.answer({
                    callee_id_name: "ciao",
                    callee_id_number: "1234567",
                        useVideo: true,
    //dedEnc: false,
     //                   mirrorInput: true,
                        useStereo: true,
                        useCamera: true,
                        //useSpeak: true,
                        useMic: true
                    });

  //}, 3000); 

/*
*/
 

    break;
   case $.verto.enum.state.hangup:
    $("#content").show();
    $("#ask").show();
    $("#cidname").hide();
    $("#hupbtn").hide();
    $("#chatwin").hide();
    $("#chatmsg").hide();
    $("#chatsend").hide();
    $("#backbtn").show();
    $("#webcam").hide();
    $("#video1").hide();
    $("#login").hide();
    $("#loginbtn").hide();
    $("#callbtn").show();
    $("#ext").show();
  $("#ext").focus();
    cur_call = null;
    console.error("HANGUP");
    break;

   case $.verto.enum.state.destroy:
    $("#content").show();
    $("#ask").show();
    $("#cidname").hide();
    $("#hupbtn").hide();
    $("#chatwin").hide();
    $("#chatmsg").hide();
    $("#chatsend").hide();
    $("#backbtn").show();
    $("#webcam").hide();
    $("#video1").hide();
    $("#login").hide();
    $("#loginbtn").hide();
    $("#callbtn").show();
    $("#ext").show();
  $("#ext").focus();
    cur_call = null;
chatting_with = false;
    console.error("DESTROY");
    break;
   case $.verto.enum.state.active:
    $("#content").hide();
    $("#ask").hide();
    $("#br").hide();
    $("#backbtn").hide();
    $("#ext").hide();
    $("#callbtn").hide();
    $("#cidname").hide();
    $("#hupbtn").show();
    $("#chatwin").hide();
    $("#chatmsg").hide();
    $("#chatsend").hide();
if(chatting_with) {

    $("#chatwin").show();
    $("#chatmsg").show();
    $("#chatsend").show();
}
    $("#webcam").show();
    $("#video1").show();


 $(document).keypress(function(event) {
  var key = String.fromCharCode(event.keyCode || event.charCode);
  var i = parseInt(key);
  var tag = event.target.tagName.toLowerCase();

if ( tag != 'input') {
  if (key === "#" || key === "*" || key === "0" || (i > 0 && i <= 9)) {
   cur_call.dtmf(key);
  }
}
 });

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
  caller_id_name: $("#login").val(),
  caller_id_number: $("#login").val(),
  useVideo: true,
  useStereo: true,
  useCamera: true,
  useMic: true
 });
}

$("#callbtn").click(function() {
 if($("#ext").val() ){
  docall();
 }
});

$("#hupbtn").click(function() {
 verto.hangup();
 cur_call = null;
chatting_with = false;
  $("#br").show();
  $("#ext").show();
  $("#ext").focus();
});

$("#loginbtn").click(function() {
		if($("#login").val()){
		init();
		$("#loginbtn").hide();
		$("#login").hide();
		$("#cidname").show();
		$("#callbtn").show();
		$("#backbtn").show();
		$("#br").show();
		$("#ext").show();
		$("#ext").focus();
		}
		});




$("#backbtn").click(function() {
 $("#login").show();
 $("#loginbtn").show();
 $("#ext").hide();
 $("#cidname").hide();
 $("#callbtn").hide();
 $("#hupbtn").hide();
 $("#chatwin").hide();
 $("#chatmsg").hide();
 $("#chatsend").hide();
 $("#backbtn").hide();
 cur_call = null;
chatting_with = false;

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
chatting_with = false;

 verto = new $.verto({
  login: $("#login").val() + "@" + $("#hostName").val(),
  passwd: $("#passwd").val(),
  socketUrl: $("#wsURL").val(),
  tag: "webcam",
  iceServers: true
 },callbacks);

 $("#cidname").keyup(function (event) {
  if (event.keyCode == 13 && !event.shiftKey) {
   $( "#callbtn" ).trigger( "click" );
  }
 });

 setupChat();
}

$(window).load(function() {
 cur_call = null;
chatting_with = false;
 $("#conference").show();
 $("#ext").hide();
 $("#backbtn").hide();
 $("#cidname").hide();
 $("#callbtn").hide();
 $("#hupbtn").hide();
 $("#chatwin").hide();
 $("#chatmsg").hide();
 $("#chatsend").hide();
 $("#webcam").hide();
 $("#video1").hide();
// init();
$("#login").keyup(function (event) {
  if (event.keyCode == 13 && !event.shiftKey) {
   $( "#loginbtn" ).trigger( "click" );
  }
 });

 $("#ext").keyup(function (event) {
  if (event.keyCode == 13 && !event.shiftKey) {
   $( "#callbtn" ).trigger( "click" );
  }
 });



});


