//Create notifications div and hide all alert on re-lode
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

//Clear client global variable
var client_suc_status = 0;

$(window).load(function() {

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


    $(document).on('click', '#nextBtn2', function(e) {
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

    function setModalsAndBackdropsOrder() {
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


// Register new user with email id
function updProfile(updProfForm) {
    waitFunc('enable');
    //console.log('regArr',regUserArr);
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
                // window.open('dashboard.php','_self');
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

$(document).on('click', '.regUser', function(e) {
    e.stopImmediatePropagation();
    var first_name = $(this).parents('.sec_two').find('input[name=first_name]').val();
    var email2 = $(this).parents('.sec_two').find('input[name=email2]').val();
    var last_name = $(this).parents('.sec_two').find('input[name=last_name]').val();
    var password = $(this).parents('.sec_two').find('input[name=password]').val();
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
    } else {
        reg_email = email2;
        reg_first_name = first_name;
        reg_last_name = last_name;
        reg_password = password;

    }


    if (check == 0) {
        var updProfForm = new FormData(),
            updProf = {
                reg_email: reg_email,
                reg_first_name: reg_first_name,
                reg_last_name: reg_last_name,
                reg_password: reg_password,
                reg_country: reg_country,
                vocation_result: vocation_result,
                groups_result: groups_result,
                target_clients: target_clients,
                target_referral_partners: target_referral_partners
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
        updProfForm.append('reg_country', reg_zip);
        updProfForm.append('reg_country', reg_city);
        updProfForm.append('vocation_result', vocation_result);
        updProfForm.append('groups_result', groups_result);
        updProfForm.append('target_clients', target_clients);
        updProfForm.append('target_referral_partners', target_referral_partners);
        updProfile(updProfForm);
        var thisSec = $(this).attr('data-sec');
        $('.next-sections').hide();
        $(thisSec).fadeIn(300);
    }
});

$(document).on('click', '.regUser_up', function(e) {
    e.stopImmediatePropagation();
    var updProfForm = new FormData(),
        updProf = {
            reg_country: reg_country,
            vocation_result: vocation_result,
            groups_result: groups_result,
            target_clients: target_clients
        };

    updProfForm.append('updProf', 'updProf');
    if ($('#blah').val() != "undefined") {
        updProfForm.append('image', $('#usrImg').prop('files')[0]);
    }

    updProfForm.append('insID', insID);
    updProfForm.append('reg_country', reg_country);
    updProfForm.append('reg_country', reg_zip);
    updProfForm.append('reg_country', reg_city);
    updProfForm.append('vocation_result', vocation_result);
    updProfForm.append('groups_result', groups_result);
    updProfForm.append('target_clients', target_clients);
    updProfForm.append('target_referral_partners', target_referral_partners);
    updProfForm.append('reg_update', '1');
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
                window.open('dashboard.php', '_self');
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
    var alert = $('<div class="alert alert-' + color + '"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' + msg + '</div>').hide();
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
function confFunc(text, func) {
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



$(document).on('click', '.dropdown-toggle', function() {
    $(this).parents('.dropdown').toggleClass('open');
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
function signIn(user_email, user_pass) {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { user_email: user_email, user_pass: user_pass },
        success: function(data) {
            if (data == 'error') {
                alertFunc('danger', 'Email or password not found');
            } else if (data == 'error_activation') {
                alertFunc('info', 'Your account is not deactivated. Please contact Admin My City');
            } else {
                $('body').append(data);
            }
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

function signInValidation(user_email, user_pass) {
    if (validateEmail(user_email) == false) {
        alertFunc('danger', 'Email address not valid');
        return;
    }
    if (user_pass == '') {
        alertFunc('danger', 'Please enter your password');
        return;
    }
    signIn(user_email, user_pass);
}

$(document).on('click', '#sign_in_button', function() {
    var user_email = $('#login_username').val().trim();
    var user_pass = $('#login_password').val().trim();
    signInValidation(user_email, user_pass);
});

$(document).on('keypress', '#signin #login_username,#signin #login_password', function(e) {
    e.stopImmediatePropagation();
    if (e.which == 13) {
        var user_email = $('#login_username').val().trim();
        var user_pass = $('#login_password').val().trim();
        signInValidation(user_email, user_pass);
    }
});


// Get User References
function getUserClients(getUserClients) {
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

// Get Search Logs
function getSearchlogs(page) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getSearchlogs: page },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('.SearcLogs').html(data);
                $('[data-toggle="tooltip"]').tooltip();
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
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

// View user references
function viewUserRef(user, view) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getUser: user, view: view },
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
            if (data == 'match') {
                alertFunc('danger', 'Sorry, user with this email already added');
                client_suc_status = 0;
            } else if (data == 'success' || parseInt(data) > 0) {
                alertFunc('success', 'User successfully saved');
                client_suc_status = 1;

                if (parseInt(data) > 0) {
                    //reward loyalty point if new know entry is being done
                    if (id == 0) {
                        raiseloyaltypoint(10, 'Contact Addition');
                        //fetch suggested referrals 
                        loadsuggestedrefferals(data, interestedprofessions, client_zip);
                    }
                } else if (data == 'success' && id > 0) {
                    var professions = $(".user_ques_text_ed").chosen().val() + '';
                    regeneratesuggestedrefferals(id, professions, client_zip);
                }
                //clearing controls 
                $('.client_name').val("");
                $('.client_ph').val("");
                $('.client_email').val("");
                $('.client_location').val("");
                $('.client_note').val("");
                $('.user_ques_text_add').val("");
                $('.client_lifestyle').val("");

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

$(document).on('click', '.addClientUser', function() {
    var client_name = $('.client_name').val().trim();
    var client_pro = $('.client_pro').val() + '';
    var client_ph = $('.client_ph').val().trim();
    var client_email = $('.client_email').val().trim();
    var client_location = $('.client_location').val().trim();
    var client_zip = $('.client_zip').val().trim();
    var client_note = $('.client_note').val().trim();
    var client_lifestyle = $('.client_lifestyle').val().trim();

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
    if (client_name == '') {
        alertFunc('danger', 'Please provide a name');
        return;
    }
    if (validateEmail(client_email) == false) {
        alertFunc('danger', 'Email not valid');
        return;
    }

    //var values =  $(".chosen-select").chosen().val();
    addNewKnow(0, client_name, client_pro, client_ph, client_email, client_location, client_zip, client_note, ques_rate, ques, user_grp, user_ques_text, client_lifestyle);
    if (client_suc_status == 1) {
        $('.client_name').val("");
        $('.client_ph').val("");
        $('.client_email').val("");
        $('.client_location').val("");
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
        url: 'includes/ajax.php',
        data: { getQues: 'getQues' },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('.questionsData').html(data);
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
$(document).on('click', '.saveQues', function() {
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
        url: 'includes/ajax.php',
        data: { allQues: allQues },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
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
            url: 'includes/ajax.php',
            data: { del_ques: id },
            success: function(data) {
                waitFunc('');
                if (data == 'error') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
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
        url: 'includes/ajax.php',
        data: { addGroup: groupName },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Group with name "' + groupName + '" already exists!');
            } else {
                getAlGroups();
                alertFunc('success', 'Group successfully added');
                $('.groupName').val('');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


// Remove Group
$(document).on('click', '.delGroup', function() {

    var thisBtn = $(this);
    var id = $('.newGrpVal').attr('data-val');
    if ($('.newGrpVal').val().trim() == '') {
        alertFunc('info', 'Please select the group first');
        return
    }
    confFunc('By deleting this group all the people in this group will also get removed. Are you sure you want to delete this group?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { del_grp: id },
            success: function(data) {
                waitFunc('');
                if (data == 'error') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'Group successfully deleted');
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


//Edit Group ********
$(document).on('change', '.userClientGrps', function() {
    var currGrp = $('.userClientGrps option:selected').text();
    var currGrpVal = $(this).val();
    if (currGrpVal == 'null') {
        $('.newGrpVal').val('');
        return;
    }
    $('.newGrpVal').val(currGrp).attr('data-val', currGrpVal);
});


// Update Group
$(document).on('click', '.updGroup', function() {
    var currGrp = $('.newGrpVal').val(),
        currGrpVal = $('.newGrpVal').attr('data-val');
    if ($('.newGrpVal').val().trim() == '') {
        alertFunc('info', 'Please select the group first');
        return
    }
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addGroup: currGrp, currGrpVal: currGrpVal },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Group with name "' + currGrp + '" already exists!');
            } else {
                getAlGroups();
                alertFunc('success', 'Group name successfully updated');
                $('.newGrpVal').val('')
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


// Get Al vocation
function getAlVocation() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getAlVocation: 'getAlVocation' },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again');
            } else {
                $('.fetVocations').html('<option value="null">-select group-</option>' + data);
                //$('#edit_people_details .ed_client_pro').html('<option value="null">-select group-</option>' + data);
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


// Update Vocation
$(document).on('click', '.updVoc', function() {
    var currVoc = $('.editVocation').val().trim(),
        currVocVal = $('.editVocation').attr('data-val');
    if (currVoc == '') {
        alertFunc('info', 'Please select the vocation first');
        return
    }
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addVocation: currVoc, currVocVal: currVocVal },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Vocation with name "' + currVoc + '" already exists!');
            } else {
                getAlVocation();
                alertFunc('success', 'Group name successfully updated');
                $('.currVoc').val('')
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


// Remove Vocation
$(document).on('click', '.delVoc', function() {
    var id = $('.editVocation').attr('data-val');
    if ($('.editVocation').val().trim() == '') {
        alertFunc('info', 'Select the vocation first');
        return
    }
    confFunc('Are you sure you want to delete this vocation?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { del_voc: id },
            success: function(data) {
                waitFunc('');
                if (data == 'error') {
                    alertFunc('danger', 'Something went wrong, please try again')
                } else {
                    alertFunc('success', 'Successfully deleted');
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
// Update  Lifestyle
$(document).on('click', '.updLifestyle', function() {
    var currLifestyle = $('.editLifestyle').val().trim(),
        currLifestyleVal = $('.editLifestyle').attr('data-val');
    if (currLifestyle == '') {
        alertFunc('info', 'Please select the lifestyle first');
        return
    }
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addLifestyle: currLifestyle, currLifestyleVal: currLifestyleVal },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Lifestyle with name "' + currLifestyle + '" already exists!');
            } else {
                getLifestyles();
                alertFunc('success', 'Lifestyle name successfully updated');
                $('.currVoc').val('')
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});
// Get Al vocation
function getLifestyles() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getLifestyles: 1 },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again');
            } else {
                $('.fetchLifestyles').html('<option value="null">-Select Lifestyle-</option>' + data);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

// Add New Group
$(document).on('click', '.addNewLifestyle', function() {
    var lifestylename = $('.lifestylename').val().trim();
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addLifestyle: lifestylename },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Lifestyle with name "' + lifestylename + '" already exists!');
            } else {
                getLifestyles();
                alertFunc('success', 'Successfully added');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


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

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { srchPeople: '1', vocSrch: vocSrch, locSrch: locSrch, nameSrch: nameSrch },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('info', 'Something went wrong, please try again')
            } else {
                $('.srdDtls').show();
                $('#srchrslts').html(data);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


//EDIT PEOPLE
$(document).on('click', '.editPeopleDetails', function() {

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

                //change to multiselect input
                var config = {
                    '.ed_client_pro': {},
                    '.ed_client_pro-deselect': { allow_single_deselect: true },
                    '.ed_client_pro-no-single': { disable_search_threshold: 10 },
                    '.ed_client_pro-no-results': { no_results_text: 'Oops, nothing found!' },
                    '.ed_client_pro-width': { width: "95%" }
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
    var loc = $('.ed_client_location').val().trim();
    var zip = $('.ed_client_zip').val().trim();
    var note = $('.ed_client_note').val().trim();
    var grp = $('.ed_user_grp').val();
    var lifestyle = $('.ed_client_lifestyle').val();

    var order = $(this).attr('id');
    var rank = [],
        ques = [];
    var user_ques_text = [];
    $('.user_ques_ed').each(function(i) {
        ques[i] = $(this).attr('data-ques');
        rank[i] = $(this).find('input:checked').val();
    });
    $('.user_ques_text_ed').each(function(i) {
        var id = $(this).attr('data-ques');
        var answer = $(this).val();
        if (answer) {
            user_ques_text[i] = {
                id: id,
                answer: answer.toString()
            };
        }
    });

    if (name == '') {
        alertFunc('danger', 'Please provide a name');
        return;
    }
    if (validateEmail(email) == false) {
        alertFunc('danger', 'Email not valid');
        return;
    }
    //addClientUser(order, name, pro, ph, email, loc,zip, note, rank, ques, grp, user_ques_text);
    addNewKnow(order, name, pro, ph, email, loc, zip, note, rank, ques, grp, user_ques_text, lifestyle);

});


//Leave A Message
$(document).on('click', '.leaveMsg', function() {
    $('#myModal').attr('data-id', $(this).attr('id'));
});

$(document).on('click', '.leaveUserMsg', function() {
    var myModal = $('#myModal').attr('data-id');
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
        url: 'includes/ajax.php',
        data: { sender_name: sender_name, sender_email: sender_email, leaveMsg: sender_msg, myModal: myModal },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('info', 'Something went wrong, please try again')
            } else {
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

// pagination
$(document).on('click', '.pagiAd li', function() {
    var page = $(this).find('a').attr('data-pg');
    getUserClients(page);
});


// pagination
$(document).on('click', '.paginationU li', function() {
    viewUserRef($('.viewUser.active').attr('data-user'), $(this).find('a').attr('data-pg'));
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
    viewUserRef($(this).attr('data-user'), 1);
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

            $('input[name="upd_username"]').val(results.username);
            $('input[name="upd_phone"]').val(results.user_phone);
            $('select[name="upd_country"] option[value="' + results.country + '"]').prop('selected', true);
            $('input[name="upd_city"]').val(results.city);
            $('input[name="upd_zip"]').val(results.zip);
            $('input[name="upd_email"]').val(results.user_email);
			$('textarea[name="about_your_self"]').val(results.about_your_self);


            $('input[name="upd_usergrp"]').prop('checked', false);
            $('input[name="upd_uservoc"]').prop('checked', false);
            $('input[name="upd_usertarget"]').prop('checked', false);
            $('input[name="upd_usertargetreferral"]').prop('checked', false);
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

$(document).on('click', '.updateUserProf', function() {

    var data_id = $('#changeAccSett').attr('data-id');
    var upd_username = $('input[name="upd_username"]').val();
    var upd_phone = $('input[name="upd_phone"]').val();
    var upd_country = $('select[name="upd_country"]').val();
    var upd_city = $('input[name="upd_city"]').val();
    var upd_zip = $('input[name="upd_zip"]').val();
    var upd_email = $('input[name="upd_email"]').val();
	var about_your_self = $('textarea[name="about_your_self"]').val();
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
    $('input[name="upd_usertarget"]:checked').each(function(i) {
        upd_usertarget[i] = $(this).val();
    });
    var upd_usertargetreferral = [];
    $('input[name="upd_usertargetreferral"]:checked').each(function(i) {
        upd_usertargetreferral[i] = $(this).val();
    });


    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { data_id: data_id, upd_username: upd_username, upd_phone: upd_phone, upd_country: upd_country, upd_city: upd_city, upd_zip: upd_zip, upd_email: upd_email, upd_usergrp: upd_usergrp, upd_uservoc: upd_uservoc, upd_usertarget: upd_usertarget, upd_usertargetreferral: upd_usertargetreferral,about_your_self:about_your_self },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('info', 'Something went wrong, please try again')
            } else {
                alertFunc('success', 'Settings successfully updated!');
                getUserClients($('.pagiAd li.active a').attr('data-pg'));
            }
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


//Save Package details
$(document).on('click', '.savePkgDetails', function() {
    var package_name = $('input[name="package_name"]').val().trim();
    var package_price = $('input[name="package_price"]').val().trim();
    var package_dur = $('select[name="package_dur"]').val();
    var ref_sh_conn = $('input[name="ref_sh_conn"]').val().trim();
    var ref_sh_conn_desc = $('input[name="ref_sh_conn_desc"]').val().trim();
    var ref_conn = $('input[name="ref_conn"]').val().trim();
    var ref_conn_desc = $('input[name="ref_conn_desc"]').val().trim();
    var tar_conn = $('input[name="tar_conn"]').val().trim();
    var tar_conn_desc = $('input[name="tar_conn_desc"]').val().trim();
    var edit_package = $('#edit_package').attr('data-id');
    var package_services = [];
    $('input[name="package_services"]').each(function(i) {
        package_services[i] = $(this).val().trim();
    });


    var packageData = {
        package_name: package_name,
        package_price: package_price,
        package_dur: package_dur,
        ref_sh_conn: ref_sh_conn,
        ref_conn: ref_conn,
        tar_conn: tar_conn,
        package_services: package_services,
        edit_package: edit_package,
        ref_sh_conn_desc: ref_sh_conn_desc,
        ref_conn_desc: ref_conn_desc,
        tar_conn_desc: tar_conn_desc
    };

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { packageData: packageData },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                alertFunc('success', 'Package saved!');
                $('.packageDetails').html(data);
                $('input[name="package_services"]').each(function(i) {
                    if (i == 0) {
                        $(this).val('');
                    } else {
                        $(this).parents('.form-group').remove();
                    }
                });
                getUserClients('1');
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

$(document).on('click', '.addNewService', function() {
    var services = '<div class="form-group">' +
        '<div class="col-sm-11 padd-5">' +
        '<input name="package_services" class="form-control" placeholder="Package service"/>' +
        '</div>' +
        '<div class="col-sm-1 padd-5">' +
        '<button class="fa fa-minus btn btn-default rmvNewService"></button>' +
        '</div>' +
        '</div>';
    $(this).parents('.services').append(services);
});

$(document).on('click', '.rmvNewService', function() {
    $(this).parents('.form-group').remove();
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


//Delete package
$(document).on('click', '.del_pkg', function() {
    var pkg = $(this).attr('data-id');
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { changePkgStatus: pkg },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'Status updated!');
            $('.packageDetails').html(data);
            $('[data-toggle="tooltip"]').tooltip();
            getUserClients('1');
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
$(document).on('click', '.srchRef', function() {
    var ref_name = $('.srchRefName').val().trim();
    var locateVoc = $('#locateVoc').val();

    if ((ref_name == '') && (locateVoc == '')) {
        getUserClients('1');
        return
    }

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { ref_name: ref_name, locateVoc: locateVoc },
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


// Add page content
function savePagesContent(title, content, data_page, id_page) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { title: title, content: content, savePagesContent: data_page, id_page: id_page },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Sorry, password did not match!');
            } else {
                alertFunc('success', 'Page content saved!');
                window.location.reload();
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

$(document).on('click', '.saveAbout', function() {
    var about_title = $('input[name="about_title"]').val().trim();
    var about_content = $('textarea[name="about_content"]').val().trim();
    savePagesContent(about_title, about_content, 'about', '0');
});

$(document).on("click", ".saveTagline", function() {
    var tagline = $('input[name="tagline"]').val().trim();
    savePagesContent("", tagline, 'tagline', '15');
});


//Delete page content
$(document).on('click', '.delPgCntnt', function() {
    var id = $(this).attr('data-id');
    var $thisContent = $(this);
    confFunc('Are you sure you want to delete this content?', function() {
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

$(document).on('click', '.updPgContent', function() {
    var about_title_ed = $('input[name="about_title_ed"]').val().trim();
    var about_content_ed = $('textarea[name="about_content_ed"]').val().trim();
    var id = $('#editContent').attr('data-id');
    savePagesContent(about_title_ed, about_content_ed, '', id);
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
            url: 'includes/ajax.php',
            data: { fback_name: name, fback_email: email, fback_coment: coment },
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


// Send Help Instruction
$(document).on('click', '.save_helpinstruction', function() {
    waitFunc('enable');
    var title = $(".help_title").val();
    var helpbody = $(".help_content").val();
    var faqid = $('#faqid').val();
    if (title != "" && helpbody != "") {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { addhelpexp: '1', title: title, helpbody: helpbody, faqid: faqid },
            success: function(data) {
                waitFunc('');
                $(".help_title").val("");
                $(".help_content").val("");
                getAllFAQs();
                alertFunc('success', 'Help explanation has been submitted!');
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again');
            }
        });
    } else {
        waitFunc('');
        alertFunc('info', 'Please first fill all help explanation fields!');
    }
});
/* get FAQs instructions */

function getAllFAQs(type) {

    waitFunc('enable');
    $.ajax({
        type: 'get',
        data: { getallfaqs: type },
        url: 'includes/ajax.php',
        success: function(data) {
            waitFunc('');
            if (type == 1)
                $("#helptable").html(data);
            else if (type == 2)
                $('#helpaccordion').html(data);
            alertFunc('success', 'FAQs retrieved successfully!');
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again');
        }
    });

}

$(document).on('click', '.get_FAQ', function() {

    $(".help_title").val("");
    $(".help_content").val("");
    $("#faqid").val("0");
    getAllFAQs(1);
});
$(document).on('click', '.getpublicfaqs', function() {
    getAllFAQs(2);
});





/* editing FAQ entry */
$(document).on('click', '.editFaq', function() {

    $('.help_title').val($(this).data('ques'));
    $('.help_content').val($(this).data('ans'));
    $('#faqid').val($(this).data('id'));

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
$(document).on('click', '.addNewTrigger', function() {
    var triggerName = $('.triggerName').val().trim();
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { addtrigger: 1, triggerName: triggerName },
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
        url: 'includes/ajax.php',
        data: { addtrigger: 1, triggerName: triggerName, currTrigID: currTrigID },
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
    /*
    if ($('.editTrigger').val().trim() == '') {
        alertFunc('info', 'Select the trigger first');
        return
    } */
    confFunc('Are you sure you want to delete this trigger?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { deltrig: id },
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
            url: 'includes/ajax.php',
            data: { getgrpmembers: 1, groupid: selGrp },
            success: function(data) {
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
        url: 'includes/ajax.php',
        data: {
            srchClient: '1',
            clientid: clientid
        },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('info', 'Something went wrong, please try again');
            } else {

                $('#displayProfile').html(data);
            }
        }
    });
});



$(document).on('click', '.getmypartners', function() {

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


function loadgrouprequestclients() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { srchnewusers: 1 },
        success: function(data) {
            $('#newuserlist').html(data);
        },
        error: function() {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}
$(document).on('click', '.newSignup', function() {
    loadgrouprequestclients();
});

$(document).on('change', 'input[name=grpstatus]:radio', function() {

    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { updgrpstate: 1, grpstatus: this.value, userid: $(this).data('userid') },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'User has been approved in the group!');
            //reload group un-approved users 
            loadgrouprequestclients();
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


function autoComplete() {

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

//saving post
$(document).on('click', '#savepost', function() {
    //load existing blog manages
    var title = $('#posttitle').val();
    var post = CKEDITOR.instances['postbody'].getData();

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { savepost: 1, title: title, content: post },
        success: function(data) {
            alertFunc('success', 'Post Updated Successfully!')
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });

});

//select post for editing 
$(document).on('click', '.editpost', function() {
    //load existing blog manages
    waitFunc('enable');
    var postid = $(this).data('postid');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getpostforediting: 1, postid: postid },
        success: function(data) {
            waitFunc('');
            var results = JSON.parse(JSON.stringify(data));
            $('#postid').val(results.post_id);
            $('#editposttitle').val(results.post_title);
            CKEDITOR.instances['editpostbody'].setData(results.post_content);
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


$(document).on('click', '#updatepost', function() {
    //load existing blog manages
    var title = $('#editposttitle').val();
    var post = CKEDITOR.instances['editpostbody'].getData();
    var id = $('#postid').val();

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { savepost: 2, title: title, content: post, postid: id },
        success: function(data) {
            alertFunc('success', 'Post Updated Successfully!')
        },
        error: function(a, b) {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});


$(document).on('change', 'input[name=poststatus]:radio', function() {

    var status = this.value;
    var postid = $(this).data('postid');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { savepost: 3, status: status, postid: postid },
        success: function(data) {

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
$(document).on('click', '#postcomment', function() {
    //load existing blog manages
    waitFunc('enable');
    var postid = $('#postid').val();
    var name = $('#name').val();
    var email = $('#email').val();
    var comment = $('#commentbody').val();

    if (name.length = 0 || email.length == 0 || comment.length == 0) {
        alertFunc('danger', 'Mandatory comment fields are missing!');

    } else {
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
        url: 'includes/ajax.php',
        data: { getratedpartners: 1, selGrp: selGrp, selVoc: selVoc },
        success: function(data) {
            waitFunc('');
            $('#getratedpartners').html(data);
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
function raiseloyaltypoint(point, description) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { loyaltypoint: 1, point: point, description: description },
        success: function(data) {
            alertFunc('success', 'You have been awarded 10 points!');
        }
    });
}


//save referrals for the new know added
function loadsuggestedrefferals(newknowid, professions, sourcezip) {
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
 

$(document).on('click', '.btncallmailsender', function() {

    $('#suggestedreferral').modal('show');
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


    $('#clientid').val(clientid);
    $('#connectemail').val(suggestemail);
    $('#connectname').val(suggestname);
    $('#suggestid').val(suggestid);
    $('#connectprofession').val(profession);
    $('#connectphone').val(phone);
    $('#introduceto').html(  "<i class='fa fa-user dark'></i> " + introduceto + "<br/><i class='fa fa-envelope dark'></i> " + to + "<br/><i class='fa fa-phone dark'></i> " + introphone );
    $('#receipent').val(to);
    $('#receipentname').val(introduceto);
    $('#receipentprof').val(introprofession);
    $('#receipentphone').val(introphone);
    $('#cc1').val(cc1);
    $('#ccname1').val(ccname1);
    $('#mailogid').val(refintroid); 
}); 

$(document).on('click', '.btnremsuggestion', function() {
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

function resultreset(control, event)
{
    $('.' + control).trigger(event);
}

$(document).on('click', '#sendsuggestedreferral', function() 
{
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
        success: function(data) 
        {
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
            ccname1: ccname1,
            templateid:templateid
        },
        success: function(data) 
        { 
		console.log(data)
            if (data == '1')
                alertFunc('success', 'Introductory email already sent!');
            if (data == 'success')
                alertFunc('success', 'Your introduction mail has been sent!'); 
            waitFunc('');
        }
    });

    //close mail sending dialog
    $('#suggestedreferral').modal('hide');
});

$(document).on('click', '.showreferrals', function() {
    var pagesize = $(this).data('pagesize');
    if (pagesize == 0) pagesize = 10;
    var pageno = $(this).data('pageno');
    if (pageno == 0) pagesize = 1;

    $('#cpage').html(pageno);
    $('.savesuggestcpage').data('pageno', pageno);

    $('#processing').modal({ backdrop: true, keyboard: false });
    var json_data;


    setTimeout(function() {

        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { scanzipdistance: 1 },
            success: function(data) {
                json_data = JSON.parse(data);
            },
            complete: function() {
                if (json_data.length > 0) {
                    var cnt = 0;
                    $.each(json_data, function(arrayID, refrow) {
                        //first calculate distance in local 

                        $.ajax({
                            type: 'post',
                            url: 'includes/ajax.php',
                            data: { checkdistancelocal: 1, refid: refrow.id, source: refrow.source, target: refrow.target },
                            success: function(data) {
                                if (data == 0) {
                                    $.ajax({
                                        type: 'post',
                                        url: 'http://egressacademy.in/api/calc_distance.php',
                                        data: { caldistance: 1, source: refrow.source, target: refrow.target },
                                        success: function(data) {
                                            var distance = JSON.parse(data).distance;
                                            console.log(refrow.source + " " + refrow.target + " Row ID:" + refrow.id + " " + distance + "<br/>");
                                            $.ajax({
                                                type: 'post',
                                                url: 'includes/ajax.php',
                                                data: { updatedistance: 1, refid: refrow.id, distance: distance }
                                            });

                                            if (distance > 30) {
                                                //Update distance trigger 
                                                $.ajax({
                                                    type: 'post',
                                                    url: 'includes/ajax.php',
                                                    data: { delreferrals: 1, refid: refrow.id, distance: distance }
                                                });
                                            }

                                        }
                                    });
                                }
                            }
                        });


                        cnt++;
                        if (cnt > 30) //process only 30 records at a time to try not to reach google limit instantly
                        {
                            return false;
                        }

                    });
                }

                $('#processing').modal('hide');
            }
        });


        //loading in the page

        $('#cpage').html(pageno);
        $('.savesuggestcpage').data('pageno', pageno);

        $.ajax({

            type: 'post',
            url: 'includes/ajax.php',
            data: { readmailogs: 1, pagesize: pagesize, activepage: pageno },
            success: function(data) {
                $('#suggestedconnects').html(data);
                $('.tooltip').tooltipster({
                    animation: 'fade',
                    delay: 200,
                    theme: 'tooltipster-punk',
                    trigger: 'click'
                });
            } 
        }); 
    }, 2000);
 
});

$(document).on('click', '.btnselecttrigger', function() {
    var receipentemail = $(this).data('remid');
    var receipent = $(this).data('rname');
    $('.btnsendtrigger').attr('data-rname', receipent);
    $('.btnsendtrigger').attr('data-remid',receipentemail  );
    $("#modaltriggermailselect").modal('show');
})


$(document).on('click', '.btnsendtrigger', function() {

    var receipentemail = $(this).data('remid');
    var receipent = $(this).data('rname');
    var templateid = $(this).data('tid');

    waitFunc('enable');
    var receipentemail = $(this).data('remid');
    var receipent = $(this).data('rname');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { sendtrigger: 1, receipentemail: receipentemail, receipent: receipent, templateid: templateid },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'Trigger email is sent!');
        }
    });


})

// coded on 13-4-2017 
$(document).on('click', '#btnsavehelp', function() {
    waitFunc('enable');
    var helptitle = $('#helptitle').val();
    var helpvideo = $('#helpvideo').val();
    var id = $(this).data('id');
	
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { savehelpbutton: 1, helpvideo: helpvideo, helptitle: helptitle, id: id },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'Help Video saved!');
            $('#helptitle').val('');
            $('#helpvideo').val('');
            reloadhelpvideos();
        }
    });
})
function reloadhelpvideos() {
    var html = "";
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { gethelpvideos: 1 },
        success: function(data) {
            $.each(JSON.parse(data), function(key, value) {
                html += '<tr><td>' + value.helptitle + "</td><td>" + value.helpvideo + "</td>";
                html += "<td><button class='btn-primary btn btn-xs edithelpvideo' data-id='" + value.id + "'><i class='fa fa-pencil'></i></button>";
                //html += "<button class='btn-danger btn btn-xs removetrigger' data-id='" + value.id + "'><i class='fa fa-times-circle'></i></button></td></tr>";

            });

            $('#divhelpvideos').html(html);
        }
    });
}

$(document).on('click', '.edithelpvideo', function() {

    var helpid = $(this).data('id');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { gethelpvideo: 1, id: helpid },
        success: function(data) {
            record = JSON.parse(data);
            $('#btnsavehelp').attr('data-id', record[0].id);
            $('#helptitle').val(record[0].helptitle);
            $('#helpvideo').val(record[0].helpvideo);
        }
    });
})
//code ended over here

$(document).on('click', '#btnsavetemplate', function() {
    waitFunc('enable');
    var subject = $('#subject').val();
    var template = $('#template').val();
    var id = $(this).data('id'); 
    var templatetype = $('#templatetype').val();
    var email = CKEDITOR.instances['emailtemplate'].getData();
 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { savemailtemplate: 1, template: template, subject: subject, email: email, templatetype:templatetype, id: id },
        success: function(data) {
            waitFunc('');
            alertFunc('success', 'Email template saved!'); 
             
            $('#subject').val('');
            $('#template').val('');
            CKEDITOR.instances['emailtemplate'].setData('');
            reloadmailtemplates();
        }
    });
})

function reloadmailtemplates() {
    var html = "";
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { gettriggermails: 1 },
        success: function(data) {
            $.each(JSON.parse(data), function(key, value) {
                html += '<tr><td>' + value.mailtype  + "</td><td>" +  value.template + "</td><td>" + value.subject + "</td>";
                html += "<td><button class='btn-primary btn btn-xs editmailtemplate' data-id='" + value.id + "'><i class='fa fa-pencil'></i></button>";
                html += "<button class='btn-danger btn btn-xs removetrigger' data-id='" + value.id + "'><i class='fa fa-times-circle'></i></button></td></tr>";

            });

            $('#triggermails').html(html);
        }
    });
}

$(document).on('click', '.editmailtemplate', function() {

    var templateid = $(this).data('id');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getmailtemplate: 1, id: templateid },
        success: function(data) {
            record = JSON.parse(data);
            $('#btnsavetemplate').attr('data-id', record[0].id);
            $('#template').val(record[0].template);
            $('#subject').val(record[0].subject);
            CKEDITOR.instances['emailtemplate'].setData(record[0].mailbody);
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



$(document).on('click', '.btn-read-more', function() {
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

$(document).on('click', '.fetchknowstats', function() {

    var key = $(this).data('key');
    pagesize = 10;
    pageno = 1;

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { fetchknowstats: 1, key: key, pagesize: pagesize, activepage: pageno },
        success: function(data) {
            $('#reloadknowstatistics').html(data);
            $('.tooltip').tooltipster({
                animation: 'fade',
                delay: 200,
                theme: 'tooltipster-punk',
                trigger: 'click'
            });
        }
    });
    // is the display area 
})

$(document).on('click', '.viewpklink', function() {
    var username = $(this).data('name');
    $('#partnername').html(username);
    var pid = $(this).data('id');

    var pagesize = $(this).data('pagesize');
    if (pagesize == 0) pagesize = 10;
    var pageno = $(this).data('pageno');
    if (pageno == 0) pagesize = 1;

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { knowsuggesthistory: 1, pid: pid, pagesize: pagesize, activepage: pageno },
        success: function(data) {
            $('#knowsuggesthistory').html(data);

            $('.tooltip').tooltipster({
                animation: 'fade',
                delay: 200,
                theme: 'tooltipster-punk',
                trigger: 'click'
            });

        }
    });
});

$(document).on('click', '.loadinbox', function() {


    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { loadinbox: 1 },
        success: function(data) {
            $('#inboxgrid').html(data);
        }
    });
});


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

$(document).on('click', '.loadmyinbox', function() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { loadrefoutbox: 1 },
        success: function(data) {
            $('#myoutboxgrid').html(data);
        }
    });

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { loadrefinbox: 1 },
        success: function(data) {
            $('#myinboxgrid').html(data);
        }
    });
});

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
            console.log(data);
            waitFunc('');
            alertFunc('success', 'Loyalty point updated.');
            $('#pcircle' + id).html(newpoint);
        }
    }); 
});

$(document).on('click', '.newKnowEntries', function() {
    loadnewknowentries();
});

function loadnewknowentries() {
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { knowentry: 1 },
        success: function(data) {
            $('#newknowentrylist').html(data);
        },
        error: function() {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}
$(document).on('click', '.listconnects', function() {
    var id = $(this).data('id');
	
	$.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { listconnects: 1, id: id },
        success: function(data) {
            $('.connectionlists').html(data);

            $('.modallistconnects').modal('show');
        },
        error: function() {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

//manual suggestions
$(document).on('click', '.suggestConnects', function() {

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

$(document).on('click', '.wizstep1btn', function() {
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
        console.log(autosource);
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
$(document).on('click', '.ref_wizard', function() {

    var options = {
        url: function(phrase) {
            return "includes/autocomplete.php?phrase=" + phrase + "&format=json";
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
    waitFunc('enable');

    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { wiz_loadconnects: 1 },
        success: function(data) {

            /*
			var dropdown=$('#wiz_memberright');
			dropdown.empty(); 
			$.each(JSON.parse(data), function (key, value)
			{
				$("#wiz_memberright").append($('<option></option>').val(value.id).html(value.client_name));
			});
			
			//change to multiselect input
                var config =
				{
                  '.wiz_memberright'           : {},
                  '.wiz_memberright-deselect'  : {allow_single_deselect:true},
                  '.wiz_memberright-no-single' : {disable_search_threshold:10},
                  '.wiz_memberright-no-results': {no_results_text:'Oops, nothing found!'},
                  '.wiz_memberright-width'     : {width:"95%"}
                }
                for (var selector in config) {
                  $(selector).chosen(config[selector]);
                }
			*/
            waitFunc('');
        }
    });

});


$(document).on('click', '.wiz_step1_show_member', function() {
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
            //console.log(data)
            var dropdown = $('#wiz_memberleft');
            dropdown.empty();
            $.each(JSON.parse(data), function(key, value) {
                $("#wiz_memberleft").append($('<option></option>').val(value.id).html(value.username));
            });

            //change to multiselect input
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


//wizard wiz_memberright step  
$(document).on('click', '#wiz_memberright', function() {


})

$(document).on('click', '.wiz_step_show_summary', function() {

    var memberleft = $(".wiz_memberleft").chosen().val();
    var memberright = $("#rmid").val();

    $('#wizstep3').removeClass('disabled');
    $('#wizstep3').addClass('complete');
    $('#wizstep2').removeClass('disabled');
    $('#wizstep2').addClass('complete');


    if (!memberleft || !memberright) {
        alert('Missing member selection!')
    } else {
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { wiz_summary: 1, memberleft: memberleft, memberright: memberright },
            success: function(data) {
                $('#wiz_summary').html(data);
            }
        });
    }

})

$(document).on('click', '.wiz_send_referral_mail', function() {

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
            wiz_sendmail: 1,
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
            //if(data == '1')
            // alertFunc('success', 'Introductory email already sent!') ;
            // if(data == 'success')
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

$(document).on('click', '#selectgroup li', function() {

    var gid = $(this).data('gid');

    $('#fetchgroupmembers').data('gid', gid);



})

$(document).on('click', '#fetchgroupmembers', function() {
    //var groupid = $(this).data('gid' );
    var groupid = $('#selectgroup').val();
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { reftracker: 1, groupid: groupid },
        success: function(data) {
            $("#reftrackboard").html(data);
        }
    });

});

$(document).on('click', '.viewrefs', function() {
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
});

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

$(document).on('click', '.loadimportedknows', function() {
    getImportedKnows(1);
})

function getImportedKnows(page) {
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getImportedKnows: page },
        success: function(data) {
            waitFunc('');
            if (data == 'error') {
                alertFunc('danger', 'Something went wrong, please try again')
            } else {
                $('.manageimportedlist').html(data);
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}