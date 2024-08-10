    var callStatus = $("#userphone");
    var answerButton = $("#answer-button");
    var hangUpButton = $("#endcall");
    var recordButton = $("#recordcall");
    var recordStopButton = $("#recordstopcall");
    var callCustomerButtons = $("#startcall");
    var device = null;
    var call_stream = null;
    var recorder = null;
    var twilio_connection = null;
    const mimeType = 'audio/ogg'; // audio/mpeg-3, audio/webm, audio/mp3, audio/wav,audio/x-mpeg-3,audio/mpeg
    let chunks = [];
    
    // channels = 1; //1 for mono or 2 for stereo
    // sampleRate = 44100; //44.1khz (normal mp3 samplerate)
    // kbps = 128; //encode 128kbps mp3
    // mp3encoder = new lamejs.Mp3Encoder(channels, sampleRate, kbps);
    // var mp3Data = [];
    // samples = new Int16Array(44100); //one second of silence (get your data from the source you have)
    // sampleBlockSize = 1152; //can be anything but make it a multiple of 576 to make encoders life easier
    // var mp3buf = [];

    var isRender = false;
    function updateCallStatus(status) {
    	//callStatus.val('');
    	callStatus.attr('placeholder', status);
    }

    $(document).ready(function(){
    	setupClient();


    	// $('#endcall').on('click',function(){
        //     /*    		var userphone = document.getElementById('userphone').value;
        //     var params = {"phoneNumber": userphone};*/
        //     $('#startcall').show();
        //     $('#endcall').hide();
        //     if(window.location.pathname.split("/").pop() == 'call_log'){
        //         document.getElementById("call_end_time").readOnly = false;
        //         var dt = new Date();
        //         var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
        //         var month = dt.getMonth()+1;
        //         var day = dt.getDate();
        //         var fdate = dt.getFullYear() + '-' +
        //         ((''+month).length<2 ? '0' : '') + month + '-' +
        //         ((''+day).length<2 ? '0' : '') + day;
        //         var fulldate = fdate + ' '+ time;
        //         $('#call_end_time').val(fulldate);
        //     }
            
        //     //device.disconnectAll();
        //     updateCallStatus("Call Ended");
        //     console.log(recorder);
        //      if(recorder && recorder.state == "active")
        //          recorder.stop();
        //     recorder = null;
        //     twilio_connection = null;
        //     call_stream = null;

        //     connection.reject();
        //     connection.disconnect();
        //     hangUpButton.hide();
        //     callCustomerButtons.show();
        //     recordButton.hide();
        //     recordStopButton.hide();
        //  }); 


    })
    function callCustomer()
    {
    	var userphone = document.getElementById('userphone').value;
    	if(userphone=='')
    	{
    		alert('please enter your phone number');
    	}
    	else
    	{
    		updateCallStatus("Calling " + userphone + "...");
    		var params = {"phoneNumber": userphone};
    		device.connect(params);
    	}
    }

	// new code
	function setupHandlers(device) {
		device.on('ready', function (_device) {
			updateCallStatus("Ready");
            hangUpButton.hide();
            recordButton.hide();
            answerButton.hide();
		});

		/* Report any errors to the call status display */
		device.on('error', function (error) {
			updateCallStatus("ERROR: " + error.message);
		});

		/* Callback for when Twilio Client initiates a new connection */
		device.on('connect', function (connection) {
            // Enable the hang up button and disable the call buttons
            hangUpButton.show();
            
            callCustomerButtons.hide();
            /*callSupportButton.prop("disabled", true);*/
            answerButton.hide();

            // If phoneNumber is part of the connection, this is a call from a
            // support agent to a customer's phone
            if ("phoneNumber" in connection.message) {
                updateCallStatus("In call with " + connection.message.phoneNumber);
            } else {
                // This is a call from a website user to a support agent
                updateCallStatus("In call with support");
            }
            twilio_connection = connection;
            
        //     //Twilio.Device.audio.inputStream
            call_stream =  connection.getLocalStream();
        //    // connection.getRemoteStream()
        //    recorder = new MediaRecorder(call_stream, { type: mimeType });

        //    // recorder.start();
        //     recorder.addEventListener('dataavailable', event => {
        //         if (typeof event.data === 'undefined') return;
        //         if (event.data.size === 0) return;
        //         chunks.push(event.data);
        //         });
        //     recorder.addEventListener('stop', () => {
        //         const recording = new Blob(chunks, {
        //         type: mimeType
        //         });
        //         //  
        //         renderRecording(recording);
        //         chunks = [];
        //     });
    
        connection.disconnect(function() {
            updateCallStatus("Call disconnected");
            hangUpButton.hide();   
            answerButton.hide();
            recordButton.hide();
            hangUpButton.hide();
            callCustomerButtons.show();
            recordButton.hide();
            recordStopButton.hide();
            
            if(recorder && recorder.state == "recording")
                recorder.stop();

            //tell the recorder to finish the recording (stop recording + encode the recorded audio)
	        // if(recorder)
            //     recorder.finishRecording();

            call_stream.getTracks().forEach( track => track.stop() );
            recorder = null;
            twilio_connection = null;
            call_stream = null;
        });

        hangUpButton.click(function(){
            updateCallStatus("Call Ended");


            if(window.location.pathname.split("/").pop() == 'call_log'){
                document.getElementById("call_end_time").readOnly = false;
                var dt = new Date();
                var time = dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();
                var month = dt.getMonth()+1;
                var day = dt.getDate();
                var fdate = dt.getFullYear() + '-' +
                ((''+month).length<2 ? '0' : '') + month + '-' +
                ((''+day).length<2 ? '0' : '') + day;
                var fulldate = fdate + ' '+ time;
                $('#call_end_time').val(fulldate);
            }


            connection.disconnect();
        
        });

            recordButton.show(); recordStopButton.hide();
            recordButton.click(function(){

                isRender = false;

                /* */
                
                recorder = new MediaRecorder(twilio_connection.getLocalStream(), { type: mimeType });
                // // start recording with 1 second time between receiving 'ondataavailable' events
                recorder.start(1000);
                recorder.addEventListener('dataavailable', event => {
                    if (typeof event.data === 'undefined') return;
                    if (event.data.size === 0) return;
                    chunks.push(event.data);

                    // For encoding mp3
                    // samples = event.data;
                    // for (var i = 0; i < samples.length; i += sampleBlockSize) {
                    //     sampleChunk = samples.subarray(i, i + sampleBlockSize);
                    //     mp3buf = mp3encoder.encodeBuffer(sampleChunk);
                    //     if (mp3buf.length > 0) {
                    //         mp3Data.push(mp3buf);
                    //     }
                    //   }
                    //   mp3buf = mp3encoder.flush();   //finish writing mp3
                      
                    //   if (mp3buf.length > 0) {
                    //       mp3Data.push(new Int8Array(mp3buf));
                    //   }

                    // 
                });
                recorder.addEventListener('stop', () => {
                    if(isRender) return; 
                    const recording = new Blob(chunks, {    // instead chunks
                    type: "audio/mp3"
                    });
                    //  
                    renderRecording(recording);
                    chunks = [];
                    isRender = true;
                });
                

                /*
                audioContext = new AudioContext();
                input = audioContext.createMediaStreamSource(twilio_connection.getLocalStream());
                encodingType = "mp3";

                recorder = new WebAudioRecorder(input, {
                    workerDir: base_url + "modules/call_logs/assets/js/", // must end with slash
                    encoding: encodingType,
                    numChannels:2, //2 is the default, mp3 encoding supports only 2
                    onEncoderLoading: function(recorder, encoding) {
                      // show "loading encoder..." display
                    },
                    onEncoderLoaded: function(recorder, encoding) {
                      // hide "loading encoder..." display
                    }
                  });
          
                  recorder.onComplete = function(recorder, blob) { 
                    if(isRender) return; 
                    renderRecording(blob);
                    isRender = true;
                  }
          
                  recorder.setOptions({
                    //timeLimit:120,
                    encodeAfterRecord:true,
                    ogg: {quality: 0.5},
                    mp3: {bitRate: 160}
                  });
          
                  //start the recording process
                  recorder.startRecording();
          
                  */

                $(this).hide();
                recordStopButton.show();
            });

            recordStopButton.click(function(){
                
                if(recorder && recorder.state == "recording")
                    recorder.stop();
                call_stream.getTracks().forEach( track => track.stop());    

                //tell the recorder to finish the recording (stop recording + encode the recorded audio)

                /*
                if(recorder)
                    recorder.finishRecording();
                */

                $(this).hide();
                recordButton.show();
                //recorder = null;
            });

            
        });

		/* Callback for when a call ends */
		device.on('disconnect', function(connection) {
            
        });

		/* Callback for when Twilio Client receives a new incoming call */
		device.on('incoming', function(connection) {
            console.log(connection);
            callCustomerButtons.hide();
            answerButton.show();
            hangUpButton.show();
            
            twilio_connection = connection;

            updateCallStatus("Incoming call");
            // Set a callback to be executed when the connection is accepted
            connection.accept(function() {
                updateCallStatus("In call with customer");
                answerButton.hide();
                callCustomerButtons.hide();
                hangUpButton.show();   
            });
            connection.reject(function() {
                updateCallStatus("Call Rejected");
                hangUpButton.hide();   
                answerButton.hide();
                recordButton.hide();
            });
            connection.cancel(function() {
                updateCallStatus("Call Cancel");
                hangUpButton.hide();   
                answerButton.hide();
                recordButton.hide();
            });
            connection.disconnect(function() {
                updateCallStatus("Call disconnected");
                hangUpButton.hide();   
                answerButton.hide();
                recordButton.hide();
            });
            
            hangUpButton.click(function(){
                updateCallStatus("Call Ended");
                    console.log(recorder);
                 if(recorder && recorder.state == "active")
                     recorder.stop();
                recorder = null;
                twilio_connection = null;
                call_stream = null;

                connection.reject();
                connection.disconnect();
                hangUpButton.hide();
                callCustomerButtons.show();
                recordButton.hide();
                recordStopButton.hide();

            });

            // Set a callback on the answer button and enable it
            answerButton.click(function() {
                callCustomerButtons.hide();
                connection.accept();
                hangUpButton.show();
            });

        });     
        
	};

	function setupClient() {
		$.post(admin_url+'call_logs/newToken', {
			forPage: window.location.pathname,
		}).done(function (data) {
            // Set up the Twilio Client device with the token
            device = new Twilio.Device();
            let obj = JSON.parse(data);
            device.setup(obj.token, { debug: true });
            setupHandlers(device);
        }).fail(function () {
            updateCallStatus("Could not get a token from server!");
        });
    };

/* Call the support_agent from the home page */
function callSupport() {
    updateCallStatus("Calling support...");

    // Our backend will assume that no params means a call to support_agent
    device.connect();
};

function renderRecording(blob) {
    const blobUrl = URL.createObjectURL(blob);
    const li = document.createElement('li');
    const audio = document.createElement('audio');
    audio.setAttribute('src', blobUrl);
    audio.setAttribute('controls', 'controls');
    li.appendChild(audio);
    
    const anchor = document.createElement('a');
    anchor.setAttribute('href', blobUrl);
    const now = new Date();
    anchor.setAttribute(
      'download',
      `recording-${now.getFullYear()}-${(now.getMonth() + 1).toString().padStart(2, '0')}-${now.getDay().toString().padStart(2, '0')}--${now.getHours().toString().padStart(2, '0')}-${now.getMinutes().toString().padStart(2, '0')}-${now.getSeconds().toString().padStart(2, '0')}.webm`
    );
    anchor.innerText = 'Download';
    
    //li.appendChild(anchor);
    // const delete_button = document.createElement('button');
    // delete_button.setAttribute('onclick', "deleteBlob()");
    // delete_button.innerText = 'Download';
    // li.appendChild(delete_button);

    document.getElementById('record_file_list').appendChild(li);
    recorded_blobs.push(blob);
    $("#lblRecordedCall").show();
  }
 // Not Using
  function uploadRecordedBlob(blob) {
    var data = new FormData();
    data.append('file', blob);
    
    $.ajax({
      url: admin_url + 'call_logs/upload_record',
      type: 'POST',
      data: data,
      contentType: false,
      processData: false,
      success: function(response) {
        // Sent to Server
      }
    });
  }
