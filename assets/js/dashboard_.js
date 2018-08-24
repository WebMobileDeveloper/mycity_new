var siteurl = "//" + window.location.hostname + "/" ; 
 
var aurl = "//" + window.location.hostname + "/api/api.php/" ; 
var ajurl = "//" + window.location.hostname  + "/" ; 
 	 	
var mid;
var mremail;
var musername;
var token;
var mrole;

var decodedCookie = decodeURIComponent(document.cookie);
var ca = decodedCookie.split(';');
var _rmtoken ;

$(document).ready(function(){
	
	$('#vocation_names').DataTable( {
        "paging":   false,
        "ordering": false,
        "info":     false
    });
	
	$('#city_names').DataTable( {
        "paging":   false,
        "ordering": false,
        "info":     false
    });
	
	
	$('#targetclient_names').DataTable( {
        "paging":   false,
        "ordering": false,
        "info":     false
    });
	
	$('#targetref_names').DataTable( {
        "paging":   false,
        "ordering": false,
        "info":     false
    });
	  
	$('.notifications, .contex-menu').remove();
    $('body').append('<div class="notifications"></div>' +
        '<div id="waitFunction">Please wait...</div>'); // wait function div
    $('.alert').hide(); 
	 



	
	
for(var i = 0; i < ca.length; i++) 
{
		var c = ca[i];
        key = ca[i].split('=')[0];  
        if(key.trim() === "_mcu")
        {
			cvalue = ca[i].substring(  6, ca[i].length   ); 
        }
		var citem = c.split('='); 
		if(citem[0].trim() == "_rmtoken")
		{
			_rmtoken = citem[1].trim();
		}
    }
 
    if(typeof cvalue   === 'undefined' )
    { 

    }
    else
    { 
        cvalue = $.parseJSON(cvalue);
        mid = cvalue.id;
        mremail = cvalue.email;
        musername = cvalue.name;
        token = cvalue.token; 
        mrole =  cvalue.role;
}

});



	// email validation
function validateEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

// Wait function
function waitFunc(status) {
    if (status == 'enable') {
        $('body').css('cursor', 'wait');
        $('body > *').css('pointer-events', 'none');
        $('#waitFunction').fadeIn(500); // show please wait
    } else {
        $('body').css('cursor', 'initial');
        $('body > *').css('pointer-events', 'auto');
        $('#waitFunction').fadeOut(500); // hide please wait
    }
} 

//Show alert
function alertFunc(color, msg) {
    var alert = $('<div class="alert alert-' + color + ' animated bounce"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + msg + '</div>').hide();
    var timeOut;
    alert.appendTo('.notifications');
    alert.slideDown();

    //Is autoclosing alert
    var delay = 10000;
    if (delay != undefined) {
        delay = parseInt(delay);
        clearTimeout(timeOut);
        timeOut = window.setTimeout(function() {
            alert.fadeOut(200, function() {
                $(this).remove();
            });
        }, delay);
    }
    // remove last notification if more then six
    var countAlert = $(".notifications").children().length;
    if (countAlert > 6) {
        $(".notifications .alert").first().remove();
    }
} 

// Delete function
function dltFunc(text, func) {
    $('body > #confirm-box').remove(); 
    $('body').append('<div class="modal fade" id="confirm-box" >' +
        '<div class="modal-dialog modal-sm">' +
        '<div class="modal-content">' +
        '<div class="modal-body">' + text +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-default btn-confirm" data-confirm="no">Cancel</button>' +
        '<button type="button" class="btn btn-danger btn-confirm" data-confirm="yes">Delete</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>');

    $('#confirm-box').modal({ backdrop: 'static', keyboard: false, show: true }); // open lightbox

    $('#confirm-box .btn-confirm').click(function(e) {
        e.stopImmediatePropagation();
        var getConf = $(this).attr('data-confirm');
        if (getConf == 'yes') {
            func();
        }
        $('#confirm-box').modal('hide');
    });
}


// confirm Yes/No function
function confFunc(text, func)
{
	$('body > #confirm-box').remove(); 
    $('body').append('<div class="modal fade" id="confirm-box" >' +
        '<div class="modal-dialog modal-sm">' +
        '<div class="modal-content">' +
        '<div class="modal-body">' + text +
        '</div>' +
        '<div class="modal-footer">' +
        '<button type="button" class="btn btn-default btn-confirm" data-confirm="no">No</button>' +
        '<button type="button" class="btn btn-success btn-confirm" data-confirm="yes">Yes</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>');

    $('#confirm-box').modal({ backdrop: 'static', keyboard: false, show: true }); // open lightbox

    $('#confirm-box .btn-confirm').click(function(e) {
        e.stopImmediatePropagation();
        var getConf = $(this).attr('data-confirm');
        if (getConf == 'yes') {
            func();
        }
        $('#confirm-box').modal('hide');
    });
}



$(document).on('click', '.pagiknows li a', function()
{
	var pageno = $(this).data('pg');
    if (pageno == 0) pageno = 1;  
	var ssf = $('#hidsf').val(); 
	if(typeof ssf === "undefined")
	{
		ssf =0;
	}
	
	var form = $('<form action="' + siteurl + 'dashboard/referrals" method="post">' +
        '<input type="hidden" name="activepage" value="' + pageno + '" />' +
		'<input type="hidden" name="ssf" value="' + ssf + '" />' +
        '</form>');
    $('body').append(form);
    form.submit();
});


$(document).on('click', '.btn3taddrelationship', function () 
{
	var relatelist = [];
	$('input[name=cb_relatedmembers]').each(function()
	{
		if(this.checked )
			relatelist.push( $(this).val() ); 
	});
	  
	if(relatelist.length > 0)
	{
		$.ajax({
			type: 'post',
				url: aurl + 'program/participant/addtracking/',
				data: { ids : relatelist.join(","), mid:mid, pid: 1},
				success: function(data) 
				{
					if (data == 'user_error') 
					{
						alertFunc('danger', 'Something went wrong, please try again');
					}
					else 
					{
						alertFunc('success', '3 Touch Program Relationships are saved!'); 
					}
					waitFunc(''); 
					 window.open(siteurl+'program/relations','_self');
				} 
		});  
	} 
})


function reloadtrack3tprogress(pid, cid, name)
{
	$('.btnadd3tq').attr('data-id', cid);
	$('.btnadd3tq').attr('data-name', name);
	$('.btndel3tq').attr('data-id', cid);
	$('.btndel3tq').attr('data-name', name);
	
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/checkrelation/',
		data:  { idtotrack:cid, mid: mid, pid: 1 } , 
		success: function(data) 
		{
			data = $.parseJSON(data);  
			if(data.allow ==0)
			{
				alertFunc('success', "Selected participant has not been added in your watched list.");  
				$('.tl-box').hide();
				$('#program-tl').empty();
				$('#programprogress').html('');				
			}
			else 
			{
				alertFunc('success', "Selected participant's program progress fetched. Scroll down to see report.");    
				prepareprogress(pid, cid, name);
			}
		} 
	}); 
}


function prepareprogress(pid, cid, name)
{
	$('#programprogress').html("<div ><img   src='../images/processing.gif' alt='Loading ...' width='160px' /></div>");
	$('.tl-box').hide();
	$('#program-tl').empty(); 
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/getassignedquestions/',
		data: {  pid : pid , mid:mid, relid:cid},
		success: function (data) 
		{
			data = $.parseJSON(data); 
			if(data.count > 0)
			{ 
				$.each(data.results, function(index, item)
				{
					if( item.a == ""|| item.a == null )
					{
						nulitem = "<li ><span></span>" ; 
						answer  = "<br/><strong>ANSWER: </strong> ";  
						answer += "<textarea  style='resize: vertical !important;' id='ans" + item.i + "' class='form-control'></textarea>";
					}
					else 
					{
						answer  = "";
						nulitem = "<li class='processed'><span></span>" ;
						answer  = "<br/><strong>ANSWER: </strong>"  ; 
						answer += "<textarea  style='resize: vertical !important;' id='ans" + item.i + "'  class='form-control'>" + item.a + "</textarea>"; 
					}
					buttons  = "<hr/><button data-qno='" +  item.qno + "' data-desc='" +  item.a + "'  data-id='" +  item.i + "' class='btn btn-primary btn-xs btnsv3tans'>Save Answer</button>" ;
					nulitem += "<div class='title'>Question #" + (index +1) + "</div>" + 
					"<div class='info'>"+ item.q ;
					nulitem += answer ;
					nulitem += (  item.af  == 'null' || item.af  == '' || item.af  ==  null   ? '' : item.af );
					nulitem +=  buttons + " </div> </li>" ;
					$('#program-tl').append(nulitem);  
				}) 
				
				$('#program-tl').attr('data-seq', data.count); 
			} 
			else 
			{
				var d = new Date();
				var curr_date = d.getDate();
				var curr_month = d.getMonth() + 1;  
				var curr_year = d.getFullYear();
				nulitem = "<li><span></span>" +
				"<div class='title'>Question #0</div>" +
				"<div class='info'>No Question Added Yet</div>" + 
				"</li>" ; 
			    $('#program-tl').append(nulitem);
				$('#program-tl').attr('data-seq', 0); 				
			}  
			$('.tl-box').show(); 
			$('#3tp_progress').html(" for " + name );
			$('#programprogress').html("");
		}
	});	 
}

$(document).on('click', '.btnadd3tq', function() 
{
	var pid = '1';
	var relid = $(this).attr('data-id');
	var mname = $(this).attr('data-name');
	
	$('.up3tquestions').attr('data-relid', relid);
	$('.up3tquestions').attr('data-name', mname);
	 
	
	laodprogramquestions( pid, relid, '3tqa' )
	$('#modal3tquestion').modal('show');
});
function laodprogramquestions( pid, relid , target)
{
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/getquestions/',
		data: {pid: pid, mid: relid },
		success: function (data) 
		{
			data = $.parseJSON(data);  
		 
			html =  "<table class='table table-responsive table-bordered'>";
			html += "<tr ><th>Select</th><th>Question</th> </tr>"  ;  
			$.each(data.results, function (index, item) 
			{
				if(item.asg == 1)
				{
					var selected ="checked";
				}
				else
				{
					var selected ="";
				}
				html += "<tr id='row" + index + "'>" + 
				"<td>" + 
				"<input "  +   selected +    " type='checkbox' class='cbpqid' data-qid='" +  item.i + "' /> " + "</td>" +
				"<td>" + item.q + "</td>"   ; 
				html +=  " </tr>"; 
			}); 
			html += "</table>";
			html +="<strong>Selected questions will be asked to the participant. If you want to remove a question from asking to the selected participant, simply deselect the question.</strong>" ;
			$('#' + target).html(html);   
		}
	}); 
} 

$(document).on('click', '.up3tquestions', function()
{ 
	var relid = $(this).attr('data-relid'); 
	var name = $(this).attr('data-name');
	var pid  =  1; 
	var myObj = {};
	var i=0;
	$(".cbpqid").each(function()
	{
		myObj["q_" + i] =  $(this).attr('data-qid');  
		if($(this).is(':checked'))
		{
			myObj["act_" + i] = "1";  
		}
		else
		{
			myObj["act_" + i] = "0";  
		}		
		i++;
	})
	myObj["totalq"] = i ;
	myObj["pid"] = pid;
	myObj["mid"] =  mid; 
    myObj["relid"] =  relid;
	var json = JSON.stringify(myObj);
	 
	$.ajax({
		type: 'post',
		url: aurl + 'member/program/questions/assign/',
		data:  json ,
		contentType: "application/json",
		success: function(data) 
		{
			data = $.parseJSON(data);  
			waitFunc(''); 
			alertFunc('success', data.errmsg);   
			 
		} 
	}); 
})

$(document).on('click', '.btnsv3tans', function() 
{
	var quesid = $(this).attr('data-id');
	var ans = $('#ans' + quesid).val();
	
	var qno = $(this).attr('data-qno');
	var add_ans = {};
	var ans_json='';
	if(qno == 3)
	{
		//read checkboxes 
		$("input[name='meet_state']").each( function () 
		{  
			if($(this).prop('checked') == true)
			{
				add_ans[  $(this).attr('data-field')   ] =  $(this).val()  ; 
            } 
		}); 
	}
	else  if(qno == 4)
	{
		//read checkboxes 
		$("input[name='follow_up']").each( function () 
		{
			if($(this).prop('checked') == true)
			{
				add_ans[  $(this).attr('data-field')   ] =  $(this).val()  ; 
            } 
		});
		$("input[name='follow_result']").each( function () 
		{  
			if($(this).prop('checked') == true)
			{
				add_ans[  $(this).attr('data-field')   ] =  $(this).val()  ; 
            } 
		}); 
	}  
	
	ans_json = JSON.stringify(add_ans);
	
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/answer/save/',
		data:  { quesid:quesid, ans: ans, add_ans:ans_json } , 
		success: function(data) 
		{
			data = $.parseJSON(data);  
			alertFunc('success', data.errmsg);      
		} 
	});
});


//suggestion wizard
function ref_wizard() 
{
	var options = {
        url: function(phrase) 
		{
             return siteurl + "my-network/autocomplete_name?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list: {
            onSelectItemEvent: function() {
                var value = $("#provider-remote").getSelectedItemData();
                $('#rmid').val(value.code); 
            }
        } 
    }; 
	 
	
    $("#provider-remote").easyAutocomplete(options); 
    $('#suggestwizard').modal('show');
    //waitFunc('enable'); 
} 


$(document).on('click', '.wiz_step1_show_member', function()
{
	 
	$('#wiz_memberleft').empty();
    $('#wizsummary').html('');
    $('#wiz_summary').empty();
	$('#wiz_summary').html('');
    $('#wiz_refmembers').empty();
    $('#wiz_membertointroduce').empty();
	
    var profession = $('#wiz_profession').chosen().val() + '';
    $('#wizstep1').removeClass('disabled');
    $('#wizstep1').addClass('complete');
	
	$.ajax({
        type: 'post',
        url: aurl + 'member/getbyvocations/',
        data: { wizstepfetchmember: 1, vocations: profession, userid: mid },
        success: function(data)
		{
			data = $.parseJSON(data);
            //console.log(data)
            var dropdown = $('#wiz_memberleft');
            dropdown.empty();
            $.each( data , function(key, value) {
                $("#wiz_memberleft").append($('<option></option>').val(value.id).html(value.username));
				$("#wiz_memberleft").trigger("chosen:updated");
			});  
            var config = {
                '.wiz_memberleft': {},
                '.wiz_memberleft-deselect': { allow_single_deselect: true },
                '.wiz_memberleft-no-single': { disable_search_threshold: 10 },
                '.wiz_memberleft-no-results': { no_results_text: 'Oops, nothing found!' },
                '.wiz_memberleft-width': { width: "95%" }
            }
            for (var selector in config) {
                $(selector).chosen(config[selector]);
            } 
        }
    });
}) 

$(document).on('click', '.wiz_step_show_summary', function() 
{
    var memberleft = $(".wiz_memberleft").chosen().val();
    var memberright = $("#rmid").val();
    
    if (!memberleft || !memberright)
    {
        alert('Missing member selection!')
    }
	else if ( memberleft == memberright) {
        alert('Self introduction is not permitted!')
    } 
    else 
    {
        
        $.ajax({
            type: 'post',
            url: aurl + 'contact/getbyids/',
            data: { wiz_summary: 1, cid: ( memberleft +"," + memberright ) },
            success: function(data) {
            data = $.parseJSON(data); 
 
     dataproperties   ='';
     leftknow=0;
     rightknow = 0;
     if(data.result[0]["a"] == memberleft ) 
     {
        leftknow=0;
        rightknow = 1;
     }
     else 
     {  
         leftknow=1;
         rightknow = 0; 
     }
 

     dataproperties =  
    " data-suggestid='" + data.result[leftknow]["a"]  +   "' data-suggestemail='" + data.result[leftknow]["g"]  +   "' "  +
    " data-suggestname='" + data.result[leftknow]["c"]  +    "' " + 
	" data-suggestphone='" + data.result[leftknow]["f"]  +  "' " + 
	" data-suggestprof='" + data.result[leftknow]["d"]  +   "' " +  
	" data-clientid='" + data.result[rightknow]["a"]  +      "' " +
	" data-receipent='"  + data.result[rightknow]["g"]  +     "' "  +
	" data-receipentname='" + data.result[rightknow]["c"]  +      "' "  +
	" data-receipentphone='" + data.result[rightknow]["f"]  +     "' "  + 
	" data-receipentprof='" + data.result[rightknow]["d"]  +    "' ";
   
	//find cc 
	if( data.result[leftknow]["b"]   != mid )
	{
		dataproperties +=  " data-cc1='"  + data.result[leftknow]["r"] +  "' " + 
        " data-ccname1='"  + data.result[leftknow]["s"]  +   "' "   ;
	}
	else
	{
		dataproperties +=  " data-cc1=''  data-ccname1='' "   ;
	}
	//set email log id
	//$suggestedreferrals = $link->query("SELECT a.id  FROM referralsuggestions as a INNER JOIN user_people as b on a.knowtorefer=b.id 
	//WHERE a.knowreferedto = '$referralid' AND a.knowtorefer =  '$membertointroduce'" );
	//$maillogid = $suggestedreferrals->fetch_array()['id'];
	//$dataproperties .=   " data-mailogid='" .  $maillogid . "' " ;

    html = '';
    html  += '<div class="wizsummary-inner">' +
              '<h3 class="alertwide text-center">You are introducing <strong>' + data.result[leftknow]["c"]   +  
              '</strong> to <strong>' + data.result[rightknow]["c"]   +     '</strong>.<br/> An email will be sent to <strong>' +
                data.result[rightknow]["c"]   +  '</strong> about this introduction.</h3>' +
			'<div class="col-md-4">' +
			'<div class="panel panel-primary">' +
            '<div class="panel-heading">' +
            '<h4>Person To Introduce</h4> ' + 
            '</div> <div class="panel-body referpanel-left">' +
            '<h3>' + data.result[leftknow]["c"]   +  '</h3>' +
            '<p>' +    data.result[leftknow]["g"]   +   '</p>' +
            '<p><small>'  + data.result[leftknow]["d"]    +  '</small></p></div>' +
            '</div></div><div class="col-md-4"> <div class="panel panel-success">' +
            '<div class="panel-heading">' +
            '<h4>Introducer</h4> </div>' +
			'<div class="panel-body referpanel-center">' +
            '<h3>'  + musername + '</h3>' +
            '<p>'  + mremail + '</p> ' +
            ' </div></div>' +
            '</div>' +
            '<div class="col-md-4">' +
            '<div class="panel panel-primary">' +
            '<div class="panel-heading">' +
            '<h4>Person receiving introduction</h4>' +
            '</div>' +
            '<div class="panel-body referpanel-right">' +
            '<h3>' + data.result[rightknow]["c"]   +   '</h3>' +
            '<p>' + data.result[rightknow]["g"]   +  '</p>' +
            '<p><small>' + data.result[rightknow]["d"]   +   '</small></p>' +
            '</div>' +
            '</div>' +
            '</div> ' +	
            '<div class="col-md-12 clearfix text-left">' +
            '<button ' + dataproperties + ' class="btn btn-success btn-lg wiz_preview_mail_template"><i class="fa fa-envelope"></i> Preview Mail Template</button>' +
			'</div></div><div class="clearfix"></div> '  ;
 

        html += '<div class="modal fade intromailtemplate" tabindex="-1" role="dialog" aria-labelledby="intromailtemplate"' +
         'id="intromailtemplate">' +
         ' <div class="modal-dialog "  >' +
         ' <div class="modal-content">' +
         '<div class="modal-header">' +
         '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
         ' <span aria-hidden="true">&times;</span></button>' +
         '<h2 class="modal-title" >Sample of Email Message</h2> ' +
         '</div>' +
         '<div class="modal-body text-left " style="height: 360px; overflow-y:scroll"  >' + 
         ' <div id="mailbody"></div>' +
         '</div>' +
         '<div class="modal-footer clearfix" >' +
         '<button   class="btn btn-primary wiz_send_referral_mail" >Send Mail</button>' +
         '<button data-dismiss="modal"  class="btn btn-danger" >Cancel</button>' +
         '</div>' +
         '</div>' +
         '</div>' +
         '</div>' +
         '</div>';  
  
                $('#wiz_summary').html(html); 
				$('#wizstep3').removeClass('disabled');
				$('#wizstep3').addClass('complete');
				$('#wizstep2').removeClass('disabled');
				$('#wizstep2').addClass('complete'); 
            }
        });
    } 
})
$(document).on('click', '.wiz_preview_mail_template', function() {

    var suggestemail = $(this).data('suggestemail');
     var suggestid = $(this).data('suggestid');
        var suggestname = $(this).data('suggestname');
        var phone = $(this).data('suggestphone');
        var profession = $(this).data('suggestprof');

        var to = $(this).data('receipent');
        var clientid = $(this).data('clientid');
        var receipentname = $(this).data('receipentname');
        var receipentprof = $(this).data('receipentprof');
        var receipentphone = $(this).data('receipentphone');

        var mailogid = $(this).data('mailogid');
        var cc1 = $(this).data('cc1');
        var ccname1 = $(this).data('ccname1');
 
     waitFunc('enable');
        $.ajax({
        type: 'post',
        url: aurl + 'mailtemplates/read/',
        data: { 
            templateid: 1 ,
            suggestemail: suggestemail,
            suggestname: suggestname,
            suggestid: suggestid,
            profession: profession,
            phone: phone,
            to: to,
            receipentname: receipentname,
            receipentprof: receipentprof,
            receipentphone: receipentphone,
            mailogid: mailogid,
            clientid: clientid,
            cc1: cc1,
            ccname1: ccname1,
            username: musername
         },
        success: function(data) {
          waitFunc(''); 
          data = $.parseJSON(data);   
            if(data == 0)
            {
                 alertFunc('danger', 'Email preview generation failed. Please retry again!'); 
            }
            else 
            {
                $('#intromailbody').html(data.templatebody);  
                $('#mailbody').html(data.templatebody);   
                $("#intromailtemplate").modal('show'); 
            } 
        }
    });
}) 

$(document).on('click', '.wiz_send_referral_mail', function()
{
    var suggestemail = $('.wiz_preview_mail_template').data('suggestemail');
    var suggestid = $('.wiz_preview_mail_template').data('suggestid');
    var suggestname = $('.wiz_preview_mail_template').data('suggestname');
    var phone = $('.wiz_preview_mail_template').data('suggestphone');
    var profession = $('.wiz_preview_mail_template').data('suggestprof'); 
    var to = $('.wiz_preview_mail_template').data('receipent');
    var clientid = $('.wiz_preview_mail_template').data('clientid');
    var receipentname = $('.wiz_preview_mail_template').data('receipentname');
    var receipentprof = $('.wiz_preview_mail_template').data('receipentprof');
    var receipentphone = $('.wiz_preview_mail_template').data('receipentphone'); 
    var mailogid = $('.wiz_preview_mail_template').data('mailogid');
    var cc1 = $('.wiz_preview_mail_template').data('cc1');
    var ccname1 = $('.wiz_preview_mail_template').data('ccname1');
    
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'email/introduction/send/',
        data: 
        {
            templateid: 1,
            suggestemail: suggestemail,
            suggestname: suggestname,
            suggestid: suggestid,
            profession: profession,
            phone: phone,
            to: to,
            receipentname: receipentname,
            receipentprof: receipentprof,
            receipentphone: receipentphone,
            mailogid: mailogid,
            clientid: clientid,
            cc1: cc1,
            ccname1: ccname1,
            userid: mid,
            username: musername
        },
        success: function(data) {
          
           
            data = $.parseJSON(data); 
            alertFunc('success', 'Your introduction mail has been sent!');
 
            //alertFunc('danger', 'Temporary mail sending error. Please try again to send introduction mail!') ;
            waitFunc('');
            //reset form 
            $('#wizstep1').removeClass('complete');
            $('#wizstep1').addClass('disabled');
            $('#wizstep2').removeClass('complete');
            $('#wizstep2').addClass('disabled');
            $('#wizstep3').removeClass('complete');
            $('#wizstep3').addClass('disabled');
            $('.wiz_memberright').val('');
            $('#wiz_summary').html(''); 
        }
    }); 
})


$(document).on('click', '.readdirectmail', function () {
    var mailid = $(this).data('id');
      
    if (mailid)
    {
        $.ajax({
            type: 'post',
            url: aurl + 'diretmails/readmail/',
            data: {  mailid: mailid },
            success: function (data) {

                data = $.parseJSON(data); 
                if (data.results[0]['emailbody'] != '')
                    $('#refmailreaderbox').html(data.results[0]['emailbody']);
                else 
                    $('#refmailreaderbox').html('<p>Seems like blank email was sent.</p>');
            }
        });
        $('#refmailreader').modal('show');
    }
})


$(document).on('click', '.updateUserProf', function()
{
	var data_id = $('#changeAccSett').attr('data-id');
    var upd_username = $('input[name="upd_username"]').val();
    var upd_phone = $('input[name="upd_phone"]').val();
    var upd_country = $('select[name="upd_country"]').val();
	var upd_street = $('input[name="upd_street"]').val();
    var upd_city = $('select[name="upd_city"]').val();
    var upd_cityov = $('input[name="upd_city"]').attr('data-ov');
    var upd_zip = $('input[name="upd_zip"]').val();
    var upd_email = $('input[name="upd_email"]').val();
    var upd_public_private = $('input[name="upd_public_private"]:checked').val();
    var upd_reminder_email = $('input[name="upd_reminder_email"]:checked').val();
	var about_your_self = $('textarea[name="about_your_self"]').val();
	if(mrole =='admin')
	var member_tags = $('select[name="member_tags"]').val() +'';
	else 
	var	member_tags='';
    //alert(about_your_self);
    var upd_usergrp = [];
    $('input[name="upd_usergrp"]:checked').each(function(i) {
        upd_usergrp[i] = $(this).val();
    });
    var upd_uservoc = [];
    $('input[name="upd_uservoc"]:checked').each(function(i) {
        upd_uservoc[i] = $(this).val();
    });
    var upd_usertarget = [];
    $('input[name="upd_usertarget"]:checked').each(function(i)
    {
        upd_usertarget[i] = $(this).val();
    });
    var upd_usertargetreferral = [];
    $('input[name="upd_usertargetreferral"]:checked').each(function(i) {
        upd_usertargetreferral[i] = $(this).val();
    });  
    var is_business = $('select[name="membertype_edit"]').val(); 
    if(is_business ==  1)
    {
        var busi_name_edit = $('input[name="busi_name_edit"]').val();
        var busi_location_edit = $('select[name="busi_location_edit"]').val();
        var busi_type_edit = $('select[name="busi_type_edit"]').val(); 
        var busi_hours_edit = $('input[name="busi_hours_edit"]').val();
        var busi_website_edit = $('input[name="busi_website_edit"]').val(); 
        var busi_location_street_edit = $('input[name="busi_location_street_edit"]').val();
    }   
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  ajurl + 'includes/ajax.php',
        data: { data_id: data_id, upd_username: upd_username, upd_phone: upd_phone, upd_country: upd_country, upd_street: upd_street, upd_city: upd_city, upd_cityov:upd_cityov, upd_zip: upd_zip, upd_email: upd_email, upd_public_private:upd_public_private, upd_reminder_email:upd_reminder_email, upd_usergrp: upd_usergrp, upd_uservoc: upd_uservoc, upd_usertarget: upd_usertarget, upd_usertargetreferral: upd_usertargetreferral,about_your_self:about_your_self, is_business : is_business,   busi_name:busi_name_edit , busi_location_street :busi_location_street_edit, busi_location:busi_location_edit, busi_type:busi_type_edit, busi_hours:busi_hours_edit, busi_website:busi_website_edit,
		usertags: member_tags},
        success: function(data) { 
            waitFunc('');
            if (data == 'error') {
                alertFunc('info', 'Something went wrong, please try again')
            } else {
                alertFunc('success', 'Settings successfully updatedd!');
                getUserClients($('.pagiAd li.active a').attr('data-pg'));
            }
			reloadselfprofile(); 
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
	
});
$(document).on('click', 'select[name=membertype_edit]', function(e) {
    
       if($(this).val() == 0 ) 
       {
        $('input[name=busi_name_edit]').prop("disabled", true);
           $('select[name=busi_location_edit]').prop("disabled", true);
           $('select[name=busi_type_edit]').prop("disabled", true);
           $('input[name=busi_hours_edit]').prop("disabled", true);
           $('input[name=busi_website_edit]').prop("disabled", true);  
		   $('input[name=busi_location_street_edit]').prop("disabled", true);  
		   
       }
       else 
       {
        $('input[name=busi_name_edit]').prop("disabled", false);
           $('select[name=busi_location_edit]').prop("disabled", false);
           $('select[name=busi_type_edit]').prop("disabled", false);
           $('input[name=busi_hours_edit]').prop("disabled", false);
           $('input[name=busi_website_edit]').prop("disabled", false);  
		    $('input[name=busi_location_street_edit]').prop("disabled", false);  
       }  
});
 

$(document).on('click', '.btncomposedirectmail', function()
{ 
	var status =0;
	var id = $(this).attr('data-id'); 
    $('#btnsenddirectemail').attr('data-id', id);
    $('#btnrequestdirectmail').attr('data-id', id);
    waitFunc('enable');
	
	  
	html =''; 
	$.ajax({
        type: 'post',
        url: aurl + 'directmail/checkstatus/',
        data: {  user_id: mid, partnerid: id  },
        success: function(data) {
            data = $.parseJSON(data);  
            waitFunc('');
            
            status = data.status; 
            
				$.ajax({
					type: 'post',
					url: aurl + 'member/completeprofile/',
                    data: {   userid : id  },
                    success: function(data) {
                        data = $.parseJSON(data);  
                       
                        waitFunc(''); 
                        
						item = data.results[0]; 
						
						$('#sendermail').val(item.user_email);
						$('#senderphone').val(item.user_phone);
						$('#receipentid').val( id );
						
						
							
                            user_picture = !(item.image ) ?  "assets/uploads/profiles/no-photo.png" :  "images/"  +  item.image;  
                            html  += "<div class='col-md-4'>" + 
                                "<img src='"  + user_picture  +  "' alt='"  +  item.username   + "' class='img-rounded' height='70' width='70'>";
                            html  += "</div> <div class='col-md-8'>";
                            html  += "<p><strong>Name:</strong>" + item.username  + "</p>";
 
                            if( status == 0)
                            {
                                html  += "<p><strong>Email:</strong> <i class='fa fa-lock'></i></p>";
                                html  += "<p><strong>Phone:</strong> <i class='fa fa-lock'></i></p>";
                            } 
                            else if( status == 1)
                            {
                                html  += "<p><strong>Email:</strong> " + item.user_email+"</p>";
                                html  += "<p><strong>Phone:</strong> " + item.user_phone+"</p>";
                            }
                            
                            
							html  += "</div>";
                            html  += '<div class="col-md-12"><hr/>';
							
							if (typeof item.about_your_self === 'undefined' || item.about_your_self == '')
                                html  += '<p><strong>About</strong></p> Not Specified<hr/>';
                            else 
                                html  += '<p><strong>About</strong></p>'+ item.about_your_self + '<hr/>'; 
                        
                            if (typeof item.target_clients === 'undefined' || item.target_clients == '') 
                                html  += '<p><strong>Target Clients</strong></p> Not Specified<hr/>';
                            else 
                                html  += '<p><strong>Target Clients</strong></p>'+ item.target_clients  + '<hr/>';
                            
                            if (typeof item.target_referral_partners === 'undefined' || item.target_referral_partners == '')
                                html  += '<p><strong>Target Referral Partners</strong></p> Not Specified<hr/>';
                            else 
                                html  += '<p><strong>Target Referral Partners</strong></p>' + item.target_referral_partners  ;
                             
                            html  += '</div> ';
							
						if(!CKEDITOR.instances.previewdirectmail)
						{ 
							CKEDITOR.replace( 'previewdirectmail' );   
						}
						else 
						{
							CKEDITOR.instances.previewdirectmail.destroy();
							CKEDITOR.replace( 'previewdirectmail' );
						}
						
						$("#memberprofilepreview2").html(html );
						$("#composedirectmodal").modal('show');
						
                    } 
                }); 
				 
             
        } 
    });   
}); 
 

$(document).on('click', '.btnconnect', function()
{
	var url = $(this).attr('data-tgt');
	var page = $(this).attr('data-pg');

	var memberid = $(this).attr('data-i') ; 
	var form = $('<form action="' + siteurl + 'business/search" method="post">' +
        '<input type="hidden" name="partnerid" value="' + memberid + '" />' +
		'<input type="hidden" name="useremail" value="' + mremail + '" />' + 
		'<input type="hidden" name="vu_member" value="1" />' +  
		'<input type="hidden" name="page" value="' + page + '" />' + 
		'<input type="hidden" name="url" value="' + url + '" />' + 
		'<input type="hidden" name="connect_req" value="send" />' +
        '</form>');
    $('body').append(form);
    form.submit(); 
	   
}) 
$(document).on('click', '.btnvu', function(){
	  var id = $(this).attr('data-id');
	  var title = $(this).attr('data-title');
	  var reminder = $(this).attr('data-reminder');
	  $('#remindtitle').html( title  );   
	  $('#remisummary').html(  reminder); 
	  $('#reminderview').modal('show'); 
});

$(document).on('change', '.fetGroupMembers', function() {
    var selGrp = $('.fetGroupMembers option:selected').val();
 
    if (selGrp != 'null') {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: aurl + 'member/get/',
            data: {  userid:  mid , groupid: selGrp },
            success: function(data){
  
                waitFunc('');
                alertFunc('success', 'Clients in selected group loaded');
                var dropdown = $('#groupMembers');
                dropdown.empty();
                $.each(JSON.parse(data), function(key, value) {
                    $("#groupMembers").append($('<option></option>').val(value.id).html(value.username));
                });
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    } 
});

$(document).on('click', '.leaveMsg', function()
{
	$('#myModal').attr('data-id', $(this).attr('id'));
});

$(document).on('click', '.leaveUserMsg', function()
{
	var send_to = $('#myModal').attr('data-id');
    var sender_name = $('#sender_name').val().trim();
    var sender_email = $('#sender_email').val().trim();
    var sender_msg = $('#sender_msg').val().trim();

    if (sender_name == '') 
	{
		alertFunc('danger', 'Please provide a name');
        return;
    }
    if (validateEmail(sender_email) == false) 
	{
		alertFunc('danger', 'Email not valid');
        return;
    }
    if (sender_msg == '') {
        alertFunc('danger', 'Please enter your message');
        return;
    }

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'leavemessage/',
        data: { sender_name: sender_name, sender_email: sender_email, leaveMsg: sender_msg, send_to: send_to , user_id:mid},
        success: function(data) {
            data = $.parseJSON(data);
            waitFunc('');
            if (  data.error == 1) 
            {
				alertFunc('info', 'Something went wrong, please try again')
            }
			else if (data.error == 10 || data.error == 11) 
            {
				alertFunc('info',  data.errmsg)
            }
            else 
            {
				alertFunc('success', 'Your message has been sent');
            }
        },
        error: function() 
		{
			waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    }); 
}); 


$(document).on('click', '.btncomposeknowinvitemail', function()
{
	var page = $(this).attr('data-pg');
	var id = $(this).attr('data-id'); 
	var email  = $(this).attr('data-email');
	var name  = $(this).attr('data-name'); 
	
	var form = $('<form id="claim_profile" action="' + siteurl + 'business/search" method="post">' +
        '<input type="hidden" name="knowid" value="' + id + '" />' +
		'<input type="hidden" name="email" value="' + email + '" />' +
		'<input type="hidden" name="name" value="' + name + '" />' + 
		'<input type="hidden" name="bcp" value="claim_profile" />' + 
		'<input type="hidden" name="page" value="' + page + '" />' + 
		'<input type="submit" name="btn" value="submit" />' + 
        '</form>');
    $('body').append(form);
	//form.submit(); 
	  
	$("#claim_profile").submit();
	
})



//EDIT PEOPLE
$(document).on('click', '.editPeopleDetails', function() 
{
 
    $("#edit_people_details").on("show", function () {
        $("body").addClass("modal-open");
      }).on("hidden", function () {
        $("body").removeClass("modal-open"); 
      });
 
 
    var person = $(this).parents('tr').attr('id');
	 
	
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  ajurl + 'includes/ajax.php',
        data: { editPerson: person },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('info', 'Something went wrong, please try again')
            } else {
                $('.editPeopleContent').html(data);
 
                var prof = $('.ed_client_pro').attr('id');
                $('.ed_client_pro option[value="' + prof + '"]').prop('selected', true);

                var grp = $('.ed_user_grp').attr('id');
                $('.ed_user_grp option[value="' + grp + '"]').prop('selected', true);

                $('.user_ques_ed').each(function() {
                    var rank = $(this).attr('data-rank');
                    $(this).find('input[type="radio"][value="' + rank + '"]').prop('checked', true);
                });
                
                //clearing chosen selects 
               
                //change to multiselect input 
                var config = 
                {
                    '.ed_client_pro': {},
                    '.ed_client_pro-deselect': { allow_single_deselect: true },
                    '.ed_client_pro-no-single': { disable_search_threshold: 10 },
                    '.ed_client_pro-no-results': { no_results_text: 'Oops, nothing found!' },
                    '.ed_client_pro-width': { width: "95%" }
                }
                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                }
                
                var config = 
                {
                    '.ed_client_location': {},
                    '.ed_client_location-deselect': { allow_single_deselect: true },
                    '.ed_client_location-no-single': { disable_search_threshold: 10 },
                    '.ed_client_location-no-results': { no_results_text: 'Oops, nothing found!' },
                    '.ed_client_location-width': { width: "95%" }
                }
                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                }
                 
                var config = {
                    '.user_ques_text_ed': {},
                    '.user_ques_text_ed-deselect': { allow_single_deselect: true },
                    '.user_ques_text_ed-no-single': { disable_search_threshold: 10 },
                    '.user_ques_text_ed-no-results': { no_results_text: 'Oops, nothing found!' },
                    '.user_ques_text_ed-width': { width: "95%" }
                }
                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                }

                var config = {
                    '.ed_client_tags': {},
                    '.ed_client_tags-deselect': { allow_single_deselect: true },
                    '.ed_client_tags-no-single': { disable_search_threshold: 10 },
                    '.ed_client_tags-no-results': { no_results_text: 'Oops, nothing found!' },
                    '.ed_client_tags-width': { width: "95%" }
                }
                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                } 
				var config = {
                    '.ed_client_lifestyle': {},
                    '.ed_client_lifestyle-deselect': { allow_single_deselect: true },
                    '.ed_client_lifestyle-no-single': { disable_search_threshold: 10 },
                    '.ed_client_lifestyle-no-results': { no_results_text: 'Oops, nothing found!' },
                    '.ed_client_lifestyle-width': { width: "95%" }
                }
                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                } 

            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


// UPDATE PEOPLE VALUES
$(document).on('click', '.updClientUser', function() {
 
    var name = $('.ed_client_name').val().trim();
    var pro = $('.ed_client_pro').chosen().val() + '';
    var ph = $('.ed_client_ph').val().trim();
    var email = $('.ed_client_email').val().trim();
    var loc = $('.ed_client_location').val() + '';
    var zip = $('.ed_client_zip').val().trim();
    var note = $('.ed_client_note').val().trim();
    var grp = $('.ed_user_grp').val();
    var lifestyle = $('.ed_client_lifestyle').val() + '';
    var client_tags = $('.ed_client_tags').val() + '';
 
	 
    var order = $(this).attr('id');
    var rank = [],
        ques = [];
   
    $('.user_ques_ed').each(function(i) {
        ques[i] = $(this).attr('data-ques');
        rank[i] = $(this).find('input:checked').val();
    });  

    i=0;
    var user_ques_text = [];
        var quesid = $('.user_ques_text_ed').attr('data-ques');
        var answer = $('.user_ques_text_ed').val() + '';
        if (answer) {
            user_ques_text[i] = {
                id: quesid,
                answer: answer.toString()
            };
        }
      
    
    if (name == '') {
        alertFunc('danger', 'Please provide a name');
        return;
    }
    if (validateEmail(email) == false) {
        alertFunc('danger', 'Email not valid');
        return;
    }
    //addClientUser(order, name, pro, ph, email, loc,zip, note, rank, ques, grp, user_ques_text);
    //addNewKnow(order, name, pro, ph, email, loc, zip, note, rank, ques, grp, user_ques_text, lifestyle);
   
    waitFunc('enable');
    $.ajax({ 
        type: 'post',
        url: aurl + 'knows/add/',
        data: { id: order, user_id:mid,  client_name:name, client_pro:pro, client_ph:ph, client_email:email, 
           client_location:loc, client_zip:zip, client_note:note, ques_rate:rank, 
           ques:ques, user_grp:grp, user_ques_text:user_ques_text, client_lifestyle:lifestyle, client_tags: client_tags },
           success: function(data) 
           {
               data = $.parseJSON(data); 
               waitFunc(''); 
                   if(data.error == 0) 
                   {        
                       alertFunc('info', 'Know information added successfully!');
					   
					   //new mapping
						$.ajax({
							type: 'post',
							url: aurl +  'knows/selectivemapping/',
							data: { uid:  mid, knowid: order} ,
							success: function(adata)
							{
								adata = $.parseJSON(adata);  
							}
							}); 
                        
                       if (data.action  ==  'u') 
                       {
                           var professions = $(".user_ques_text_ed").chosen().val() + '';
                          // regeneratesuggestedrefferals(id, professions, client_zip);
                          alertFunc('success', 'Your new contact has some matching connects waiting for introduction!');
                       } 
                   }
                   else 
                       alertFunc('info', 'Something went wrong, please try again'); 
           },
           error: function() 
           {
               waitFunc('');
               alertFunc('info', 'Something went wrong, please try again')
           }
    });
	 
});
 
$(document).on('click', '.btnselecttrigger', function()
{
    var receipentemail = $(this).data('remid');
    var receipent = $(this).data('rname');
	var receipentid = $(this).data('rpt');
	var introducee = $(this).data('introducee');
    var phone = $(this).data('phone');
    
    $( ".btnsendtrigger" ).removeData( "rname" );
	$( ".btnsendtrigger" ).removeData( "remid" );
	$( ".btnsendtrigger" ).removeData( "reid" ); 
	$( ".btnsendtrigger" ).removeData( "introducee" ); 
    $( ".btnsendtrigger" ).removeData( "phone" );
    
    $('.btnsendtrigger').attr('data-rname', receipent);
    $('.btnsendtrigger').attr('data-remid',receipentemail  );
	$('.btnsendtrigger').attr('data-reid',receipentid  );
	$('.btnsendtrigger').attr('data-introducee',introducee  );
    $('.btnsendtrigger').attr('data-phone',phone  );
    $("#modaltriggermailselect").modal('show');
})


$(document).on('click', '.btncallmailsender', function() 
{
	var to = $(this).data('to');
	var clientid = $(this).data('clientid');
	var introduceto = $(this).data('introto');
    var introprofession = $(this).data('introprofession');
    var introphone = $(this).data('introphone'); 
    var suggestname = $(this).data('suggestname');
    var suggestid = $(this).data('suggestid');
    var suggestemail = $(this).data('suggestemail');
    var refintroid = $(this).data('refintroid');
    var profession = $(this).data('profession');
    var phone = $(this).data('phone');
    var cc1 = $(this).data('cc1');
    var ccname1 = $(this).data('ccname1'); 
    $('#spconnectname').html(suggestname);
    $('#spconnectemail').html(suggestemail);
    $('#spconnectphone').html(phone); 
	$('#spconnectprofession').html(profession); 
    $('#clientid').val(clientid);
    $('#connectemail').val(suggestemail);
    $('#connectname').val(suggestname);
    $('#suggestid').val(suggestid);
    $('#connectprofession').val(profession);
    $('#connectphone').val(phone);
    $('#introduceto').html( 
	"<i class='fa fa-user dark'></i> " + introduceto + 
	"<br/><i class='fa fa-envelope dark'></i> " + to + 
	"<br/><i class='fa fa-phone dark'></i> " + introphone + 
	"<br/><i class='fa fa-briefcase dark'></i> " + introprofession);
    $('#receipent').val(to);
    $('#receipentname').val(introduceto);
    $('#receipentprof').val(introprofession);
    $('#receipentphone').val(introphone);
    $('#cc1').val(cc1);
    $('#ccname1').val(ccname1);
    $('#mailogid').val(refintroid); 
	//replacing template variables  
	$('.panel-emailtemplates .tplvar_receipent').html(introduceto);
    $('.panel-emailtemplates .tplvar_introducee').html(suggestname);
    $('.panel-emailtemplates .tplvar_rated_by').html(ccname1);
	$('.panel-emailtemplates .tplvar_introducee_profession').html(profession);
	$('.panel-emailtemplates .tplvar_introducee_email').html(suggestemail);
	$('.panel-emailtemplates .tplvar_introducee_phone').html(phone); 
	$('#suggestedreferral').modal('show');
});
$(document).on('click', '.sendintromail', function() 
{
    var templateid = $(this).data('tid'); 
    var suggestemail = $('#connectemail').val();
    var suggestid = $('#suggestid').val();
    var suggestname = $('#connectname').val();
    var to = $('#receipent').val();
    var receipentname = $('#receipentname').val();
    var receipentprof = $('#receipentprof').val();
    var receipentphone = $('#receipentphone').val();
    var profession = $('#connectprofession').val();
    var phone = $('#connectphone').val();
    var refintroid = $('#refintroid').val();
    var mailogid = $('#mailogid').val();
    var clientid = $('#clientid').val();
    var cc1 = $('#cc1').val();
    var ccname1 = $('#ccname1').val();
    
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl +  'mails/sendintroducemail/',
        data: {
            sendintroducemail: 1,
            suggestemail: suggestemail,
            suggestname: suggestname,
            suggestid: suggestid,
            profession: profession,
            phone: phone,
            to: to,
            receipentname: receipentname,
            receipentprof: receipentprof,
            receipentphone: receipentphone,
            mailogid: mailogid,
            clientid: clientid,
            cc1: cc1,
            ccname1: ccname1,
            templateid:templateid,
            user_id:mid,
            username: musername
        },
        success: function(data) 
        {   
            data = $.parseJSON(data); 
            if (data.error ==  0 )
                alertFunc('success', 'Introductory email already sent!');
            if (data.error == 1 || data.error == 10)
                alertFunc('success', 'Introductory email could not be sent!'); 
            waitFunc('');
        }
    }); 
    //close mail sending dialog
    $('#suggestedreferral').modal('hide');
});


$(document).on('click',  '#remintrosuggest', function()
{
	var userid = $(this).attr('data-uid'); 
	if(typeof userid === 'undefined')
	{
		userid = mid ;
	}
	 
	$introids =   $("input[name=refintro\\[\\]]");
	confFunc('Are you sure you want to remove this referral suggestion?', function() {
	var suggestids='';
	$.each($introids , function (index, item)
	{
		if($(item).is(":checked"))
		{
			suggestids += $(item).attr('data-introid') + ','; 
		} 
	}); 
	var goto = $("#remintrosuggest").attr('data-pageno') ;
	if(goto > 1)
	{
		goto =goto -1;
	}	
	 
	var form = $('<form action="' + siteurl + 'dashboard/referrals" method="post">' +
        '<input type="hidden" name="remknows" value="1" />' +
		'<input type="hidden" name="kids" value="' + suggestids + "0" + '" />' +
        '</form>');
    $('body').append(form);
    form.submit();
	  
	 }); 
});


$(document).on('click', '.ref_directtodirectwizard', function() 
{
	
	var sourceoptions = {
        url: function(phrase) {
             return siteurl + "connection/autocomplete_left_name?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list: {
            onSelectItemEvent: function() {
                var value = $("#dtdleftmember").getSelectedItemData();
                $('#dtdlmid').val(value.code); 
            }
        } 
    }; 
	
	$("#dtdleftmember").easyAutocomplete(sourceoptions);
	 
	 
	 var options = {
        url: function(phrase) {
             return siteurl + "connection/autocomplete_name?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list: {
            onSelectItemEvent: function() {
                var value = $("#dtdrightmember").getSelectedItemData();
                $('#dtdrmid').val(value.code); 
            }
        } 
    }; 
	
	$("#dtdrightmember").easyAutocomplete(options);
	 
    $('#onetooneintroduction').modal('show');
    $('#suggestwizard').modal('hide');
});


$(document).on('click', '.dwiz_step_show_summary', function() 
{
	var memberleft = $("#dtdlmid").val();
	var memberright = $("#dtdrmid").val();
	preparesuggestionwizard(memberleft, memberright , 'dtdwiz_summary');
});


function preparesuggestionwizard(memberleft, memberright, printzone)
{
	if (!memberleft || !memberright)
    {
		alert('Missing member selection!')
    }
	else if ( memberleft == memberright) {
        alert('Self introduction is not permitted!')
    } 
    else 
    {
		var userid = $('.refereruid').val(   ); 
		var refname =  $('.referername').val(   );
		var referemail =  $('.refereremail').val(   );
		 
	
		if(typeof userid == 'undefined' &&  typeof referemail == 'undefined' && typeof refname == 'undefined'  )
		{
			userid =mid;  refname = musername ;  referemail=  mremail;
		}
	
		$.ajax({
			type: 'post',
            url: aurl + 'contact/getbyids/',
            data: { wiz_summary: 1, cid: ( memberleft +"," + memberright ) },
            success: function(data) {
            data = $.parseJSON(data); 
  
     dataproperties   ='';
     leftknow=0;
     rightknow = 0;
     if(data.result[0]["a"] == memberleft ) 
     {
        leftknow=0;
        rightknow = 1;
     }
     else 
     {  
         leftknow=1;
         rightknow = 0; 
     }
	 
	dataproperties =  
    " data-suggestid='" + data.result[leftknow]["a"]  +   "' data-suggestemail='" + data.result[leftknow]["g"]  +   "' "  +
    " data-suggestname='" + data.result[leftknow]["c"]  +    "' " + 
	" data-suggestphone='" + data.result[leftknow]["f"]  +  "' " + 
	" data-suggestprof='" + data.result[leftknow]["d"]  +   "' " +  
	" data-clientid='" + data.result[rightknow]["a"]  +      "' " +
	" data-receipent='"  + data.result[rightknow]["g"]  +     "' "  +
	" data-receipentname='" + data.result[rightknow]["c"]  +      "' "  +
	" data-receipentphone='" + data.result[rightknow]["f"]  +     "' "  + 
	" data-receipentprof='" + data.result[rightknow]["d"]  +    "' ";
   
	//find cc 
	if( data.result[leftknow]["b"]   != mid )
	{
		dataproperties +=  " data-cc1='"  + data.result[leftknow]["r"] +  "' " + 
        " data-ccname1='"  + data.result[leftknow]["s"]  +   "' "   ;
	}
	else
	{
		dataproperties +=  " data-cc1=''  data-ccname1='' "   ;
	}
	 

    html = '';
    html  += '<div class="wizsummary-inner">' +
              '<h3 class="alertwide text-center">You are introducing <strong>' + data.result[leftknow]["c"]   +  
              '</strong> to <strong>' + data.result[rightknow]["c"]   +     '</strong>.<br/> An email will be sent to <strong>' +
                data.result[rightknow]["c"]   +  '</strong> about this introduction.</h3>' +
			'<div class="col-md-4">' +
			'<div class="panel panel-primary">' +
            '<div class="panel-heading">' +
            '<h4>Person To Introduce</h4> ' + 
            '</div> <div class="panel-body referpanel-left">' +
            '<h3>' + data.result[leftknow]["c"]   +  '</h3>' +
            '<p>' +    data.result[leftknow]["g"]   +   '</p>' +
            '<p><small>'  + data.result[leftknow]["d"]    +  '</small></p></div>' +
            '</div></div><div class="col-md-4"> <div class="panel panel-success">' +
            '<div class="panel-heading">' +
            '<h4>Introducer</h4> </div>' +
			'<div class="panel-body referpanel-center">' +
            '<h3>'  + refname + '</h3>' +
            '<p>'  + referemail + '</p> ' +
            ' </div></div>' +
            '</div>' +
            '<div class="col-md-4">' +
            '<div class="panel panel-primary">' +
            '<div class="panel-heading">' +
            '<h4>Person receiving introduction</h4>' +
            '</div>' +
            '<div class="panel-body referpanel-right">' +
            '<h3>' + data.result[rightknow]["c"]   +   '</h3>' +
            '<p>' + data.result[rightknow]["g"]   +  '</p>' +
            '<p><small>' + data.result[rightknow]["d"]   +   '</small></p>' +
            '</div>' +
            '</div>' +
            '</div> ' +	
            '<div class="col-md-12 clearfix text-left">' +
            '<button ' + dataproperties + ' class="btn btn-success btn-lg wiz_preview_mail_template"><i class="fa fa-envelope"></i> Preview Mail Template</button>' +
			'</div></div><div class="clearfix"></div> ' ;
 

         html += '<div class="modal fade intromailtemplate" tabindex="-1" role="dialog" aria-labelledby="intromailtemplate"' +
         'id="intromailtemplate">' +
         ' <div class="modal-dialog "  >' +
         ' <div class="modal-content">' +
         '<div class="modal-header">' +
         '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
         ' <span aria-hidden="true">&times;</span></button>' +
         '<h2 class="modal-title" >Sample of Email Message</h2> ' +
         '</div>' +
         '<div class="modal-body text-left " style="height: 360px; overflow-y:scroll"  >' + 
         ' <div id="intromailbody"></div>' +
         '</div>' +
         '<div class="modal-footer clearfix" >' +
         '<button   class="btn btn-primary wiz_send_referral_mail" >Send Mail</button>' +
         '<button data-dismiss="modal"  class="btn btn-danger" >Cancel</button>' +
         '</div>' +
         '</div>' +
         '</div>' +
         '</div>' +
         '</div>';  
			$('#' + printzone).html(html); 
			$('#wizstep1').removeClass('disabled');
			$('#wizstep1').addClass('complete'); 
			
			$('#wizstep2').removeClass('disabled');
			$('#wizstep2').addClass('complete'); 
			$('#wizstep3').removeClass('disabled');
			$('#wizstep3').addClass('complete'); 
            }
        });
    } 
} 
$(document).on('click', '.wiz_send_referral_mail', function()
{
	var refuserid = $('.refereruid').val(   ); 
	var refusername =  $('.referername').val(   );
	   
	var suggestemail = $('.wiz_preview_mail_template').data('suggestemail');
    var suggestid = $('.wiz_preview_mail_template').data('suggestid');
    var suggestname = $('.wiz_preview_mail_template').data('suggestname');
    var phone = $('.wiz_preview_mail_template').data('suggestphone');
    var profession = $('.wiz_preview_mail_template').data('suggestprof'); 
    var to = $('.wiz_preview_mail_template').data('receipent');
    var clientid = $('.wiz_preview_mail_template').data('clientid');
    var receipentname = $('.wiz_preview_mail_template').data('receipentname');
    var receipentprof = $('.wiz_preview_mail_template').data('receipentprof');
    var receipentphone = $('.wiz_preview_mail_template').data('receipentphone'); 
    var mailogid = $('.wiz_preview_mail_template').data('mailogid');
    var cc1 = $('.wiz_preview_mail_template').data('cc1');
    var ccname1 = $('.wiz_preview_mail_template').data('ccname1');
    
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'email/introduction/send/',
        data: 
        {
            templateid: 1,
            suggestemail: suggestemail,
            suggestname: suggestname,
            suggestid: suggestid,
            profession: profession,
            phone: phone,
            to: to,
            receipentname: receipentname,
            receipentprof: receipentprof,
            receipentphone: receipentphone,
            mailogid: mailogid,
            clientid: clientid,
            cc1: cc1,
            ccname1: ccname1,
            userid: refuserid,
            username: refusername
        },
        success: function(data) {
           
           data = $.parseJSON(data); 
            alertFunc('success', 'Your introduction mail has been sent!');
			waitFunc('');
            //reset form 
            $('#wizstep1').removeClass('complete');
            $('#wizstep1').addClass('disabled');
            $('#wizstep2').removeClass('complete');
            $('#wizstep2').addClass('disabled');
            $('#wizstep3').removeClass('complete');
            $('#wizstep3').addClass('disabled');
            $('.wiz_memberright').val('');
            $('#wiz_summary').html(''); 
        }
    }); 
})

$(document).on('click', '.edittrigger', function() 
{
    var triggerid = $(this).data('id');
    var question = $('#trigbody-' + triggerid).html();  
	$('input[name="triggername"]').val(question);
	$('input[name="triggerid"]').val(triggerid); 
	$('input[name="btn_savetrigger"]').html("Update"); 
});


$(document).on('click', '.btnratemembers', function()
{ 
	 
	var id = $(this).attr( 'data-id');
	$('#btnsavememberrating').attr('data-mid', id); 
	$('#member_rating_box').html("<div class='text-center'><img   src='../images/processing.gif' alt='Loading ...' /></div>");
	//load  questions
	$.ajax({
        type: 'post',
        url: aurl + 'member/rating/getquestions/', 
		data: {  userid : mid , memid: id },
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc('');
		 
			if(data.error ==0)
			{
				html ='';
				$.each(data.results, function(i, item) 
				{ 
					html += "<div class='raterow'><div class='row'><div class='col-xs-12 col-md-6'>" ; 
					html += "<label class='custom-label'>" + item.question +"</label></div><div class='col-xs-12 col-md-6'>" ;
					html += "<label class='radio-inline'><input data-id='" + item.id + "' type='radio' name='rating_" + i + item.id + "' value='1' " + (item.ranking == 1 ? "checked" : "" ) + " > 1</label>";
					html += "<label class='radio-inline'><input data-id='" + item.id + "' type='radio' name='rating_"  + i+ item.id + "' value='2'  " + (item.ranking == 2 ? "checked" : "" ) + "> 2</label>";
					html += "<label class='radio-inline'><input data-id='" + item.id + "'type='radio' name='rating_" + i + item.id + "' value='3'  " + (item.ranking == 3 ? "checked" : "" ) + "> 3</label>";
					html += "<label class='radio-inline'><input data-id='" + item.id + "' type='radio' name='rating_" + i + item.id + "' value='4'  " + (item.ranking == 4 ?"checked" : "" ) + "> 4</label>";
					html += "<label class='radio-inline'><input data-id='" + item.id + "' type='radio' name='rating_" + i + item.id + "' value='5'  " + (item.ranking == 5 ? "checked" : "" ) + "> 5</label>"; 
					html += "</div></div></div>";  	
								
				});
				
				$('#member_rating_box').html(html);
			}
			  
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    });
	
	$('#memberratingmodal').modal('show'); 
	  
})



$(document).on('click', '#btnsavememberrating',  function(){
	
	 
	var rate1 = $('input[name="rating_01"]:checked').val();
	var ques1 = $('input[name="rating_01"]:checked').attr('data-id'); 
	var rate2 = $('input[name="rating_12"]:checked').val();
	var ques2 = $('input[name="rating_12"]:checked').attr('data-id'); 
	var rate3 = $('input[name="rating_23"]:checked').val();
	var ques3 = $('input[name="rating_23"]:checked').attr('data-id'); 
	var rate4 = $('input[name="rating_34"]:checked').val();
	var ques4 = $('input[name="rating_34"]:checked').attr('data-id'); 
	var rate5 = $('input[name="rating_45"]:checked').val();
	var ques5 = $('input[name="rating_45"]:checked').attr('data-id');
	var ques5 = $('input[name="rating_45"]:checked').attr('data-id');
	 
	var memid= $('#btnsavememberrating').attr('data-mid' ); 
	waitFunc('enable');
    $.ajax({
        type: 'post', 
        url: aurl + 'member/rating/saveratings/',
        data: { userid:mid, memid : memid, rate1: rate1 , rate2: rate2 , rate3: rate3 , rate4: rate4 , rate5: rate5 , ques1:ques1, ques2:ques2, ques3:ques3,  ques4:ques4, ques5:ques5    },
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc('');
			alertFunc('danger',  data.errmsg ); 
        }
    }); 
})


$(document).on('click', '.btnchangedirectmailstatus', function()
{ 
    var partner = $(this).attr('data-id');
	
	if(typeof partner ==='undefined')
	{
		var partneremail = $(this).attr('data-e'); partner=0;
	}
	 else 
	 {
		 partneremail='';
	 }
	
    var status = $(this).attr('data-st'); 
   
    waitFunc('enable');
    $.ajax({
       type: 'post',
       url: aurl + 'directmail/request/update/',
      data: { partnerid: partner , partneremail:partneremail, user_id: mid , status: status },
       success: function(data) {
           data = $.parseJSON(data);  
           waitFunc('');
           alertFunc('info', "Changes are saved!" );
           window.location.href = siteurl +"connection/requests_received/";
		   
		   $( "#sboxid" + partner ).remove();
		   
           if(status == '1')
           {
               $(this).addClass('btn-primary');
               $(this).removeClass('btn-warning');
           }
           else 
           {
            $(this).removeClass('btn-primary');
            $(this).addClass('btn-warning'); 
           }
       },
       error: function( ) 
       { 
           waitFunc(''); 
           alertFunc('info',  'Something went wrong, please try again')
       }
    }); 
});


$(document).on('click', '.delUserClient', function() {
	 
    var id = $(this).attr('data-id');
    var thisUser = $(this);
     
	
	confFunc('Are you sure you want to delete this user?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { delUserClient: id },
            success: function(data) {
                waitFunc('');
                if (data == 'error') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'User successfully deleted');
                    $(thisUser).parents('tr').remove();
                }
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    }) 

});


$(document).on('click', '.vuconcount', function()
{
	var id = $(this).attr('data-id');
	$('#commonconnects').modal('show');  
	 $('.cctable').html("<div class='text-center'><img src='../images/processing.gif' alt='Loading ...' /></div>");
	$('.ccviewtable').html( '' );   
	$.ajax({
        type: 'post',
        url: aurl + 'members/commonconnects/',
        data: { uid: id},
        success: function(data)
        {
            
            data = $.parseJSON(data);    
            if(data.error == 1 )
            { 
                alertFunc('danger',  'Something went wrong, please try again');
            } 
            else 
            {
                var html = "<table class='table table-sm'>";
                html += "<tr id='$rand-$id'>" +
                "<th>Partner Name</th>" +
                "<th>Number of Matching Connections</th>"   + 
				 "<th>Action</th>"  
                "</tr> " ;
    
                $.each(data.results, function (index, item) 
                {
                    username = item.username ;
                    matchingconnects = item.matchingconnects ;
                    partnerid = item.id ;
                   
                    html += "<tr id='$rand-$id'>" +
                        "<td>"  + username  + "</td>" +
                        
                        "<td style='text-align:left !important'>"  + matchingconnects  +  "</td><td>" + 
						"<button data-uid='" + id+ "' data-pid='" + partnerid + "' class='btn btn-xs vuconnections'>View </button>" +
						"</td>" + 
                        "</tr> " ;
                });
				 
                html += "</table>"; 
                $('.cctable').html( html );
				 
            } 
        }  
    });    
}); 


$(document).on('click', '.changePass', function() {
    $('div[data-type="changePass"]').slideToggle(300);
});
 