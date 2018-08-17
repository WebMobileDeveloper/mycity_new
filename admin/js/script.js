var insID;
var reg_email;
var reg_first_name;
var reg_last_name;
var reg_password;
var reg_country;
var reg_zip;
var reg_city;
var vocation_result = new Array();
var groups_result = new Array();
var target_clients = new Array();
var target_referral_partners = new Array(); 
var reg_membertype;
var reg_street ;
var busi_name  ;
var busi_location ;
var busi_type ;
var busi_hours  ; 
var busi_website ;
var busi_location_street ;  
var mid;
var mremail;
var musername;
var muserphone;
var token;
var mrole;
var mgroups;
var mzip; 
var aurl = "//" + window.location.hostname + "/api/api.php/"; 
function nulltospace (value) { return (value == null) ? "" : value }
$.fn.modal.Constructor.prototype.enforceFocus = function() {
  modal_this = this
  $(document).on('focusin.modal', function (e) {
    if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
    && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
    && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
      modal_this.$element.focus()
    }
  })
};


//Clear client global variable
var client_suc_status = 0; 
$(window).load(function() 
{
	var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
	var _rmtoken ; 
	for(var i = 0; i <ca.length; i++) 
    {
		var c = ca[i];
        key = ca[i].split('=')[0];  
		 
        if(key.trim() === "_mcu")
        {
            cvalue = ca[i].substring( 6, ca[i].length   ); 
			 
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
		if(cvalue[0] != '{')
			cvalue = '{' + cvalue; 
		 
        cvalue = $.parseJSON(cvalue);
        mid = cvalue.id;
        mremail = cvalue.email;
        musername = cvalue.name.replace('+',' '), 
        token = cvalue.token; 
        mrole =  cvalue.role;
		mgroups= cvalue.grps;
		muserphone = cvalue.phone;
		mzip = cvalue.mzip;
    }
 
    $('[data-toggle="tooltip"]').tooltip();

    function nextSection(thisSec) {
        //console.log(thisSec);
        $('.next-sections').hide();
        $(thisSec).fadeIn(300);
    }

    nextSection('#main-section'); 
    /*--------------------------------- Registration Start --------------------------------------------*/
    function storeEmail(email, thisSec) {
        waitFunc('enable');
        $.ajax({
            url: "includes/ajax.php",
            type: 'post',
            data: { storeEmail: email },
            success: function(data) {
                var results = jQuery.parseJSON(JSON.stringify(data));
                if (results.MsgType == "Done") {
                    insID = results.insID;
                    nextSection(thisSec);
                    $('input[name=email2]').val(email);
                } else {
                    if (results.Msg == 'You are already registered.') {
                        alertFunc("danger", "You are already registered.");
                    } else {
                        alertFunc("danger", results.Msg);
                    }
                }
                waitFunc('disable')
            },
            error: function(textStatus, errorThrown) {
                waitFunc('disable');
                alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
            }
        });
    }

    $(document).on('click', '#nextBtn1', function(e) {
        e.stopImmediatePropagation();
        var email = $(this).parents('.input-group').find('input[name=email1]').val();
        if (!validateEmail(email)) {
            alertFunc('danger', 'Enter Email in Correct Format');
        } else {
            var thisSec = $(this).attr('data-sec');
            storeEmail(email, thisSec);
        }
    }); 
    
    $(document).on('click', '#nextBtn2', function(e) 
    {
        e.stopImmediatePropagation();
        var thisSec = $(this).attr('data-sec');
        nextSection(thisSec);
    });

    $(document).on('click', '#nextBtn6', function(e) {
        e.stopImmediatePropagation();
        var country = $(this).parents('.next-sections').find('select[name=country]').val();
        var zip = $(this).parents('.next-sections').find('input[name=zip]').val();
        var city = $(this).parents('.next-sections').find('input[name=city]').val();
        if (country == null) {
            alertFunc('danger', 'Select the Country!');
        } else if (zip == '') {
            alertFunc('danger', 'ZIP code is Empty!');
        } else if (zip.length != 5) {
            alertFunc('danger', 'Incorrect ZIP code!');
        } else if (city == '') {
            alertFunc('danger', 'City is Empty!');
        } else {
            reg_country = country;
            reg_zip = zip;
            reg_city = city;
            var thisSec = $(this).attr('data-sec');
            nextSection(thisSec);
        }
    });

    var vocation_count = 3;
    $(document).on('click', '#add_more_vocation', function(e) {
        e.stopImmediatePropagation();
        vocation_count++;
        var vocation = $('select[name="interests[1]"]').parents('.form-group').clone();
        $('.vocation_append').append(vocation);
        $('select[name="interests[1]"]:last').attr('name', 'interests[' + vocation_count + ']');
    });

    $(document).on('click', '#nextBtn3', function(e) {
        e.stopImmediatePropagation();
        $('.vocation_append select[name^="interests"]').each(function(i) {
            var selVal = $(this).val();
            if (selVal == 'null') { return }
            vocation_result[i] = selVal;
        });
        if (vocation_result.length == 0) {
            alertFunc('danger', 'At-least 1 vocation need to be selected!')
        } else {
            var thisSec = $(this).attr('data-sec');
            nextSection(thisSec);
        }
    }); 
	
    var groups_counts = 1;
    $(document).on('click', '#add_more_groups', function(e) {
        e.stopImmediatePropagation();
        groups_counts++;
        var groups = $('.groups_append').find('.form-group:eq(0)').clone();
        $('.groups_append').append(groups);
    });

    $(document).on('click', '#nextBtn4', function(e) {
        e.stopImmediatePropagation();
        $('.groups_append select[name^="groups"]').each(function(i) {
            var selVal = $(this).val();
            if (selVal == 'null') { return }
            groups_result[i] = selVal;
        });
        if (groups_result.length == 0) {
            alertFunc('danger', 'At-least 1 group need to be selected!')
        } else {
            var thisSec = $(this).attr('data-sec');
            nextSection(thisSec);
        }
    });

    var targets_counts = 3;
    $(document).on('click', '#add_more_trargeted_clients', function(e) {
        e.stopImmediatePropagation();
        targets_counts++;
        var target_client = $('select[name="targeted_clients[1]"]').parents('.form-group').clone();
        $('.targeted_client_append').append(target_client);
        $('select[name="targeted_clients[1]"]:last').attr('name', 'targeted_clients[' + target_client + ']');
    });
    
    $(document).on('click', '#nextBtn5', function(e) {
        e.stopImmediatePropagation();
        $('.targeted_client_append select[name^="targeted_clients"]').each(function(i) {
            var selVal = $(this).val();
            if (selVal == 'null') { return }
            target_clients[i] = selVal;
        });
        if (target_clients.length == 0) {
            alertFunc('danger', 'At-least 1 Targeted Client need to be selected!')
        } else {
            var thisSec = $(this).attr('data-sec');
            nextSection(thisSec);
        }
    }); 
	
    var targets_referral_count = 3;
    $(document).on('click', '#add_more_targeted_referral', function(e) {
        e.stopImmediatePropagation();
        targets_referral_count++;
        var target_client = $('select[name="targeted_referral_partners[1]"]').parents('.form-group').clone();
        $('.targeted_referral_append').append(target_client);
        $('select[name="targeted_referral_partners[1]"]:last').attr('name', 'targeted_referral_partners[' + targets_referral_count + ']');
    });

    $(document).on('click', '#nextBtn7', function(e) {
        e.stopImmediatePropagation();
        $('.targeted_referral_append select[name^="targeted_referral_partners"]').each(function(i) {
            var selVal = $(this).val();
            if (selVal == 'null') { return }
            target_referral_partners[i] = selVal;
        });
        if (target_referral_partners.length == 0) {
            alertFunc('danger', 'At-least 1 Targeted Client need to be selected!')
        } else {
            var thisSec = $(this).attr('data-sec');
            nextSection(thisSec);
        }
    });
	
    /*--------------------------------- Registration End --------------------------------------------*/ 
	
    $('.notifications, .contex-menu').remove();
    $('body').append('<div class="notifications"></div>' +
        '<div id="waitFunction">Please wait...</div>'); // wait function div
    $('.alert').hide();
 
    $(document)
        .on('show.bs.modal', '.modal', function(event) {
            $(this).appendTo($('body'));
        })
        .on('shown.bs.modal', '.modal.in', function(event) {
            setModalsAndBackdropsOrder();
        })
        .on('hidden.bs.modal', '.modal', function(event) {
            setModalsAndBackdropsOrder();
        });

    function setModalsAndBackdropsOrder() 
	{
		var modalZIndex = 1040;
		$('.modal.in').each(function(index) {
            var $modal = $(this);
            modalZIndex++;
            $modal.css('zIndex', modalZIndex);
            $modal.next('.modal-backdrop.in').addClass('hidden').css('zIndex', modalZIndex - 1);
        });
        $('.modal.in:visible:last').focus().next('.modal-backdrop.in').removeClass('hidden');
    }
});

$('[data-toggle="tooltip"]').tooltip();

$(function()
{
		$('#city_names').DataTable( {
			"paging":   false,
			"ordering": false,
			"info":     false 
		} ); 
		$('#vocation_names').DataTable( {
			"paging":   false,
			"ordering": false,
			"info":     false 
		} ); 
		$('#targetclient_names').DataTable( {
			"paging":   false,
			"ordering": false,
			"info":     false 
		} ); 
		
		$('#targetref_names').DataTable( {
			"paging":   false,
			"ordering": false,
			"info":     false 
		} ); 
});
	

// Register new user with email id
function updProfile(updProfForm)
{ 
	
	waitFunc('enable');
    //console.log('regArr',regUserArr);
    $.ajax({
        url: "includes/ajax.php",
        type: 'post',
        cache: false,
        contentType: false,
        processData: false,
        data: updProfForm,
        success: function(data)
        { 
			var results = jQuery.parseJSON(JSON.stringify(data));  
			if (results.MsgType == "Done") {
                alertFunc('success', results.Msg);
                // window.open('dashboard.php','_self');
            }  
            waitFunc('disable')
        } 
    });
}

$(document).on('click', 'select[name=membertype]', function(e) {
 
 var busitype = $(this).val();
 
	 if(busitype == null ) return;
	 
    if( $(this).val()  == 0 ) 
    { 
		$('#busininfoarea').addClass('hide');
		$('#busininfoarea').removeClass('show');
        //$('input[name=busi_location]').prop("disabled", true);
        //$('input[name=busi_type]').prop("disabled", true);
        //$('input[name=busi_hours]').prop("disabled", true);
    }
    else 
    { 
		$('#busininfoarea').addClass('show');
		$('#busininfoarea').removeClass('hide');
        //$('input[name=busi_location]').prop("disabled", false);
        //$('input[name=busi_type]').prop("disabled", false);
        //$('input[name=busi_hours]').prop("disabled", false);
    } 

});
 

$(document).on('click', '.regUser', function(e) {
    e.stopImmediatePropagation();
    var first_name = $(this).parents('.sec_two').find('input[name=first_name]').val();
    var email2 = $(this).parents('.sec_two').find('input[name=email2]').val();
    var last_name = $(this).parents('.sec_two').find('input[name=last_name]').val();
    var password = $(this).parents('.sec_two').find('input[name=password]').val();
	
	
	var country = $(this).parents('.sec_two').find('select[name=country]').val();
	var street = $(this).parents('.sec_two').find('input[name=street]').val();
	var zip = $(this).parents('.sec_two').find('input[name=zip]').val();
	var city = $(this).parents('.sec_two').find('input[name=city]').val();
    var membertype =$(this).parents('.sec_two').find('select[name=membertype]').val();
    var busi_name =$(this).parents('.sec_two').find('input[name=busi_name]').val();
	var busi_location_street =$(this).parents('.sec_two').find('input[name=busi_location_street]').val();
    var busi_location =$(this).parents('.sec_two').find('input[name=busi_location]').val();
    var busi_type =$(this).parents('.sec_two').find('input[name=busi_type]').val();
    var busi_hours =$(this).parents('.sec_two').find('input[name=busi_hours]').val();
    var busi_website =$(this).parents('.sec_two').find('input[name=busi_website]').val();
      
	 
    var check = 0;
    if (!validateEmail(email2)) 
    {
        alertFunc('danger', 'Insert Email in Corrent Format');
        check = 1;
    }
    else if (first_name == '')
    {
        alertFunc('danger', 'First Name is Empty!');
        check = 1;
    }
    else if (last_name == '')
    {
        alertFunc('danger', 'Last Name is Empty!');
        check = 1;
    }
    else if (password == '')
    {
        alertFunc('danger', 'Password Field is Empty!');
        check = 1;
    } else if (country == null)
    {
		alertFunc('danger', 'Select the Country!');
		check = 1;
    }
    else if (zip == '')
    {
		alertFunc('danger', 'ZIP code is Empty!');
		check = 1;
    }
    else if (zip.length != 5) {
		alertFunc('danger', 'Incorrect ZIP code!');
		check = 1;
	} else if (city == '') {
		alertFunc('danger', 'City is Empty!');
		check = 1;
	} else 
	{
		reg_email = email2;
        reg_first_name = first_name;
        reg_last_name = last_name;
        reg_password = password; 
		reg_membertype = membertype;
		reg_country = country;
		reg_street=street;
		reg_zip = zip;
        reg_city = city; 
        busi_name =busi_name;
        busi_location =busi_location;
        busi_type = busi_type;
        busi_hours = busi_hours; 
        busi_website=busi_website;
		busi_location_street = busi_location_street; 
    }
 
    if (check == 0) {
        var updProfForm = new FormData(),
            updProf = {
                reg_email: reg_email,
                reg_first_name: reg_first_name,
                reg_last_name: reg_last_name,
                reg_password: reg_password,
                reg_country: reg_country,
				reg_street: reg_street,
				reg_zip: reg_zip,
				reg_city: reg_city,
                vocation_result: vocation_result,
                groups_result: groups_result,
                target_clients: target_clients,
                target_referral_partners: target_referral_partners,
                reg_membertype: reg_membertype,
                busi_name:busi_name,
				busi_location_street:busi_location_street,
                busi_location:busi_location,
                busi_type:busi_type,
                busi_hours:busi_hours  ,
                busi_website:busi_website 
            };

        updProfForm.append('updProf', 'updProf');
        if ($('#blah').val() != "undefined") {
            updProfForm.append('image', $('#usrImg').prop('files')[0]);
        }
        updProfForm.append('insID', insID);
        updProfForm.append('reg_email', reg_email);
        updProfForm.append('reg_first_name', reg_first_name);
        updProfForm.append('reg_last_name', reg_last_name);
        updProfForm.append('reg_password', reg_password);
        updProfForm.append('reg_country', reg_country);
		updProfForm.append('reg_street', reg_street);
        updProfForm.append('reg_zip', reg_zip);
        updProfForm.append('reg_city', reg_city);
        updProfForm.append('vocation_result', vocation_result);
        updProfForm.append('groups_result', groups_result);
        updProfForm.append('target_clients', target_clients);
        updProfForm.append('target_referral_partners', target_referral_partners);
        updProfForm.append( 'reg_membertype' , reg_membertype );
        updProfForm.append( 'busi_name' , busi_name );
		updProfForm.append( 'busi_location_street' , busi_location_street );
        updProfForm.append( 'busi_location' , busi_location );
        updProfForm.append( 'busi_hours' , busi_hours );
        updProfForm.append( 'busi_type' , busi_type );
        updProfForm.append('busi_website', busi_website);
         
        updProfile(updProfForm);
        var thisSec = $(this).attr('data-sec');
        $('.next-sections').hide();
        $(thisSec).fadeIn(300); 
    }
});

$(document).on('click', '.regdet_update', function(e) {
    e.stopImmediatePropagation(); 
    var updProfForm = new FormData(),
        updProf = {
            reg_country: reg_country,
            vocation_result: vocation_result,
            groups_result: groups_result,
            target_clients: target_clients
        };

    //updProfForm.append('updProf', 'updProf');
    if ($('#blah').val() != "undefined")
	{
		updProfForm.append('image', $('#usrImg').prop('files')[0]);
    }
	
	updProfForm.append('insID', insID);
    updProfForm.append('reg_country', reg_country);
	updProfForm.append('reg_street', reg_street);
    updProfForm.append('reg_zip', reg_zip);
    updProfForm.append('reg_city', reg_city);
    updProfForm.append('vocation_result', vocation_result);
    updProfForm.append('groups_result', groups_result);
    updProfForm.append('target_clients', target_clients);
    updProfForm.append('target_referral_partners', target_referral_partners);
    updProfForm.append('regdet_update', '1');
	
	waitFunc('enable');
    $.ajax({
		url: "includes/ajax.php",
        type: 'post',
        cache: false,
        contentType: false,
        processData: false,
        data: updProfForm,
        success: function(data) 
        {
			var results = jQuery.parseJSON(JSON.stringify(data));
            if (results.MsgType == "Done")
			{
				alertFunc('success', results.Msg);
                window.open('dashboard.php', '_self');
            }
            else
            {
                alertFunc('danger', results.Msg);
            }
            waitFunc('disable')
        },
        error: function(textStatus, errorThrown)
		{
            waitFunc('disable');
            alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
        }
    }); 
});

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


// Image Preview
function imgPrev(selector, prevImg) {
    if (selector.files && selector.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $(prevImg).attr('src', e.target.result);
        };

        reader.readAsDataURL(selector.files[0]);
    }
}


// email validation
function validateEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}


// Forgot password
function resetPW(emAdd) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { resetPW: emAdd },
        success: function(data) {
            if (data == 'success') {
                alertFunc(data, 'Password reset link has been sent to this email.')
            } else {
                alertFunc('danger', data);
            }
            waitFunc('');
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
            waitFunc('');
        }
    });
}
$(document).on('click', '#resPWBtn', function(e) {
    e.stopImmediatePropagation();
    var emAdd = $('#forgPWEmail').val().trim();
    if (validateEmail(emAdd) == false) {
        alertFunc('warning', 'Enter valid email address please.')
    } else {
        resetPW(emAdd);
    }
});
  

// Tabs
$(document).on('click', '.contentPages li a', function() {
    var id = $(this).attr('data-id');
    var page = $(this).text().trim();
    $('.pageType').slideUp('slow');
    $('.pageType.' + id).slideDown('slow').attr('data-id', id);
});

$(document).on('click', '.close_drop', function() {
    $('.contentPages').slideUp('slow');
});

$(document).on('change', '.btn-file :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});


// Sign in user
function signIn(user_email, user_pass, rememberme) 
{ 
	 
     $.ajax({
        type: 'post',
        url: aurl + 'login/',
        data: { email: user_email, password: user_pass, rememberme: (rememberme == true ? 1 :  0)  },
        success: function(data) {
            
            data = $.parseJSON(data);   
            if (data.id ==  0 ) {
                alertFunc('danger', 'Email or password not found');
            } else if (data.id >  0 ) 
            {
                if (data.status  == 0  ) 
                {
                    alertFunc('info', 'Your account is not deactivated. Please contact Admin My City.');
                }
                else
                {
                    var exdate=new Date();
                    exdate.setDate(exdate.getTime()+60*60*1000*24); 
                    document.cookie = "_mcu= " + JSON.stringify( data ) + "; " + exdate.toUTCString() ; 
				    document.cookie = "_rmtoken=" + JSON.stringify( data.retoken ) + "; expires="   + exdate.toUTCString() ; 
					  
					   
				  var now = new Date();
				  var time = now.getTime();
				  var expireTime = time + 1000*36000;
				  now.setTime(expireTime); 
				  document.cookie = "_rmtoken=" + JSON.stringify( data.retoken ) + ";expires=" +now.toGMTString() ;
				  
				  window.open('dashboard.php','_self');  
                } 
            }  
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

function signInValidation(user_email, user_pass, rememberme)
{
    if (validateEmail(user_email) == false)
    {
        alertFunc('danger', 'Email address not valid');
        return;
    }
    if (user_pass == '')
    {
        alertFunc('danger', 'Please enter your password');
        return;
    }
    signIn(user_email, user_pass, rememberme);
}

$(document).on('click', '#sign_in_button', function()
{
    var user_email = $('#login_username').val().trim();
    var user_pass = $('#login_password').val().trim();
    signInValidation(user_email, user_pass);
}); 

$(document).on('click', '#form_sign_in_button', function()
{
	var user_email = $('#form_login_username').val().trim();
	var user_pass = $('#form_login_password').val().trim();
	var rememberme = $('#form_login_remember_me').prop('checked');
	signInValidation(user_email, user_pass, rememberme );
});


$(document).on('keypress', '#signin #login_username,#signin #login_password', function(e)
{
	e.stopImmediatePropagation();
	if (e.which == 13)
	{
		var user_email = $('#login_username').val().trim();
        var user_pass = $('#login_password').val().trim();
        signInValidation(user_email, user_pass);
    }
});
 

// Get User References
function getUserClients(getUserClients) 
{
	waitFunc('enable');
    $.ajax({
		type: 'post',
        url: 'includes/ajax.php',
        data: { getUserClients: getUserClients },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('.clientsUsers').html(data);
                $('[data-toggle="tooltip"]').tooltip();
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

//Get User Suggested Clients
function getUserSuggested(getUserSuggested) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getUserSuggested: getUserSuggested },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('#sgstdrslts').html(data);
                $('[data-toggle="tooltip"]').tooltip();
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again');
        }
    });
}

//Get User Suggested Partners
function getUserSuggestedPartners(getUserSuggestedPartners) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getUserSuggestedPartners: getUserSuggestedPartners },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('#sgstdprtnrsrslts').html(data);
                $('[data-toggle="tooltip"]').tooltip();
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again');
        }
    });
}  



$(document).on('click', '.loadsearchlog', function() { 
	getSearchlogs(1);
}) 
$(document).on('click', '.loadknowsormembers', function() {
	
	getUserClients('1');
})
$(document).on('click', '.loadquestions', function() {
	
	getQues();
})
 
$(document).on('click', '.loadhomesearchlog', function() {
	getHomeSearchlogs(1);
})


function getSearchlogs(page) 
{ 
     
    var goto = page;
    var pagesize = 10;   
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl+ 'logs/vocationsearch/',
        data: { goto: goto, pagesize:pagesize },
        success: function(data) {
            waitFunc('');
            if (data.error ==  1 ) {
                alertFunc('danger', 'Something went wrong, please try again')
            }
            else 
            {
                data = $.parseJSON(data);
                html = "";
               
                $.each(data.result, function (index, item) 
                {
                   
                    html += "<tr id='row" + index + "'>" + 
                    "<td>" + item.username + "</td>" +
                    "<td>" + item.vocation + "</td>" +
                    "<td>" + item.location + "</td>" +
                    "<td>" + item.created_at + "</td>" + 
                    "</tr>"; 
                });
 
                var pages = data.pages; 
                var prev =  goto == 1 ? 1 :  parseInt(goto) -1;
                var next =  goto ==  pages ?  pages :  parseInt(goto) + 1; 
                html  += "<tr><td colspan='8'><ul class='pagination pageslog'><li><a data-func='prev' data-pg='" + prev + "'>«</a></li>";
                for( i=1;  i <= pages;  i++)
                {
                    active =  i ==  goto ? 'active' : '';
                    html += "<li class='" + active + "'><a data-pg='" + i + "'>" + i + "</a></li>";
                }
                
                html += "<li><a data-func='next' data-pg='" + next +  "'>»</a></li></ul></td></tr>";
    
                $('.SearcLogs').html(html);
                 $('[data-toggle="tooltip"]').tooltip();
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

// Get Home Search Logs
function getHomeSearchlogs(page) {

    var goto = page ;
    var pagesize = 10;   
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl+ 'logs/homesearch/',
        data: { goto: goto, pagesize:pagesize },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
               //$('.HomeSearchLogs').html(data);
                data = $.parseJSON(data);

                var html = "";
                $.each(data, function (index, item) 
                {
                     client_name = item.city ;
                     client_profession = item.zip ;
                     client_phone = (item.vocation=='null'? 'Not Specified' : item.vocation)  ;
                     client_entrydate =  item.created_at ;

        
                    html += "<tr id='$rand-$id'>" +
                        "<td>"  + client_name  + "</td>" +
                        "<td>" + client_profession +  "</td>" +
                        "<td>" + client_phone + "</td>" +
                        "<td>" + client_entrydate + "</td>" +
                        "</tr> " ;
                });
            
            var pages = 10; 
            var prev =  goto == 1 ? 1 :  parseInt(goto ) -1;
            var next =  goto ==  pages ?  pages :    parseInt(goto ) + 1; 
            html  += "<tr><td colspan='8'><ul class='pagination homesearchlog'><li><a data-func='prev' data-pg='" + prev + "'>«</a></li>";
            for( i=1;  i <= pages;  i++)
            {
                active =  i ==    parseInt(goto ) ? 'active' : '';
                html += "<li class='" + active + "'><a data-pg='" + i + "'>" + i + "</a></li>";
            }
            
            html += "<li><a data-func='next' data-pg='" + next +  "'>»</a></li></ul></td></tr>";

            $('.HomeSearchLogs').html(html); 
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

// Get Msg logs
function getMsglogs(page) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getMsglogs: page },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('.SearcMsgLogs').html(data);
                $('[data-toggle="tooltip"]').tooltip();
            }
        },
        error: function()
        {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}


// View user references
function viewUserRef(user, name, voc, ema, loc, view) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getUser: user, view: view, name: name , voc : voc, ema : ema, loc :loc},
        success: function(data) {
            waitFunc('');
            if (data == 'error') {  
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('.userDetails').html(data);
                $('[data-toggle="tooltip"]').tooltip();
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
} 

// Add user clients
function addClientUser(id, client_name, client_pro, client_ph, client_email, client_location, client_zip, client_note, ques_rate, ques, user_grp, ques_text) {
    waitFunc('enable');
    var data = {
        'id': id,
        'client_name': client_name,
        'client_pro': client_pro,
        'client_ph': client_ph,
        'client_email': client_email,
        'client_location': client_location,
        'client_zip': client_zip,
        'client_note': client_note,
        'user_grp': user_grp,
        'ques_rate': ques_rate,
        'ques': ques,
        'ques_text': ques_text,
    };
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addClientUser: data },
        success: function(data) {
            waitFunc('');
            if (data == 'match') {
                alertFunc('danger', 'Sorry, user with this email already added');
                client_suc_status = 0;
            } else if (data == 'success') {
                alertFunc('success', 'User successfully saved');
                client_suc_status = 1;
                //clearing controls 
                $('.client_name').val("");
                $('.client_ph').val("");
                $('.client_email').val("");
                $('.client_location').val("");
                $('.client_note').val("");
                $('.user_ques_text_add').val("");
				
				$(".user_ques_text_add").val('').trigger("chosen:updated");
				$(".client_pro").val('').trigger("chosen:updated");
				
				 
                if (id == 0)
                    getUserClients('1');
            } else if (data == 'limit') {
                alertFunc('warning', 'Upgrade your account to add more references!');
            }
        },
        error: function() {
            alertFunc('info', 'Something went wrong, please try again');
            waitFunc('');
        }
    });
}

//add new know (updated)
function addNewKnow(id, client_name, client_pro, client_ph, client_email, client_location, client_zip, client_note, ques_rate, ques, user_grp, ques_text, client_lifestyle) {
    waitFunc('enable');
    var interestedprofessions = $(".user_ques_text_add").chosen().val() + '';
    var data = {
        'id': id,
        'client_name': client_name,
        'client_pro': client_pro,
        'client_ph': client_ph,
        'client_email': client_email,
        'client_location': client_location,
        'client_zip': client_zip,
        'client_note': client_note,
        'user_grp': user_grp,
        'ques_rate': ques_rate,
        'ques': ques,
        'ques_text': ques_text,
        'client_lifestyle': client_lifestyle
    };
     
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addClientUser: data },
        success: function(data) {
            if (data == 'no_session') 
            {
				alertFunc('danger', 'Your session has expired!');
				window.location='index.php'; 
            } 
            if (data == 'match') 
			{
				alertFunc('danger', 'Sorry, user with this email already added');
                client_suc_status = 0;
            }
			else if (data == 'success' || parseInt(data) > 0) {
                alertFunc('success', 'User successfully saved');
                client_suc_status = 1;

                if (parseInt(data) > 0) {
                    //reward loyalty point if new know entry is being done
                    if (id == 0) {
                        raiseloyaltypoint(10, 'Contact Addition');
                        //fetch suggested referrals 
                       // loadsuggestedrefferals(data, interestedprofessions, client_zip);
                    }
                } else if (data == 'success' && id > 0) {
                    var professions = $(".user_ques_text_ed").chosen().val() + '';
                    //regeneratesuggestedrefferals(id, professions, client_zip);
                }
                //clearing controls 
                $('.client_name').val("");
                $('.client_ph').val("");
                $('.client_email').val("");
                $('.client_location').val("");
                $('.client_note').val(""); 
                $('.client_lifestyle').val('0');  
                $('.client_zip').val("");
                $('.client_pro').val("");
                $('#e_prof_chosen ul.chosen-choices li.search-choice').remove(); 
                $('#answer9_chosen ul.chosen-choices li.search-choice').remove(); 
				
				$(".user_ques_text_add").val('').trigger("chosen:updated");
				$(".client_pro").val('').trigger("chosen:updated");
				
                if (id == 0)
                    getUserClients('1');
            } else if (data == 'limit') {
                alertFunc('warning', 'Upgrade your account to add more references!');
            }
            waitFunc('');
        },
        error: function() {
            alertFunc('info', 'Something went wrong, please try again');
            waitFunc('');
        }
    });
} 

//api
$(document).on('click', '.addnewknow', function() {
    
	 
    var interestedprofessions = $(".user_ques_text_add").chosen().val() + '';
    
     var client_name = $('.client_name').val().trim();
     var client_pro = $('.client_pro').val() + '';
     var client_ph = $('.client_ph').val().trim();
     var client_email = $('.client_email').val().trim();
     var client_location = $('.client_location').val() + '';
     var client_zip = $('.client_zip').val().trim();
     var client_note = $('.client_note').val().trim();
     var client_lifestyle = $('.client_lifestyle').val() + '';
     var client_tags = $('.client_tags').val()  + '';


     if (client_name == '')
     {
         alertFunc('danger', 'Please provide a name');
         return;
     }
     if (validateEmail(client_email) == false)
     {
         alertFunc('danger', 'Email not valid');
         return;
     }
     
     var ques_rate = [],
         ques = [];
    
     $('.user_ques_main').each(function(i) {
         ques[i] = $(this).attr('data-ques');
         ques_rate[i] = $(this).find('.user_ques:checked').val();
     });

     var user_ques_text = []; 
     i=0;
     var quesid = $(".user_ques_text_add").attr('data-ques');
     var answer = $(".user_ques_text_add").chosen().val() + '';
     if (answer) 
     {
         user_ques_text[i] = {
             id: quesid,
             answer: answer.toString()
        };
    } 
     var user_grp = $('.user_grp').val(); 
     waitFunc('enable');
     $.ajax({
         
         type: 'post',
         url: aurl + '/knows/add/',
         data: { user_id:mid,  client_name:client_name, client_pro:client_pro, client_ph:client_ph, client_email:client_email, 
            client_location:client_location, client_zip:client_zip, client_note:client_note, ques_rate:ques_rate, 
            ques:ques, user_grp:user_grp, user_ques_text:user_ques_text, client_lifestyle:client_lifestyle, client_tags: client_tags },
            success: function(data) 
            {
                data = $.parseJSON(data);
    
                waitFunc(''); 
                if(data.error == 0) 
                {        
                    alertFunc('info', 'Know information added successfully!');
                     
                    if (data.action  ==  'i') 
                    {
                        
                        //reward loyalty point if new know entry is being done
                        
                            raiseloyaltypoint(10, 'Contact Addition');
                            
                            
                            //fetch suggested referrals 
                           // loadsuggestedrefferals(data.knowid, interestedprofessions, client_zip);
                         
                    }
                    else if (data.action  ==  'u') 
                    {
                        var professions = $(".user_ques_text_ed").chosen().val() + '';
                        //regeneratesuggestedrefferals(id, professions, client_zip);
                    } 
                  

                    $('.client_name').val("");
                    $('.client_ph').val("");
                    $('.client_email').val("");
                    $('.client_location').val("");
					$('.client_location ul.chosen-choices li.search-choice').remove();  
                    $('.client_note').val(""); 
                    $('.client_lifestyle').val();  
                    $('.client_zip').val("");
                    $('.client_pro').val("");
                    $('.client_tags').val("");
                    $('#e_prof_chosen ul.chosen-choices li.search-choice').remove(); 
                    $('#answer9_chosen ul.chosen-choices li.search-choice').remove(); 
                    
                    $(".user_ques_text_add").val('').trigger("chosen:updated");
                    $(".client_pro").val('').trigger("chosen:updated");
                    $(".client_lifestyle").val('').trigger("chosen:updated");
					$(".client_location").val('').trigger("chosen:updated");
                    $(".client_tags").val('').trigger("chosen:updated");
                    
                }
                else 
                    alertFunc('info',  data.errmsg );
            },
            error: function() 
            {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again!')
            }
     }); 
     
     if (client_suc_status == 1)
     {
         $('.client_name').val("");
         $('.client_ph').val("");
         $('.client_email').val("");
         $('.client_location').val("");
         $('.client_zip').val("");
         $('.client_pro').val("");
         $('.user_ques_text_add').val("");
         $(".user_ques_text_add").val('').trigger("chosen:updated");
         $(".client_pro").val('').trigger("chosen:updated");
     }
 });


$(document).on('click', '.addClientUser', function()
{
    var client_name = $('.client_name').val().trim();
    var client_pro = $('.client_pro').val() + '';
    var client_ph = $('.client_ph').val().trim();
    var client_email = $('.client_email').val().trim();
    var client_location = $('.client_location').val().trim();
    var client_zip = $('.client_zip').val().trim();
    var client_note = $('.client_note').val().trim();
    var client_lifestyle = $('.client_lifestyle').val() + '';
	 
	if (client_name == '')
	{
		alertFunc('danger', 'Please provide a name');
        return;
    }
    if (validateEmail(client_email) == false)
	{
        alertFunc('danger', 'Email not valid');
        return;
    }
	
    var ques_rate = [],
        ques = [];
    var user_ques_text = [];
    $('.user_ques_main').each(function(i) {
        ques[i] = $(this).attr('data-ques');
        ques_rate[i] = $(this).find('.user_ques:checked').val();
    });
    $('.user_ques_text_add').each(function(i) {
        var id = $(this).attr('data-ques');
        var answer = $(".chosen-select").chosen().val();

        if (answer) {
            user_ques_text[i] = {
                id: id,
                answer: answer.toString()
            };
        }
    });
	
    var user_grp = $('.user_grp').val();
      
    //var values =  $(".chosen-select").chosen().val();
    addNewKnow(0, client_name, client_pro, client_ph, client_email, client_location, client_zip, client_note, ques_rate, ques, user_grp, user_ques_text, client_lifestyle);
    if (client_suc_status == 1) {
        $('.client_name').val("");
        $('.client_ph').val("");
        $('.client_email').val("");
        $('.client_location').val("");
        $('.client_zip').val("");
        $('.client_pro').val("");
        $('.user_ques_text_add').val("");  
		
		$(".user_ques_text_add").val('').trigger("chosen:updated");
		$(".client_pro").val('').trigger("chosen:updated");
    } 
});
 

//Delete User Client
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


// Get Questions
function getQues() {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'questions/', 
        success: function(data) {

            data = $.parseJSON(data);
            waitFunc(''); 
            html ='';
            i=1;
            $.each (data.results, function(index, item) 
            {
                name = "question0".$i; 

                rating_selected='';
                text_selected   = '';
                if( item.c == 'text')
                    text_selected = 'selected';
                else if( item.c ==  'rating')
                     rating_selected = 'selected';
        
                html +="<div class='col-xs-12 no-padd ques_low'>" +
                " <div class='col-sm-6 col-xs-12 padd-8'>" +
                " <input type='text' value='" +item.b +"' class='form-control question_fld' data-id='" +item.a +"'>" +
                " </div>" +
                "<div class='col-sm-5 text-center col-xs-12 padd-8'>" +
                "  <span style='margin-top: 10px; display: inline-block;' class='starRating main'>" +
                "<select class='form-control quesList-" +item.a +"'>" +
                "<option value='rating' " + rating_selected + ">Rating</option>" +
                "<option value='text' " + text_selected + ">Text</option>" +
                "</select>" +
                "</div>" +
                "<div class='col-sm-1 col-xs-12 padd-8'>" +
                "<button class=' btn-danger btn btn-xs rmvQues' data-id='" +item.a +"' style='margin-top: 10px '>" +
                "<i class='fa fa-times-circle'></i>" +
                "</button>" +
                "</div>" +
                "</div>";
         i++;
            }); 

            $('.questionsData').html('');
            if (data.error == 1) 
            {
                alertFunc('danger', 'Something went wrong, please try again')
            }
            else if (data.error == 10) 
            {
                alertFunc('info', data.errmsg) ;
            }else 
            { 
                $('.questionsData').html(html);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}


// Add New Question
$(document).on('click', '.addNewQues', function() {

    var inc = $('.questionsData .col-xs-12.no-padd').size() + 1;
    var name = 'question01' + inc;

    var ques = '<div class="col-xs-12 no-padd ques_low">' +
        '<div class="col-sm-6 col-xs-12 padd-8">' +
        '<input type="text" placeholder="Enter new question" class="form-control question_fld" data-id="stg-0">' +
        '</div>' +
        '<div class="col-sm-5 text-center col-xs-12 padd-8">' +
        '<span style="margin-top: 10px; display: inline-block;" class="starRating main">' +
        '<select class="form-control quesList-stg-0">' +
        '<option value="rating">Rating</option>' +
        '<option value="text">Text</option>' +
        '</select>' +
        '</span>' +
        '</div>' +
        '<div class="col-sm-1 col-xs-12 padd-8">' +
        '<button class=" btn-danger btn btn-xs rmvQues" data-id="stg-0" style="margin-top: 10px">' +
        '<i class="fa fa-times-circle"></i>' +
        '</button>' +
        '</div>' +
        '</div>';

    $('.questionsData').append(ques);
});


// Save questions
$(document).on('click', '.saveQues', function() 
{
    var allQues = [];
    $('.question_fld').each(function(i) {
        var data_id = $(this).attr('data-id');
        allQues.push({
            'data_value': $(this).val().trim(),
            'data_id': data_id,
            'q_type': $('.quesList-' + data_id).val()
        });
    });

     
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'savequestions/',
        data: { allQues: allQues, role: mrole  },
        success: function(data) {
            data = $.parseJSON(data); 
            waitFunc('');
            if (data.error ==  1 ) {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                getQues();
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


// Remove Question
$(document).on('click', '.rmvQues', function() {

    var thisBtn = $(this);
    var id = $(this).attr('data-id');
    confFunc('Are you sure you want to delete this question?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url:  aurl + 'delete/', 
            data: { id: id, role: mrole },
            success: function(data) {
                waitFunc('');
                data = $.parseJSON(data);
                if (data.error == 1) 
                {
                    alertFunc('danger', 'Something went wrong, please try again')
                }else 
                if (data.error == 10) 
                {
                    alertFunc('info',  data.errmsg)
                }
                else 
                {
                    alertFunc('success',  data.errmsg)
                    $(thisBtn).parents('.ques_low').remove();
                }
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});


// Get Al Groups
function getAlGroups() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getGroups: 'getGroups' },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again');
            } else {
                $('.userClientGrps').html('<option value="null">-select group-</option>' + data);
                $('.user_grp').html(data);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}


// Add New Group
$(document).on('click', '.addNewGroup', function() {
    var groupName = $('.groupName').val().trim();

    if (groupName == '') { alertFunc('danger', 'Please enter group name!'); return; }

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'groups/save/',
        data: { addGroup: groupName , id:0, groupname: groupName, role: mrole},
        success: function(data) {
            data = $.parseJSON(data);
            waitFunc('');
            if (data.error ==  0 ) {
                alertFunc('success', data.errmsg );
                $('.groupName').val('');
                getAlGroups();
            } else {
               
                alertFunc('danger', data.errmsg); 
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

  
$(document).on('click', '.loadprofile', function() 
{
	 reloadselfprofile(); 
});

function reloadselfprofile()
{
	
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  aurl + "member/getbyid/"  ,
        data: { profileid : mid },
        success: function(data) 
		{
			 
			data = $.parseJSON(data); 
			 
            waitFunc('');
            if (data.error == '1' || data.error == '10')
            {
				
            }
            else 
            { 
				$('#profilep').html(
                    "<strong>" + data[0].username + "<br/>" + data[0].user_email + 
                    "<br/>Phone: " + data[0].user_phone +  
                    "<br/>Package Name:" +data[0].user_pkg + "</strong>"); 
                    $('#groupnames').html(data[0].group_names);
				$('#memberdetails').html(
                        "<p><strong>Target Clients:</strong> " + 
                        ( data[0].target_clients =='' ? 'Not Specified':  data[0].target_clients ) + "</p>" +
                        "<p><strong>Target Referral Partners:</strong> " + 
                        ( data[0].target_referral_partners =='' ? 'Not Specified':  data[0].target_referral_partners ) + "</p>" +
                        "<p><strong>Vocation:</strong> " + 
                        ( data[0].vocations =='' ? 'Not Specified':  data[0].vocations ) + "</p>");    

				$('#businessi').html(
                    "<strong>" + data[0].busi_name + "</strong>" +
					"<br/><strong>Business Type:</strong> " + data[0].busi_type +  
                    "<br/><strong>Location: </strong>" +data[0].busi_location + 
					"<br/><strong>Business Hours: </strong>" +data[0].busi_hours  ); 			
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
} 

// Add New Group
$(document).on('click', '.addNewVoc', function() {
    var vocationName = $('.vocationName').val().trim();
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addVocation: vocationName },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Vocation with name "' + vocationName + '" already exists!');
            } else {
                getAlVocation();
                alertFunc('success', 'Successfully added');
                //$('.groupName').val('');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


// Update/Edit Vocations ********
$(document).on('change', '.fetVocations', function() {
    var currVoc = $('.fetVocations option:selected').text();
    var currVocVal = $(this).val();
    if (currVocVal == 'null') {
        $('.editVocation').val('');
        return;
    }
    $('.editVocation').val(currVoc).attr('data-val', currVocVal);
}); 




// Update/Edit Lifestyles ********
$(document).on('change', '.fetchLifestyles', function() {
    var currLifestyle = $('.fetchLifestyles option:selected').text();
    var currLifestyleVal = $(this).val();
    if (currLifestyleVal == 'null') {
        $('.editLifestyle').val('');
        return;
    }
    $('.editLifestyle').val(currLifestyle).attr('data-val', currLifestyleVal);

});

// Get Al vocation
function getLifestyles() {
    $.ajax({
        type: 'post',
        url: aurl + 'lifestyle/',
        data: { id: 0  },
        success: function(data) 
        {
            data = $.parseJSON(data);
            waitFunc('');
            if (data.error ==1  ) {
                alertFunc('danger', 'Something went wrong, please try again');
            }
            else  if (data.error == 10  ) {
                alertFunc('info', data.errmsg);
            }else
            { 
                html = '<option value="null">-Select Lifestyle-</option>';
                $.each(data.results, function(index, item){ 
                    html +=  "<option value='" + item.id  + "'>" + item.ls_name + "</option>"; 
                })  
                $('.fetchLifestyles').html( html );
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

  

// SEARCH TARGET CLIENT/PARTNER
$(document).on("click", '.srchTarget', function() {
    var nameSrch = $("#nameSrch").val();
    waitFunc("enable");
    $.ajax({

        type: 'post',
        url: 'includes/ajax.php',
        data: {
            srchTarget: '1',
            nameSrch: nameSrch
        },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('info', 'Something went wrong, please try again');
            } else {
                $('.targetDtls').show();
                $('#srchTargetRslts').html(data);
            }
        }
    });
});

/* $(document).on('click', '.srchTarget', function () {
	var targetSrch = $('#targetSrch').val();
	console.log(targetSrch);
	
	waitFunc('enable');
	$.ajax({
		type: 'post',
		url: 'includes/ajax.php',
		data: {
			targetSrch: targetSrch
		},
		success: function (data) {
			waitFunc('');
			if(data == 'error') {
				alertFunc('info', 'Something went wrong, please try again');
			} else {
				$('.targetDtls').show();
				$('#srchTargetRslts').html(data);
			}
		}
	});
}); */

// SEARCH
$(document).on('click', '.srchPeople', function() {
    
    var vocSrch = $('#vocSrch').val();
    var locSrch = $('#locSrch').val().trim();
    var nameSrch = $('#nameSrch').val();																																
	
	if( typeof locSrch === 'empty' ||  locSrch =='' || typeof vocSrch === 'empty' ||  vocSrch =='')
	{
		alertFunc('danger', 'Miissing search filter. Please provide vocation and zip code.') 
		return;		
	} 
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'member/search/',
        data: { srchPeople: '1', goto: '1', vocSrch: vocSrch, locSrch: locSrch, nameSrch: nameSrch , userid: mid, role: mrole, groups: mgroups, mzip: mzip },
        success: function(data) {
            waitFunc('');  
            data = $.parseJSON(data); 
			 
			$('.srdDtls').show();
			$('#srchrslts').html(data.results); 
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});
 

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
        url: 'includes/ajax.php',
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


//Leave A Message
$(document).on('click', '.leaveMsg', function() {
    $('#myModal').attr('data-id', $(this).attr('id'));
});

$(document).on('click', '.leaveUserMsg', function() {
    var send_to = $('#myModal').attr('data-id');
    var sender_name = $('#sender_name').val().trim();
    var sender_email = $('#sender_email').val().trim();
    var sender_msg = $('#sender_msg').val().trim();

    if (sender_name == '') {
        alertFunc('danger', 'Please provide a name');
        return;
    }
    if (validateEmail(sender_email) == false) {
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
            }else if (data.error == 10 || data.error == 11) 
            {
                alertFunc('info',  data.errmsg)
            }
            else 
            {
                alertFunc('success', 'Your message has been sent');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    }); 
}); 


// Contact us
$(document).on('keypress', 'input[name="phone"], input[name="ref_sh_conn"], input[name="tar_conn"], input[name="ref_conn"]', function(e) {
    return !(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57));
});

$(document).on('click', 'button[name="submit_contact"]', function(event) {
    event.preventDefault();
    var fname = $('input[name="fname"]').val().trim();
    var email = $('input[name="email"]').val().trim();
    var company = $('input[name="company"]').val().trim();
    var phone = $('input[name="phone"]').val().trim();
    var message = $('textarea[name="message"]').val().trim();

    if (fname == '') { alertFunc('warning', 'Please provide your name'); return; }
    if (validateEmail(email) == false) { alertFunc('danger', 'Email address not valid!'); return; }
    if (company == '') { alertFunc('warning', 'Please provide your company name'); return; }
    if (phone == '') { alertFunc('warning', 'Please provide your phone number'); return; }
    if (message == '') { alertFunc('warning', 'Please enter your message'); return; }

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { fname: fname, email: email, company: company, phone: phone, contact_us: message },
        success: function(data) {

            waitFunc('');
            if (data == 'error') {

                alertFunc('info', 'Something went wrong, please try again')
            } else {
                $("#contectForm")[0].reset();
                alertFunc('success', 'Thanks for contacting with us, we will get get back to you soon!')
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });

});

// pagination
$(document).on('click', '.pagimlog li', function() {
    var page = $(this).find('a').attr('data-pg');
    getMsglogs(page);
});

// pagination
$(document).on('click', '.pageslog li', function() {
    var page = $(this).find('a').attr('data-pg');
    getSearchlogs(page);
});

// pagination for home search log
$(document).on('click', '.homesearchlog li', function() {
    var page = $(this).find('a').attr('data-pg'); 
    getHomeSearchlogs(page);
});




// pagination
$(document).on('click', '.pagiAd li a', function() {
    var page = $(this).attr('data-pg');  
    getUserClients(page);
});


$(document).on('click', '.pagiAd #knowlistgopage', function()
{ 
    var pageno = $('#knowlistgotopageno').val( );
   
    if (pageno <= 0) pagesize = 1; 
    getUserClients(pageno);
}); 


// pagination
$(document).on('click', '.gorefsearch li a', function() {
	 
	 
    var page = $(this).attr('data-pg');
    var ref_name = $(this).attr('data-name')  ;
    var locateVoc = $(this).attr('data-voc')  ;
	var filterLifestyle = $('#filterLifestyle').chosen().val() + '';
    var filtercity = $('#filtercity').chosen().val() + ''; 
    var filtertag = $('#filterTags').chosen().val() + '';
	var srchZipCode = $('.srchZipCode').val();
     
 
    if ((ref_name == '') && (locateVoc == '') && (filterLifestyle == '') && (filtercity == '') ) 
	{
		getUserClients('1');
        return
    }
	
	if(filtercity == 'null')
	{
		filtercity ='';
	}
	if(locateVoc == 'null')
	{
		locateVoc ='';
	}
	if(filterLifestyle == 'null')
	{
		filterLifestyle ='';
	} 

  
    if(filtertag == 'null')
    {
        filtertag ='';
    }     
        
    waitFunc('enable');
 
        $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { srchZipCode: srchZipCode, ref_name: ref_name, locateVoc: locateVoc , pageno: page,lifestyle:filterLifestyle,
             city:filtercity,  tag:filtertag},
        success: function(data) {
            waitFunc('');  
            $('.clientsUsers').html(data);
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
 

});

// pagination
$(document).on('click', '.paginationU li', function() {

	var name = $('#searchnam').val();
	var voc = $('#searchvoc').val();
	var ema = $('#searchema').val();
	var loc = $('#searchloc').val();
	var val = $('.viewUser.active').attr('data-user');
 

	if (typeof val === 'undefined'){
		val = $('#viewuseridi').val();
		viewUserRef(val, name, voc, ema, loc, $(this).find('a').attr('data-pg'));
	}else {
		viewUserRef($('.viewUser.active').attr('data-user'), name, voc, ema, loc, $(this).find('a').attr('data-pg'));
	}
}); 

// pagination
$(document).on('click', '.pagiimportknow li', function() {
    var page = $(this).find('a').attr('data-pg');
    getImportedKnows(page);
}); 

// Get User Ref
$(document).on('click', '.viewUser', function() { 
    $('.viewUser').removeClass('active');
    $(this).addClass('active');
	var name = $('#searchnam').val();
	var voc = $('#searchvoc').val();
	var ema = $('#searchema').val();
	var loc = $('#searchloc').val();
    viewUserRef($(this).attr('data-user'), name, voc, ema, loc, 1);
});
// Get User Reset Ref
$(document).on('click', '.resetUser', function() {
	document.getElementById("searchnam").value = '';
	document.getElementById("searchvoc").value = '';
	document.getElementById("searchema").value = '';
	document.getElementById("searchloc").value = ''; 
});

// Edit client details
$(document).on('click', '.changeAccSett', function() {

    $('div[data-type="changePass"]').hide();

    $('input[name="old_pass"]').val('');
    $('input[name="new_pass"]').val('');
    $('#changeAccSett').attr('data-id', $(this).attr('data-id'));

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { changeAccSett: $(this).attr('data-id') },
        success: function(data) {
            waitFunc('');
            var results = JSON.parse(JSON.stringify(data));
			
			if( results.profile_missing == 1)
			{
				$('#profilemsg').html('<div class="alertmsg">User has not specified his/her profile!</div>');
			}
			else 
			{
				$('#profilemsg').html('');
			}
			
            $('input[name="upd_username"]').val(results.username);
            $('input[name="upd_phone"]').val(results.user_phone);
            $('select[name="upd_country"] option[value="' + results.country + '"]').prop('selected', true);
			$('input[name="upd_street"]').val(results.street);
            $('select[name="upd_city"]').val(results.city);
            $('input[name="upd_city"]').attr('data-ov', results.city);
            $('input[name="upd_zip"]').val(results.zip); 
            $('input[name="upd_email"]').val(results.user_email);
            $('input[name="upd_public_private"][value='+results.upd_public_private+']').prop('checked', true);
            $('input[name="upd_reminder_email"][value='+results.upd_reminder_email+']').prop('checked', true);
			$('textarea[name="about_your_self"]').val(results.about_your_self);
			
			$('input[name="upd_usergrp"]').prop('checked', false);
            $('input[name="upd_uservoc"]').prop('checked', false);
            $('input[name="upd_usertarget"]').prop('checked', false);
            $('input[name="upd_usertargetreferral"]').prop('checked', false);
			
			$('input[name=busi_name_edit]').prop("disabled", false);
			$('select[name=busi_location_edit]').prop("disabled", false);
			$('select[name=busi_type_edit]').prop("disabled", false); 
			$('input[name=busi_hours_edit]').prop("disabled", false);
			$('input[name=busi_website_edit]').prop("disabled", false);  
		    $('input[name=busi_location_street_edit]').prop("disabled", false);  
		      
			$('select[name="membertype_edit"] option[value="' + results.user_type + '"]').prop('selected', true); 
			
			$('input[name="busi_name_edit"]').val(results.busi_name);
			$('input[name="busi_location_street_edit"]').val( results.busi_location_street); 
            $('select[name="busi_location_edit"]').val( results.busi_location);
            $('select[name="busi_type_edit"]').val(results.busi_type); 
            $('input[name="busi_hours_edit"]').val(results.busi_hours);
			$('input[name="busi_website_edit"]').val(results.busi_website); 
			 $('input[name="linkedin_profile"]').val(results.linkedin_profile); 
            if (results.grp.match(/,/g)) {
                var groups = results.grp.split(",");
                for (var i = 0; i < groups.length; i++) {
                    $('input[name="upd_usergrp"][value="' + groups[i] + '"]').prop('checked', true);
                }
            } else {
                $('input[name="upd_usergrp"][value="' + results.grp + '"]').prop('checked', true);
            }

            if (results.voc.match(/,/g)) {
                var voc = results.voc.split(",");
                for (var i = 0; i < voc.length; i++) {
                    $('input[name="upd_uservoc"][value="' + voc[i] + '"]').prop('checked', true);
                }
            } else {
                $('input[name="upd_uservoc"][value="' + results.voc + '"]').prop('checked', true);
            }

            if (results.target_clients.match(/,/g)) {
                var target_clients = results.target_clients.split(",");
                for (var i = 0; i < target_clients.length; i++) {
                    $('input[name="upd_usertarget"][value="' + target_clients[i] + '"]').prop('checked', true);
                }
            } else {
                $('input[name="upd_usertarget"][value="' + results.target_clients + '"]').prop('checked', true);
            }

            if (results.target_referral_partners.match(/,/g)) {
                var target_referral_partners = results.target_referral_partners.split(",");
                for (var i = 0; i < target_referral_partners.length; i++) {
                    $('input[name="upd_usertargetreferral"][value="' + target_referral_partners[i] + '"]').prop('checked', true);
                }
            } else {
                $('input[name="upd_usertargetreferral"][value="' + results.target_referral_partners + '"]').prop('checked', true);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again');
        }
    });
});

$(document).on('click', '.changePass', function() {
    $('div[data-type="changePass"]').slideToggle(300);
});

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
	var linkedin_profile = $('input[name="linkedin_profile"]').val();
	
	
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
        url: 'includes/ajax.php',
        data: { data_id: data_id, upd_username: upd_username, upd_phone: upd_phone, upd_country: upd_country, upd_street: upd_street, upd_city: upd_city, upd_cityov:upd_cityov, upd_zip: upd_zip, upd_email: upd_email, upd_public_private:upd_public_private, upd_reminder_email:upd_reminder_email, upd_usergrp: upd_usergrp, upd_uservoc: upd_uservoc, upd_usertarget: upd_usertarget, upd_usertargetreferral: upd_usertargetreferral,about_your_self:about_your_self, is_business : is_business,   busi_name:busi_name_edit , busi_location_street :busi_location_street_edit, busi_location:busi_location_edit, busi_type:busi_type_edit, busi_hours:busi_hours_edit,
		busi_website:busi_website_edit, linkedin_profile:linkedin_profile,
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

$(document).on('click', '.savePass', function() {
    var old_pass = $('input[name="old_pass"]').val().trim();
    var new_pass = $('input[name="new_pass"]').val().trim();
    var data_id = $('#changeAccSett').attr('data-id');
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { old_pass: old_pass, new_pass: new_pass, data_id: data_id },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Sorry, password did not match!')
            } else {
                alertFunc('success', data)
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}); 

// Edit Packages
$(document).on('click', '.edit_package', function() {
    var package_id = $(this).attr('data-id');
    $('#edit_package').attr('data-id', package_id);
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getPackageData: package_id },
        success: function(data) {
            waitFunc('');
            var results = JSON.parse(JSON.stringify(data));

            $('input[name="package_name"]').val(results.package_title);
            $('input[name="package_price"]').val(results.package_price);
            $('select[name="package_dur"]').val(results.package_limit);
            $('input[name="ref_sh_conn"]').val(results.share_limit);
            $('input[name="ref_sh_conn_desc"]').val(results.share_desc);
            $('input[name="ref_conn"]').val(results.ref_limit);
            $('input[name="ref_conn_desc"]').val(results.ref_desc);
            $('input[name="tar_conn"]').val(results.conn_limit);
            $('input[name="tar_conn_desc"]').val(results.conn_desc);

            $('.services').html('<small>Package Services (leave empty if no service)</small><div class="form-group"><div class="col-sm-11 padd-5">' +
                '<input name="package_services" class="form-control" placeholder="Package service"/></div><div class="col-sm-1 padd-5">' +
                '<button class="fa fa-plus btn btn-default addNewService"></button></div></div>');
                
            $.each(results.services, function(i, val) {
                if (i > 0) {
                    var services = '<div class="form-group">' +
                        '<div class="col-sm-11 padd-5"><input name="package_services" class="form-control" placeholder="Package service"/></div>' +
                        '<div class="col-sm-1 padd-5"><button class="fa fa-minus btn btn-default rmvNewService"></button></div>' +
                        '</div>';

                    $('.services').append(services);
                }
                $('input[name="package_services"]:eq(' + i + ')').val(results.services[i]);
            });

        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}); 
 


// Chnage client package
$(document).on('click', '.selUserPkg a', function() {
    var newPkg = $(this).attr('data-id');
    var newPkgTxt = $(this).text();
    var user = $(this).parents('tr').attr('data-id');
    if ($(this).parents('li').hasClass('disabled')) {
        alertFunc('info', 'Sorry, this package is deactivated. Please activate this package first!');
        return
    }
    confFunc('Are you sure you want to change this user package to ' + newPkgTxt + '?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { changeUserPkg: newPkg, user: user },
            success: function(data) {
                waitFunc('');
                if (data == 'user_error') {
                    alertFunc('danger', 'Something went wrong, please try again');
                } else if (data == 'package_error') {
                    alertFunc('danger', 'Something went wrong, please try again');
                } else if (data == '200') {
                    alertFunc('success', 'Package changed to ' + newPkgTxt);
                    $('[data-toggle="tooltip"]').tooltip();
                    getUserClients($('.pagiAd li.active a').attr('data-pg'));
                }
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});


//Deactivate / Activate user
$(document).on('click', '.rmvUser', function() {
    var user = $(this).attr('data-user');
    var status = $(this).attr('title');

    confFunc('Are you sure you want to ' + status + ' this user?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { changeUserSts: user },
            success: function(data) {
                if (data == 'user_error') {
                    alertFunc('danger', 'Something went wrong, please try again');
                } else {
                    alertFunc('success', 'Changes successfully saved');
                    getUserClients($('.pagiAd li.active a').attr('data-pg'));
                }
                waitFunc('');
            },
            error: function() {
                alertFunc('info', 'Something went wrong, please try again')
                waitFunc('');
            }
        });
    });
});

//Delete user
$(document).on('click', '.delUser', function() {
    var userID = $(this).attr('data-user');

    confFunc('This action is irreversible and will delete all data related to this user.<br/><br/> Are you sure you want to DELETE this user?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { delUser: userID },
            success: function(data) {
                if (data == 'user_error') {
                    alertFunc('danger', 'Something went wrong, please try again');
                } else {
                    alertFunc('success', 'User and relevant data deleted.');
                    getUserClients($('.pagiAd li.active a').attr('data-pg'));
                }
                waitFunc('');
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});

// Search Reference
$(document).on('click', '.srchRef', function() 
{
	getmemberlists(1);
});

// pagination
$(document).on('click', '.pagination.pageml li a', function() {
    var page = $(this).attr('data-pg');  
    
	getmemberlists(page);
	
	
});

function getmemberlists(page)
{
	 
	var ref_name = $('.srchRefName').val().trim();
    var locateVoc = $('#locateVoc').chosen().val() + '';
	var filterLifestyle = $('#filterLifestyle').chosen().val() + '';
    var filtercity = $('#filtercity').chosen().val() + '';
    var srchentryDate = $('.srchentryDate').val( );

     
    var filtertag = $('#filterTags').chosen().val() + '';
	var srchZipCode = $('.srchZipCode').val();
    var srchPhone = $('.srchPhone').val();
	var srchemail = $('.srchemail').val();

    if ((ref_name == '') && (locateVoc == '') && (filterLifestyle == '') && (filtercity == '') ) 
	{
		getUserClients('1');
        return
    }
	 
    if(!filtercity || filtercity == 'null')
	{
        filtercity =''; 
    }

    
	if(locateVoc == 'null')
	{
		locateVoc ='';
    }
    
	if(filterLifestyle == 'null')
	{
		filterLifestyle ='';
	} 
    
    if(filtertag == 'null')
    {
        filtertag ='';
    }   
	
	waitFunc('enable');
    $.ajax({
		type: 'post',
        url: 'includes/ajax.php', 
        data: { srchZipCode: srchZipCode, ref_name: ref_name, locateVoc: locateVoc, lifestyle:filterLifestyle , phone:srchPhone, city: filtercity, entrydate: srchentryDate, tag:filtertag, email:srchemail,  pageno:page},
        
        
        success: function(data) 
		{ 
            waitFunc(''); 
            $('.clientsUsers').html(data);
            $('[data-toggle="tooltip"]').tooltip();
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}
 

//Delete page content
$(document).on('click', '.delPgCntnt', function() 
{
	var id = $(this).attr('data-id');
    var $thisContent = $(this);
    confFunc('Are you sure you want to delete this content?', function()
	{
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { delContent: id },
            success: function(data) {
                waitFunc('');
                if (data == 'error') {
                    alertFunc('info', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'Content successfully deleted!');
                    $thisContent.parents('.pageDataInner').remove();
                }
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});

// Edit page content
$(document).on('click', '.edPgCntnt', function() {
    var id = $(this).attr('data-id');
    $('#editContent').attr('data-id', id);
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { edit_content: id },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Sorry, password did not match!');
            } else {
                var result = JSON.parse(JSON.stringify(data));
                $('input[name="about_title_ed"]').val(result.title);
                $('textarea[name="about_content_ed"]').val(result.content);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}); 

// Add blog
function getBlogName(selector) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { BLogNames: 'addBLogName' },
        success: function(data) {
            waitFunc('');
            $(selector).html(data);
            $('.blogSelectedVal').val($('.blogNameList option:selected').val());
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

$(document).on('click', '.saveBlogName', function() {
    var addBLogName = $('.addBLogName').val().trim();

    if (addBLogName == '') { alertFunc('warning', 'Please provide a name!'); return; }

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addBLogName: addBLogName },
        success: function(data) {
            waitFunc('');
            if (data == 'match') {
                alertFunc('info', 'Sorry, Blog name already exists!');
            } else {
                alertFunc('success', 'Blog name saved!');
                getBlogName('.blog_list, .blog_list_ed, .blogNameList');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

$(document).on('click', '.addBlogContent', function() {
    var uplProdForm = new FormData();
    var blog_list = $('.blog_list').val();
    var blogTitle = $('.blogTitle').val();
    var blogContent = $('.blogContent').val();

    uplProdForm.append('addBlogContent', 'addBlogContent');
    uplProdForm.append('blog_list', blog_list);
    uplProdForm.append('blogTitle', blogTitle);
    uplProdForm.append('blogContent', blogContent);
    uplProdForm.append('image', $('.blogImage').prop('files')[0]);
    uplProdForm.append('video', $('.blogVideo').prop('files')[0]);

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        cache: false,
        contentType: false,
        processData: false,
        data: uplProdForm,
        success: function(data) {
            waitFunc('');
            $('.blogsData').html(data);
            alertFunc('success', 'Blog successfully saved');
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });

});


// Edit blog
$(document).on('click', '.editBlogData', function() {
    $('#edit-2').attr('data-id', $(this).attr('data-id'));
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getBlogContent: $(this).attr('data-id') },
        success: function(data) {
            waitFunc('');
            var results = JSON.parse(JSON.stringify(data));
            $('.blog_list_ed option[value="' + results.blog_name + '"]').prop('selected', true);
            $('.blogTitle_ed').val(results.content_title);
            $('.blogContent_ed').val(results.blog_content);

            $('.editBlogDetails .media').html('');
            if (results.blog_image != '') {
                $('.editBlogDetails .media').append('<img src="blog/' + results.blog_image + '"/>');
            }
            if (results.blog_video != '') {
                $('.editBlogDetails.media').append('<video src="blog/' + results.blog_video + '" controls/>');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

$(document).on('change', '.blogNameList', function() {
    $('.blogSelectedVal').val($(this).val());
});

$(document).on('click', '.editValBlog', function() {
    $('.saveValBlog, .blogSelectedVal').show();
    $('.editValBlog').hide();
});

$(document).on('click', '.saveValBlog', function() {
    var addBLogName = $('.blogSelectedVal').val().trim();
    var update_name = $('.blogNameList').val();
    if (addBLogName == '') { alertFunc('warning', 'Please provide a name!'); return; }

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addBLogName: addBLogName, update_name: update_name },
        success: function(data) {
            waitFunc('');
            if (data == 'match') {
                alertFunc('info', 'Sorry, Blog name already exists!');
            } else {
                alertFunc('success', 'Blog name saved!');
                getBlogName('.blog_list, .blog_list_ed, .blogNameList');
                $('.saveValBlog, .blogSelectedVal').hide();
                $('.editValBlog').show();
                $('.blogsData').html(data);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

$(document).on('click', '.updblogData', function() {
    var data_id = $('#edit-2').attr('data-id');
    var uplProdForm = new FormData();
    var blog_list = $('.blog_list_ed').val();
    var blogTitle = $('.blogTitle_ed').val();
    var blogContent = $('.blogContent_ed').val();

    uplProdForm.append('addBlogContent', 'addBlogContent');
    uplProdForm.append('blog_list', blog_list);
    uplProdForm.append('blogTitle', blogTitle);
    uplProdForm.append('blogContent', blogContent);
    uplProdForm.append('data_id', data_id);
    uplProdForm.append('image', $('.image_ed').prop('files')[0]);
    uplProdForm.append('video', $('.video_ed').prop('files')[0]);

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        cache: false,
        contentType: false,
        processData: false,
        data: uplProdForm,
        success: function(data) {
            waitFunc('');
            $('.blogsData').html(data);
            alertFunc('success', 'Blog successfully saved');
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


// Delete blog
$(document).on('click', '.removeBlogName', function() {
    var blog = $('.blogNameList').val();
    confFunc('By removing this Blog name all the blog contents related to this Blog will be removed. Continue?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { deleteBlog: blog },
            success: function(data) {
                waitFunc('');
                alertFunc('success', 'Data successfully removed!');
                getBlogName('.blog_list, .blog_list_ed, .blogNameList');
                $('.saveValBlog, .blogSelectedVal').hide();
                $('.editValBlog').show();
                $('.blogsData').html(data);
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});


// Delete Blog Content
$(document).on('click', '.rmvBlogContent', function() {
    var blog = $(this).attr('data-id');
    confFunc('Are you sure you want to removing this blog?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { deleteBlogData: blog },
            success: function(data) {
                waitFunc('');
                alertFunc('success', 'Data successfully removed!');
                $('.blogsData').html(data);
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});
 



// Send Feedback
$(document).on('click', '.send_feedback', function() {
 
    waitFunc('enable');
    var name = $(".feedback_name").val();
    var email = $(".feedback_email").val();
    var coment = $(".feedback_comment").val();
    if (name != "" && email != "" && coment != "") {
        $.ajax({
            type: 'post',
            url: aurl + 'email/send/',
            data: { name: name, email: email, comment: coment },
            success: function(data) { 
  
                waitFunc('');
                $(".feedback_name").val("");
                $(".feedback_email").val("");
                $(".feedback_comment").val("");
                alertFunc('success', 'Your Feedback has been submitted!');
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again');
            }
        });
    } else {
        waitFunc('');
        alertFunc('info', 'Please first fill all feedback fields!');
    }
}); 

 
$(document).on('click', '.getpublicfaqs', function()
{
	type =2;
	waitFunc('enable');
    $.ajax({
        type: 'get', 
        url:  aurl + 'get/faqs/',
        success: function(data) 
		{ 
            waitFunc(''); 
			data = $.parseJSON(data);    
            if (data.error == 10 || data.error == 1 ) 
            {
                alertFunc('danger', data.errmsg  )
            }
            else
            {
				html = "<div class='panel-group' id='accordion' role='tablist' aria-multiselectable='true'>";
				
				html += "<div  class='panel panel-default'>" +
                        "<div class='panel-heading' role='tab' id='head0'>"   +
						"<h2 class='panel-title'>" +
						"<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col0' aria-expanded='true' aria-controls='collapseOne'>MyCity Calling System</a></h2></div>" +
						"<div id='col0' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head0'>" +
						"<div class='panel-body'><img width='100%' src='assets/img/edgeup_network_success_system.jpg' alt='MyCity Calling System' /></div></div></div>";
				
				
				html += "<div  class='panel panel-default'>" +
                        "<div class='panel-heading' role='tab' id='head1'>"   +
						"<h2 class='panel-title'>" +
						"<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col1' aria-expanded='true' aria-controls='collapseOne'>MyCity Business Growth</a></h2></div>" +
						"<div id='col1' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head1'>" +
						"<div class='panel-body'><img width='100%' src='assets/img/mycity_business_growth.jpg' alt='Edgeup Network Success System' /></div></div></div>";
				
				html += "<div  class='panel panel-default'>" +
                        "<div class='panel-heading' role='tab' id='head2'>"   +
						"<h2 class='panel-title'>" +
						"<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col2' aria-expanded='true' aria-controls='collapseOne'>Interview Training Video</a></h2></div>" +
						"<div id='col2' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head2'>" +
						"<div class='panel-body'><div class='embed-responsive embed-responsive-16by9 tmvideo'>" +
						"<iframe class='embed-responsive-item' frameborder='0' width='100' height='315' " +
						"src='https://www.youtube.com/embed/KYmyrMQ0ucw' ></iframe> </div> </div></div></div>";
				
				 
				$.each( data.result , function(index, obj)
				{
					idx = index+3; 
					html += "<div  class='panel panel-default'>" +
                        "<div class='panel-heading' role='tab' id='head" + idx + "'>"   +
						"<h2 class='panel-title'>" +
						"<a role='button' data-toggle='collapse' data-parent='#accordion' href='#col" + idx + 
						"' aria-expanded='true' aria-controls='collapseOne'>" + obj.helptitle +  "</a></h2></div>" +
						"<div id='col" + idx + "' class='panel-collapse collapse in' role='tabpanel' aria-labelledby='head" + idx + "'>" +
						"<div class='panel-body'>"  + obj.helptext  +"</div></div></div>";
					})  
                html +='</div>';
			}  
			$('#helpaccordion').html(html); 
            alertFunc('success', 'FAQs retrieved successfully!');
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again');
        }
    }); 
});



// Forgot password
function resetPW(emAdd) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { resetPW: emAdd },
        success: function(data) {
            if (data == 'success') {
                alertFunc(data, 'Password reset link has been sent to this email.')
            } else {
                alertFunc('danger', data);
            }
            waitFunc('');
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
            waitFunc('');
        }
    });
}
$(document).on('click', '#resPWBtn', function(e) {
    e.stopImmediatePropagation();
    var emAdd = $('#forgPWEmail').val().trim();
    if (validateEmail(emAdd) == false) {
        alertFunc('warning', 'Enter valid email address please.')
    } else {
        resetPW(emAdd);
    }
});


$(function() {
    $("#helptable").sortable({
        update: function() {
            $.each($("#helptable .ui-sortable-handle:not(.ui-sortable-placeholder)"), function(key, val) {

                position = key;
                id = $(val).data('id');
                $.ajax({
                    type: 'post',
                    url: 'includes/ajax.php',
                    data: { updfaqpos: '1', position: position, id: id }
                });


            });
        }
    });
    $("#helptable").disableSelection();
});


// Get All Triggers
function getAlVocation() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getTriggers: 1 },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again');
            } else {
                $('.fetchTriggers').html('<option value="null">-select group-</option>' + data);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
} 
// Add New Group
$(document).on('click', '.addNewTrigger', function()
{
	var triggername = $('.triggerName').val().trim();
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  aurl + 'trigger/add/',
        data: { userid: mid,   triggername: triggername, triggerid: 0},
        success: function(data) {
			 
			waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Trigger with name "' + triggerName + '" already exists!');
            } else {
                getAlVocation();
                alertFunc('success', 'Successfully added');
                //$('.groupName').val('');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

// Update/Edit Trigger  
$(document).on('change', '.fetchTriggers', function() {
    var currTrig = $('.fetchTriggers option:selected').text();
    var currTrigVal = $(this).val();
    if (currTrigVal == 'null') {
        $('.editTrigger').val('');
        return;
    }
    $('.editTrigger').val(currTrig).attr('data-val', currTrigVal);
});


$(document).on('click', '.edittrigger', function() {
    var triggerid = $(this).data('id');
    var question = $('.tbody-' + triggerid).html();
    $("#edittrig-" + triggerid).toggle();
    $('#trigbody-' + triggerid).toggle();
    $('#trigtext-' + triggerid).attr('size', $('#trigtext-' + triggerid).val().length);

    $('.editTrigger').val(question);
});

//update trigger from table
$(document).on('click', '.updatetrig', function() {

    var triggerid = $(this).data('id');
    $("#edittrig-" + triggerid).toggle();
    $('#trigbody-' + triggerid).toggle();
    var triggerName = $('#trigtext-' + triggerid).val().trim();
    var currTrigID = $(this).data('id');
    if (currTrigID == '') {
        alertFunc('info', 'Please select the trigger first');
        return
    } 
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  aurl + 'trigger/add/',
        data: { userid: mid,    triggername: triggerName, triggerid: currTrigID},
		 
        success: function(data) {
			 
            waitFunc('');
			
            if (data == 'error') {
                alertFunc('danger', 'Trigger with name "' + currTrig + '" already exists!');
            } else {
                //getAlVocation();
                alertFunc('success', 'Trigger successfully updated');
                $('#trigbody-' + triggerid).html(triggerName);

                $('.currTrig').val('')
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
	 
	
	
});

 

// Update Trigger
$(document).on('click', '.updTrig', function() {
    var triggerName = $('.editTrigger').val().trim();

    var currTrigID = $('.editTrigger').attr('data-val');
    if (currTrigID == '') {
        alertFunc('info', 'Please select the trigger first');
        return
    }

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addtrigger: 1, triggerName: triggerName, currTrigID: currTrigID },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Trigger with name "' + currTrig + '" already exists!');
            } else {
                //getAlVocation();
                alertFunc('success', 'Trigger successfully updated');
                $('.currTrig').val('')
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

// Remove Trigger
$(document).on('click', '.removetrigger', function() {
    var id = $(this).data('id');
    
    confFunc('Are you sure you want to delete this trigger?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url:  aurl + 'tools/deleterow/',
            data: { trn: id ,  tn: "trig" },
            success: function(data) {
                waitFunc('');
                if (data == 'error') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'Trigger successfully deleted');
                    window.location.reload();
                }
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});

//get my group partners
// Update/Edit Trigger  
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

$(document).on('click', '.showselectedProfile', function() {
    var clientid = $('.groupMembers option:selected').val(); 
    waitFunc("enable");
    $.ajax({
        type: 'post',
        url: aurl + 'member/getbyid/',
        data: 
        { 
            profileid: clientid
        },
        success: function(data) 
        { 
            data = $.parseJSON(data); 
            waitFunc('');
            if (data.error == '1' || data.error == '10')
                {
                    alertFunc('danger',   'Something went wrong, please try again' );
                }
                else 
                {    
                     user_picture = !(data[0].image ) ?  "images/no-photo.png" :  "images/"  +  data[0].image;  
                     html  = `<div class="panel panel-default">
                        <div class="panel-body">
                        <div class="row">
                        <div class="col-md-2">
                        <img src='`  + user_picture  + `' alt='`  +  data[0].username   + `'  
                        class="img-rounded" height="120" width="120">
                        </div>
                        <div class="col-md-9">`;
                        
                     

                    html  += "<p><strong>Name:</strong>" + data[0].username  + "</p>";
                    html  += "<p><strong>Email:</strong>" + data[0].user_email  + "</p>";
                    html  += "<p><strong>Phone:</strong>" + data[0].user_phone  + "</p>"; 

  

                    html  += "</div>";
                    html  += '<div class="col-md-1">';
                    html  += "<button data-toggle='modal' id='" + data[0].user_id  +  "' data-target='#myModal' class='btn-primary btn btn-xs leaveMsg'><i class='fa fa-envelope'></i></button>";
                    html  += '</div><div class="col-md-12"><hr/>';

                    html  += '<p><strong>About</strong></p>'+ data[0].about_your_self + '<hr/>';
                    html  += '<p><strong>Target Clients</strong></p>'+ data[0].$target_clients  + '<hr/>'; 
                    html  += '<p><strong>Target Referral Partners</strong></p>' + data[0].target_referral_partners + '<hr/>';
                    
                    html  += '<p><strong>Triggers</strong></p>';
                    html  += '</div></div></div></div>';


                    $('#displayProfile').html(html);
                }
        }
    });
});


$(document).on('click', '.btnsearchpartner', function() 
{
    var partnername = $('#searchname').val();
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + '/member/searchbyname/',
        data: {  searchpartner: '1', name: partnername, email: mremail },
        success: function(data) { 
            waitFunc('');
            data = $.parseJSON(data); 
            if(data.error == 10 || data.error == 1)
            {
                alertFunc('info', 'Something went wrong, please try again')
                $('#partnersearchresult').html(" ");
            }
            else 
            { 
                $('#partnersearchresult').html(data.results);
            } 
        }
    });
});


$(document).on('click', '.getmypartners', function() 
{
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { srcgrppartners: 1 },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'Group Partners Loaded');
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});
 
function loadgrouprequestclients(page) {
    $.ajax({
        type: 'post',
        url: aurl + 'member/grouprequest/',
        data: { srchnewusers: 1, state: 0, goto: page },
        success: function(data) {
            data = $.parseJSON(data);
            
            if(data.error == 0)
            { 
             html =`<table class='table table-condensed'>
             <thead>
             <tr> <th>Name</th>
             <th>Email</th>  
             <th>Group Name</th>  
             <th>Package</th>
             <th>Approve Group</th> 
             </tr>
         </thead><tbody> ` ;
            
         $.each(data.results, function (index, item) 
         { 
             messagepart =  item.e.split(/\s+/).slice(0,5).join(" ");
 
             html +=  "<tr id='row-"  +  item.a   + "'><td>" +   item.t   +  "</td><td>"+   item.r +   "</td>" ;
             html +=  "<td>" +   item.g   +  "</td>" +"<td>" +   item.v   +  "</td>";
              

             html +=  "<td><div class='btn-group' id='status'  data-toggle='buttons'>" +
                  "<label class='btn btn-default btn-on'>" +
                  "<input class='grpstatus'  type='radio' value='1' data-page='"+ page +"' data-userid='" + item.a  + "' name='grpstatus'  >YES</label>" +
                  "<label class='btn btn-default btn-off active'>" +
                  "<input class='grpstatus'  type='radio'  value='0' data-page='"+ page +"' data-userid='" + item.a  + "' name='grpstatus' checked='checked'>NO</label>" +
                  "</div></td>" ;
 
             html +=   "</tr>";   
 
         });
         
         html +=  "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td><td></td></tr>";
         html += '</table>';
           
 
         prev =  (page == 1) ? 1 :  parseInt(page) -1;
         next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1;  
          
         html += " <ul class='pagination grouprequest'><li>" +
             "<a    data-func='prev' data-page='" + prev + "'>«</a></li>";
             for( i=1;  i<= data.pages;  i++){
                 
                   active =  i == page ? 'active' : '';
                   html +=  "<li class='" + active + "'><a  data-page='"+i   +"'>"+ i 
                 +"</a></li>";
             }
             html += "<li><a  data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";  

             
            $('#newuserlist').html(  html );

            }
        },
        error: function() {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

$(document).on('click', '.pagination.grouprequest li a', function() {
    var page = $(this).attr('data-page'); 
    loadgrouprequestclients(page);
});
 

$(document).on('click', '.newSignup', function() {
    loadgrouprequestclients(1);
});

$(document).on('change', 'input[name=grpstatus]:radio', function() 
{ 
    var page = $(this).attr('data-page'); 
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { updgrpstate: 1, grpstatus: this.value, userid: $(this).data('userid') },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'User has been approved in the group!');
            //reload group un-approved users 
            loadgrouprequestclients(page);
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


//name search autocomplete 
var clients;
$(document).on('click', '#nameSrch', function() {
    clients = '';
    var namepart = $(this).val();
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { namekey: namepart },
        success: function(resultdata) {
            clients = resultdata;
            autoComplete();
        }
    });
});
 
function autoComplete()
{
	var $client = $('#nameSrch');
    $client.autocomplete({
        minLength: 0,
        source: JSON.parse(clients),
        focus: function(event, ui) {
            $client.val(ui.item.label);
            return false;
        }
    });
 
    $client.data("ui-autocomplete")._renderItem = function(ul, item) {

        var $li = $('<li>'),
            $img = $('<img>');
        imagepath = 'http://mycity.com/images/no-photo.png';
        if (item.icon.length > 0) {
            imagepath = 'http://mycity.com/images/' + item.icon;
        }
        $img.attr({
            src: imagepath,
            alt: item.label,
            width: '50px'
        });

        $li.attr('data-value', item.label);
        $li.append('<a href="#">');
        $li.find('a').append($img).append(item.label);

        return $li.appendTo(ul);
    };
}


//subnav control 
$(document).on('click', '.subnavctrl', function() {
    var subnav = $(this).data('target');


    if ($(subnav).hasClass('in')) {
        $(subnav).removeClass('in')
    } else {
        $(subnav).addClass('in')
    }
});



$(document).on('click', '#manageblog', function() {
    //load existing blog manages

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getallpost: 1 },
        success: function(data) {

            $('#allpost').html(data);

        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });

});

 


//select post for editing 
$(document).on('click', '.readpost', function() {
    //load existing blog manages
    waitFunc('enable');
    var postid = $(this).data('postid');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { readpost: 1, postid: postid },
        success: function(data) {
            waitFunc('');
            var results = JSON.parse(JSON.stringify(data));
            $('#blogheading').html(results.post_title);
            $('#blogcontent').html(results.post_content);
            $('#postid').val(results.post_id);
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

//Post comment now 
$(document).on('click', '#postcomment', function() 
{
    //load existing blog manages
    waitFunc('enable');
    var postid = $('#postid').val();
    var name = $('#name').val();
    var email = $('#email').val();
    var comment = $('#commentbody').val();
 
    if (name.length = 0 || email.length == 0 || comment.length == 0)
    {
        alertFunc('danger', 'Mandatory comment fields are missing!'); 
    } 
    else
     {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { savecomment: 1, postid: postid, name: name, email: email, comment: comment },
            success: function(data) {
                waitFunc('');
                $('#comment').html('<hr/><div class="alert alert-success">Your comment has been posted. Please wait for mediation</div>');
            },
            error: function(a, b) {
                alertFunc('info', 'Something went wrong, please try again');
            }
        });
    }
    waitFunc('');
});

//clear result 
$(document).on('change', '.ratedmemberGroup', function() {
    $('#getratedpartners').html('');
})

//select post for editing 
$(document).on('click', '.showratedpartners', function() {
    //load existing blog manages
    waitFunc('enable');
    var selGrp = $('.ratedmemberGroup option:selected').val();
    var selVoc = $('.memberVocation option:selected').val();
 
    $.ajax({
        type: 'post',
        url: aurl +  '/get/partners/',
        data: { userid: mid, goto: 1,  group: selGrp, vocation: selVoc },
        success: function(data) {
            waitFunc('');  
            data = $.parseJSON(data);   
            if (data.error == 10 || data.error == 1 ) 
            {
                alertFunc('danger', data.errmsg  )
            }
            else
            {
                html =`<table class="table table-bordered table-alternate">
                <tr>
                <th>Reference Name</th>
                <th>Vocation</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Location</th>
                <th>Group</th>
                <th>Ratings</th>
                </tr>`;
                
                $.each( data.result , function(idx, obj){
                    html += "<tr>" +
                        "<td>"  + obj.client_name + "</td>" +
                        "<td>"  + obj.client_profession + "</td>" +
                        "<td>"  + nulltospace(obj.client_phone)   + "</td>" +
                        "<td>"  + obj.client_email + "</td>" +
                        "<td>"  + nulltospace(obj.client_location) + "</td>" +
                        "<td>"  + nulltospace(obj.user_group) + "</td>" +
                        "<td>"  + (  !obj.rate  ? "0" :  obj.rate )  + "</td> </tr>"; 
                }) 
                  
                html +='</table>'; 
                $('#getratedpartners').html(html);
            } 

        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

//load interested groups of a client 
$(document).on('click', '.getinterestedgroups', function() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getinterestedvoc: 1 },
        success: function(data) {
            var dropdown = $('#targetvocations');
            dropdown.empty();
            $.each(JSON.parse(data), function(key, value) {
                $("#targetvocations").append($('<option></option>').val(value.id).html(value.username));
            });

        }
    });

});


//get suggested partners
$(document).on('click', '.suggestpartners', function() {

    var group = $('.mygroups option:selected').val();
    var vocation = $('.targetvocations option:selected').val();
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getsuggestedconnects: 1, group: group, vocation: vocation },
        success: function(data) {
            waitFunc('');
            $('#suggestedconnects').html(data);
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});



//Increase loyalty point 
function raiseloyaltypoint(point, description) 
{
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { loyaltypoint: 1, point: point, description: description },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'You have been awarded 10 points!');
            alertFunc('success', 'Your new contact has some matching connects waiting for introduction!');
        }
    });
}
 

//save referrals for the new know added
function loadsuggestedrefferals(newknowid, professions, sourcezip) 
{ 
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { generatesmartsuggest: 1, professions: professions, newknowid: newknowid, sourcezip: sourcezip },
        success: function(data) {
            
            alertFunc('success', 'Your new contact has some matching connects waiting for introduction!');
            waitFunc('');
        }
    });
} 
function regeneratesuggestedrefferals(newknowid, professions, sourcezip) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { generatesmartsuggest: 1, professions: professions, newknowid: newknowid, sourcezip: sourcezip },
        success: function(data) {
            alertFunc('success', 'Your new contact has some matching connects waiting for introduction!');
            waitFunc('');
        }
    });
} 
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

$(document).on('click', '.btnremsuggestion', function() 
{
    var refid = $(this).data('refintroid');
    confFunc('Are you sure you want to remove this referral suggestion?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { remsuggestion: 1, refid: refid },
            success: function(data) {
                waitFunc('');
                if (data != '1') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'Referral suggestion is removed.');
                    // window.location.reload();
                    //resultreset("showreferrals", "click");
                    $('#row-' + refid).remove();
                }
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    }); 
});

function resultreset(control, event) {
    $('.' + control).trigger(event);
}
 

$(document).on('click', '#sendsuggestedreferral', function() {
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
        url: 'includes/ajax.php',
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
            ccname1: ccname1

  


        },
        success: function(data) {
            if (data == '1')
                alertFunc('success', 'Introductory email already sent!');
            if (data == 'success')
                alertFunc('success', 'Your introduction mail has been sent!');
            //alertFunc('danger', 'Temporary mail sending error. Please try again to send introduction mail!') ;
            waitFunc('');
        }
    });

    //close mail sending dialog
    $('#suggestedreferral').modal('hide');
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

$(document).on('click', '.showreferrals', function() 
{
	var userid = $(this).attr('data-user'); 
	if(typeof userid === 'undefined')
	{
		userid = mid ;
	}   
    //generate smart mapping of knows 
    var pagesize = $(this).data('pagesize');
    if (pagesize == 0) pagesize = 10;
    var pageno = $(this).data('pageno');
    if (pageno == 0) pagesize = 1; 
    reloadknows(pagesize, pageno, userid); 
});


//LinkedIn Contact pagination
$(document).on('click', '.pagiknows li a', function()
{
	var pageno = $(this).data('pg');
    if (pageno == 0) pageno = 1;  
	var ssf = $('#hidsf').val(); 
	if(typeof ssf === "undefined")
	{
		ssf =0;
	}
	
	$.ajax({
		 type: 'post',
		 url: 'includes/ajax.php',
		 data: { readmailogs: 1, pagesize: 10, activepage: pageno , ssf: ssf},
         success: function(data) 
		 {
			$('#suggestedconnects').html(data);
			$('.tooltip').tooltipster({
				animation: 'fade',
                delay: 200,
                theme: ['tooltipster-default' , 'tooltipster-default-customized' ],
                trigger: 'hover'
			});
		 } 
	});
	
	
});

$(document).on('click', '.pagiknows #gopage', function()
{
	var userid = $(this).attr('data-uid'); 
	if(typeof userid === 'undefined')
	{
		userid = mid ;
	}
    var pageno = $('#gotopageno').val( );
    if (pageno <= 0) pagesize = 1; 
    reloadknows(10, pageno, userid);  
});  

//api converted
function reloadknows(pagesize, pageno, userid)
{
	var ssf = $('#hidsf').val(); 
	if(typeof ssf === "undefined")
	{
		ssf =0;
	} 
	$('#cpage').html(pageno);
    $('.savesuggestcpage').data('pageno', pageno); 
	$('#processing').modal({ backdrop: true, keyboard: false });
	var json_data;
		//mapping referrals one at a time  
		$.ajax({
			type: 'post',
			url: aurl +  'knows/mapping/',
			data: { uid:  userid} ,
			success: function(data)
			{
				data = $.parseJSON(data);  
			}
		});
	
	setTimeout(function() 
    { 
		$.ajax({
			 type: 'post',
			 url: 'includes/ajax.php',
			 data: { readmailogs: 1, pagesize: pagesize, activepage: pageno , ssf: ssf, uid:  userid},
			 success: function(data) 
			 {
				$('#suggestedconnects').html(data);
				$('.tooltip').tooltipster({
					animation: 'fade',
					delay: 200,
					theme: ['tooltipster-default' , 'tooltipster-default-customized' ],
					trigger: 'hover'
				});
				
				$('#processing').modal('hide');
				
			 } 
		}); 
		
	}, 1000); 
}


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

$(document).on('click', '.btnsendtrigger', function() {

    var receipentemail = $(this).data('remid');
    var receipent = $(this).data('rname');
	var receipentid = $(this).data('reid');
    var templateid = $(this).data('tid');
    var introducee = $(this).data('introducee');
    var phone = $(this).data('phone');
	 
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { sendtrigger: 1, receipentid:receipentid,  receipentemail: receipentemail, receipent: receipent, 
            templateid: templateid,suggestname: introducee, phone: phone },
        success: function(data) {

           
            
            waitFunc('');
            alertFunc('success', 'Trigger email is sent!');
        }
    });
 
})

 


 

$(document).on('click', '.savesuggestcpage', function() {
    var cpage = $(this).data('pageno');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { udtcpage: 1, cpage: cpage },
        success: function(data) {
            alertFunc('success', 'Current working page number is saved!');

            $('#lpage').html(cpage);
            $('.page-link').data('pageno', cpage);
        }
    });
})
 
$(document).on('click', '.btn-read-more', function()
{
    var postid = $(this).data('id');
    var url = document.location.origin + '/read-post.php';
    var form = $('<form action="' + url + '" method="post">' +
        '<input type="hidden" name="id" value="' + postid + '" />' +
        '</form>');
    $('body').append(form);
    form.submit();
});

$(document).on('click', '[data-toggle="suggesttip"]', function() {
    $(this).tooltip();
})
 




// pagination for home search log
$(document).on('click', '.mailboxpager li', function() {
   var page = $(this).find('a').attr('data-page');   
	var type = $(this).find('a').attr('data-mint');  
  
	reloadinbox(page, type)
});

$(document).on('click', '.loadinbox', function() 
{
	$('#menu63').removeClass('active');
	$('#menu21b').addClass('active');  
	 
	var type = $(this).attr('data-mint');
	if(typeof type === 'undefined')
	{
		type = 0; 
	} 
	reloadinbox(1, type)
});

function reloadinbox(page, emailtype)
{ 
	
    $.ajax({
        type: 'post',
        url: aurl + 'mail/inbox/',
        data: { mailtype: emailtype, page: page, receipent: mremail },
        success: function(data) { 
           data = $.parseJSON(data);    
		    
           if(data.error == 0)
           { 
            html ="<table class='table table-condensed'><thead><tr><th></th><th></th><th>Sender</th><th>Subject</th><th>Date</th><th>Action</th></tr></thead><tbody> " ;
           
        $.each(data.result, function (index, item) 
        { 
            if(item.e == 0)
			{
				estate ='<span class="badge">New</span>';
				bt ='strong';
			}
			else 
			{
				estate = ' ';bt =' ';
			}
            html +=  "<tr  data-id='"  +  item.id   + "'   ><td><input type='checkbox' class='delmail' data-id='" + item.id + "'></td><td>" +  estate +"</td><td class='" + bt +"' data-id='"  +  item.id   + "' class='readinmail'>" + 
            item.f  + "</td><td class='" + bt +"' data-id='"  +  item.id   + "' class='readinmail'>" +   item.a   +  "</td><td class='" + bt +"'>"+   item.c +   "</td>" ;
             
            html +=  "<td>";
			
			html  += "<button   data-id='" + item.id  +  "' class='btn-primary btn   readdirectmail'>Read</button> "; 
			if(emailtype ==10)
			{
				html  += "<button data-st='1' data-e='" + item.s  +  "' class='btn-primary btn   btnchangedirectmailstatus'>Accept</button> "; 
			}
			html +=  "<button data-id='" + item.partnerid  +  "' class='btn btn-primary   btncomposedirectmail'> Message</button>"   ;
			
			
			html +=  "</td> ";
				  
            html +=   "</tr>";     

        }); 
        html += '</table>'; 
	    
		prev =  (page == 1) ? 1 :  parseInt(page) -1;
        next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1;  
         
        html += " <ul class='pagination mailboxpager' ><li>" +
            "<a  data-mint='" + emailtype +"'  data-func='prev' data-page='" + prev + "'>«</a></li>";
            for( i=1;  i<= data.pages;  i++){
                
                  active =  i == page ? 'active' : '';
                  html +=  "<li class='" + active + "'><a data-mint='" + emailtype +"' data-page='"+i   +"'>"+ i 
                +"</a></li>";
            }
            html += "<li><a data-mint='" + emailtype +"' data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";  
            $('#myinboxdc' + emailtype).html(html);  
    }
    else if(data.error == 10)
    {
		$('#myinboxdc' + emailtype).html(''); 
    }else 
    {
        
        $('#myinboxdc'  + emailtype ).html(''); 
    }   
        }
    });
}


$(document).on('click', '.readinmail', function() {
	
	$('#contentblock').html("<div ><img   src='../images/processing.gif' alt='Loading ...' /></div>");
    var mailid = $(this).data('id'); 
	var msgcnt=0;
	
	if (mailid)
	{
		$.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { readinmail: 1, mailid: mailid },
            success: function(data) 
			{
				$('#contentblock').html( data) ;
				
				$.ajax({
					type: 'post',
					url: aurl + 'mailbox/count/',
					data: { email:mremail, userid: mid},
					success: function(data) 
					{
						
						data = $.parseJSON(data);
						
						if( data[0]['error1']  == 0)    
							msgcnt = data[0]['count'];
						 
						 
						 
						 if( msgcnt > 8) 
						 {
							 $('#__mcv').html("<span class='bubble bubble-wide bubblemsg'>8+</span>");
						 } 
						 else if( msgcnt > 0) 
						 {
							 $('#__mcv').html("<span class='bubble bubble-wide bubblemsg'>" + msgcnt +"</span>");
						 }else  
						 {
							 $('#__mcv').html(" ");
						 }    
					}
				});
		
				
            }
        });
		  
        $('#menu21b').removeClass('active');
		 $('#menu63').addClass('active'); 
    } 
}) 


$(document).on('click', '.rmvdminbox', function() 
{ 
    var mailid = $(this).data('id'); 
    confFunc('Are you sure you want to remove this email?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: aurl + 'tools/deleterow/',
            data: { tn: 'ebox', trn: mailid },
            success: function(data) {
                waitFunc(''); 
				data = $.parseJSON(data);
				alertFunc('success', data.errmsg);
				$('#row-' + mailid).remove(); 
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});

$(document).on('click', '.loadoutbox', function() {
    
    reloadmailbox(1)
});
 
 
function reloadmailbox(page)
{ 
    $.ajax({
        type: 'post',
        url: aurl + 'mails/',
        data: { loadinbox: 1, page: page },
        success: function(data) { 
           data = $.parseJSON(data);    
           if(data.error == 0)
           { 
              html ="<table class='table table-condensed'><thead><tr><th></th><th>Sender</th><th>Email</th><th>Company</th><th>Message</th><th>Date</th><th>Action</th></tr></thead><tbody> " ;
           
           
        $.each(data.result, function (index, item) 
        { 
            messagepart =  item.e.split(/\s+/).slice(0,5).join(" ");

            html +=  "<tr id='row-"  +  item.a   + "'><td><input type='checkbox' class='delmail' data-id='" + item.a + "'></td><td>" + 
            item.b  + "</td><td>" +   item.c   +  "</td><td>"+   item.i +   "</td>" ;
            html +=  "<td><a href='#' data-id='" +  item.a   +  "' class='readmail'>" + messagepart + "</a></td>";
			html +=  "<td>" + item.f + "</td>";
            html +=  "<td><button class=' btn-danger btn btn-xs rmvContactMail' data-id='" +  item.a   +  "' style='margin-top: 10px '>" +
                 "<i class='fa fa-times-circle'></i> </button></td> ";
            html +=   "</tr>";   

        });
        
        html +=  "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td><td></td><td></td></tr>";
        html += '</table>';
          

        prev =  (page == 1) ? 1 :  parseInt(page) -1;
        next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1;  
         
        html += " <ul class='pagination mailboxpager'><li>" +
            "<a    data-func='prev' data-page='" + prev + "'>«</a></li>";
            for( i=1;  i<= data.pages;  i++){
                
                  active =  i == page ? 'active' : '';
                  html +=  "<li class='" + active + "'><a  data-page='"+i   +"'>"+ i 
                +"</a></li>";
            }
            html += "<li><a  data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";  
            $('#inboxgrid').html(html);  
    }
    else if(data.error == 10)
    {
        alertFunc('info',  data.errmsg );
        $('#inboxgrid').html(''); 
    }else 
    {
        alertFunc('danger', 'Something went wrong, please try again');
        $('#inboxgrid').html(''); 
    }  
        }
    });
} 
$(document).on('click', '.readmail', function() {
    var mailid = $(this).data('id');

    if (mailid) {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { readmail: 1, mailid: mailid },
            success: function(data) {
                $('#mailreaderbox').html(data);
            }
        });
        $('#mailreader').modal('show');
    }
})


$(document).on('click', '.loadmyinbox', function ()
{
     
    var filter =0 ; 
    $('#searchreceipent').val(''); 
    var page = $(this).data('page'); 
    if (typeof page === "undefined")
    {
        page = 1;
	} 
    $.ajax({
        type: 'post',
        url: aurl + 'mail/outbox/',
        data: { loadrefoutbox: 1 , triggermail: filter, page:page, userid: mid},
        success: function(data) {
  
            data = $.parseJSON(data); 
 
  html  =`<table class="table table-condensed">
                <thead>
                <tr><th></th><th>Referral Introducee</th> 
                <th>Referral Introduction Receipent</th> 
                <th>Sent On</th>
                <th>Action</th> 
                </tr>
            </thead><tbody>` ; 
  
            $.each( data.result , function(key, row) {  
                html  += "<tr class=' mailrow' id='row-"  +  row.m_a  +  
                "' ><td ><input type='checkbox' class='delmail' data-id='"   +  row.m_a  + 
                 "'></td> <td  class='readrefmail' data-id='"  +  row.m_a  + "'>" + musername  + "<br/>" + mremail + "</td>" + 
                 "<td  class='readrefmail' data-id='"  +  row.m_a  + "'>"   +  row.cn  + "<br/>" +  row.ce  +  "<br/>" 
                 +  row.cl  + "</td><td><a href='#' data-id='" +  row.m_a  + "' class='readrefmail'>" +  row.m_f  + "</a></td>";
                html += "<td><button class=' btn-primary btn btn-xs queryfeedback' data-rpt='" +  row.m_c  + 
                "'  data-id='" +  row.m_a  + "' style='margin-top: 10px '>Feedback</button> " + 
                "<button style='margin-top: 10px' data-rpt='"+  row.m_a  + "' data-rname='" +  row.cn  +  
                "'  data-introducee='"  + musername +   "'  data-remid='" +  row.ce  + 
                "' class='btn btn-success btn-xs btnselecttrigger'><i class='fa fa-envelope'></i></button> " +
                "<button class='btn-danger btn btn-xs rmvMail'  data-id='" +  row.m_a  +  "' style='margin-top: 10px '> " +
                "<i class='fa fa-times-circle'></i> </button></td> </tr>";
                
            });
 
     
            html += "<tr><td colspan='2'><button class='btn btn-danger  btn btn-xs'>Remove Selected Mail</a></td><td></td><td></td> </tr>";
            html +='</table>'; 
    
        
        prev =  (page == 1) ? 1 :  page -1;
        next = (  page == data.pages ) ?  data.pages : page + 1;  
         
        html += " <ul class='pagination pagiAd'><li>" +
            "<a data-mf='" + filter + "' data-skey='' class='btn-mailfilter' data-func='prev' data-page='" + prev + "'>«</a></li>";
            for( i=1;  i<= data.pages;  i++){
                
                  active =  i == page ? 'active' : '';
                  html +=  "<li class='" + active + "'><a data-mf='1' data-skey='' class='btn-mailfilter' data-page='"+i   +"'>"+ i 
                +"</a></li>";
            }
            html += "<li><a data-mf='1' data-skey='' class='btn-mailfilter' data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";  
			
			
		 html += '<div class="modal fade mine-modal" id="queryfeedback" tabindex="-1" role="dialog">'+
		'<div class="modal-dialog">'+
        '<div class="modal-content">'+
            '<div class="modal-header">'+
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'+
				'<span aria-hidden="true">&times;</span></button>'+
                '<h3 class="modal-title">Did you meet the introduction/referral?</h3>'+
            '</div> '+
			 ' <div class="modal-body text-left" id="queryfeedbackform">'+
			'<div  class="form-group">'+
				'<label>Question #1:</label>'+
			 '<select class="form-control" name="quest1">'+
				'<option>Have you met the contact who was introduced to you?</option>'+
			' </select>'+
			'</div>'+
			'<div  class="form-group"> '+
			 '<label>Question #2:</label>'+
			 '<select class="form-control"  name="quest2">'+
				'<option>How was the meeting?</option>'+
			 '</select>'+
			' </div> '+
			  '</div>'+
			'<div class="modal-footer">'+
				'<input type="hidden" id="datamid"/>'+
				'<input type="hidden" id="datarpt"/>'+
				'<button  class="btn btn-primary sendmeetingfeedback">Send Feedback Enquiry Mail</button>'+
			'</div>'+
		  '</div>'+
		  '</div>'+
		'</div>';
			
			
			
         $('#myoutboxgrid' + filter).html(html);
        }
    }); 
 
});



$(document).on('click', '.searchmailbox', function() {
    var page = $(this).data('page'); 
    var receipentname = $('#searchreceipent').val( ); 
    var filtervalue = 0;
    //filter
    var filter = $('button.btn-mailfilter').each(function( index )
    {
      
      if(  $( this ).hasClass('btn-primary') ) 
      {
            filtervalue = $( this ).data('mf'); 
      } 
    });  
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { searchmailbox: 1 , triggermail: filtervalue ,  receipent: receipentname , page:page },
        success: function(data) {
            $('#myoutboxgrid').html(data);
        }
    });  
});

$(document).on('click', '.btn-mailfilter', function () 
{
	var filter = $(this).attr('data-mf');
    var searchkey = $(this).attr('data-skey');
	var page = $(this).attr('data-page');
    if (typeof page === "undefined") {
        page = 1;
    }
    refreshmailgrid(filter, searchkey, page);    

}) 
    
 
function refreshmailgrid(filter = 0, searchkey , page = 1)
{
	
    if (typeof searchkey != 'undefined' && searchkey != '') {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { searchmailbox: 1, triggermail: filter, receipent: searchkey, page: page },
            success: function (data) {
                $('#myoutboxgrid' + filter).html(data);
            }
        });
    }
    else {
		 
        $.ajax({
            type: 'post',
            url: aurl + 'mail/outbox/',
            data: { loadrefoutbox: 1, triggermail: filter, page: page, userid: mid, useremail: mremail },
            success: function (data) {
                data = $.parseJSON(data);
                if (filter == 1) {
                    html = `<table class="table table-condensed">
                        <thead>
                        <tr><th></th><th>Trigger Mail Receipent</th> 
                        <th>Sent On</th>
                        <th>Action</th> 
                        </tr>
                    </thead><tbody>` ;

                    $.each(data.result, function (key, row) {
                        html += "<tr class=' mailrow' id='row-" + row.m_a + "' ><td ><input type='checkbox' class='delmail' data-id='" +
                            row.m_a + "'></td><td  class='readrefmail' data-id='" + row.m_a + "'>" + row.cn + "<br/>" + row.ce + "<br/>" +
                            row.cl + "</td><td><a href='#' data-id='" + row.m_a + "' class='readrefmail'>" + row.m_f + "</a></td>";

                        html += "<td><button class=' btn-primary btn   queryfeedback' data-rpt='" + row.m_c +
                            "'  data-id='" + row.m_a + "' style='margin-top: 10px '>Feedback</button>" +
                            "<button style='margin-top: 10px' data-rpt='" + row.m_c + "' data-rname='" + row.cn +
                            "' data-introducee='" + row.m_f + "'  data-remid='" + row.ce +
                            "' class='btn btn-success  btnselecttrigger'><i class='fa fa-envelope'></i></button>" +
                            " <button class='btn-danger btn  rmvMail'  data-id='" + row.m_fa + "' style='margin-top: 10px '>" +
                            "<i class='fa fa-times-circle'></i> </button></td>";
                        html += "</tr>";

                    });

                    html += "<tr><td colspan='2'><button class='btn btn-danger btn '>Remove Selected Mail</a></td><td></td><td></td> </tr>";
                    html += '</table>';
                }
                else if (filter == 2) {
                    html = '<table class="table table-condensed"> <thead> <tr><th></th><th>LinkedIn Invite Mail Receipent</th> <th>Sent On</th> <th>Action</th>  </tr> </thead><tbody>' ;
				
				$.each(data.result, function (key, row) {

                        html += "<tr class=' mailrow' id='row-" + row.m_a +
                            "' ><td ><input type='checkbox' class='delmail' data-id='" + row.m_a + "'></td>" +
                            "<td  class='readrefmail' data-id='" + row.m_a + "'>" + row.cn + "<br/>" + row.ce + "<br/>" +
                            row.cl + "</td><td><a href='#' data-id='" + row.m_a + "' class='readrefmail'>" + row.m_f + "</a></td>";
                        html += "<td><button class=' btn-primary btn  queryfeedback' data-rpt='" + row.m_c +
                            "'  data-id='" + row.m_a + "' style='margin-top: 10px '>Feedback</button> " +
                            "<button style='margin-top: 10px' data-rpt='" + row.m_c + "' data-rname='" + row.cn +
                            "' data-introducee='" + musername + "'  data-remid='" + row.ce +
                            "' class='btn btn-success  btnselecttrigger'><i class='fa fa-envelope'></i></button> " +
                            "<button class='btn-danger btn   rmvMail'  data-id='" + row.m_a + "' style='margin-top: 10px '>" +
                            "<i class='fa fa-times-circle'></i></button></td>";
                        html += "</tr>";

                    });


                    html += "<tr><td colspan='2'><button class='btn btn-danger  btn '>Remove Selected Mail</a></td><td></td><td></td> </tr>";
                    html += '</table>'; 

                }
                else if (filter == 0) {

                    html = '<table class="table table-condensed"> <thead> <tr><th></th><th>Referral Introducee</th> <th>Referral Introduction Receipent</th> <th>Sent On</th> <th>Action</th>  </tr> </thead><tbody>' ;

                    $.each(data.result, function (key, row) 
					{
						html += "<tr class=' mailrow' id='row-" + row.m_a +
                            "' ><td ><input type='checkbox' class='delmail' data-id='" + row.m_a +
                            "'></td> <td  class='readrefmail' data-id='" + row.m_a + "'>" + musername + "<br/>" + mremail + "</td>" +
                            "<td  class='readrefmail' data-id='" + row.m_a + "'>" + row.cn + "<br/>" + row.ce + "<br/>"
                            + row.cl + "</td><td><a href='#' data-id='" + row.m_a + "' class='readrefmail'>" + row.m_f + "</a></td>";
                        html += "<td><button class=' btn-primary btn  queryfeedback' data-rpt='" + row.m_c +
                            "'  data-id='" + row.m_a + "' style='margin-top: 10px '>Feedback</button> " +
                            "<button style='margin-top: 10px' data-rpt='" + row.m_a + "' data-rname='" + row.cn +
                            "'  data-introducee='" + musername + "'  data-remid='" + row.ce +
                            "' class='btn btn-success  btnselecttrigger'><i class='fa fa-envelope'></i></button> " +
                            "<button class='btn-danger btn  rmvMail'  data-id='" + row.m_a + "' style='margin-top: 10px '> " +
                            "<i class='fa fa-times-circle'></i> </button></td> </tr>"; 
                    });

                    html += "<tr><td colspan='2'><button class='btn btn-danger  btn  '>Remove Selected Mail</a></td><td></td><td></td> </tr>";
                    html += '</table>';

                }
                else if (filter == 3) {

                    html = `<table class="table table-condensed">
                    <thead>
                    <tr><th></th><th>Mail Receipent</th> 
                    <th>Subject</th> 
                    <th>Sent On</th>
                    <th>Action</th> 
                    </tr>
                </thead><tbody>` ;

                    $.each(data.result, function (key, row) {


                        html += "<tr class='directmailrow' id='row-" + row.id +
                            "' ><td ><input type='checkbox' class='delmail' data-id='" + row.id +
                            "'></td> <td  class='readdirectmail' data-id='" + row.id + "'>" + row.f + "<br/>" + row.i + "</td>" +
                            "<td  class='readdirectmail' data-id='" + row.id + "'>" + row.a + "</td><td>" + row.c + "</td>";
                        html += "<td> ";
						
		 html += "<button class='btn-primary btn  readdirectmail'  data-id='" + row.id + "' style='margin-top: 10px '>Read Mail</button>";
		 html += " <button class='btn-danger btn   rmvdirectMail'  data-id='" + row.id + "' style='margin-top: 10px '> " +
                            " Delete</button></td> </tr>";


                    });
                     
                    html += '</table>'  ;

                } 
                prev = (page == 1) ? 1 : page - 1;
                next = (page == data.pages) ? data.pages : page + 1;

                html += " <ul class='pagination pagiAd'><li>" +
                    "<a data-mf='" + filter + "' data-skey='' class='btn-mailfilter' data-func='prev' data-page='" + prev + "'>«</a></li>";
                for (i = 1; i <= data.pages; i++) {

                    active = i == page ? 'active' : '';
                    html += "<li class='" + active + "'><a data-mf='" + filter + "' data-skey='' class='btn-mailfilter' data-page='" + i + "'>" + i
                        + "</a></li>";
                }
                html += "<li><a data-mf='" + filter + "' data-skey='' class='btn-mailfilter' data-func='next' data-page='" + next + "'>»</a></li></ul> ";
                $('#myoutboxgrid' + filter).html(html); 
            }
        }); 
    } 
}

 
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



$(document).on('click', '.readrefmail', function() {
    var mailid = $(this).data('id');
 

    if (mailid) {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { readrefmail: 1, mailid: mailid },
            success: function(data) {
                $('#refmailreaderbox').html(data);
            }
        });
        $('#refmailreader').modal('show');
    }
})

$(document).on('click', '.rmvContactMail', function() {
	 
    var mailid = $(this).data('id'); 
    confFunc('Are you sure you want to remove this email?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: aurl + 'tools/deleterow/',
            data: { tn: 'contact', trn: mailid },
            success: function(data) {
                waitFunc(''); 
				data = $.parseJSON(data);
				alertFunc('success', data.errmsg);
				$('#row-' + mailid).remove(); 
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});


$(document).on('click', '.rmvMail', function() {
    var mailid = $(this).data('id');
    confFunc('Are you sure you want to remove this email?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { rememail: 1, mailid: mailid },
            success: function(data) {
                waitFunc('');
                if (data != '1') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'Email is removed.');
                    // window.location.reload();
                    //resultreset("showreferrals", "click");
                    $('#row-' + refid).remove();
                }
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});

$(document).on('click', '.rmvdirectMail', function () {
    var mailid = $(this).data('id');
    confFunc('Are you sure you want to remove this email?', function () {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: aurl + 'tools/deleterow/',
            data: { tn: 'ebox', trn: mailid },
            success: function (data) {
                waitFunc('');
                data = $.parseJSON(data);
                alertFunc('info', data.errmsg);
                refreshmailgrid(); 
            } 
        });
    });
});





$(document).on('click', '.queryfeedback', function() {
    waitFunc('enable');
    var mailid = $(this).data('id');
    var rpt = $(this).data('rpt');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { feedbackmailcheck: 1, mailid: mailid },
        success: function(data) {
			 
            if (parseInt(data) == 0) {
                waitFunc('');
                $('#datamid').val(mailid);
                $('#datarpt').val(rpt);
                $('#queryfeedback').modal('show');
            } else {
                waitFunc('');
                alertFunc('warning', 'Meeting feedback enquiry is already sent!');
            }
        }
    });
}); 

$(document).on('click', '.sendmeetingfeedback', function() {
    //save meeting feedback  
    var q1 = $('select[name=quest1]').val();
    var q2 = $('select[name=quest2]').val();
    var mailid = $('#datamid').val();
    var rpt = $('#datarpt').val();

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { sendfeedbackmail: 1, q1: q1, q2: q2, mailid: mailid, rpt: rpt },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'Contact meeting feedback enquiry is sent.');
            $('#queryfeedback').modal('hide');
        }
    });
})

$(document).on('click', '.fetchpoints', function() {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { fetchpoints: 1 },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'Loyalty points earned by members are loaded successfully.');
            $('#showloyaltypoints').html(data);
        }
    });
});

$(document).on('click', '.resetPoint', function() {

    var id = $(this).data('id');
    var newpoint = $('#point' + id).val();
    var oldpoint = $(this).data('cval');

    if (newpoint % 10 > 0) {
        alertFunc('danger', 'Loyalty point should be multiple of 10!');
        return;
    }
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { resetPoint: 1, id: id, point: newpoint, oldpoint: oldpoint },
        success: function(data) { 
            waitFunc('');
            alertFunc('success', 'Loyalty point updated.');
            $('#pcircle' + id).html(newpoint);
        }
    });

});
 

 


//manual suggestions
$(document).on('click', '.suggestConnects', function() 
{
    var id = $(this).data('id');
    var name = $(this).data('name');
    var profession = $(this).data('prof');
    var email = $(this).data('email');
    $('#cname').html(name);
    $('#cprofession').html(profession);
    $('#cemail').html(email);
    $('.suggestconnectmodal').modal('show');
});


//suggestion wizard
$(document).on('click', '.refwizard', function() {
    $('#suggestwizard').modal('show'); 
});


$(document).on('click', '.wizstep1btn', function() 
{
    //reset the wizard 
        $('#wizsummary').html('');
        $('#wiz_refmembers').empty();
        $('#wiz_membertointroduce').empty();

        var profession = $('#wiz_profession').chosen().val() + '';
        $('#wizstep1').removeClass('disabled');
        $('#wizstep1').addClass('complete');

        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { wizstepfetchmember: 1, profession: profession },
            success: function(data) {
                var dropdown = $('#wiz_refmembers');
                dropdown.empty();
                $.each(JSON.parse(data), function(key, value) {
                    $("#wiz_refmembers").append($('<option></option>').val(value.id).html(value.username));
                });
            }
        });
    })
    //wizard step 2
$(document).on('click', '#wiz_refmembers', function() {
    /* multiple select 
    var members =  []; 
    $('#wiz_refmembers :selected').each(function(i, selected){ 
      members[i] = $(selected).val(); 
    });
    */

    var member = $('#wiz_refmembers').find("option:selected").val();
    var profession = $('#wiz_profession').chosen().val() + '';
    if (!member) {
        alert('No member selected!')
    } else {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { wizstepintroreferrals: 1, member: member, profession: profession },
            success: function(data) {
                var dropdown = $('#wiz_membertointroduce');
                dropdown.empty();
                $.each(JSON.parse(data), function(key, value) {
                    $("#wiz_membertointroduce").append($('<option></option>').val(value.id).html(value.username));
                });
            }
        });
        $('#wizstep2').removeClass('disabled');
        $('#wizstep2').addClass('complete');
    } 
})


//wizard step 3
$(document).on('click', '#wiz_membertointroduce', function() {
    var membertointroduce = $(this).find("option:selected").val();
    var member = $('#wiz_refmembers').find("option:selected").val(); 
    $('#wizstep3').removeClass('disabled');
    $('#wizstep3').addClass('complete'); 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { wizfinal: 1, membertointroduce: membertointroduce, member: member },
        success: function(data) {
            $('#wizsummary').html(data);
        }
    }); 
})

$(document).on('click', '.wizsendreferralmail', function() {

    /* 
    data-suggestid="36013" 
    data-suggestemail="aaron.haley@insperity.com" 
    data-suggestname="Aaron Haley" data-suggestphone="310-493-0901" 
    data-suggestprof="Accountant,Accounting,CPA,PEO- Personal Employment Agency" 

    data-clientid="669" 
    data-receipent="adam.torres@usa.net" 
    data-receipentname="Adam Torres" 
    data-receipentphone="310-494-1535" 
    data-receipentprof="Accounting - Bookkeeping,Art Gallery,Wealth Advisor" data-cc1=""

     data-ccname1="" data-mailogid="17602" 
     */

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
        url: 'includes/ajax.php',
        data: {
            wizsendmail: 1,
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
            ccname1: ccname1
        },
        success: function(data) {
            if (data == '1')
                alertFunc('success', 'Introductory email already sent!');
            if (data == 'success')
                alertFunc('success', 'Your introduction mail has been sent!');

            //alertFunc('danger', 'Temporary mail sending error. Please try again to send introduction mail!') ;
            waitFunc('');

        }
    }); 
    //close mail sending dialog
    $('#suggestedreferral').modal('hide'); 
})

var autosource;


$(document).on('change', '#wizmrightd', function() {

    var request = $(this).val();
    if (request.legnth < 4) return;
    $.ajax({
        url: "includes/autocomplete.php",
        type: "GET",
        data: request,
        success: function(data) {
            autosource = data;
        }
    });
    if (jQuery.type(autosource) === "undefined") {

    } else {
        
        $("#wizmright").autocomplete({
            source: "includes/autocomplete.php"
        });

        var options = {
            url: autosource,
            getValue: "name",
            list: {
                match: {
                    enabled: true
                }
            }
        };

        $("#wizmright").easyAutocomplete(options);
    }
    // $("#wizmright").focus();			 
});

//suggestion wizard
$(document).on('click', '.ref_wizard', function() 
{
	
	$('.refereruid').val( mid  );
	$('.referername').val(  musername );
	$('.refererrole').val(  mrole );
	$('.refereremail').val( mremail );
	 

	var sourceoptions = {
        url: function(phrase)
		{
			return "includes/autocomplete-member.php?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list: 
		{
            onSelectItemEvent: function() 
			{
                var value = $("#leftmemberremote").getSelectedItemData();
                $('#lmid').val(value.code); 
            }
        } 
    };
	
	$("#leftmemberremote").easyAutocomplete(sourceoptions); 
    
	var options =
	{
		url: function(phrase)
		{
			return "includes/autocomplete.php?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list:
		{
			onSelectItemEvent: function()
			{
				var value = $("#provider-remote").getSelectedItemData();
                $('#rmid').val(value.code); 
            }
        } 
    };

    $("#provider-remote").easyAutocomplete(options); 
    $('#suggestwizard').modal('show'); 
	$('#onetooneintroduction').modal('hide'); 
   
});



//suggestion wizard
$(document).on('click', '.ref_wizard_byadmin', function() 
{
	var uid = $(this).attr('data-user');
	var role = $(this).attr('data-role');
	var refname = $(this).attr('data-refname');
	var refemail = $(this).attr('data-refemail');
	if(typeof uid != 'undefined' && typeof role != 'undefined'  && typeof refname != 'undefined' && typeof refemail != 'undefined' )
	{
		$('.refereruid').val(uid  );
		$('.referername').val(  refname );
		$('.refererrole').val(  role );
		$('.refereremail').val(  refemail );
	} 
	
	var sourceoptions = {
        url: function(phrase)
		{
			return "includes/autocomplete-member-byadmin.php?phrase=" + phrase + "&format=json" + "&uid=" +  uid + "&role=" +  role ;
        },
        getValue: "name", 
        list: 
		{
            onSelectItemEvent: function() 
			{
                var value = $("#leftmemberremote").getSelectedItemData();
                $('#lmid').val(value.code); 
            }
        } 
    };
	
	$("#leftmemberremote").easyAutocomplete(sourceoptions); 
    
	var options =
	{
		url: function(phrase)
		{
			return "includes/autocomplete.php?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list:
		{
			onSelectItemEvent: function()
			{
				var value = $("#provider-remote").getSelectedItemData();
                $('#rmid').val(value.code); 
            }
        } 
    };

    $("#provider-remote").easyAutocomplete(options); 
    $('#suggestwizard').modal('show'); 
	$('#onetooneintroduction').modal('hide'); 
   
});
 

//suggestion wizard
$(document).on('click', '.ref_wiz_rated6_byadmin', function() 
{
	
	var uid = $(this).attr('data-user'); 
	var role = $(this).attr('data-role');
	var refname = $(this).attr('data-refname');
	var refemail = $(this).attr('data-refemail'); 
	if(typeof uid != 'undefined' && typeof role != 'undefined'  && typeof refname != 'undefined' && typeof refemail != 'undefined' )
	{
		$('.refereruid').val(uid  );
		$('.referername').val(  refname );
		$('.refererrole').val(  role );
		$('.refereremail').val(  refemail );
	}
	$('#wiz_list').html("<div ><img   src='../images/processing.gif' alt='Loading ...' width='160px' /></div>");
	  
	  
	$.ajax({
        type: 'post',
        url: aurl + 'get_knows/tag/',
        data: { 
            mid: uid , mzip:mzip, tag: 'Rated 6 Need to Contact'
         },
        success: function(data)
		{
			waitFunc(''); 
			data = $.parseJSON(data);   
           
			var html = "<table class='table table-sm'>";
                html += "<tr>" +
                "<th>Know Name</th>" +
                "<th>Email</th>"   + 
				"<th>Profession</th>"   + 
				"<th>Distance</th>"   + 
				"<th>Action</th>"  
                "</tr> " ;
				var i=0;
                $.each(data.results, function (index, item) 
                {
					username = item.client_name ; 
                    knowid = item.id  ;
					partnerid = item.user_id ;
					email = item.client_email;
					profession = item.client_profession;
					distance = item.distance;
                    html += "<tr  >" +
                        "<td>"  + username  + "</td>" +
                        "<td>"  + email  + "</td>" +
						"<td>"  + profession  + "</td>" +
						"<td>"  + distance  + "</td>" + 
                        "<td>" + 
						"<button data-id='" + partnerid +"' data-knid='" +knowid+"'  data-email='" +email+"'  data-receipent='" +username+"'  class='btn btn-xs wiz_preview_rated6_mail_template'>View Mail</button>" +
						"</td>" + 
                    "</tr> " ;
					i++;
					
                });
				 
                html += "</table>"; 
				if(i ==0)
				{
					$('#wiz_list').html( "No knows who are Rated 6 found within 30 miles radius");
				}
				else 
				{
					$('#wiz_list').html( html );
				}
			$("#suggest_wiz_3").modal('show');  
        }
    });
});
  

$(document).on('click', '.wiz_preview_rated6_mail_template', function() 
{
	var to = $(this).data('email'); 
	var receipentname = $(this).data('receipent');  
	var knowid = $(this).data('knid');
	var partner = $(this).data('id');
	
	
	waitFunc('enable');
        $.ajax({
        type: 'post',
        url: aurl + 'mailtemplates/rated6/',
        data: { 
            templateid: 2 , 
            to: to,
            receipentname: receipentname,
            knowid: knowid,
            username: musername.replace('+',' '), 
			useremail:mremail,
			partnerid: partner
         },
        success: function(data)
		{
			waitFunc(''); 
			data = $.parseJSON(data);   
             if(data == 0)
            {
                 alertFunc('danger', 'Email preview generation failed. Please retry again!'); 
            }
            else 
            { 
				$('#rated6_mailbody').html(data.templatebody);  
				if(CKEDITOR.instances.rated6_wiz_emailbody)
				{
					CKEDITOR.instances.rated6_wiz_emailbody.destroy(); 
				}
				CKEDITOR.replace( 'rated6_wiz_emailbody' ); 
				CKEDITOR.instances.rated6_wiz_emailbody.setData( $('#rated6_mailbody').html() );
				$("#suggest_wiz_3").modal('hide'); 
				
				$('.wiz_rated6_send_referral_mail').attr('data-email', to);
				$('.wiz_rated6_send_referral_mail').attr('data-receipent', receipentname);
				$('.wiz_rated6_send_referral_mail').attr('data-knid', knowid);
				$('.wiz_rated6_send_referral_mail').attr('data-id', partner);
				$("#rated6_intromail").modal('show'); 
            }	 
        }
    });
})

$(document).on('click', '.wiz_rated6_send_referral_mail', function()
{ 
	var email = $(this).data('email');
	var receipent = $(this).data('receipent');
	var knid = $(this).data('knid');
	var partner = $(this).data('id');
	var emailbody = CKEDITOR.instances.rated6_wiz_emailbody.getData(); 
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + '/email/rated6invite/',
        data: 
        {
			templateid: 1,
            receipent: receipent,
            email: email, 
			emailbody:emailbody,
			knowid: knid,
			partner:partner
        },
        success: function(data) 
		{ 
			alertFunc('success', 'Invite to join mycity.com has been sent!');
			waitFunc('');
           $("#rated6_intromail").modal('hide'); 
        }
		,
		error: function() {
         waitFunc('');
         alertFunc('info', 'Something went wrong, please try again')
     }
    });  
})



//suggestion wizard
//suggestion wizard
$(document).on('click', '.view_comm_vocation', function() 
{
	var uid = $(this).attr('data-user');   
	var html ='';
	waitFunc('enable');
	$.ajax({
		 type: 'post',
		 url: aurl + 'commonvocations/get/',
		 data: {  mid:  mid, kid:uid },
		 success: function(data) 
		 {
			 data = $.parseJSON(data);
			 var comvoc = data.common_vocs;
			  
			  if(typeof comvoc !== 'undefined' || comvoc !='' )
			  {
				var voclists = comvoc.split(','); 	
				$.each( voclists, function (index, item) 
                {
					html += "<input type='checkbox' name='import_comvoc' value='" + item +"'> " +item + "<br/>";  
                }); 
				
				html += "<br/><button data-user='" +  uid+ "' class='btn btn-primary btn-xs btnimportcomvocs'> Add Selected Vocations</button>";
				
				$('#memcommvoc').html(  html); 
				
			  }
			  else 
				 $('#memcommvoc').html("No Vocations saved!"); 
		
			   	 
			 waitFunc(''); 
		 } 
	 });
		 		
    $.ajax({
        type: 'post',
        url: aurl + 'knows/getprofile/',
        data: {  id: uid },
        success: function(data) 
		{
		  data = $.parseJSON(data);   
		 
          var vocation = data.profile[0].client_profession;
		  $('#memexistvoc').html( vocation );  
		  waitFunc(''); 
        } 
    }); 
	   
    $('#commonvocmodal').modal('show');  
   
});


$(document).on('click', '.btnimportcomvocs', function() {
	var kid = $(this).attr('data-user');   
   var import_comvoc = [];
    $('input[name="import_comvoc"]:checked').each(function(i) {
        import_comvoc[i] = $(this).val();
    });
	  
	 
	waitFunc('enable');
	$.ajax({
        type: 'post',
        url: aurl + 'knows/update/vocations/',
        data: {  vocations: import_comvoc, knowid: kid },
        success: function(data) 
		{ 
		  waitFunc('');   
        } 
    }); 
	
})


$(document).on('click', '.ref_directtodirectwizard', function() {


var sourceoptions = {
        url: function(phrase) {
            return "includes/autocomplete-member.php?phrase=" + phrase + "&format=json";
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
	 
    var options =
	{
		url: function(phrase)
		{
			return "includes/autocomplete.php?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list:
		{
			onSelectItemEvent: function()
			{
				var value = $("#dtdrightmember").getSelectedItemData();
                $('#dtdrmid').val(value.code); 
            }
        } 
    };
 
    $("#dtdrightmember").easyAutocomplete(options); 
    $('#onetooneintroduction').modal('show');
    $('#suggestwizard').modal('hide');
});



$(document).on('click', '.wiz_step1_show_member', function()
{
	//reset the wizard 
	$('#wiz_memberleft').empty();
    $('#wizsummary').html('');
    $('#wiz_summary').empty();
    $('#wiz_summary').html('');
    $('#wiz_refmembers').empty();
    $('#wiz_membertointroduce').empty();

    var profession = $('#wiz_profession').chosen().val() + '';
    $('#wizstep1').removeClass('disabled');
    $('#wizstep1').addClass('complete');
	
	var userid = $('.refereruid').val(   );
	var role =  $('.refererrole').val(    );
	 
	 
	if(typeof userid == 'undefined' &&  typeof role == 'undefined'   )
	{
		userid =mid; role =mrole; 
	}
	   
    $.ajax({
        type: 'post',
        url: aurl + 'member/getbyvocations/',
        data: { wizstepfetchmember: 1, vocations: profession, userid: userid, userrole: role },
        success: function(data) {
         
          data = $.parseJSON(data);
            //console.log(data)
            var dropdown = $('#wiz_memberleft');
            dropdown.empty();
            $.each( data , function(key, value) {
                $("#wiz_memberleft").append($('<option></option>').val(value.id).html(value.username));
				$("#wiz_memberleft").trigger("chosen:updated");
				//$(".chosen-results").append($('li class="active-result" data-option-array-index="'+value.id+'"></li>').html(value.username));
            }); 
            //change to multiselect input
            var config = 
			{
                '.wiz_memberleft': {},
                '.wiz_memberleft-deselect': { allow_single_deselect: true },
                '.wiz_memberleft-no-single': { disable_search_threshold: 10 },
                '.wiz_memberleft-no-results': { no_results_text: 'Oops, nothing found!' },
                '.wiz_memberleft-width': { width: "95%" }
            }
            for (var selector in config) 
			{
				$(selector).chosen(config[selector]);
            } 
        }
    });
}) 

$(document).on('click', '.dwiz_step_show_summary', function() 
{
	var memberleft = $("#dtdlmid").val();
	var memberright = $("#dtdrmid").val();
	preparesuggestionwizard(memberleft, memberright , 'dtdwiz_summary');
});

$(document).on('click', '.wiz_step_show_summary', function() 
{
	var memberleft = $(".wiz_memberleft").chosen().val();
	var memberright = $("#rmid").val();
	var html = preparesuggestionwizard(memberleft, memberright, 'wiz_summary');
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
         ' <div style="visibility: hidden; display: none;" id="mailbody"></div>' +
		 '<div id="wiz_emailbody" class="wiz_emailbody"></div> ' + 
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

$(document).on('click', '.wiz_preview_mail_template', function() 
{
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
            username: musername.replace('+',' '), 
			useremail:mremail
         },
        success: function(data)
		{
			waitFunc(''); 
			data = $.parseJSON(data);   
            if(data == 0)
            {
                 alertFunc('danger', 'Email preview generation failed. Please retry again!'); 
            }
            else 
            {
				 
				$('#mailbody').html(data.templatebody); 
				  
				if(CKEDITOR.instances.wiz_emailbody)
				{
					CKEDITOR.instances.wiz_emailbody.destroy(); 
				}
				CKEDITOR.replace( 'wiz_emailbody' ); 
				CKEDITOR.instances.wiz_emailbody.setData( $('#mailbody').html() );
				$("#suggestwizard").modal('hide');
				$("#onetooneintroduction").modal('hide');
				$("#intromailtemplate").modal('show'); 
            }
        }
    });
})

$(document).on('click', '.wiz_send_referral_mail', function()
{
	
	 
	var refuserid = $('.refereruid').val(   ); 
	var refusername =  $('.referername').val(   );
	var emailbody = CKEDITOR.instances.wiz_emailbody.getData();   
		
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
            username: refusername,
			mremail: mremail,
			emailbody:emailbody
			
        },
        success: function(data) 
		{
			 
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

 
 

 /* $(document).on('click', '.viewrefs', function() {
    $("#reflist").html('');
    var mid = $(this).data('id');
    var count = $(this).data('count');
    var goto = $(this).data('goto');
    var rs = $(this).data('rs');
    $('#loading').show();
    if (count > 0) {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { trackreferrals: 1, mid: mid, goto: goto, rs: rs },
            success: function(data) {
                $("#reflist").html(data);
                $('#loading').hide();
            }
        });
        if (goto == 1) {
            $('#reftrackingboard').modal('show');
        }
    }
}); */  

$(document).on('click', '.viewperformance', function(){
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'performance/get/',
        data: { userid: mid },
        success: function(data) {
  
         waitFunc('');
         data = $.parseJSON(data);

        
         if ( typeof data.error === "undefined" )
         {
             alertFunc('success', 'Performance Report Generated!');
              
             html  = '<h3 class="text-center">Weekly Performance Summary</h3>';
             html += '<p class="text-center">This report is for current week (from ' +   data.start_week  + ' to '  +  data.current_week_end   + ' ) </p><br/>';
             
             html += '<div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' +
             '<span class="count_top"><i class="fa fa-user"></i> Referrals</span>' +
             '<div class="counter">'  +  data.currentweekcnt + '</div>' +
             '<span class="count_bottom"><i class="green">'  +  data.currentweekgrowthpc + ' % </i> From last Week</span>' +
             '</div></div>';  
             
             
             html += '<div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' +
             '<span class="count_top"><i class="fa fa-user"></i> Referrals</span>' +
             '<div class="counter">'  +  data.currentweekrefsmailcnt + '</div>' +
             '<span class="count_bottom"><i class="green">'  +  data.cweekemailgrowthpc + ' % </i> From last Week</span>' +
             '</div></div>'; 

             html += '<div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' +
             ' <span class="count_top"><i class="fa fa-user"></i> Groups With Known Contacts</span>' +
             '<div class="counter">' +  data.groupcount +  '</div>' +
             ' <span class="count_bottom"><span class="btn btn-link pr-btn-showgroup" >Views Groups</span></span>' +
             ' </div></div>'; 

             html += '<div class="col-md-4 col-sm-4 col-xs-6"><div class="tile_stats_count">' +
           '<span class="count_top"><i class="fa fa-user"></i> Trigger Mails Sent</span>' +
           ' <div class="counter">'  +  data.triggermailscount +   '</div>' +
           '<span class="count_bottom"><i class="green">This reflects total trigger mails sent so far.</span>' +
           ' </div></div>'; 

           html +=	'<div class="modal fade prallgroupsmodal" tabindex="-1" role="dialog" aria-labelledby="prallgroupsmodal"' +
        ' id="prallgroupsmodal">' +
        ' <div class="modal-dialog ">' +
        ' <div class="modal-content">' +
        '   <div class="modal-header">' +
        ' <button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
        ' <span aria-hidden="true">&times;</span></button>' +
        ' <h2 class="modal-title" >All Groups With Known Referrals</h2>' +
        ' <small ></small>' +
        ' </div>' +
        ' <div class="modal-body modal-body-no-pad" style="max-height: 320px; overflow-y:scroll; text-align:left"> ' +
        ' <h4>You have known referrals in the following groups</h4>' +
        ' <hr/>	 '  +  data.groupnames + ' </div>' +
        ' <div class="modal-footer" >' +
        ' <button data-dismiss="modal"  class="btn btn-primary">Close</button>' +
        '  </div>' +
        '  </div>' +
        '</div>' +
        '</div>'; 
        
        $('#performdashboard').html(html);	
        
        }
         else 
         {
            alertFunc('danger', 'Something went wrong, please try again')
         }

     },
     error: function() {
         waitFunc('');
         alertFunc('info', 'Something went wrong, please try again')
     }
    });
}); 



$(document).on('click', '.btnrem', function(){
	  
	var id = $(this).attr('data-id');
    var thisUser = $(this);
    confFunc('Are you sure you want to delete this reminder?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { delReminder: id },
            success: function(data) {
                waitFunc('');
                if (data == 'error') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'Reminder successfully deleted');
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

$(document).on('click', '.btnvu', function(){
	  var id = $(this).attr('data-id');
	  var title = $(this).attr('data-title');
	  var reminder = $(this).attr('data-reminder');
	  $('#remindtitle').html( title  );   
	  $('#remisummary').html(  reminder); 
	  $('#reminderview').modal('show'); 
});
 

$(document).on('click', '.btnedit', function(){
	 
	var id = $(this).attr('data-id');
	var type = $(this).attr('data-type');
	var assignedto = $(this).attr('data-assignto');
	var remdate = $(this).attr('data-remday'); 
	var remhr = $(this).attr('data-remhr')  ;
	var remformat = 'AM'; 	
	if(remhr > 12) 
	{
		var remformat = 'PM'; 	
		remhr =remhr-12;
	} 
	var remmin = $(this).attr('data-remmin'); 	 
	var title = $(this).attr('data-title'); 
	var reminder = $(this).attr('data-reminder');
    autocompleteenablemembers();
     
	$.ajax({
            type: 'post',
            url: aurl + 'contact/getname/',
            data: { contactid: assignedto },
            success: function(data)
			{
                data = $.parseJSON(data);
                $('#assignno').val(data.contactname);
            } 
        });  
	
	$('#remindermailday').val(  remdate );
	$('#hour').val(  remhr );
	$('#min').val(  remmin );
	$('#title').val(title);
	$('#text').val(reminder);
	$('#reminder').val(reminder);
	$('#hrformat').val(remformat); 
	$('#hidassignno').val(assignedto);
	$('#btnsavereminder').attr('data-id', id);  
	
});


$(document).on('click', '.configureReminder', function() {
	autocompleteenablemembers();
});	

function autocompleteenablemembers()
{
	var options = {
        url: function(phrase) {
            return aurl + "members/getall/" + phrase + "/";
        },
        getValue: "name", 
        list: {
            onSelectItemEvent: function() {
                var value = $("#assignno").getSelectedItemData();
                $('#hidassignno').val(value.code); 
            }
        } 
    }; 
	 
    $("#assignno").easyAutocomplete(options);  
}


	
function autocompleteenable()
{ 
    var options = {
        url: function(phrase) {
            return "includes/autocomplete.php?phrase=" + phrase + "&format=json";
        },
        getValue: "name", 
        list: {
            onSelectItemEvent: function() {
                var value = $("#assignno").getSelectedItemData();
                $('#hidassignno').val(value.code); 
            }
        } 
    }; 
    $("#assignno").easyAutocomplete(options);  
}




$(document).on('click', '.pr-btn-showgroup', function(){
	  
	  var id = $(this).attr('data-id');
	  
	 $('.prallgroupsmodal').modal('show');
	 
});

$(document).ready(function(){
	
	$('#remindermailday').datepicker({ dateFormat: 'dd-mm-yy' });
	 
    var sourceoptions = {
        url: function (phrase) {
            return "includes/autocomplete-vocations.php?phrase=" + phrase + "&format=json";
        },
        getValue: "name",
        list: {
            onSelectItemEvent: function () {
                var value = $("#gskey").getSelectedItemData();

            }
        }
    };

    $("#gskey").easyAutocomplete(sourceoptions);
	
	var sourceoptions = {
        url: function (phrase) {
            return "includes/autocomplete-city.php?phrase=" + phrase + "&format=json";
        },
        getValue: "name",
        list: {
            onSelectItemEvent: function () {
                var value = $("#gscityorzip").getSelectedItemData();

            }
        }
    }; 
    $("#gscityorzip").easyAutocomplete(sourceoptions);
	
	
})

$(document).on('click', '#btnsavereminder', function(e)
{
	e.stopImmediatePropagation();
	var remindertype =  $("input[name='type']:checked"). val();
	var title = $('#title').val();
	var text = $('#text').val(); 
	var reminderdate = $('#remindermailday').val();
	var hr = $('#hour').val();
	var min = $('#min').val();
    var hrformat = $('#hrformat').val();
    var remid = $(this).attr('data-id'); 
	var reminderData = new FormData();
	
	if(mid == 1)
		var assignedto = $('#hidassignno').val();
	else 
		var assignedto =0;
	
	var reminderdate = $('#remindermailday').val();
	reminderData.append('set_reminder', '1');
	reminderData.append('type', remindertype);
    reminderData.append('title', title);
    reminderData.append('text', text);
	reminderData.append('assignedto', assignedto);
	reminderData.append('reminderdate', reminderdate);
	reminderData.append('hr', hr);
	reminderData.append('min', min);
	reminderData.append('hrformat', hrformat);
    reminderData.append('userid', mid);
    
	if(typeof remid !== 'undefined') 
    {
        reminderData.append('remid', remid);
    }
    else 
    {
        reminderData.append('remid', 0);
    }

	waitFunc('enable');
    
	$.ajax({
        url: aurl + "reminder/save/",
        type: 'post',
        cache: false,
        contentType: false,
        processData: false,
        data: reminderData,
        success: function(data) 
		{   
            var results = jQuery.parseJSON(JSON.stringify(data)); 
			waitFunc('disable');
			alertFunc('success', 'Reminder created successfully!');
        },
        error: function(textStatus, errorThrown) {
            waitFunc('disable');
            alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
        }
    });
	
})



$(document).on('click', '.btneditcomvoc', function(){
	
	var src = $(this).attr('data-src'); 
	var trg = $(this).attr('data-trg');
	
	$("#member_voc").val(src).trigger("chosen:updated"); 	
	var trglist = trg.split(','); 	
	$("#common_vocations").val(trglist).trigger("chosen:updated"); 
	
})


$(document).on('click', '.savesettingscv', function() {
    waitFunc('enable');
	var membervoc =   $("#member_voc").chosen().val() + ''; 
	var vocation =   $(".common_vocations").chosen().val() + '';
	 
	
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { settings : 1, vocation: vocation, membervoc:membervoc },
        success: function(data) { 
			alertFunc('success', 'Settings saved succesfully!');
			waitFunc('');
        }
    });
})  

$(document).on('click', '.editcommonvocation', function() {
   
	$.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { loadsettings : 1, id: $(this).attr('data-id') },
        success: function(data) { 
			$('#settingsvalue').html(data);
			 $('#commonvocationmodal').modal('show');
        }
    }); 
	
}) 
  
$(document).on('click', '.addcommonvocation', function() {
   waitFunc('enable');
	 
	$.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addcommonvocation : 1 , vocation: $('#commonvoc').val(), knowid:   $('#commonvoc').attr('data-id')  },
        success: function(data) {
			 alertFunc('success', 'Settings saved succesfully!');
			 waitFunc('');
			 
        }
    });  
}) 

$(document).on('click', '.importknows', function() {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { importknows: 1 },
        success: function(data) {
		 
            if (data == "nofile") {
                alertFunc('danger', 'No import file is present!');
                waitFunc('');
            } else {
                alertFunc('success', 'New knows are imported successfully!');
                waitFunc('');
            }
        }
    });
})
 
$(document).on('click', '.linkedinimport', function()
{
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { linkedinimport: 1 },
        success: function(data) {
          
            if (data == "nofile") {
                alertFunc('danger', 'No LinkedIn archive file is present!');
                waitFunc('');
            } else {
                alertFunc('success', 'Linkedin contacts imported successfully!');
                waitFunc('');
            }
        }
    });
}) 

$(document).on('click', '.linkedinsignup', function() {
  getlinkedinsignup (1, '');
})

function getlinkedinsignup(page, searchkey) 
{
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { linkedinsignups: page , key:searchkey},
        success: function(data) {
            
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('#linkedinsignuplist').html(data);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
} 




$(document).on('click', '.btnsendlinkedininvite', function() 
{ 
	var templateid = $(this).data('tid');
    var email = $(this).data('email');
    var name = $(this).data('name');
    var contactid = $(this).data('contactid');
    
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { invitelinkedincontact: contactid, email: email, receipent: name, templateid: templateid  },
        success: function(data) 
		{     
            
            waitFunc('');
			if(data ==0)
			{
				 alertFunc('danger', 'Something went wrong, please try again')
			}
			else 
			{
				alertFunc('success', 'Invite sent successfully!');
			} 
        }
    }); 
}) 

$(document).on('click', '.loadimportedknows', function() 
{
    getImportedKnows(1);
})

function getImportedKnows(page) {
    waitFunc('enable');
    pages = 1;
    $.ajax({
        type: 'post',
        url:  aurl + "contacts/getimportedknows/"  ,
        data: { goto : page, userid: 1, size: 10  },
        success: function(data) 
        { 
            //"error":"0","errmsg":"Contacts are retrieved!","numrows":"21204","data"
            waitFunc('');
            data = $.parseJSON(data); 
            pages = Math.ceil( data.numrows/10);
   
            if (data.error == 10 || data.error == 1 ) 
            {
                alertFunc('danger', 'Something went wrong, please try again')
            }
            else
            {
                html ='<table class="table table-bordered table-alternate"><tr><th>Reference Name</th><th>Vocation</th><th>Phone</th><th>Email</th><th>Location</th><th>Group</th><th>Action</th></tr>';
                $.each( data.result , function(idx, obj){
                    html += "<tr>" +
                        "<td>"  + obj.c + "</td>" +
                        "<td>"  + obj.d + "</td>" +
                        "<td>"  + nulltospace(obj.e)   + "</td>" +
                        "<td>"  + obj.f + "</td>" +
                        "<td>"  + nulltospace(obj.g) + "</td>" +
                        "<td>"  + nulltospace(obj.j) + "</td>" +
     "<td><button data-toggle='modal' data-target='#edit_people_details' class='btn-primary btn btn-xs editPeopleDetails'><i class='fa fa-pencil'></i></button>" +
     "<button data-id='" + obj.id + "' class='btn-success btn btn-xs editcommonvocation'><i class='fa fa-briefcase'></i></button>" +
     "<button class='btn-danger btn btn-xs delUserClient' data-id='" + obj.a + "'><i class='fa fa-times-circle'></i></button></td>" + 
     "</tr>"; 
                })   
                prev =  (page == 1) ? 1 : page-1;
                next = ( page == pages ) ? pages : page + 1 ;
                
                html += "<tr><td colspan='7'><ul class='pagination pagiimportknow'><li><a data-func='"+ prev +"' data-pg='"+ prev +"'>«</a></li>";
                for( i=1;  i<= pages;  i++)
                {
                     active =  i == page ? 'active' : '';
                     html += "<li class='"+ active +"'><a data-pg='"+ i +"'>"+ i +"</a></li>";
                }
                html += "<li><a data-func='"+ next +"' data-pg='"+ next +"'>»</a></li></ul></td></tr>";
                html +='</table>';
                $('.manageimportedlist').html(html);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    }); 
} 
 
$(document).on('click', '.btnshownmailtemplates', function() {
	event.preventDefault();
	waitFunc('enable');
    var name = $(this).data('receipent');
	var email = $(this).data('email');
	var id = $(this).data('id');
	
	var mailsent = $(this).data('mailsent');
	waitFunc('');
	if(mailsent == 1)
	{
		alertFunc('info', 'Invitation email already sent!');
	}
	else 
	{
		$.ajax({
			type: 'post',
			url: 'includes/ajax.php',
			data: { loadlinvitemailtemplate: 1 ,name:name, email: email, contact: id },
			success: function(data)
			{ 
				$('#linkedinmails').html(data); 
				$('#selectlinkedinmail').modal('show');
			} 
		});
	} 
})	

$(document).on('click', '.btnsendlinkedinmail', function() 
{
	event.preventDefault();
    waitFunc('enable');
	var id = $(this).attr('data-id');
	var mailsent = $(this).data('mailsent'); 
	var page = $('.pagilinkedin li.active a').data('pg');
	
	if(mailsent == 1)
	{
		waitFunc('');  
		alertFunc('info', 'Invitation email already sent earlier!');
		exit;
	}
 
	$.ajax({
		type: 'post',
        url: 'includes/ajax.php',
        data: { invitelinkedincontact: id },
        success: function(data)
		{
			waitFunc('');  
			if(data ==1)
			{
				alertFunc('info', 'Invitation sent!'); 
				getLinkedInImportedContacts(page); 
			}
			else
			{
				alertFunc('danger',  'Something went wrong, please try again');
			}
        } ,
        error: function()
		{
            waitFunc(''); 
        }
    });  
})


 
$(document).on('click', '.reminderread', function(){
	
	var id = $(this).attr('data-id');
	var isread = $(this).attr('data-isread');	
	if(isread ==1)
		return; 
	
	$.ajax({
        type: 'post',
        url: aurl + 'reminder/markread/',
        data: { remid: id, isread:isread },
        success: function(data)
		{
			var remcnt = $('#bubble').html();
			
			if(typeof remcnt != 'undefined')
			{
				remcnt = parseInt(remcnt);
				 
				remcnt--;
				
				if(remcnt == 0) $('#bubble').addClass('hide');
				 $('#bubble').html(remcnt);
			}
			
        } ,
        error: function() {
            waitFunc(''); 
        }
    }); 
})
 

$(document).on('click', '.fetchreminder', function()
{
	
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'reminders/get/',
        data: { userid: mid },
        success: function(data)
		{ 
            waitFunc('');    
            
            data = $.parseJSON(data);
            html="";
			 
            if(data[0].error1  == 10) 
            {
                html = '<div class="panel panel-default panel-danger"><div class="panel-body"><p>' ; 
                html +=  '<div  class="text-center">You haven\'t set any reminder!</div>';
                html += '</div></div>'; 

            }
            else 
            { 
           //for left block
		    
            $.each(  data[0].resultset1 , function(i, item) {
                
                switch(item.type )
                { 
                  case 'CALL':
                    icon  ='<i class="fa fa-phone dark"></i> ';
                    break;  
                    case 'NOTE':
                        icon  ='<i class="fa fa-pencil dark"></i> ';
                        break;
                   case 'TASK':
                    icon  ='<i class="fa fa-tasks dark"></i> ';
                        break;
                     case 'EMAIL':
                     icon  ='<i class="fa fa-envelope dark"></i> ';
                        break;
                     case 'MEETING':
                     icon  ='<i class="fa fa-users dark"></i> ';
                        break;
                     case 'PHONE':
                     icon  ='<i class="fa fa-phone dark"></i> ';
                        break;
                        
                }
                html += '<div class="panel panel-default  panel-success"><div class="panel-body"><p>' + icon + '<strong>' + 
                item.subject +  '</strong></p><hr/>' ;
                html +=    item.reminderbody  ;
                
                html += '<hr/><p>Reminder set on: <span class="badge badge-remindate">' +  item.emailreminderon  + '</span> </p>' ;
                html += '</div></div>';  
            }); 
            
        }
           $('#reminder-gridleft').html(html); 
            
            html="";
             
            //for left block
            if(data[1].error2  == 100) 
            {
                html = '<div class="panel panel-default panel-danger"><div class="panel-body"><p>' ; 
                html +=  '<div  class="text-center">All reminders catched up!</div>';
                html += '</div></div>'; 

            }
            else 
            {
                $.each(  data[1].resultset2 , function(i, item) {
                    
                                   switch(item.type )
                                   { 
                                     case 'CALL':
                                       icon  ='<i class="fa fa-phone dark"></i> ';
                                       break;  
                                       case 'NOTE':
                                           icon  ='<i class="fa fa-pencil dark"></i> ';
                                           break;
                                      case 'TASK':
                                       icon  ='<i class="fa fa-tasks dark"></i> ';
                                           break;
                                        case 'EMAIL':
                                        icon  ='<i class="fa fa-envelope dark"></i> ';
                                           break;
                                        case 'MEETING':
                                        icon  ='<i class="fa fa-users dark"></i> ';
                                           break;
                                        case 'PHONE':
                                        icon  ='<i class="fa fa-phone dark"></i> ';
                                           break;
                                           
                                   }
                                   html += '<div class="panel panel-default  panel-success"><div class="panel-body"><p>' + icon + '<strong>' + 
                                   item.subject +  '</strong></p><hr/>' ;
                                   html +=    item.reminderbody  ;
                                   
                                   html += '<hr/><p>Reminder set on: <span class="badge badge-remindate">' +  item.emailreminderon  + '</span> </p>' ;
                                   html += '</div></div>'; 
                               
                               });
            }
             
            $('#reminder-gridright').html(html);  
        } ,
        error: function() {
            waitFunc(''); 
        }
    }); 
	
}) 
 

$(document).on('click', '#menu-content li a', function() {
       
	 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { checksession: 1 },
        success: function(data)
        {  
           if(data =='session_out')
           {
                 
                $('body > #confirm-box').remove();

                $('body').append('<div class="modal fade" id="confirm-box" >' +
                    '<div class="modal-dialog modal-sm">' +
                    '<div class="modal-content"><div class="modal-header modal-header-sm">Session Expired</div>' +
                    '<div class="modal-body modal-body-sm"><p>Your active session has expired. Please login again.</p></div>' +
                    '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-default btn-confirm" data-confirm="no">Ok</button>' + 
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>');

                $('#confirm-box').modal({ backdrop: 'static', keyboard: false, show: true }); // open lightbox

                $('#confirm-box .btn-confirm').click(function(e) {
                     
                    var exdate=new Date();
                    exdate.setDate(exdate.getTime() - 60*60*1000*24); 
                    document.cookie = "_mcu= ; " + exdate.toUTCString() ;   
                    
                    $('#confirm-box').modal('hide');
                    window.location ='index.php';
                }); 
           }  
        }  
    });
}) 
   

$(document).on('click', '.showmyknows', function()
{ 
    var page = $(this).attr('data-page'); 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { showmyknows: 1 , page: page },
        success: function(data)
        {  
            $('#myknowsgrid').html(data);
        }
    });
})

  

$(document).on('click', '.vuconcount', function()
{
	var id = $(this).attr('data-id');
	$('#commonconnects').modal('show');  
	 $('.cctable').html("<div class='text-center'><img   src='../images/processing.gif' alt='Loading ...' /></div>");
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

$(document).on('click', '.vuconnections', function()
{
	var uid = $(this).attr('data-uid');
	var partnerid = $(this).attr('data-pid');
	 
	 $('.ccviewtable').html("<div class='text-center'><img   src='../images/processing.gif' alt='Loading ...' /></div>");
	 
	 $.ajax({
        type: 'post',
        url: aurl + 'members/commonconnects/getall/',
        data: { uid: uid, partnerid: partnerid},
        success: function(data)
        { 
            data = $.parseJSON(data);    
            if(data.error == 1 )
            { 
				 $('.ccviewtable').html("");
                alertFunc('danger',  'Something went wrong, please try again');
            } 
			if(data.error == 10 )
            { 
				 $('.ccviewtable').html("");
                alertFunc('danger',  data.errmsg);
            } 
            else 
            {
                var html = "<table class='table table-sm'>";
                html += "<tr id='$rand-$id'>" +
                "<th>Connection Name</th>" +
                "<th>Email</th>"   + "<th>Phone</th>"  +
				"<th>Profession</th>" +  "</tr> " ;
    
                $.each(data.results, function (index, item) 
                {
                    client_name = item.client_name ;
                    client_profession = item.client_profession ;
                    client_email = item.client_email ;
					client_phone = item.client_phone ;
                   
                    html += "<tr id='$rand-$id'>" +
                        "<td>"  + client_name  + "</td>" +
                         "<td>"  + client_email  + "</td>" +
                        "<td  >"  +client_phone   +  "</td><td>" + 
						  client_profession +
						"</td>" + 
                        "</tr> " ;
                });
				 
                html += "</table>"; 
                $('.ccviewtable').html( html );  
            } 
        }  
    }); 

}); 

$(document).on('click', '.btnknowreport', function()
{
    var page = $(this).attr('data-page'); 
    var userrole = $(this).attr('data-role');
    $.ajax
    ({
        type: 'post',
        url: 'includes/ajax.php',
        data: { generateknowstates: 1 , page: page, role: userrole },
        success: function(data)
        {   
            $('#knowentrylog').html(data);
        }
    });
})    


 




 
$(document).on('click', '.btnshownewcontacts', function() {

	//show here contactsaddedlastweek
	var userid = $(this).attr('data-mid'); 
	var page = $(this).attr('data-pg'); 
	var key = $(this).attr('data-key'); 
	loadnewcontacts(page, key, userid); 
	
});

//LinkedIn Contact pagination
$(document).on('click', '.paginewcontacts li', function() {
	var userid = $(this).find('a').attr('data-mid'); 
    var page = $(this).find('a').attr('data-pg');
	var key = $(this).find('a').attr('data-key');
    loadnewcontacts(page, key, userid);
});

function loadnewcontacts(page, key, userid)
{
	waitFunc('enable');
	$.ajax({
		type: 'post',
		url: 'includes/ajax.php',
		data: { getnewcontacts: 1, userid:userid, page: page, key:key },
		success: function(data) { 
			$('#contactsaddedlastweek').html(data);
			waitFunc('');
        },
        error: function() {
			waitFunc('');
			alertFunc('info', 'Something went wrong, please try again')
        }
    }); 
}


$(document).on('click', '.btnsendvideolink', function() {
	event.preventDefault();
	waitFunc('enable');
	var email = $(this).attr('data-email');
	var id = $(this).attr('data-id'); 
	var receipent = $(this).attr('data-receipent'); 
	 
 
		$.ajax({
			type: 'post',
			url: 'includes/ajax.php',
			data: { loadvideomailtemplates: 1 ,name:receipent, email: email, contact: id, mailtype:3 },
			success: function(data)
			{ 
				 
				waitFunc('');
				$('#videomailtemplates').html(data); 
				$('#selectvideomail').modal('show');
			} 
		});
	 
})	


$(document).on('click', '.btnsendvideourl', function(){
	var email = $(this).attr('data-email');
	var id = $(this).attr('data-contactid'); 
	var templateid = $(this).attr('data-tid') ;
	var receipent = $(this).attr('data-name'); 
	var videourl  = $('#tbvideourl').val(); 
	
	waitFunc('enable');
	$.ajax({
		type: 'post',
		url: 'includes/ajax.php',
		data: { sendvideourl: 1, userid:id, email:  email, receipent:receipent, templateid:templateid , url:videourl },
		success: function(data) {  
			waitFunc('');
        },
        error: function() {
			waitFunc('');
			alertFunc('info', 'Something went wrong, please try again')
        }
    }); 
	
});

 $(document).on('click', '.btnlinkedinsignup', function()
{
	 var ques_rate = [],
        ques = [];
    var user_ques_text = [];
    $('.luser_ques_main').each(function(i) {
        ques[i] = $(this).attr('data-ques');
        ques_rate[i] = $(this).find('.luser_ques:checked').val();
    });
    $('.luser_ques_text_add').each(function(i) {
        var id = $(this).attr('data-ques');
        var answer = $(".chosen-select").chosen().val();

        if (answer) {
            user_ques_text[i] = {
                id: id,
                answer: answer.toString()
            };
        }
    });

    waitFunc('enable');
    var data = {  
        'ques_rate': ques_rate,
        'ques': ques,
        'ques_text': user_ques_text,
    };  
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { updateLinkedContact: data },
        success: function(data) { 
            waitFunc(''); 
            alertFunc('success', 'Self rating is complete and your account information is emailed to you!'); 
            window.location ='signup.php';  
        } 
    });
})
 
  
 

//keep this always at the bottom
$(document).on('click', 'button', function() {
    
	if( $(this).attr('id') == 'sign_in_button' ||  $(this).attr('id') == 'form_sign_in_button' ||
      $(this).hasClass('close')   || $(this).attr('id') == 'lsignup' || 
	  $(this).attr('id') == 'form_search_business' || $(this).hasClass('regUser')  || 
	  $(this).attr('id') == 'nextBtn2'  ||  $(this).attr('id') == 'nextBtn3'  || 
	  $(this).attr('id') == 'nextBtn4'  || $(this).attr('id') == 'nextBtn5'  || 
	  $(this).attr('id') == 'nextBtn7'  ||  $(this).attr('id') == 'regdet_update'  ||  
	  $(this).attr('id') == 'btn-confirm' 
	  ) 
	{
	   return;
    }
    
    if(   $(this).hasClass('usersignup')  ) 
	{
	   return;
    }
     
	
	$.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { checksession: 1 },
        success: function(data)
        {  
           if(data =='session_out')
           {
               $('body > #confirm-box').remove();

                $('body').append('<div class="modal fade" id="confirm-box" >' +
                    '<div class="modal-dialog modal-sm">' +
                    '<div class="modal-content"><div class="modal-header modal-header-sm">Session Expired</div>' +
                    '<div class="modal-body modal-body-sm"><p>Your active session has expired. Please login again.</p></div>' +
                    '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-default btn-confirm" data-confirm="no">Ok</button>' + 
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>'); 
			   
			   
                $('#confirm-box').modal({ backdrop: 'static', keyboard: false, show: true }); // open lightbox

                $('#confirm-box .btn-confirm').click(function(e) {
                    
                    $('#confirm-box').modal('hide');
                    window.location ='index.php';
                });
           }  
        }  
    });
}) 




$(document).on('click', '.usersignup', function(e) {
    e.stopImmediatePropagation();
    var first_name = $(this).parents('.sec_two').find('input[name=first_name]').val();
    var email2 = $(this).parents('.sec_two').find('input[name=email2]').val();
    var last_name = $(this).parents('.sec_two').find('input[name=last_name]').val();
    var password = $(this).parents('.sec_two').find('input[name=password]').val(); 
	var country = $(this).parents('.sec_two').find('select[name=country]').val();
	var zip = $(this).parents('.sec_two').find('input[name=zip]').val();
	var city = $(this).parents('.sec_two').find('input[name=city]').val();
	
		 

    var check = 0;
    if (!validateEmail(email2)) {
        alertFunc('danger', 'Insert Email in Corrent Format');
        check = 1;
    } else if (first_name == '') {
        alertFunc('danger', 'First Name is Empty!');
        check = 1;
    } else if (last_name == '') {
        alertFunc('danger', 'Last Name is Empty!');
        check = 1;
    } else if (password == '') {
        alertFunc('danger', 'Password Field is Empty!');
        check = 1;
    } else if (country == null) {
		alertFunc('danger', 'Select the Country!');
		check = 1;
	} else if (zip == '') {
		alertFunc('danger', 'ZIP code is Empty!');
		check = 1;
	} else if (zip.length != 5) {
		alertFunc('danger', 'Incorrect ZIP code!');
		check = 1;
	} else if (city == '') {
		alertFunc('danger', 'City is Empty!');
		check = 1;
	} else 
	{
		reg_email = email2;
        reg_first_name = first_name;
        reg_last_name = last_name;
        reg_password = password;  
		reg_country = country;
		reg_zip = zip;
		reg_city = city; 
    }


    if (check == 0) {
        var updProfForm = new FormData(),
            updProf = {
                reg_email: reg_email,
                reg_first_name: reg_first_name,
                reg_last_name: reg_last_name,
                reg_password: reg_password,
                reg_country: reg_country,
				reg_zip: reg_zip,
				reg_city: reg_city 
            }; 
        updProfForm.append('updProf', 'updProf');
        updProfForm.append('reg_email', reg_email);
        updProfForm.append('reg_first_name', reg_first_name);
        updProfForm.append('reg_last_name', reg_last_name);
        updProfForm.append('reg_password', reg_password);
        updProfForm.append('reg_country', reg_country);
        updProfForm.append('reg_zip', reg_zip);
        updProfForm.append('reg_city', reg_city); 
        updProfForm.append('reg_pkg', 'Invite'); 
        waitFunc('enable');
   
       $.ajax({
            url: "includes/ajax.php",
            type: 'post',
            cache: false,
            contentType: false,
            processData: false,
            data: updProfForm,
            success: function(data) {
                var results = jQuery.parseJSON(JSON.stringify(data));
                if (results.MsgType == "Done") {
                    alertFunc('success', results.Msg);
                    window.open('dashboard.php','_self');
                } else {
                    alertFunc('danger', results.Msg);
                }
                waitFunc('disable')
            },
            error: function(textStatus, errorThrown) {
                waitFunc('disable');
                alertFunc('danger', 'Sorry, unable to fetch data. Check your internet connection please.');
            }
    }); 
    }
});  


$(document).on('click', '.messagetomember', function()
{
	showinterestedpartners(1,'','', '');
    //reloadallpartners(1);
})

// pagination
$(document).on('click', '.allpartners li', function() {
    var page = $(this).find('a').attr('data-pg');
    reloadallpartners(page);
}); 

function reloadallpartners(page) 
{
    //partners/get/
    waitFunc('enable'); 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { fetchallpartners: page},
        success: function(data) { 
            waitFunc(''); 
            $('#mypartnerslist').html(data); 
        } 
    });
}
// pagination
$(document).on('click', '.showmailpreviewmodal', function() 
{ event.preventDefault();
    var email = $(this).attr('data-email');
    var name =  $(this).attr('data-name') ;

    $('#eptbreceipent').val(name); 
    $('#eptbreceipentemail').val(email);   
    waitFunc('enable');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { showinvitemaimpreview: 1, email: email , name:   name },
        success: function(data) { 
            waitFunc(''); 

            if(!CKEDITOR.instances.mailpreview)
            { 
                CKEDITOR.replace( 'mailpreview' );   
            }
            else 
            {
                CKEDITOR.instances.mailpreview.destroy();
                CKEDITOR.replace( 'mailpreview' );
            }  
            if(data == 0)
            {
                alertFunc('danger', 'Email preview failed. Please retry again!'); 
            }
            else
            {
                //$('.mailpreview').html(data);  
                CKEDITOR.instances['mailpreview'].setData(data); 
                $('#previewinviteemail').modal('show');
                $('#btnsendinvitemail').attr('data-name', name)    ;
                $('#btnsendinvitemail').attr('data-email', email)    ;   
            } 
        } 
    }); 
});


$(document).on('click', '#btnsendinvitemail', function()
{
    var email = $(this).attr('data-email');
    var name =  $(this).attr('data-name') ; 
    waitFunc('enable'); 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { sendinvitemail: 1, email: email , name:   name },
        success: function(data) { 
            waitFunc('');  
            if(data == 0)
            {
                alertFunc('danger', 'Email sending failed. Please retry again!'); 
            }
            else 
            {
                alertFunc('success', 'Email send successfully!');
                $('.mailpreview').html('');
                $('#previewinviteemail').modal('hide');       
            }
        }
    });
});



$(document).on('click', '.editinvitemailtemplate', function() 
{  
    event.preventDefault();  
    waitFunc('enable'); 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { editinvitemailpreview: 1  },
        success: function(data) { 
            $('#previewinvitemail').val(data);
            waitFunc(''); 
            if(!CKEDITOR.instances.previewinvitemail)
            { 
                CKEDITOR.replace( 'previewinvitemail' );   
            }
            else 
            {
                CKEDITOR.instances.previewinvitemail.destroy();
                CKEDITOR.replace( 'previewinvitemail' );
            }
             
            if(data == 0)
            {
                alertFunc('danger', 'Email preview failed. Please retry again!'); 
            }
            else
            {   
                $('#editinvitemailtemplate').modal('show');   
           } 
        } 
    }); 
});
 
 


$(document).on('click', '#btnsaveinvitemail', function() 
{ 
    var email = CKEDITOR.instances['previewinvitemail'].getData(); 
    waitFunc('enable');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { saveinvitemail: 1, emailcontent:  email },
        success: function(data) { 
            
            waitFunc(''); 
            
        } 
    }); 
});


$(document).on('click', '.btnmakeprofilepublic', function() 
{ 
    
    waitFunc('enable');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { makeprofilepublic: 1  },
        success: function(data) { 
            
            waitFunc(''); 
window.open('dashboard.php','_self');  			
        } 
    }); 
});

 

$(document).on('click', '.btncontactsignup', function()
{
	var id = $(this).attr('data-id');
    var email = $(this).attr('data-email');  
    $('.sendemailforunifinishsignup').attr('data-id', id);
    $('.sendemailforunifinishsignup').attr('data-email', email);
    $('#emailunifinishsignup').modal('show'); 
}) 

$(document).on('click', '.sendemailforunifinishsignup', function()
{
    var id = $(this).attr('data-id');
    var email = $(this).attr('data-email'); 
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'signups/incomplete/reinvite/',
        data: { fileno: '1', receipent:  email },
        success: function(data) {
            data = $.parseJSON(data); 
           
            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {  
                $("#unfinishedsignup").html( html );
                alertFunc('success',   data.errmsg);

                $('#emailunifinishsignup').modal('hide'); 

            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    });
 

});

$(document).on('click', '.sendemailforunifinishsignup', function()
{
    var id = $(this).attr('data-id');
    var email = $(this).attr('data-email');
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'signups/incomplete/reinvite/',
        data: { fileno: '1', receipent:  email },
        success: function(data) {
            data = $.parseJSON(data); 
           
            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {  
                $("#unfinishedsignup").html( html );
                alertFunc('success',   data.errmsg); 

                $('#emailunifinishsignup').modal('hide'); 

            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
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
            if (data.error != 0 ) 
            {
                alertFunc('info',  'Something went wrong, please try again')
            }
            else 
            {
				$.ajax({
                    type: 'post',
                    url: aurl + 'member/completeprofile/',
                    data: {   userid : id  },
                    success: function(data) {
                        data = $.parseJSON(data);  
                       
                        waitFunc(''); 
                        if (data.error != 0 ) {
                            alertFunc('info',  'Something went wrong, please try again')
                        }
                        else 
                        {
							item = data.results[0]; 
                            user_picture = !(item.image ) ?  "images/no-photo.png" :  "images/"  +  item.image;  
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
                            
                            if( status == 0)
                            {
                                $("#memberprofilepreview").html(html );
                                $("#senddirectmailrequest").modal('show');
                            } 
                            else if( status == 1)
                            {
                                $("#memberprofilepreview2").html(html );
                               $("#composedirectmodal").modal('show');
							    
                            }  
                        }
                    },
                    error: function( )
                    {
                        waitFunc(''); 
                        alertFunc('info',  'Something went wrong, please try again')
                    }
                }); 
				 
            }
        },
        error: function( )
        {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    });
	 
	
}); 

$(document).on("click", '#btnrequestdirectmail', function(){
    var id = $(this).attr('data-id');
    
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'directmail/request/',
        data: {  user_id: mid, partnerid: id , uname: musername, usermail: mremail },
        success: function(data) {
            data = $.parseJSON(data);  
           
            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {  
                $("#senddirectmailrequest").modal('hide');
                alertFunc('success', "Direct email communication request sent!");  
            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
})
 
$(document).on('click', '#btnsenddirectemail', function()
{
    var id = $(this).attr('data-id');
    var mailbody = CKEDITOR.instances['previewdirectmail'].getData();;  
    var subject = $("#membermailsubject").val();

   
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'directmail/send/',
        data: { id:  id, subject:subject, mailbody: mailbody, user_id: mid, username: musername, senderemail: mremail },
        success: function(data) {
            data = $.parseJSON(data);   
            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {
				$("#membermailsubject").val('');
				CKEDITOR.instances.previewdirectmail.setData('');
				alertFunc('success',   data.errmsg);  
            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
});



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
           alertFunc('info', "Request from fellow member granted!" );
           
		   
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
 
$(document).on('click', '.btnviewdmrequests', function()
{ 
    showdirectmailrequests(1, 1, '1');
}); 

$(document).on('click', '.pagination.dmrequestpager li a', function()
{ 
    var page = $(this).attr('data-pg'); 
    var io = $(this).attr('data-dir'); 
    var st = $(this).attr('data-st');  
    showdirectmailrequests(page, io, st ); 
}); 
 
function showdirectmailrequests(goto, direction, status)
{
	 
    waitFunc('enable'); 
	
	surl = aurl + 'directmail/getrequests/';
    
	
	$.ajax({
        type: 'post',
        url: surl,
        data: { userid: mid , page: goto , dir: direction, rstatus: status},
        success: function(data) {
            data = $.parseJSON(data);   
             
			
            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {
				html = "<div class='rb-head'><h2 class='rb-heading'>Recent Connections</h2></div>"; 
				var allemails = [ ];

				$.each(data.results, function(index, item)
				{
					pos = allemails.indexOf( item.user_email );
				 
					if( pos == -1 )
					{
						user_picture =   !(item.image) ?  "images/no-photo.png" :  "images/"  +  item.image;  
						html += '<div class="search-box" id=\'sboxid' + item.id  +  '\'><div class="row"><div class="col-xs-4 col-md-2">' ;
						html += "<img src='"  + user_picture  +  "' alt='"  +  item.username   + "' onerror='imgError(this);' class='img-rounded'  width='80'> " + '</div>' ;
						html += '<div class="col-xs-8 col-md-7"><strong>' + item.username  +'</strong>'  +'<br/>' ;

						if(item.city != '' && typeof item.city !== 'undefined'  &&  item.city !==  null )	
							html +=	item.city + " " + item.zip  + '<br/>' ; 	
						
						if(item.country != '' && typeof item.country !== 'undefined'  &&  item.country !==  null )
							html +=	 item.country  +'<br/>'  ;
						
						html +=	   '</div>' ;
						html += '<div class="col-xs-12 col-md-3">'   ;
						
						if(item.status == 0)
						{
							if(item.firstpartner == mid )
							{
								html  += "<span data-st='1' class='badge-blue' >Your connection <br/>request is <br/>pending approval.</span>";
							}
							else 
							{
								html  += "<button data-st='1' data-id='" + item.id  +  "' class='btn-primary btn btnchangedirectmailstatus'>Accept</button>"; 
							}
						}
						else 
						{
							html  += "<button data-id='" + item.id  +  "' class='btn btn-primary btncomposedirectmail'><i class='fa fa-envelope'></i> Message</button>";
							if(item.secondpartner == mid )
							{
								html  += "<br/><br/> <button data-st='0' data-id='" + item.id  +  "' class='btn-warning btn   btnchangedirectmailstatus'><i class='fa fa-close'></i> Reject</button>"; 
							}
						}
							  
						html +=  '</div></div></div>' ;
					
					}
					 
					 allemails.push( item.user_email); 
					 
				});      
				  
				html += "</div>";
				
				 
                var pages = data.pages;

                if(pages > 1)
                {
                    var prev =  goto == 1 ? 1 :  parseInt(goto) -1;
                    var next =  goto ==  pages ?  pages :  parseInt(goto) + 1; 
                    html  += "<div class='col-md-12'><hr/><ul class='pagination dmrequestpager'><li><a data-st='" + status + "'  data-dir='" + direction + "'  data-func='prev' data-pg='" + prev + "'>«</a></li>";
                    for( i=1;  i <= pages;  i++)
                    {
                        active =  i ==  goto ? 'active' : '';
                        html += "<li class='" + active + "'><a  data-st='" + status + "' data-dir='" + direction + "' data-pg='" + i + "'>" + i + "</a></li>";
                    } 
                    html += "<li><a data-st='" + status + "' data-dir='" + direction + "' data-func='next' data-pg='" + next +  "'>»</a></li></ul></div>";
                }
                if(direction == 0)
                {
                    $('#interestedmembers').html(html);
                }
                else
                {
                    $('#interestedmembers').html(html); 
                } 
		   
            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
} 

$(document).on('click', '.btnviewdmrequestspending', function()
{ 
    showdirectmailrequests(1, 0,  0 );
})


$(document).on('click', '.btnviewdmrequestssent', function()
{
    showdirectmailrequests(1, 1,  0 );
}); 
 
 

$(document).on('click', '.pagination.dmrpager li a', function()
{ 
    page = $(this).attr('data-pg'); 
    var name = $('#dmname').val() ;
    var city = $('#dmcity').val() + ''; 
    var vocations = $('#dmvocations').val() + '';
	showinterestedpartners(page, name, city, vocations)
}); 

function showinterestedpartners(goto, name, city, vocations) 
{
	waitFunc('enable');
    $.ajax({
		type: 'post',
		url: aurl + 'member/recentconnections/',
        data: { userid: mid , page: goto, name: name, city: city,  vocations:vocations },
        success: function(data) {
            data = $.parseJSON(data);
			 
			waitFunc('');
            if (data.error != 0 ) 
			{
				alertFunc('danger',  data.errmsg );
            }
            else 
            {
                html = "<div class='rb-head'><h2 class='rb-heading'>Connections Nearby</h2></div>"; 
				
				$.each(data.results, function(index, item)
				{  
					user_picture =   !(item.image) ?  "images/no-photo.png" :  "images/"  +  item.image; 
					 
					html += '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">' ;
					html += "<img src='"  + user_picture  +  "' alt='"  +  item.username   + "' onerror='imgError(this);' class='img-rounded'  width='80'> " + '</div>' ;
					html += '<div class="col-xs-8 col-md-7"><strong>' + item.username  +'</strong>'  +'<br/>' ;

					if(item.city != '' && typeof item.city !== 'undefined'  &&  item.city !==  null )	
						html +=	item.city + " " + item.zip  + '<br/>' ; 	
					
					if(item.country != '' && typeof item.country !== 'undefined'  &&  item.country !==  null )
						html +=	 item.country  +'<br/>'  ;
					
					html +=	   '</div>' ;
					html += '<div class="col-xs-12 col-md-3">' ; 
						
					if(item.isconnected == 1)
					{
						html +="<button data-id='" + item.id  +  "' class='btn btn-primary btn-block btncomposedirectmail'>Message Now</button>";
					}
					else 
					{
						html +="<button data-id='" + item.id  +  "' class='btn btn-primary btn-block btncomposedirectmail'>Invite Now</button>";
					}
					html +=	'</div></div></div>' ; 
					
				});      
				  
				html += "</div>";  
                var pages = data.pages; 
                if(pages > 1)
                {
                    var prev =  goto == 1 ? 1 :  parseInt(goto) -1;
                    var next =  goto ==  pages ?  pages :  parseInt(goto) + 1;
                    
                    html  += "<hr/><div class='col-md-12'><ul class='pagination dmrpager'><li><a data-func='prev' data-pg='" + prev + "'>«</a></li>";
                    
                    for( i=1;  i <= pages;  i++)
                    {
                        active =  i ==  goto ? 'active' : '';
                        html += "<li class='" + active + "'><a data-pg='" + i + "'>" + i + "</a></li>";
                    } 
                    html += "<li><a data-func='next' data-pg='" + next +  "'>»</a></li></ul></div>";
                } 
	
                $('#interestedmembers').html(html); 
            }
        },
        error: function( ) 
        {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
}

$(document).on('click', '.btnsearchdmmembers', function()
{ 
   var name = $('#dmname').val() ;
    var city = $('#dmcity').val() + ''; 
    var vocations = $('#dmvocations').val() + ''; 
    searchratedmembers(name, city, vocations, 1); 
})

 

$(document).on('click', '.btnsendratingrq', function()
{ 
    $("#modaluc").modal('show');
})

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



$(document).on('click', '#form_search_business', function()
{
	$.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { checksession: 1 },
        success: function(data)
        {  
           if(data =='session_out')
           {
               $('body > #confirm-box').remove();
			   $('body').append('<div class="modalbl modal fade" id="confirm-box" >' + 
			   '<div class="modal-dialog  ">' +
			   '<div class="modal-content"><div class="modal-header  "><i class="fa fa-warning yellow"></i> Member Only Feature</div>' +
			   '<div class="modal-body text-center"><p class="txtbg">Search for potential clients or referral partners, rated by other members.<br/> You must be a member to search.</p> ' + 
			   '<button type="button" id="btn-confirm" class="btn btn-primary btn-confirm btn-lg" data-confirm="no">Join here!</button>' + 
			   '</div>' +
			   '</div>' +
			   '</div>' + 
			   '</div>');  
			   
                $('#confirm-box').modal({ backdrop: 'static', keyboard: false, show: true }); // open lightbox

                $('#confirm-box .btn-confirm').click(function(e) {
                    
                    $('#confirm-box').modal('hide');
                    window.location ='index.php';
                });
           } 
else 
{
	var city = $('#tbsearchbycity').val();
	var vocations = $('#tbsearchbyvoc').val();
	
	var form = $('<form action="//' + window.location.hostname + '/business-search.php" method="post">' +
        '<input type="hidden" name="tbsearchbycity" value="' + city + '" />' +
		'<input type="hidden" name="tbsearchbyvoc" value="' + vocations + '" />' +
        '</form>');
    $('body').append(form);
    form.submit();
	
}	
        }  
    });
	 
}); 
$(document).on('click', '.notifclose', function()
{
	$('.carousel.infoalertzone').hide();
	
});


$(document).on('click', '.businesslog', function()
{ 
    showbusinesslog(1 );
}); 
 
$(document).on('click', '.pagination.bslog li a', function()
{ 
    var page = $(this).attr('data-pg');  
    showbusinesslog(page ); 
}); 

function showbusinesslog(goto  )
{ 
    waitFunc('enable'); 
    surl = aurl + 'business/search/logs/';
      
    $.ajax({
        type: 'post',
        url: surl,
        data: {  goto: goto  },
        success: function(data) {
            data = $.parseJSON(data);   
           
            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {    
                html   = "<table class='table table-alternate'>";
				html  += "<tr><td><strong>Member</strong></td><td><strong>Business Searched</strong></td><td><strong>City</strong></td><td><strong>Search Logging Date</strong></td></tr>" ; 
                $.each(data.results, function(index, item)
				{
                    user_picture =   "images/"  +  item.image;  
					html  += "<tr><td>" + 
					"<img src='"  + user_picture  +  "' alt='"  +  item.username   + "' class='img-rounded' height='40' width='40'> "; 
					html  +=   item.username  + "</td>";
					html  += "<td>" + item.vocation  + "</td>"; 
					html  += "<td>" + item.city  + "</td>"; 
					html  += "<td>" + item.created_at  + "</td>"; 
					html  += "</tr>";  
                }) 
                var pages = data.pages;

                if(pages > 1)
                {
                    var prev =  goto == 1 ? 1 :  parseInt(goto) -1;
                    var next =  goto ==  pages ?  pages :  parseInt(goto) + 1; 
                    html  += "<tr><td colspan='4'><ul class='pagination bslog'><li><a   data-func='prev' data-pg='" + prev + "'>«</a></li>";
                    for( i=1;  i <= pages;  i++)
                    {
                        active =  i ==  goto ? 'active' : '';
                        html += "<li class='" + active + "'><a  data-pg='" + i + "'>" + i + "</a></li>";
                    } 
                    html += "<li><a data-func='next' data-pg='" + next +  "'>»</a></li></ul></td></tr>";
                }
				html  += "</table>" ; 
                $('#businesssearchlog').html(html); 
            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
} 
$(document).on('click', '.btnshownoteedit', function(){
	$('#mystatement').modal('show'); 
});

$(document).on('click', '#btnsavenote', function()
{
	var notes = CKEDITOR.instances['instantnote'].getData();  
    waitFunc('enable');
    $.ajax({
       type: 'post',
       url: aurl + 'notes/add/',
       data: { id: 0 , userid: mid , notes: notes },
       success: function(data) 
	   {
		   data = $.parseJSON(data);  
		   $('#mystatement').modal('hide');
           waitFunc('');  
		   window.open('dashboard.php','_self');  
       } 
    });
}); 

$(document).on('click', '.btnshownoteedit', function(e) { 

	var note = $('#fp_note').html(); 
	var editor = CKEDITOR.instances['instantnote'];
    if (editor) { editor.destroy(true); } 
	CKEDITOR.replace( 'instantnote' ); 
	CKEDITOR.instances['instantnote'].setData(note);

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
	 
	$.ajax({
		type: 'post',
		url: aurl + 'referralsuggestion/remove/',
		data: {  introids: suggestids +"0"  },
		success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc(''); 
			
		   reloadknows(10,goto , userid ); 
		} 
	});
			
	 }); 
});

 
$(document).on('click',  '.managedistances', function()
{ 
	$.ajax({
		type: 'post',
		url: aurl + 'managedistance/', 
		success: function(data) 
		{
			data = $.parseJSON(data); 
			
			 html =`<table class="table table-bordered table-alternate">
                <tr>
                <th>Source Zip</th>
                <th>Target Zip</th>
                <th>Distance in Miles</th>
                <th>Action</th> 
                </tr>`;
                
                $.each( data.results , function(idx, obj){
                    html += "<tr>" +
                        "<td>"  + obj.sourcezip + "</td>" +
                        "<td>"  + obj.targetzip + "</td>" +
                        "<td><input type='text' id='zipdistance"  + obj.id + "'  class='form-control' style='width:60px' value='0' Placeholder='Miles'/> </td>" +
                        "<td><input type='button' data-id='"  + obj.id + "' class='btn btn-primary btnsavedistance'  value='Add Distance' /></td> </tr>"; 
                }) 
                  
                html +='</table>'; 
				
			$('#distancegrid').html( html)
		} 
	});
});			 
 
$(document).on('click',  '.btnsavedistance', function()
{
	var id = $(this).attr('data-id');
	var distance = $('#zipdistance'+id).val();
	
	$.ajax({
		type: 'post',
		url: aurl + 'zipdistance/save/',
		data: {  distance:distance, refsugid:  id },
		success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc(''); 
            alertFunc('info',   data.errmsg ) 
		} 
	}); 
	 
});





$(document).on('click',  '.importmemberknows', function()
{
	var memberid = $(this).attr('data-user');
	
	$('#hidliuserid').val(memberid);	
	
	$('#liimportmodal').modal('show');
});

$(document).on('click', '.linkedinimportba', function()
{
	var memberid = $('#hidliuserid').val() ;
	
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { linkedinimport: 1, memid : memberid },
        success: function(data) {
          
            if (data == "nofile") {
                alertFunc('danger', 'No LinkedIn archive file is present!');
                waitFunc('');
            } else {
                alertFunc('success', 'Linkedin contacts imported successfully!');
                waitFunc('');
            }
        }
    });
	  
}) 



$(document).on('click', '.linkedinimportbalist', function() {
    getLinkedInImportedbaContacts (1, '');
}) 

function getLinkedInImportedbaContacts(page, searchkey) 
{
	var memberid = $('#hidliuserid').val() ;
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  aurl + 'knows/showallimported/',
        data: { goto: page, key:searchkey, userid:memberid},
        success: function(data) {
            waitFunc('');

            data = $.parseJSON(data); 
 
            if(data.error == 10)
            {
                $('#linkedinlist').html('');
                 alertFunc('info', data.errmsg);     
            }
            else  if(data.error == 1 )
            {
                $('#linkedinlist').html('');
                alertFunc('danger', 'Something went wrong, please try again')
            }
            else 
            { 
                    html  = "<table class='table table-colored table-alternate table-bordered'>"  +
					"<tr><th>Name</th><th>Email</th><th>Profession</th><th>Company</th> <th>Action</th></tr> ";
                    $.each(data.result, function(idx, item){ 
                        html  += "<tr><td>"  + item.c   + '</td><td>'    + item.g   +  '</td><td>'   + item.d   +  
                        '</td><td>'   + item.n   +  '</td>';
                            
                        html  += '<td>' +
                        '<button data-email="'  + item.g  +  '"  data-receipent="'  + item.c   + 
                        '" data-mailsent="0" data-id="'   + item.a   +  '" class="btn btn-primary btn-small btnshownmailtemplates"><i class="fa fa-envelope"></i></button></td></tr>' ;   
                        
                    });
        
                    lastpage =  data.pages ;
                    prev =  (page == 1) ? 1 :  page-1;
                    next = (page == data.pages) ? data.pages : page +1; 
                    
                    html  += "<tr><td colspan='5'><ul class='pagination pagilinkedin'><li><a data-key='" + searchkey  + 
                    "' data-func='prev' data-pg='" + prev +"'>«</a></li>";
                    if( page  > 10) 	 
                    html  += "<li><a data-key='" + searchkey  +"'  data-func='next' title='Show last few pages' data-pg='1'> ... </a></li>";
                    
                    if(page < 10)
                    { 
                        for(var j= 1 ;  j  <=  10  ;  j++)
                        {
                            if( j > data.pages)
                            {
                                break;
                            }
                            active = (j == page ) ? 'active' : '';
                            html  += "<li class='$active'><a data-key='" + searchkey  +"'  data-pg='" + j  +"'>" + j  +"</a></li>";
                        }
                    }
                    else
                    {
						for(var i = (page - 5) ;  i<= page + 4;  i++)
						{
							if( i > data.pages)
							{
								break;
							}
							active = ( i == page) ? 'active' : '';
							html  += "<li class='$active'><a data-key='" + searchkey  +"'  data-pg='" + i  +"'>" + i  +"</a></li>";
						}
                    }
					
					if( page < (lastpage - 10 ) )
                        html  += "<li><a data-key='" + searchkey  +"'  data-func='next' title='Show last few pages'"+ 
                        "data-pg='" + lastpage  +"'> ... </a></li>";
                    
                    
                    html  +=  "<li><a data-key='" + searchkey  +"'  data-func='next' title='Next Page' data-pg='" + next  +"'>»</a></li></ul></td></tr>"; 
                    html  +=  "</table>"; 
                        
                        
                    html += `<div class="modal mine-modal fade" id="selectlinkedinmail" tabindex="-1" role="dialog">
						<div class="modal-dialog">
						<div class="modal-content"> 
							<div class="modal-header ">
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
								<h4 class="modal-title">Select Mail Template</h4>
							</div>
							<div class="modal-body text-left" style="height: 450px; overflow-y: scroll">
								<div id="linkedinmails"></div>
							</div>
							<div class="modal-footer"> 
								<div class="col-xs-12"> 
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
							</div> 
						</div>
					</div>
				</div>` ;  
				$('#linkedinlist').html(html);
				
			}
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
} 

$(document).on('click', '.composememberemail', function()
{
	var ui = $(this).attr('data-i');
	var cn = $(this).attr('data-cn');
	var ce = $(this).attr('data-ce'); 
	
	if(typeof cn !== 'undefined')
		$('#compose_name').val(cn + " <"  + ce  + ">");
	else 
		$('#compose_name').val( ce  );
	$('#compose_cmail').val(ce); 
}) 

$(document).on('click', '#btnsendemailtomember', function()
{
	var em = $('#compose_cmail').val();
	var cc = $('#compose_cc').val();
	var subject = $('#compose_subject').val();
	var body =  CKEDITOR.instances['emailbody'].getData();
	
	
    var udata ; 
    udata = new FormData();
    udata.append( 'to',  em );
	udata.append( 'subject',  subject );
	udata.append( 'body',  body );
	if(typeof cc !== 'undefined')
	{
		udata.append('cc', cc);
	}
	
	
	waitFunc('enable');
	$.ajax({
        type: 'post',
        url: aurl +  'mail/sent/', 
		cache: false,
		contentType: false,
		processData: false,
		data: udata,
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc('');
			alertFunc('success', data.errmsg); 
        }
    }); 
})
 

$(document).on('click', '.switchuser', function()
{
	waitFunc('enable');
	var id= $(this).attr('data-user');
	$.ajax({
		type: 'post',
		url: aurl + 'user/switch/', 
		data: {  id: id},
		success: function(data) 
		{
			data = $.parseJSON(data);   
            if (data.id ==  0 )
			{
				alertFunc('danger', 'Email or password not found');
            }
			else if (data.id >  0 )
            {
				 
				if (data.status  == 0  ) 
                {
                    alertFunc('info', 'Your account is not deactivated. Please contact Admin My City.');
                }
                else
                {
                    var exdate=new Date();
                    exdate.setDate(exdate.getTime()+60*60*1000*24); 
                    document.cookie = "_mcu= " + JSON.stringify( data ) + "; " + exdate.toUTCString() ; 
				    document.cookie = "_rmtoken=" + JSON.stringify( data.retoken ) + "; expires="   + exdate.toUTCString() ; 
					
					var now = new Date();
					var time = now.getTime();
					var expireTime = time + 1000*36000;
					now.setTime(expireTime); 
					document.cookie = "_rmtoken=" + JSON.stringify( data.retoken ) + ";expires=" +now.toGMTString() ;
					window.open('dashboard.php','_self');  
                }
            } 	
		} 
	});  
})


$(document).on('click', '#btnactswitch', function()
{
	waitFunc('enable');
	var id= $(this).attr('data-user');
	$.ajax({
		type: 'post',
		url: aurl + 'user/switch/', 
		data: {  id: id},
		success: function(data) 
		{
			data = $.parseJSON(data);   
            if (data.id ==  0 )
			{
				alertFunc('danger', 'Email or password not found');
            }
			else if (data.id >  0 )
            {
				if (data.status  == 0  ) 
                {
                    alertFunc('info', 'Your account is not deactivated. Please contact Admin My City');
                }
                else
                {
                    var exdate=new Date();
                    exdate.setDate(exdate.getTime()+60*60*1000*24); 
                    document.cookie = "_mcu= " + JSON.stringify( data ) + "; " + exdate.toUTCString() ; 
				    document.cookie = "_rmtoken=" + JSON.stringify( data.retoken ) + "; expires="   + exdate.toUTCString() ; 
					
					var now = new Date();
					var time = now.getTime();
					var expireTime = time + 1000*36000;
					now.setTime(expireTime); 
					document.cookie = "_rmtoken=" + JSON.stringify( data.retoken ) + ";expires=" +now.toGMTString() ;
					window.open('dashboard.php','_self');  
                }
            } 	
		} 
	});  
})

$(document).on('click', '.btnsearchdmmembers2', function()
{ 
    var name = $('#dmname2').val() ;
    var city = $('#dmcity2').val() + ''; 
    var vocations = $('#dmvocations2').val() + '';
	 
    searchratedmembers(name, city, vocations, 1);
	 
})
 

$(document).on('click', '.btn-gsearch', function()
{ 
     var keyword = $('#gskey').val(); 
	var gscityorzip = $('#gscityorzip').val(); 
	 
	
	if( typeof gscityorzip === "undefined" || gscityorzip == '')
	{
		gscityorzip ='';
	}  
	
	$.ajax({
        type: 'post',
        url: aurl + 'keyword/log/',
        data: { keyword: keyword, cityzip:gscityorzip,  id: mid }
    }); 
 
	searchratedmembers(keyword , gscityorzip );
})

 $(document).on('click', '.pagination.knowgslist li a', function()
{
	var page = $(this).attr('data-page');
	var keyword = $(".pagination.knowgslist").attr('data-keyword');
	var gscityorzip =$(".pagination.knowgslist").attr('data-city');
	var vocation  =$(".pagination.knowgslist").attr('data-vocation'); 
	searchratedmembers(keyword , gscityorzip,  vocation , page, 2 );  
})
 
$(document).on('click', '.pagination.membergslist li a', function()
{
	var page = $(this).attr('data-page');
	var keyword = $(".pagination.membergslist").attr('data-keyword');
	var gscityorzip =$(".pagination.membergslist").attr('data-city');
	var vocation  =$(".pagination.membergslist").attr('data-vocation'); 
	 
	searchratedmembers(keyword , gscityorzip,  vocation , page, 1 );  
})

$(document).on('click', '.btn_gs_gotopage', function()
{
	var page = $('.gs_gotopage').val();
	var keyword = $(".pagination.knowgslist").attr('data-keyword');
	var gscityorzip =$(".pagination.knowgslist").attr('data-city');
	var vocation  =$(".pagination.knowgslist").attr('data-vocation'); 
	searchratedmembers(keyword , gscityorzip,  vocation , page, 1 );  
}) 

function searchratedmembers(keyword, city = '', vocation= '' , page =1, utype=1  )
{  
	if(typeof city === 'undefined' || city == '' || city == 'null' )
	{
		city ='';
	}
	
	if(typeof vocation === 'undefined' || vocation == '' || vocation == 'null' )
	{
		vocation ='';
	}
	var iszip = ( $.isNumeric (city) == true ? 1 : 0 );
	 
	  
	waitFunc('enable');
	var rowcount =0;
	$.ajax({
		type: 'post',
        url: aurl +  'member/searchnearest/', 
		data: {  name : keyword, userid:mid, city: city, vocation: vocation , page: page, iszip:iszip, utype:utype},
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc('');  
			 
			if(data.error == 0)
			{
				html = "";  
				if(utype == 1  )
				{
					//refresh grid
				$.each(data.result , function (outindex, item)
				{
					user_picture =   !(item.f ) ?  "images/no-photo.png" :  "images/"  +  item.f;
					html += '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">' ;
					html += "<img src='"  + user_picture  +  "' alt='"  +  item.b   + "' onerror='imgError(this);' class='img-rounded'  width='80'> " + '</div>' ;
					html += '<div class="col-xs-8 col-md-7"><strong>' + item.b  +'</strong>'  +   
					'<input type="hidden" value="' + item.b + '" id="bcname"><br/>' + item.w  +'<br/>' ;
					
					if(item.p != '' && typeof item.p !== 'undefined'  &&  item.p !==  null )	
						html +=	item.p  +'<br/>' ;
					
					if(item.q != '' && typeof item.q !== 'undefined'  &&  item.q !==  null )
						html +=	 item.q + " "  + item.r +'<br/>'  ;
					
					html +=	 item.s ; 
					
					mrate =  Math.ceil( item.rating / 5 ) ;
					var star ='';
					for(var sc =0; sc < 5; sc++)
					{
						if(sc < mrate)
							star  += "<i class='fa fa-star orange'></i>";
						else 
							star  += "<i class='fa fa-star lgray'></i>";
					}
					
					if(mrate ==  5)
						html += "<br/><span class='badge badge-green'><i class='fa fa-sun-o'></i> Top Rated Member</span>"    ;
					else if(mrate > 0)
						html += "<br/>" +	star    ;
					else 
						html += "<br/><span class='badge badge-blue'>Non Rated Member</span>"  ;
					
					html += '</div> <div class="col-xs-4 col-md-3">';
					 
					if(item.isconnected == 1 )
					{
						html += '<button type="button" data-id="' + item.ui + '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>'; 
					}
					else
					{
						html += '<button type="button" data-i="' + item.ui + '" class="btn btn-primary btn-solid btn-block btnconnect" ><i class="fa fa-envelope"></i> Connect</button>' ; 
					}
					html += '<button type="button" data-id="' + item.ui + '" class="btn btn-primary btn-block btnratemembers" ><i class="fa fa-star"></i> Rate Now</button>';
					html +=	'</div></div></div>' ;

					rowcount++;	
				}); 
				
				var pages  = data.pages; 
				if(pages > 1)
				{					
					prev =  (page == 1) ? 1 :  parseInt(page) -1;
					next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1;  
					 
					html += " <ul class='pagination membergslist' data-keyword='" +  keyword +"'  data-city='" +  city +"'  data-vocation='" +  vocation +"'><li>" +
						"<a  data-func='prev' data-page='" + prev + "'>«</a></li>";
						for( i=1;  i<= data.pages;  i++){
							
							  active =  i == page ? 'active' : '';
							  html +=  "<li class='" + active + "'><a  data-page='"+i   +"'>"+ i 
							+"</a></li>";
						}
					html += "<li><a  data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";
				}
				html += "</div>";
				
				
				if(rowcount > 0) 
					$('.memberlist').html(html); 
				else 
					$('.memberlist').html( '<div style="padding: 10px; text-align:center; font-size: 14px; font-weight: bold">' +data.msg1 + '</div>' ); 
				 
				}
				 
				var knowhtml ='';
				rowcount =0;
				$.each(data.knows , function (outindex, item)
				{
					user_picture =   !(item.f ) ?  "images/no-photo.png" :  "images/"  +  item.f;
					knowhtml += '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">' ;
					knowhtml += "<img src='"  + user_picture  +  "' alt='"  +  item.b   + "' onerror='imgError(this);' class='img-rounded'  width='80'> " + '</div>' ;
					knowhtml += '<div class="col-xs-8 col-md-6"><strong>' + item.b  +'</strong>'  +   
					'<input type="hidden" value="' + item.b + '" id="bcname"><br/>' + item.w  +'<br/>' ;
					
					 if(item.q != '' && typeof item.q !== 'undefined'  &&  item.q !==  null )
						knowhtml +=	 item.q + " "  + item.r +'<br/>'  ;
					 
					knowrate =  Math.ceil( item.rating / 5 ) ;
					var star ='';
					for(var sc =0; sc < 5; sc++)
					{
						if(sc < knowrate)
							star  += "<i class='fa fa-star orange'></i>";
						else 
							star  += "<i class='fa fa-star lgray'></i>";
					}
					
					if(knowrate ==  5)
					{
						knowhtml += "<br/><span class='badge badge-green'><i class='fa fa-sun-o'></i> Top Rated Know</span>"    ;
						knowhtml += " <span class='badge badge-dark'>Rated by: " + item.un +"</span>";
					}
					else if(knowrate > 0)
					{
						knowhtml += "<br/>" +	star    ;
						knowhtml += " <span class='badge badge-dark'>Rated by: " + item.un +"</span>";
					}
					else 
						knowhtml += "<br/><span class='badge badge-blue'>Non Rated Know</span>"  ;
					
					
					knowhtml += '</div> <div class="col-xs-4 col-md-4">';
					
					knowhtml += '<button type="button" data-id="' + item.knid + '" data-name="' + item.b + 
					'" data-email="' + item.a + '" data-voc="' + item.w + '" class="btn btn-primary btn-block btncomposeknowinvitemail" ><i class="fa fa-envelope"></i> Click to Connect</button>'; 
  
					knowhtml +=	'</div></div></div>' ;
					rowcount++;					
				});
				
				pages  = data.know_pages; 
				if(pages > 1)
				{
					prev =  (page == 1) ? 1 :  parseInt(page) -1;
					next = (  page == data.know_pages ) ?  data.know_pages : parseInt(page) + 1;
					
					knowhtml += " <ul class='pagination knowgslist' data-keyword='" +  keyword +"'  data-city='" +  city +"'  data-vocation='" +  vocation +"'><li>" +
						"<a    data-func='prev' data-page='" + prev + "'>«</a></li>";
						 
						if( page > 50) 
						{
							knowhtml += "<li><a  data-func='previous' title='Show Previous 10 Records' data-page='1' > ... </a></li>";
						
						}
						if( page < 50) 
						{
							for(var j = 1 ; j  <=  50  ; j++)
							 {
								 if(j > pages)
								 {
									 break;
								 }
								 active =  j ==  page ? 'active' : ''; 
								 knowhtml += "<li class='" + active + "'><a  data-page='" + j  + "' >" + j  + "</a></li>";
							 } 
						}
						else 
						{ 
							for( var i = parseInt(page) - 25;  i <=   parseInt(page) + 24  ;  i++ )
							{
								if( i > pages)
								{
									 break;
								}
								active  =  i ==  page ? 'active' : '';
								knowhtml +=   "<li class='" + active + "'><a data-page='" + i  + "'  >" + i  + "</a></li>";
							 }
						}
						
						if( parseInt(page)  < ( pages - 50 ) )
						{
							knowhtml +=  "<li><a data-func='next' title='Show last few pages' data-page='" + pages + "'> ... </a></li>"; 
						}
						
						knowhtml += "<li> <input class= 'form-control gs_gotopage'  type='text' style='width: 120px; height: 32px; margin-top: 2px; margin-right: 5px; float: left; display: inline-block;' placeholder= 'Go to page ...' > </li>";
						knowhtml += "<li> <input class='btn btn_gs_gotopage' type='button'  value='Go' style='width: 50px; float: left; height: 32px; margin-top: 2px; display: inline-block;  background-color: #2e353d; color: #fff;'   > </li>";
						knowhtml += "<li><a  data-func='next' title='Next Page' data-page='" + next +  "'>»</a></li> "; 
						knowhtml += "<li><a  data-func='last' title='Last Page' data-page='" + pages +  "'>Last Page</a></li> ";
						
					  
					knowhtml += " </ul> ";
				} 
				knowhtml += "</div>"; 
				
				if(rowcount > 0) 
					$('.knowlist').html(knowhtml);
				else 
					$('.knowlist').html( '<div style="padding: 10px; text-align:center; font-size: 14px; font-weight: bold">' +data.msg2 + '</div>' ); 
				 
				$('#interestedmembers').html(html); 
			}   
			else
			{
				$('.memberlist').html( '<div style="padding: 10px; text-align:center; font-size: 14px; font-weight: bold">' + data.errmsg + '</div>');
				$('#interestedmembers').html( '<div style="padding: 10px; text-align:center; font-size: 14px; font-weight: bold">' + data.errmsg + '</div>' ); 
			}
		}
    });   
} 

 $(document).on('click', '.btncomposeknowinvitemail', function()
{
	var id = $(this).attr('data-id'); 
	var email  = $(this).attr('data-email');
	 
	waitFunc('enable');
	$.ajax({
        type: 'post',
        url: aurl + 'member/claimprofileemail/send/',
        data: { to: email, id: id, mid: mid},
        success: function(data) 
		{
			data = $.parseJSON(data);  
			waitFunc('');
			alertFunc('info',  data.errmsg)			
        } 
	});
	
}) 


$(document).on('click', '.profileclaimmessages', function()
{ 
	var gotopage = $(this).attr('data-pg');
	if(typeof gotopage === 'undefined' || gotopage == '')
	{
		gotopage =1;
	}
	var tab = $(this).attr('data-tab');
	if(typeof tab === 'undefined' || tab == '')
	{
		tab = 0;
	}
	 
	$.ajax({
        type: 'post',
        url: aurl + 'member/claimprofile/getemails/', 
		 data: { page: gotopage, type:tab },
        success: function(data) 
		{
			data = $.parseJSON(data);
			 
			
			html = "<table class='table table-responsive'>";
			html += "<tr ><th>Name</th><th>Profession</th><th>Date</th><th>Searched By</th></tr>"  ; 
			$.each(data.result_1, function(index, item) 
			{
				html += "<tr id='row" + index + "'>" + 
				"<td>" + item.client_name + "</td>" +
				"<td>" + item.client_profession + "</td>" + 
"<td>" + item.invitedate + "</td>" +  				
				"<td>" + item.membername + "</td>" + 
				"</tr>";	 		
			});
			html += "</table>"; 
			
			var pages = data.pages_1; 
			if(pages > 1)
			{
                    var prev =  gotopage == 1 ? 1 :  parseInt(gotopage) -1;
                    var next =  gotopage ==  pages ?  pages :  parseInt(gotopage) + 1; 
                    html  += "<div class='col-md-12'><hr/><ul class='pagination'><li><a  class='profileclaimmessages' data-tab='1'  data-func='prev' data-pg='" + prev + "'>«</a></li>";
                    for( i=1;  i <= pages;  i++)
                    {
                        active =  i ==  gotopage ? 'active' : '';
                        html += "<li class='" + active + "'><a  class='profileclaimmessages' data-tab='1' data-pg='" + i + "'>" + i + "</a></li>";
                    } 
                    html += "<li><a class='profileclaimmessages' data-tab='1' data-func='next' data-pg='" + next +  "'>»</a></li></ul></div>";
            }
				 
			
			html2 ='';
			html2 = "<table class='table table-responsive'>";
			html2 += "<tr ><th>Name</th><th>Profession</th><th>Date</th><th>Searched By</th></tr>"  ; 
			$.each(data.result_1, function(index, item) 
			{
				html2 += "<tr id='row" + index + "'>" + 
				"<td>" + item.client_name + "</td>" +
				"<td>" + item.client_profession + "</td>" +  
				"<td>" + item.invitedate + "</td>" + 
				"<td>Public Visitor</td>" + 
				"</tr>";	 		
			});
			html2 += "</table>";
			var pages = data.pages_2; 
			if(pages > 1)
			{
                    var prev =  gotopage == 1 ? 1 :  parseInt(gotopage) -1;
                    var next =  gotopage ==  pages ?  pages :  parseInt(gotopage) + 1; 
                    html2  += "<div class='col-md-12'><hr/><ul class='pagination conreqlist'><li><a class='profileclaimmessages' data-tab='2' data-func='prev' data-pg='" + prev + "'>«</a></li>";
                    for( i=1;  i <= pages;  i++)
                    {
                        active =  i ==  gotopage ? 'active' : '';
                        html2 += "<li class='" + active + "'><a class='profileclaimmessages' data-tab='2'  data-pg='" + i + "'>" + i + "</a></li>";
                    } 
                    html2 += "<li><a class='profileclaimmessages' data-tab='2' data-func='next' data-pg='" + next +  "'>»</a></li></ul></div>";
            }
			
			$('#oboxprofileclaims').html(html);
			$('#oboxprofileclaimspublic').html(html2); 
        } 
	});
	
}) 

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

$(document).on('click', '.btnconnect', function(){
	var memberid = $(this).attr('data-i') ;
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'member/request/connection/',
        data: {  user_id: mid, partnerid: memberid, useremail:mremail   },
        success: function(data) 
		{
			data = $.parseJSON(data); 
			waitFunc('');
			alertFunc('danger',  data.errmsg );
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    });  
})


$(document).on('click', '.getconnectionrequest', function()
{
	showconnectionreqs(1, 1, '-1', 1);
})
 

$(document).on('click', '.getconnectioninrequest', function()
{ 
    showconnectionreqs(1, 0, '-1', 1);
})


$(document).on('click', '.pagination.conreqlist li a', function()
{
	var gotopage = $(this).attr('data-pg');
	var direction = $(this).attr('data-dir');
	showconnectionreqs( gotopage ,  direction , '-1', 1);
})



function showconnectionreqs(gotopage, direction, status, rtype)
{ 
 
    waitFunc('enable'); 
	surl = aurl + 'member/connections/getall/'; 
	$.ajax({
		
		type: 'post',
        url: surl,
        data: { userid: mid , page: gotopage , dir: direction, rstatus: status, rtype:rtype},
		success: function(data) 
		{
			
			data = $.parseJSON(data);
			
			waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {
				html = "";
				$.each(data.results, function(index, item)
				{
					user_picture =   !(item.image) ?  "images/no-photo.png" :  "images/"  +  item.image; 
					html += '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">' ;
					html += "<img src='"  + user_picture  +  "' alt='"  +  item.username   + "' onerror='imgError(this);' class='img-rounded'  width='80'> " + '</div>' ;
					html += '<div class="col-xs-8 col-md-8"><strong>' + item.username  +'</strong>'  +'<br/>' ;

					if(item.city != '' && typeof item.city !== 'undefined'  &&  item.city !==  null )	
						html +=	item.city + " " + item.zip  + '<br/>' ; 	
					
					if(item.country != '' && typeof item.country !== 'undefined'  &&  item.country !==  null )
						html +=	 item.country  +'<br/>'   ;
					
					
					if(direction == 0)
					{ 
						if(item.status == 0)
						{
							html  += "<p><strong>Phone:</strong> " + item.user_phone  + "</p>";  
							html +=	   '</div>' ; 
							html += '<div class="col-xs-12 col-md-2">'     ; 
							html  += "<button data-st='1' data-id='" + item.id  +  "' class='btn-primary btn btn-block btnchangedirectmailstatus'><i class='fa fa-check'></i> Accept</button>";
						
							html +=   '</div> ' ; 
						}
						else 
						{
							html  += "<p><strong>Phone:</strong> <i class='fa fa-lock' title='Phone not display as member has not approve connection'></i></p>";
							html +=	   '</div>' ;
							
							html += '<div class="col-xs-12 col-md-2">' ;
							html += '<button type="button" data-id="' + item.id + '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>'; 
							html  += " <button data-st='0' data-id='" + item.id  +  "' class='btn-warning btn btn-block btnchangedirectmailstatus'><i class='fa fa-close'></i> Reject</button>";
							
							html +=  '</div> ' ; 
							 
						}
					}
					else 
					{
						if(item.status == 1)
						{
							html  += "<p><strong>Phone:</strong> " + item.user_phone  + "</p>";  
							html +=	   '</div>' ; 
							html += '<div class="col-xs-12 col-md-2">'     ; 
							html += '<button type="button" data-id="' + item.id + '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>'; 
							html +=   '</div> ' ; 
						}
						else 
						{
							html  += "<p><strong>Phone:</strong> <i class='fa fa-lock' title='Phone not display as member has not approve connection'></i></p>";
							html +=	   '</div>' ;
							
							html += '<div class="col-xs-12 col-md-2">' ;

							 html +=  '</div> ' ; 
						
						}
					}				
						 
					html +=   ' </div></div>' ;
					
				});      
				   
				  
                var pages = data.pages; 
                if(pages > 1)
                {
                    var prev =  gotopage == 1 ? 1 :  parseInt(gotopage) -1;
                    var next =  gotopage ==  pages ?  pages :  parseInt(gotopage) + 1; 
                    html  += "<div class='col-md-12'><hr/><ul class='pagination conreqlist'><li><a data-st='" + status + "'  data-dir='" + direction + "'  data-func='prev' data-pg='" + prev + "'>«</a></li>";
                    for( i=1;  i <= pages;  i++)
                    {
                        active =  i ==  gotopage ? 'active' : '';
                        html += "<li class='" + active + "'><a  data-st='" + status + "' data-dir='" + direction + "' data-pg='" + i + "'>" + i + "</a></li>";
                    } 
                    html += "<li><a data-st='" + status + "' data-dir='" + direction + "' data-func='next' data-pg='" + next +  "'>»</a></li></ul></div>";
                }
              
			  if(html != '')
				$('#conreqlist' + direction).html(html);
               else 
				   $('#conreqlist' + direction).html('<div class="col-md-8 col-md-offset-2"><p class="alert alert-info text-center">You have zero pending connection requests!</p></div>');
				
            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
}  

function imgError(image)
{
	image.src =  "images/no-photo.png" ; 
} 

$(document).on('click', '#startsurvey', function()
{
	$('#surverymodal').modal('show'); 
}) 

 

 
 
$(document).on('click', '.searchmemberbyemail', function()
{
	var memberemail  =  $("#memberemail").val()  ;  
	
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'member/knows/getratedbyemail/',
        data: { memberemail: memberemail,  ranking:'25'   },
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc(''); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th>Name</th><th>Profession</th><th>Email</th><th>Know Rating</th><th></th></tr>"  ; 
			$.each(data.results, function (index, item) 
			{
				html += "<tr id='row" + index + "'>" + 
				"<td>" + item.client_name + "</td>" +
				"<td>" + item.client_profession + "</td>" +
				"<td>" + item.client_email + "</td>" +
				"<td>" + item.rate + "</td>" + 
				"<td><button data-id='" + item.id + "' data-email='" + item.client_email + "' data-name='" + item.client_name + "' data-voc='" + item.client_profession + "' class='btn btn-primary btn-xs btncomposeinvite'>Sent Invitation</button></td>" + 
				"</tr>"; 
            });
			html += "</table>"; 
			
			 
			$('#topratedknows').html(html);
			alertFunc('info',  data.errmsg );
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('danger',  'Something went wrong, please try again')
        }
    });  
})




$(document).on('click', '.btncomposeinvite', function(){
	
	var knowid = $(this).attr('data-id');
	var name  = $(this).attr('data-name');
	var email  = $(this).attr('data-email');
	var voc  = $(this).attr('data-voc');
	
    $('#btnsendclaimprofile').attr('data-e', email);  
    $('#btnsendclaimprofile').attr('data-i', knowid);  


	var html  = '<p><strong>'+ name  + '</strong></p> '; 
	html  += '<p><strong>'+ email + '</strong></p> '; 
	html  += '<p><strong>Vocation</strong><br/>'+ voc+ '</p>';
	
	$.ajax({
        type: 'post',
        url: aurl + 'member/getclaimprofileemail/',
        data: {   name: name, knowid:knowid   },
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc(''); 
			 
			if(data.error == 0 )
			{
				//$('#btnsendinvitemail').val( data.mailbody); 
				//showing profile
				if(!CKEDITOR.instances.knowinviteemail)
				{
					CKEDITOR.replace( 'knowinviteemail' );   
				}
				else 
				{
					CKEDITOR.instances.knowinviteemail.destroy();
					CKEDITOR.replace( 'knowinviteemail' );
				} 
				CKEDITOR.instances['knowinviteemail'].setData( data.mailbody );
				$('#knowprofilesummary').html( html );	
				$('#composeinvitemail').modal('show'); 
			}
			else 
			{
				alertFunc('danger',  data.errmsg );
			}  
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    });  
})  


$(document).on('click', '#btnsendclaimprofile', function()
{
    var email = $(this).attr('data-e');
    var id = $(this).attr('data-i');
	var subject = $('#knowinvitemailsubject').val()  ;
 
	waitFunc('enable');
	$.ajax({
        type: 'post',
        url: aurl + 'member/claimprofileemail/send/',
        data: { to: email, subject: subject, id: id},
        success: function(data) 
		{
			data = $.parseJSON(data); 
			waitFunc('');
			alertFunc('info',  data.errmsg)			
        } 
	}); 
})



$(document).on('click', '.changeProfilePhoto', function () {

    var id = $(this).attr('data-id');  
    var path = $(this).attr('data-path');  
    $("#hidmid").val(id);  

    user_picture = !(path) ? "images/no-photo.png" : "images/" + path; 
    html  = "<img src='" + user_picture + "' alt='Profile Picture' onerror='imgError(this);' class='img-rounded'  width='160'> "  ;
     

    $("#curmemphoto").html(html );  
    $('#changememberpicture').modal('show');

})

$(document).on('click', '#btnupdatememphoto', function () {

    var id = $("#hidmid").val();  
   
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'members/updatephoto/',
        data: { id: id },
        success: function (data) {
            data = $.parseJSON(data);
            waitFunc('');
            alertFunc('info', data.errmsg); 
        }
    });

})


$(document).on('click', '.btnviewknowprofile', function () {

    var id = $(this).attr('data-i');

    $('#knowprofile').html("<div class='text-center'><img   src='../images/processing.gif' alt='Loading ...' /></div>");

    $.ajax({
        type: 'post',
        url: aurl + 'knows/getprofile/',
        data: { id: id },
        success: function (data) {
            data = $.parseJSON(data);


            if (data.error == 0) {

                var html = '<div class="row">';

                var profile = data.profile[0];
                var group = data.group[0];



                if (String(profile['client_lifestyle']) != 'null')
                    lifestyle = profile['client_lifestyle'];
                else
                    lifestyle = 'Not Specified';

                html += '<div class="col-md-12"><div class="profile-summary"><h3>' + profile['client_name'] + '</h3>';
                html += "<p><strong>Profession:</strong> " + profile['client_profession'] +
                    " <strong>Assigned group:</strong> " + group['grp_name'] + " <strong>Lifestyle:</strong> " + lifestyle + "</p>";
                html += "</div></div>";
                html += '<div class="col-md-6"><div class="profile-summary"><h3> Contact Information</h3>';
                html += "<p class='medium'>" + profile['client_email'] + "</p>";
                html += "<p class='medium'><strong>" + profile['client_phone'] + "</strong></p>";
                html += "</div></div>";
                html += '<div class="col-md-6"><div class="profile-summary"><h3> Contact Address</h3>';
                html += "<p class='medium'><strong>Location:</strong> " + profile['client_location'] + "</p>";
                html += "<p class='medium'><strong>Zip Code:</strong> " + profile['client_zip'] + "</p>";
                html += "</div></div>";


                html += '<div class="col-md-12"><hr/><h3> User Ratings</h3><hr/><div>';
                var ratinghtml = ''; var star;
                $.each(data.ratings, function (index, item) {
                    ratinghtml += "<div class='col-md-4'><p><strong>" + item.question + "</strong>:</p></div>";


                    star = "<div class='col-md-8'><p>";
                    for (var sc = 0; sc < 5; sc++) {
                        if (sc < item.ranking)
                            star += "<i class='fa fa-star orange'></i>";
                        else
                            star += "<i class='fa fa-star lgray'></i>";
                    }

                    star += "</p></div>";
                    ratinghtml += star;

                })

                if (ratinghtml == '')
                    html += '<p class="badge">Non rated know</p>';
                else
                    html += ratinghtml;
                html += '</div>';
                html += '</div>';
                $('#knowprofile').html(html);
            }
        }
    }); 

    $('#knowprofilemodal').modal('show');

})


$(document).on('click', '.showknowentryform', function () 
{
	$.ajax({
        type: 'post',
        url: aurl + 'vocations/',
        data: { id: 0 },
        success: function (data) {
            data = $.parseJSON(data); 
			
			var voclist;
			$.each(data, function(indx, item){
				voclist += "<option value='" + item.voc_name  + "'>" + item.voc_name + "</option>";  
			})
			$('.user_ques_text_add').html(voclist); 
			$('#e_prof').html(voclist);
			var config = 
                {
                    '.user_ques_text_add': {},
                    '.user_ques_text_add-deselect': { allow_single_deselect: true },
                    '.user_ques_text_add-no-single': { disable_search_threshold: 10 },
                    '.user_ques_text_add-no-results': { no_results_text: 'Oops, nothing found!' },
                    '.user_ques_text_add-width': { width: "95%" }
                }
                for (var selector in config) {
                    $(selector).chosen(config[selector]);
                } 
        }
    });
	 
}) 

$(document).on('change', '.client_pro', function () 
{
	var cur_voc = $('.client_pro').chosen().val() + '';
	if(cur_voc != '')
	{
		 
		$.ajax({
			type: 'post',
			url: aurl + 'commonvocations/fill/',
			data: { source_voc : cur_voc },
			success: function (data) 
			{
				data = $.parseJSON(data); 
				$(".user_target_voc").val('').trigger("chosen:updated");  
				if(data.error == 0)
				{
					str = data.common_vocs ;
					var voclists = str.split(',');  
					$(".user_target_voc").val(voclists).trigger("chosen:updated"); 
				} 
					
				 
			}
		});
	
	}  
}) 


$(document).on('click', '.btnrequestcity', function () 
{
	var cityname = $("#tbnewcityname").val();
	 
	$.ajax({
		type: 'post',
		url: aurl + 'cities/requestlisting/',
		data: { cityname:cityname, mid: mid },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			alertFunc('info',  data.errmsg  ); 
		}
	});

})
$(document).on('click', '.btnloadcitylisting', function () 
{
loadcitylisting();	
})

function loadcitylisting()
{	  
	$.ajax({
		type: 'post',
		url: aurl + 'cities/getrequestlisting/',
		data: { newlisting: 0 },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			if(data.error != 0)
			{ 
				
				$('#newcitylistings').html('<div class=" alertred">' + data.errmsg +"</div>");
				
			}
			else 
			{
				var html ="<table class='table table-condensed'> <thead> <tr><th>City Name</th><th>Listing Requested By</th><th>Action</th>  </tr> </thead><tbody>";
            
         $.each(data.result, function (index, item) 
         {  
 
             html +=  "<tr id='row-"  +  item.i   + "'><td>" + item.g +  "</td><td>" + item.n +  "</td>";
              

             html +=  "<td><button class='btn btn-primary btnlistcity' data-action='a'  data-value='"  +  item.i   + "' >List City</button>" +
			  " <button class='btn btn-primary btnlistcity' data-action='r'  data-value='"  +  item.i   + "' >Remove City</button>";
			 html +=   "</td> </tr>";   
 
         });
         
          html += '</table>';
		  
		  
		  $('#newcitylistings').html(html);
				
			}
		}
	});

}


$(document).on('click', '.btnlistcity', function () 
{
	 var i = $(this).attr('data-value');
	 var a = $(this).attr('data-action');
	  
	 
	$.ajax({
		type: 'post',
		url: aurl + 'cities/updatelisting/',
		data: { act: a, i : i },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			
			if(data.error == 0)
			{
				loadcitylisting();	
			} 
		}
	}); 
}) 
 



 

$(document).on('click', '.cfg_assignemail', function () 
{
	var page = $(this).attr('data-pg'); 
	if(typeof page == 'undefined' || page =='')
	{
		page =1;
	}
	pareparetimelinecanvas(page, page, page); 
})

 

$(document).on('click', '.btnupdateschedule', function () 
{
	var id = $(this).attr('data-id');
	var schdate = $(this).attr('data-schdate');
	
	var mid = $(this).attr('data-mid');
	var mname = $(this).attr('data-mname'); 
	$(".email_schupdate").attr('data-id', id );
	$(".email_schupdate").attr('data-mid', mid );
	$(".email_schupdate").attr('data-mname', mname ); 
	$('#mod_changeschedule').modal('show'); 
}) 

$(document).on('click', '.btnprocessseq', function () 
{
	var id = $(this).attr('data-id');
	var mid   = $(this).attr('data-mid');
	var mname   = $(this).attr('data-mname');
	
	$.ajax({
		type: 'post',
		url: aurl + 'emailsprogram/process/',
		data: { id : id  },
		success: function (data) 
		{
			data = $.parseJSON(data);
			alertFunc('info',  data.errmsg  );  
			
			preparetimeline(mid, mname);
		}
	});
}) 

$(document).on('click', '.email_schupdate', function () 
{
	var mid   = $(this).attr('data-mid'); 
	var mname   = $(this).attr('data-mname');
	
	var id = $(this).attr('data-id'); 
	var aemschedule = $('#rescheduledate').val(); 
	var aemschedulehr = $('#aemreschedulehr').val();
	var aemschedulemin = $('#aemreschedulemin').val();
	var aemscheduleper = $('#aemrescheduleper').val();
	
	$.ajax({
		type: 'post',
		url: aurl + 'asignemail/updatedate/',
		data: { id:id,aemschedule:aemschedule, hr:aemschedulehr, min:aemschedulemin, period: aemscheduleper },
		success: function (data) 
		{
			data = $.parseJSON(data);
			preparetimeline(mid, mname);
		}
	});
})

$(document).on('click', '.btn_deac_acclient', function () 
{
	var state = $(this).attr('data-s');
	 
	var deaclist = [];
	$('input[name=cb_actmembers]').each(function()
	{
		if(this.checked )
			deaclist.push( $(this).val() ); 
	});
	 
	if(deaclist.length > 0)
	{
		$.ajax({
			type: 'post',
				url: aurl + 'member/statusupdate/',
				data: { ids : deaclist.join(","), role: mrole, state: state },
				success: function(data) {
					 
					if (data == 'user_error') 
					{
						alertFunc('danger', 'Something went wrong, please try again');
					} else 
					{
						alertFunc('success', 'Changes are saved!'); 
					}
					waitFunc(''); 
					pareparetimelinecanvas(1, 1, 1); 
				} 
		});  
	} 
})

$(document).on('click', '#btn_ac_acclient', function () 
{
	var  aclist = [];
	$('input[name=cb_deactmembers]').each(function() {
		if(this.checked )
			aclist.push( $(this).val() ); 
	}); 
	
	if(aclist.length > 0)
	{
		$.ajax({
				type: 'post',
				url: aurl + 'member/statusupdate/',
				data: { ids : aclist.join(","), role: mrole , state: '1'},
				success: function(data) {
					if (data == 'user_error') 
					{
						alertFunc('danger', 'Something went wrong, please try again');
					} else 
					{
						alertFunc('success', 'Changes are saved!'); 
					}
					waitFunc(''); 
					pareparetimelinecanvas(1, 1, 1); 
				} 
		});  
	} 
})


$(".btnassignemail").click(function()
{
	var page = $(this).attr('data-pg'); 
	if(typeof page == 'undefined' || page =='')
	{
		page =1;
	} 
	$('#assignemailgrid').html("<div ><img   src='../images/processing.gif' alt='Loading ...' /></div>");  
	
	$('#mod_assignemail').modal('show');
	
	var id = $(this).attr('data-id'); 
	var name = $(this).attr('data-name'); 
	 
	$('.email_select').attr('data-mid', id );
	$('.email_select').attr('data-name', name); 
	
	$.ajax({
		type: 'post',
		url: aurl + 'emailsprogram/fetch/',
		data: { ep:1, page: page },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			
			if(data.error == 0)
			{
				html = "<table class='table table-responsive'>";
				html += "<tr ><th>Email Heading</th><th>Select Mail</th></tr>"  ; 
				$.each(data.results, function (index, item) 
				{
					html += "<tr id='row" + index + "'>" +  
					"<td id='col_mhead" + item.id + "'>" + item.a + "</td>" +  
					"<td><input type='radio' name='radioemtemp' value='" + item.id + "' /> </td>" + 
					"</tr>";
				});
				html += "</table>";
				$('#assignemailgrid').html(html); 
			}
		}
	});
});

$(".email_select").click(function()
{
	var lastseq = $('#events-tl').attr('data-seq' ); 
	
	lastseq = parseInt(lastseq) +1;
	
	var em_client = $(this).attr('data-id');
	var mid = $(this).attr('data-mid'); 
	var name = $(this).attr('data-name'); 
	
	 
	var aemschedule = $('#aemschedule').val();
	var radioemtemp = $('input:radio[name=radioemtemp]:checked').val();
	 
	
	var aemschedulehr = $('#aemschedulehr').val();
	var aemschedulemin = $('#aemschedulemin').val();
	var aemscheduleper = $('#aemscheduleper').val();
	 
	var startTime; 
	
	if(typeof radioemtemp !== 'undefined' || radioemtemp != '')
	{
		em_heading =  $('#col_mhead' + radioemtemp ).html();
		startTime  = aemschedule;
		  
		$.ajax({
			type: 'post',
			url: aurl + 'asignemail/save/',
			data: { em_client:em_client,aemschedule:aemschedule , radioemtemp:radioemtemp , hr:aemschedulehr, min:aemschedulemin, period: aemscheduleper },
			success: function (data) 
			{
				data = $.parseJSON(data); 
				
				
				var nulitem = "<li><span></span>" +
                    "<div class='title'>Sequence # " + lastseq + ":" + em_heading +  " </div>" +
                    "<div class='info'>"+ em_heading + 
					"<br/><button class='btn btn-primary btn-xs'>Change Schedule</button>" +
					"</div>" +
					"</div>" + 
                    "<div class='time' >" +
                        "<span>" + aemschedule + "</span>" + 
                    "</div>" +
                "</li>" ;
				
				if(lastseq==1)
				{
					$('#events-tl').empty(); 
				}
				$('#events-tl').append(nulitem); 
			}
		});  
	}
	else 
	{
		alertFunc('info', "No email selected!"  ); 
	} 
}) 
 
 
 
$(document).on('click', '.btnvm_edit', function()
{
	var id =   $(this).attr('data-id')  ;
	var adate = $(this).attr('data-adate')  ;
	$('#vm_description').val( $(this).attr('data-desc') );
	adate = new Date( adate ); 
	
	$('#vm_assigndate').val( adate.getFullYear() + "-" + (adate.getMonth() +1) + "-" + adate.getDate() );

	 $('.cfg_save_voicemail').attr('data-vmid', id);
})

$(document).on('click', '.btnvm_completed', function()
{
	var id =   $(this).attr('data-id');
	$.ajax({
		type: 'post',
		url: aurl + 'assignvoicemail/save/',
		data: {vmid:id, s:  1},
		success: function (data) 
		{
			data = $.parseJSON(data);  
			alertFunc('success', data.errmsg);
			preparevoicemailtimeline( $('#vmevent-tl').attr('data-id' ),
			$('#vmevent-tl').attr('data-name', name ) ); 
		}
	});
})


 
$(document).on('click', '#btn_srhvmclient', function () 
{
	var em_client = $('#vm_client').val();
	var page = $(this).attr('data-pg'); 
	if(typeof page == 'undefined' || page =='')
	{
		page =1;
	}
	reloadvoicemaillogs(page, page, em_client);
}) 
 


 



$(document).on('click', '.cfg_getallvoicemail', function() 
{
	var page = $(this).attr('data-page' );
	if(typeof page ==='undefined' || page =='')
	{
		page = 1;
	}
	reloadvoicemaillogs(page, page)
})

$(document).on('click', '.vmlog_pager li a', function() 
{
	var page = $(this).attr('data-pg' );
	if(typeof page ==='undefined' || page =='')
	{
		page = 1;
	}
	var page2 = $(this).attr('data-pg2' );
	if(typeof page ==='undefined' || page =='')
	{
		page2 = 1;
	}
	
	reloadvoicemaillogs(page, page2)
})

  
function reloadvoicemaillogs(page, page2 ,  client='')
{
	$.ajax({
		type: 'post',
		url: aurl + 'voicemails/allmembers/',
		data: {page: page, page2: page2 ,  client:client },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Package</th><th>Last Action</th><th>Next Action</th><th>Action Snapshot</th><th>Action</th></tr>"  ;  
			$.each(data.results, function (index, item) 
			{
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td><img src='"  + user_picture  +  "' alt='"  +  item.username   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.d + "</td><td>" +  item.f + "</td>" +
				"<td>" + item.lastbroadcast + "</td>" + 
				"<td>" + item.nextbroadcast + "</td>" + 
				"<td>" + item.da + "</td>" + 
				"<td>";
				html += "<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" +
				"<ul class='dropdown-menu'> " +
				"<li><a  href='#menu70' data-toggle='tab' class='btn_client_mgt'  data-id='" + item.a + "' data-name='" + item.d + "'  >New Voicemail</a></li>" +
				"</ul> " ;
				html +=  "</div></td></tr>";
			});
			
			var pages = data.page ;
			var prev =  page == 1 ? 1 :  parseInt(page) -1;
			var next =  page ==  pages ?  pages :  parseInt(page) + 1; 
			html  += "<tr><td colspan='5'><ul class='pagination vmlog_pager'><li><a data-func='prev' data-pg2='" + page2 + "' data-pg='" + prev + "'  >«</a></li>";
			for( i=1;  i <= pages;  i++)
			{
				active =  i ==  page ? 'active' : '';
				html += "<li class='" + active + "'><a data-pg2='" + page2 + "' data-pg='" + i + "' >" + i + "</a></li>";
			} 
			html += "<li><a data-func='next' data-pg2='" + page2 + "' data-pg='" + next +  "'  >»</a></li></ul></td></tr>";
			html += "</table>";	 	
			$('#voicemail_logs').html(html); 
			
			
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Package</th><th>Last Broadcast</th><th>Next Broadcast</th><th>Action Snapshot</th><th>Action</th></tr>"  ;  
			$.each(data.results2, function (index, item) 
			{
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td><img src='"  + user_picture  +  "' alt='"  +  item.username   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.d + "</td><td>" +  item.f + "</td>" +
				"<td>" + item.lastbroadcast + "</td>" + 
				"<td>" + item.nextbroadcast + "</td>" + 
				"<td>" + item.da + "</td>" + 
				"<td>";
				html += "<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" +
				"<ul class='dropdown-menu'> " +
				"<li><a  href='#menu70' data-toggle='tab' class='btn_client_mgt'  data-id='" + item.a + "' data-name='" + item.d + "'  >Client Management</a></li>" +
				"</ul> " ;
				html +=  "</div></td></tr>";
			});
			
			var pages = data.page2 ;
			var prev =  page2 == 1 ? 1 :  parseInt(page2) -1;
			var next =  page2 ==  pages ?  pages :  parseInt(page2) + 1; 
			html  += "<tr><td colspan='5'><ul class='pagination vmlog_pager'><li><a data-func='prev' data-pg='" + page  + "' data-pg2='" + prev + "'  >«</a></li>";
			for( i=1;  i <= pages;  i++)
			{
				active =  i ==  page2 ? 'active' : '';
				html += "<li class='" + active + "'><a data-pg='" + page  + "' data-pg2='" + i + "' >" + i + "</a></li>";
			} 
			html += "<li><a data-func='next' data-pg='" + page  + "' data-pg2='" + next +  "'  >»</a></li></ul></td></tr>";
			html += "</table>";	 	
			$('#novoicemail_logs').html(html); 
			
		}
	}); 
}
 
$(document).on('click', '.navactionlog', function()
{
	$('#menu70').hide();
	$('#vmevent-tl').empty();
	$('.cfg_save_voicemail').attr('data-id', ''); 
})

$(document).on('click', '.btn_statechange', function () 
{
	var state = $(this).attr('data-s');
	var id = $(this).attr('data-user');
	
	$.ajax({
		type: 'post',
		url: aurl + 'member/statusupdate/',
		data: { ids :  id , role: mrole, state: state },
		success: function(data)
		{
			if (data == 'user_error') 
					{
						alertFunc('danger', 'Something went wrong, please try again');
					} else 
					{
						alertFunc('success', 'Changes are saved!'); 
					}
					waitFunc('');  
				} 
		});  
}) 

$(document).on('click', '.join3tprogram', function () 
{
	var ppid = $(this).attr('data-ppid'); 
	$.ajax({
		type: 'post',
		url: aurl + 'member/joinprogram/',
		data: { id : mid , s :1, ppid: ppid },
		success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc('');
			alertFunc('success', data.errmsg); 
			window.open('dashboard.php','_self'); 
		} 
	});  
})  
 

$(document).on('click', '.btnsaveanswers', function () 
{
	var totalquestions =  $(this).attr("data-qc");
	var qstring='';
	var myObj = {};

	myObj["totalq"] = totalquestions ;
	for(var i=0; i < totalquestions; i++)
	{
		myObj["q" + i] =  $('#qstid' + i).attr('data-qid'); 
		myObj["a" + i] =  $('#qstid' + i).val(); 
	}
	var json = JSON.stringify(myObj);
	    
	$.ajax({
		type: 'post',
		url: aurl + 'member/program/answers/save/',
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

 
 
$(document).on('click', '.btnassignpqs', function()
{
	var mid = $(this).attr('data-mid');
	var pid  = $(this).attr('data-pid');
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

 
$(document).on('click', '.btndelpqs', function()
{
	$('#memquestions').html(''); 
})
 
  
 
 $(document).on('click', '.showremindersummary', function()
{
	reloadreminder(mid, 1);
}); 


$(document).on('click', '.mgreminder li a', function () 
{
	var page = $(this).attr('data-pg'); 
	reloadreminder(  mid, page );
})
$(document).on('click', '.mgreminder .gotopage', function()
{
	var page = $('.mgreminder .tbgotopage').val( );  
	reloadreminder(  mid, page ); 
});

function reloadreminder(userid,page)
{ 
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'reminder/get/',
        data: { userid: userid, page: page},
        success: function(data) {
			 
         data = $.parseJSON(data) ; 
		  
         waitFunc('');
        
         html ='<table class="table table-bordered table-striped">';
         html +='<tr><th>Sl. No.</th><th>Type</th><th>Reminder Title</th><th>Created On</th>' +
         '<th>Reminder Date and Time</th><th>Action</th></tr>';

         $.each( data.result , function(idx, obj){
             html += 
                '<tr id="' + obj.a + '"><td>' + (  idx + 1) +  '</td>' + 
                 '<td >'  + obj.b +   '</td>'  + 
                 '<td >'  + obj.c +   '</td>'  +
                 '<td >'  + obj.g +  '</td>'  + 
                 '<td >'  + obj.f +   '</td>'  +
                 '<td >' +
                 '<button data-id="' + obj.a +  '"  data-remdate="' + obj.f +   '" data-title="' + obj.c +  
                 '" data-reminder="'  + obj.d +  '"  class="btn btn-primary btn-xs btnvu">View</button>' +
                 '<a data-id="' + obj.a +   '" data-type="' + obj.b +  
                 '" data-assignto="' + obj.e +  
                 '"  data-remday="' + obj.f + 
                 '" data-remhr="' + obj.f +  
                 '" data-remmin="' + obj.f + 
                 '" data-remdate="'  + obj.f +  
                 '" data-title="' + obj.c +  
                 '" data-reminder="' + obj.d +   '"  data-toggle="tab" href="#menu32"  class="btn btn-success btn-xs btnedit">Edit</a>' +
                '<button data-id="' + obj.a +  '" class="btn btn-danger btn-xs btnrem">Remove</button></td></tr>';
                  
         }) 
          
				
		var pages = data.pages;
		var prev =  page == 1 ? 1 :  parseInt(page) -1;
			var next =  page ==  pages ?  pages :  parseInt(page) + 1;  
			html  += "<tr><td colspan='6'><ul class='pagination mgreminder'>" +
		    "<li><a  data-func='first' title='First Page' data-pg='1'  >First Page</a></li> " +
			"<li><a data-func='prev' data-pg='" + prev + "'    >«</a></li>";
			
			if( page > 10)
				html += "<li><a  data-func='next' title='Show last few pages' data-pg='1' > ... </a></li>";
			if( page < 10) 
			{
				for(var j = 1 ; j  <=  10    ; j++)
				 {
					 if(j > pages)
					 {
						 break;
					 }
					 active =  j ==  page ? 'active' : ''; 
					 html += "<li class='" + active + "'><a  data-pg='" + j  + "' >" + j  + "</a></li>";
				 } 
			}
			else 
			{ 
				for( var i = parseInt(page) - 5;  i <=   parseInt(page) + 5  ;  i++ )
				{
					
					if( i > pages)
					{
						 break;
					}
					active  =  i ==  page ? 'active' : '';
					html +=   "<li class='" + active + "'><a data-pg='" + i  + "' >" + i  + "</a></li>";
				 }
			}
			
			if( parseInt(page)  < ( pages - 10 ) )
			{
				html +=  "<li><a data-func='next' title='Show last few pages' data-pg='" + pages + "'> ... </a></li>"; 
			}
		html += "<li> <input class= 'form-control tbgotopage'  type='text' style='width: 120px; height: 32px; margin-top: 2px; margin-right: 5px; float: left; display: inline-block;' placeholder= 'Go to page ...' > </li>";
		html += "<li> <input class='btn gotopage' type='button'  value='Go' style='width: 50px; float: left; height: 32px; margin-top: 2px; display: inline-block;  background-color: #2e353d; color: #fff;'   > </li>";
		html += "<li><a  data-func='next' title='Next Page'   data-pg='" + next +  "'>»</a></li> "; 
		html += "<li><a  data-func='last' title='Last Page'   data-pg='" + pages +  "'>Last Page</a></li> ";
		html += "</ul></td></tr>";	
			 
	
         html +='</table>';
         
         html += '<div class="modal fade reminderview" tabindex="-1" role="dialog" aria-labelledby="reminderview" id="reminderview">'+ 
                  '<div class="modal-dialog ">'+
                     '<div class="modal-content">'+
                         '<div class="modal-header">'+
                             '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'+
                             '<span aria-hidden="true">&times;</span></button>'+
                             '<h2 id="remindtitle" class="modal-title" >Reminder Summary</h2>'+
                             '<small   id="cprofession"></small>'+
                         '</div>'+
                         '<div class="modal-body modal-body-no-pad"  style="max-height: 520px; overflow-y:scroll; text-align:left"> '+
                             '<div id="remisummary">'+
                             '</div>'+
                         '</div>'+
                          '<div class="modal-footer" >'+
                          '<button class="btn btn-danger btn-lg" data-dismiss="modal" aria-label="Close" >Close</button>'+
                          '</div>'+
                     '</div>'+
                 '</div>'+
         '</div>' ;
  
         $('#remindersummary').html(html);			
        }
    });

}
 
$(document).on('change', '.ed_client_pro', function () 
{
	var cur_voc = $('.ed_client_pro').chosen().val() + '';
	if(cur_voc != '')
	{
		$.ajax({
			type: 'post',
			url: aurl + 'commonvocations/fill/',
			data: { source_voc : cur_voc },
			success: function (data) 
			{
				data = $.parseJSON(data); 
				$(".user_target_voc_ed").val('').trigger("chosen:updated");  
				if(data.error == 0)
				{
					str = data.common_vocs ;
					var voclists = str.split(',');  
					$(".user_target_voc_ed").val(voclists).trigger("chosen:updated"); 
				}   
			}
		}); 
	} 
})

$(document).on('click', '.load_rated6invites', function () 
{
	var page = 0;
	loadrated6invites(page);
})

function loadrated6invites(page)
{ 
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'email/rated6invite/getlog/',
        data: {   page: page },
        success: function(data) {
			 
        data = $.parseJSON(data) ; 
		 
         waitFunc(''); 
         html ='<table class="table table-bordered table-striped">';
         html +='<tr><th>Sl. No.</th><th>Member Name</th><th>Know Invited</th><th>Sent On</th>' +
         '<th>Join Date</th></th> </tr>'; 
         $.each( data.results , function(idx, obj){
             html += 
                '<tr "><td>' + (  idx + 1) +  '</td>' + 
                 '<td >'  + obj.member_name +   '</td>'  + 
                 '<td >'  + obj.know_name +   '</td>'  +
                 '<td >'  + obj.send_date +  '</td>'  +  
				  '<td >'  + ( obj.join_date == null ? '<span class="badge">Not Signup Yet</span>' : obj.join_date)  +  '</td>'  +  
                 ' </tr>';
                  
         })    
         html +='</table>';  
         $('#invitedrated6list').html(html);			
        }
    });  
}
 

$(document).on('click', '.seo_modal_admin', function () 
{
	var mid = $(this).attr("data-user");
	$('#hidseomid').val(mid);
	
	$.ajax({
		 type: 'post',
		url: aurl + 'member/seo/get/',
		data: {   mid:  mid}, 
		success: function(data) 
		{
			data = $.parseJSON(data);  
			waitFunc('');  
			$.each( data.results , function(idx, obj){
				$("#seo_tags").val(  obj.meta ) ;
				$("#seo_keywords").val(  obj.keywords ) ;   
			})  
		 
		},
		 error: function() { 
			waitFunc(''); 
		}
	});
	  
	$('#seo_modal').modal('show');
	
})
 
 
$(document).on('click', '.btn_save_seo', function () 
{
	var seo_tags = $("#seo_tags").val();
	var seo_keywords = $("#seo_keywords").val();
	var mid = $('#hidseomid').val(); 
	$.ajax({
		 type: 'post',
		url: aurl + 'member/seo/save/',
		data: {   tags: seo_tags,  keywords : seo_keywords, mid:  mid}, 
		success: function(data) 
		{
			data = $.parseJSON(data);  
			waitFunc(''); 
			alertFunc('success', data.errmsg);   
		},
		 error: function() { 
			waitFunc(''); 
		}
	});  
})




$(document).ready(function() {
  /******************************
      BOTTOM SCROLL TOP BUTTON
   ******************************/ 
  // declare variable
  var scrollTop = $(".scrollTop"); 
  $(window).scroll(function() {
    // declare variable
    var topPos = $(this).scrollTop();

    // if user scrolls down - show scroll to top button
    if (topPos > 100)
	{
		$(scrollTop).css("opacity", "1"); 
	}
	else
	{
		$(scrollTop).css("opacity", "0");
    } 
 }); // scroll END

  //Click event to scroll to top
  $(scrollTop).click(function() {
    $('html, body').animate({
      scrollTop: 0
    }, 800);
    return false; 
  }); // click() scroll top EMD
 
  // declare variable
  var h1 = $("#h1").position();
  var h2 = $("#h2").position();
  var h3 = $("#h3").position();

  $('.link1').click(function() {
    $('html, body').animate({
      scrollTop: h1.top
    }, 500);
    return false;

  }); // left menu link2 click() scroll END
  
  $('.link2').click(function() {
    $('html, body').animate({
      scrollTop: h2.top
    }, 500);
    return false;
  }); // left menu link2 click() scroll END

  $('.link3').click(function() {
    $('html, body').animate({
      scrollTop: h3.top
    }, 500);
    return false;

  }); // left menu link3 click() scroll END

}); // ready() END

$(function(){
  $('[data-toggle="tooltip"]').tooltip({
    container: 'body'
  });
}); 