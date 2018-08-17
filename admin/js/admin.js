//Create notifications div and hide all alert on re-load
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
var token;
var mrole;
var mgroups;
var mzip; 

//var aurl = "http://mycity.com/api/api.php/";  
 
var aurl = "//" + window.location.hostname + "/api/api.php/";

function nulltospace (value)
{
    return (value == null) ? "" : value
}
 
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
		mgroups= cvalue.grps;
		mzip = cvalue.mzip;
    } 
});

$(document).on('click', '.trendingsrclog', function()
{
	var page = $(this).attr('data-pg');
	
	if(typeof page === 'undefined') 
		page = 1;
	  
	showadminlogs( page ); 
});

function showadminlogs( page )
{ 
    waitFunc('enable'); 
    surl = aurl + 'trending/search/';
      
    $.ajax({
		
		type: 'post',
        url: surl,
        data: { gotopage : page },
        success: function(data) 
		{
			data = $.parseJSON(data);
			
			console.log(data.results);
			waitFunc('');
            if (data.error != 0 )
			{
				alertFunc('danger',  data.errmsg );
            }
            else 
            {    
                html   = "<table class='table table-alternate'>";
				html  += "<tr><td><strong>Member</strong></td><td><strong>Search Keyword</strong></td><td><strong>City or Zip</strong></td></tr>" ; 
                $.each(data.results, function(index, item)
				{
                    user_picture =   "images/"  +  item.image;  
					html  += "<tr><td>" + 
					"<img src='"  + user_picture  +  "' alt='"  +  item.username   + "' class='img-rounded' height='40' width='40'> "; 
					html  +=   item.username  + "</td>";
					html  += "<td>" + item.keyword  + "</td>"; 
					
					 
					if(item.city_zip && item.city_zip !== "null" && item.city_zip !== "undefined"   )
					{
						html  += "<td>" + item.city_zip  + "</td>";   
					}
					else 
					{
						html  += "<td>Not specified</td>"; 
					}
					
					html  += "</tr>";  
                }) 
                var pages = data.pages;

                if(pages > 1)
                {
					var prev =  page == 1 ? 1 :  parseInt(page) -1;
                    var next =  page ==  pages ?  pages :  parseInt(page) + 1; 
                    html  += "<tr><td colspan='4'><ul class='pagination '><li><a  class='trendingsrclog' data-func='prev' data-pg='" + prev + "'>«</a></li>";
                    for( i=1;  i <= pages;  i++)
                    {
                        active =  i ==  page ? 'active' : '';
                        html += "<li class='" + active + "'><a class='trendingsrclog' data-pg='" + i + "'>" + i + "</a></li>";
                    } 
                    html += "<li><a class='trendingsrclog'  data-func='next' data-pg='" + next +  "'>»</a></li></ul></td></tr>";
                }
				html  += "</table>" ; 
                $('#trendingsearchgrid').html(html); 
            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
} 



// Send Help Instruction
$(document).on('click', '.save_helpinstruction', function() {
    waitFunc('enable');
    var title = $(".help_title").val();
    var helpbody = $(".help_content").val();
    var faqid = $('#faqid').val();
    if (title != "" && helpbody != "") {
        $.ajax({
            type: 'post',
            url: aurl+ 'faqs/save/',
            data: { addhelpexp: '1',role:mrole,  title: title, helpbody: helpbody, faqid: faqid },
            success: function(data) 
            {
                data = $.parseJSON(data);
               
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

$(document).on('click', '.get_FAQ', function() {

    $(".help_title").val("");
    $(".help_content").val("");
    $("#faqid").val("0");
    
	type =1;
	
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
				html ='';
                $.each( data.result , function(idx, obj){ 
				
					html += "<div style='cursor:crosshair' data-pos='" +  obj.position  + "' data-id='" + obj.id  + 
					"' class='panel panel-default ui-sortable-handle'><div class='panel-body'><div class='row'><div class='col-md-3'>"  + 
					obj.helptitle  + "</div><div class='col-md-7'>"  + obj.helptext  + "</div>";
					
					html += "<div class='col-md-2'>" +
					"<button class=' btn-primary btn btn-xs editFaq' data-ques='" + obj.helptitle  + "' data-ans='" + obj.helptext  + "'  data-id='" + obj.id  + 
					"' style='margin-top: 10px '><i class='fa fa-pencil'></i></button>" +
					"<button class=' btn-danger btn btn-xs rmvFaq' data-id='" + obj.id  + "' style='margin-top: 10px '>" +
					"<i class='fa fa-times-circle'></i></button></div></div></div> </div>"; 
					})   
			}  
			$('#helptable').html(html); 
            alertFunc('success', 'FAQs retrieved successfully!');
        }, 
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again');
        } 
    });  
});


/* editing FAQ entry */
$(document).on('click', '.editFaq', function() {

    $('.help_title').val($(this).data('ques'));
    $('.help_content').val($(this).data('ans'));
    $('#faqid').val($(this).data('id'));

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


// Update  Lifestyle
$(document).on('click', '.updLifestyle', function() {
    var currLifestyle = $('.editLifestyle').val().trim(),
        currLifestyleVal = $('.editLifestyle').attr('data-val');
    if (currLifestyle == '') 
	{
        alertFunc('info', 'Please select the lifestyle first');
        return
    }
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  aurl +  'lifestyles/add/',
        data: { lifestyle: currLifestyle , role: mrole, currlifestyle: currLifestyleVal },
        success: function(data) {
            data = $.parseJSON(data);
            
                        waitFunc('');
                        if (data.error != 0 ) {
                            alertFunc('danger',  data.errmsg );
                        } else {
                            getLifestyles();
                            alertFunc('success',   data.errmsg);
                           
                        }
                 
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});
// Add New Group
$(document).on('click', '.addNewLifestyle', function() {
    var lifestylename = $('.lifestylename').val().trim();
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  aurl +  'lifestyles/add/',
        data: { lifestyle: lifestylename , role: mrole, currlifestyle:0 },
        success: function(data) {
            data = $.parseJSON(data);
            
                        waitFunc('');
                        if (data.error != 0 ) {
                            alertFunc('danger',  data.errmsg );
                        } else {
                            getLifestyles();
                            alertFunc('success',   data.errmsg);
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
        url: aurl + 'groups/save/', 
        data: {   id: currGrpVal , groupname: currGrp, role: mrole}, 
        success: function(data) {
            waitFunc('');
            data = $.parseJSON(data);
            if (data.error == 0)
            { 
                getAlGroups();
                alertFunc('success',  data.errmsg );
                $('.newGrpVal').val('');
            }else 
            { 
                alertFunc('danger', data.errmsg );
            }  
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});



//code ended over here 
$(document).on('click', '#btnsavetemplate', function() {
    waitFunc('enable');
    var 
    subject = $('#subject').val();
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

$(document).on('click', '.editmailtemplate', function()
{
    var templateid = $(this).data('id'); 
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { getmailtemplate: 1, id: templateid },
        success: function(data)
        {
            record = JSON.parse(data);
            $('#btnsavetemplate').attr('data-id', record[0].id);
            $('#template').val(record[0].template);
            $('#subject').val(record[0].subject);
            CKEDITOR.instances['emailtemplate'].setData(record[0].mailbody);
        }
    });
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



$(document).on('click', '.shownewsignups', function() 
{
    var startdate = $('#startDate').val();
    var enddate = $('#endDate').val();
    shownewsignups(1  , startdate ,  enddate); 

});

//LinkedIn Contact pagination
$(document).on('click', '.newsignpaginate li', function() {
    var page = $(this).find('a').attr('data-pg'); 
    var startdate = $('#startDate').val();
    var enddate = $('#endDate').val();
    shownewsignups( page  , startdate ,  enddate);  
}); 

function shownewsignups(page , startdate ,  enddate ) 
{
    $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { newsignups: 1 , page:page,   startdate:startdate, enddate:enddate },
            success: function(data) {
                $('#newsignups').html(data);
            },
            error: function()
            {
                alertFunc('info', 'Something went wrong, please try again')
            }
    });
} 

$(document).on('click', '.searchpartners', function() 
{ 
    //var groupid = $(this).data('gid' );
    var rrvocation = $('#rrvocation').chosen().val() + '';
    var  rrselectgroup = $('#rrselectgroup').chosen().val() + '';
    loadnewknowentries( rrvocation, rrselectgroup);  
});

function loadnewknowentries(vocation, group) 
{
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { knowentry: 1, vocation:vocation, group: group },
        success: function(data) {
            $('#newknowentrylist').html(data);
        },
        error: function()
        {
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
}

$(document).on('click', '.listconnects', function()
{
	var id = $(this).data('id');
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { listconnects: 1, id: id },
        success: function(data)
		{
			$('.connectionlists').html(data);
			$('.modalreferalintrolist').modal('show');
        },
        error: function() 
		{
			alertFunc('info', 'Something went wrong, please try again')
        }
    });
}); 

$(document).on('click', '#selectgroup li', function() 
{
	var gid = $(this).data('gid'); 
    $('#fetchgroupmembers').data('gid', gid); 
})

$(document).on('click', '#fetchgroupmembers', function() 
{
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


$(document).on('click', '.3tperformances', function() 
{
	prepareparticipantsreport(1, 1) ;
});	


function prepareparticipantsreport(page, program)
{
	$('.tl-box').hide(''); 
	$.ajax({
		type: 'post',
		url: aurl + 'program/member/performance/',
		data: {page: page, program: program, mid: 0 },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			 
			
			html = "<table class='table table-responsive'>";
			html += "<tr ><th style='width: 120px'></th><th style='width: 220px'>Name</th><th  >Relations</th> </tr>"  ;  
			$.each(data.results, function (index, item) 
			{
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td style='width: 120px'><img src='"  + user_picture  +  "' alt='"  +  item.un   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td style='width: 220px'>" + item.un + "</td>"; 
				html += "<td  >"  ; 
				if(item.relations != 'na')
				{
					$.each(item.relations, function(k, relitem){
						html += "<span class='nlink showrelpr' data-id='" + item.b + "' data-relid='" + relitem.a + "'>" + relitem.b + "</span> "  ;  
					})
				}
				else
				{
					html += "No relation added";
				}
				
				html +=  "</td>";  
				html +=  " </tr>";
			});
			 
			html += "</table>";	
			$('#progpartrptgrid').html(html);  
		}
	}); 
}


$(document).on('click', '.showrelpr', function()
{
	var relid  = $(this).attr('data-relid');
	var name = $(this).html();
	var pid = 1;
	var ppid = $(this).attr('data-id');
	
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/checkrelation/',
		data:  { idtotrack:relid, mid: ppid, pid: 1 } , 
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
				showprogramreport(pid, relid, name, ppid);
			}
		} 
	});
	
})


function showprogramreport(pid, cid, name, ppid)
{  
	$('#p_rel_track').html("<div ><img   src='../images/processing.gif' alt='Loading ...' width='160px' /></div>");
	$('.tl-box').hide();
	$('#progrel-tl').empty(); 
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/getassignedquestions/',
		data: {  pid : pid , mid:ppid, relid:cid},
		success: function (data) 
		{
			data = $.parseJSON(data);
 	
			if(data.count > 0)
			{ 
				$.each(data.results, function(index, item)
				{
						
					if( item.a == ""|| item.a == null )
					{
						processed ='';  
						buttons  = "<hr/><button data-ppid='"+ ppid +"'  data-relid='" +  cid + "' class='btn btn-primary btn-xs btnnoti_rel'>Notify Pending Action</button>" ;
						ans='';
					}
					else 
					{
						processed='processed'; 
						buttons  ='';
						ans= "<br/><strong>ANSWER: </strong><span>" + item.a + "</span>"
					}
					
					nulitem = "<li class='" +  processed + "'><span></span>" ;
					nulitem += "<div class='title'>Question #" + (index +1) + "</div>" + 
							"<div class='info'>"+ item.q  + ans;
					
					nulitem +=   buttons + " </div>" + "</li>" ;
					$('#progrel-tl').append(nulitem);  
				}) 
				
				$('#progrel-tl').attr('data-seq', data.count); 
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
				
			    $('#progrel-tl').append(nulitem);
				$('#progrel-tl').attr('data-seq', 0); 				
			} 
			 
			$('.tl-box').show(); 
			$('#3tp_relprog').html(" for " + name );
			$('#p_rel_track').html("");
		}
	});	 
}


$(document).on('click', '.loadprogramquestions', function() 
{
	relaodallquestions( );
});

function relaodallquestions( )
{
	$.ajax({
		type: 'post',
		url: aurl + 'program/getallquestions/', 
		success: function (data) 
		{
			data = $.parseJSON(data);
			html =  "<table class='table table-responsive table-bordered'>";
			html += "<tr ><th>Sl. No.</th><th>Question</th> <th>Edit</th><th>Delete</th></tr>"  ;  
			$.each(data.results, function (index, item) 
			{  
				html += "<tr id='row" + index + "'>" + 
				"<td>" + (index+1 )+ "</td>" + 
				"<td>" + item.q + "</td>" +  
				"<td>" +
				"<button type='button' class='btn btn-primary btn-xs editclques' data-qid='" +  item.i + "'  data-pid='" +  item.p + "' data-qs='" +  item.q + "'> Edit</button>" +
				"</td><td><button type='button' class='btn btn-danger btn-xs delclques' data-qid='" +  item.i + "'  > Delete</button>" +
				"</td>"  ; 
				html +=  " </tr>";
			}); 
			html += "</table>";
			  	
			$('#progquestions').html(html);  
		}
	}); 
}

$(document).on('click', '.editclques', function()
{
	var quesid = $(this).attr('data-qid'); 
	var pid = $(this).attr('data-pid'); 
	var question = $(this).attr('data-qs');
	 
	$('#tbquest').val(question);
	$('#btnsavequest').attr('data-prog',  pid );  
	$('#btnsavequest').attr('data-qid',  quesid );  
})

$(document).on('click', '#btnsavequest', function(){
	var pid =  $('#programname').val(); 
	var question = $('#tbquest').val(); 
	var qid = $(this).attr('data-qid');
	if(typeof(qid) === 'undefined' || qid == '')
	{
		qid=0;
	}
	   
	$.ajax({
		type: 'post',
		url: aurl + 'member/program/question/save/',
		data:  {pid:pid, question:question, qid: qid} , 
		success: function(data) 
		{
			data = $.parseJSON(data);  
			waitFunc(''); 
			alertFunc('success', data.errmsg);   
			relaodallquestions( );
		} 
	});  
	
})
$(document).on('click', '.delclques', function()
{
	var quesid = $(this).attr('data-qid');
	 
	confFunc('This action is irreversible and will delete the question assigned to client. <br/><br/> Are you sure you want to DELETE the selected question?', function() 
	{
        $.ajax({
			type: 'post',
			url: aurl + 'tools/deleterow/',
			data:  { tn:'m_p_q', trn: quesid } , 
			success: function(data) 
			{
				data = $.parseJSON(data);  
				waitFunc('');   
				relaodallquestions(); 
			} 
		});
    });
	
})


$(document).on('click', '.loadprogramparticipants', function() {
	
	var page = $(this).attr('data-page' );
	if(typeof page ==='undefined' || page =='')
	{
		page = 1;
	}
	
	loadprogramparticipants(page, 1); 
})

function loadprogramparticipants(page, program)
{  

	$.ajax({
		type: 'post',
		url: aurl + 'program/getmembers/',
		data: {page: page, program: program },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Email</th><th>Total Relations</th><th>Action</th></tr>"  ;  
			$.each(data.results, function (index, item) 
			{
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td><img src='"  + user_picture  +  "' alt='"  +  item.un   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.un + "</td>" + 
				"<td>" + item.e + "</td>" + 
				"<td>" + (item.tq == 0 ?'<span class="badge badge-red"> New Signup</span>' : item.tq ) + "</td>" + 
				"<td>";
				html += "<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" +
				"<ul class='dropdown-menu'> " +
				"<li><a href='#memquestions' data-toggle='tab' class='getmemquestions' data-prog='"+  item.p + "' data-id='" + item.c + "' data-name='" + item.un + "'  >Add Questions</a></li>" +
				"</ul> " ;
				html +=  "</div></td></tr>";
			});
			
			var pages = data.page ;
			if(pages > 1)
			{
				var prev =  page == 1 ? 1 :  parseInt(page) -1;
				var next =  page ==  pages ?  pages :  parseInt(page) + 1; 
				html  += "<tr><td colspan='5'><ul class='pagination progmems'><li><a data-func='prev'   data-pg='" + prev + "'  >«</a></li>";
				for( i=1;  i <= pages;  i++)
				{
					active =  i ==  page ? 'active' : '';
					html += "<li class='" + active + "'><a  data-pg='" + i + "' >" + i + "</a></li>";
				} 
				html += "<li><a data-func='next'  data-pg='" + next +  "'  >»</a></li></ul></td></tr>";
				 	 
			}
			html += "</table>";
			$('#progmems').html(html);  
		}
	}); 
}

$(document).on('click', '.getmemquestions', function() 
{
	var pid = $(this).attr('data-prog');
	var mid = $(this).attr('data-id');
	var mname = $(this).attr('data-name');
	//relaodquestions( pid, mid );
}); 

function relaodquestions( pid, mid )
{
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/getquestions/',
		data: {pid: pid, mid: mid },
		success: function (data) 
		{
			data = $.parseJSON(data);  
			html = " <div class='panel panel-default  panel-success'>";
			html +="<div class='panel-heading'><h2>Questions Assigned to Participant</h2></div><div class='panel-body'>";
			 
			html += "<table class='table table-responsive table-bordered'>";
			html += "<tr ><th>Select</th><th>Question</th><th>Answer</th></tr>"  ;  
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
				"<td>" + item.q + "</td>" + 
				"<td>" + ( item.a  == null  || item.a == '' ? 'No Answered' :  item.a ) + "</td>" ; 
				html +=  " </tr>";
			});
			
			html += "<tr >"+
			"<td colspan='4' >" +
			"<button class='btn btn-primary btn-xs btnassignpqs' data-pid='" + pid +"' data-mid='" + mid +"'>Assign Questions</button>" + 
			
			"</td></tr>"  ;  
			
			html += "</table>"; 
			
			html +="<strong>Selected questions will be asked to the participant. If you want to remove a question from asking to the selected participant, simply deselect the question.</strong>" ;
			
			html +="</div></div>";	
			$('#memquestions').html(html);  
		}
	}); 
}

$(document).on('click', '.viewunfinishedsignup', function() {
    var from = $('#tbfrom').val() ;
    var to = $('#tbto').val() ;
    var page = $(this ).attr('data-page') ;
    loadunfinishsignups(page)
});

$(document).on('click', '.pagination.ufsu li a', function(){
    var page = $(this ).attr('data-page') ;
    loadunfinishsignups(page)
})

function loadunfinishsignups(page)
{ 
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'signups/incomplete/',
        data: {  goto:page },
        success: function(data) {
            data = $.parseJSON(data); 
          
            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            }
            else 
            {
                html  ="<table class='table table-bordered table-colored'><tr><td>Sl. No.</td><td>Signup Email</td><td>Signup Date</td><td>Action</td></tr>";
                i=1;
                $.each(data.results, function(index, item)
                { 
                    html  += '<tr><td>' +  i  + "</td><td>"+  item.user_email + "</td><td>"+  item.createdon + "</td>"; 
                    html  += "<td><button data-id='" + item.id + "'  data-email='" + item.user_email + "' class='btn btn-primary btn-sm btncontactsignup'><i class='fa fa-envelope'></i></button></td></tr>";
                    i++;
                })

                html += "</table>";
                 

                prev =  (page == 1) ? 1 :  parseInt(page) -1;
                next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1;  
                 
                html += " <ul class='pagination ufsu'><li>" +
                    "<a    data-func='prev' data-page='" + prev + "'>«</a></li>";
                    for( i=1;  i<= data.pages;  i++){
                        
                          active =  i == page ? 'active' : '';
                          html +=  "<li class='" + active + "'><a  data-page='"+i   +"'>"+ i 
                        +"</a></li>";
                    }
                    html += "<li><a  data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";  
 

                $("#unfinishedsignup").html( html );
                alertFunc('success',   data.errmsg);
                 

            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    }); 
}
 

$(document).on('click', '.btnnoti_rel', function(){
	
	var relid  = $(this).attr('data-relid');
	var ppid  = $(this).attr('data-ppid'); 
	$.ajax({
		type: 'post',
		url: aurl + 'program/participant/sendnotice/',
		data:  { relid:relid, mid: ppid } , 
		success: function(data) 
		{
			data = $.parseJSON(data);  
			
			console.log(data); 
			alertFunc('success',  data.errmsg);   
		} 
	});
	
})


$(document).on('click', '.reversetrackpartner', function() { 
    var searchkey = $('#tbknowsearchkey').val();  
    var location = $('#reversetracklocation').val() + '';
    var vocations = $('#reverselookupvoc').val() + '';
    var filtertags = $('#reversetracktags').val() + '';
	var filterlifestyle = $('#reversetracklifestyle').val() + ''; 
	var filterzip = $('#tbzip').val()  ;
    reloadreversetrackknow( searchkey,vocations, location, filtertags,filterlifestyle,  1, filterzip )
});

$(document).on('click', '.reversetrackpager li a', function() { 
    var searchkey = $(this).attr('data-key');
    var goto = $(this).attr('data-pg'); 
    var vocations = $('#reverselookupvoc').val() + '';
    var location = $('#reversetracklocation').val() + '';
    var filtertags = $('#reversetracktags').val() + '';
	var filterlifestyle = $('#reversetracklifestyle').val() + ''; 
	var filterzip = $('#tbzip').val()  ;
    reloadreversetrackknow( searchkey, vocations,location, filtertags,filterlifestyle,  goto , filterzip)
});
  
$(document).on('click', '.btn_rtk_gotopage', function()
{
  
	var goto = $('.rtk_gotopage').val();  
	var searchkey = $('#tbknowsearchkey').val();
    var vocations = $('#reverselookupvoc').val() + '';
    var location = $('#reversetracklocation').val() + '';
    var filtertags = $('#reversetracktags').val() + '';
	var filterlifestyle = $('#reversetracklifestyle').val() + '';
	var filtercity = $('#dbcity').val() + '';
	var filterzip = $('#tbzip').val()  ;
    reloadreversetrackknow( searchkey, vocations,location, filtertags,filterlifestyle,  goto,   filterzip); 
});
  

function reloadreversetrackknow(searchkey,vocations, location, tags, lifestyle,  goto,   zip)
{  
    $.ajax({
        type: 'post',
        url: aurl + 'knows/search/',
        data: { key: searchkey, goto: goto , vocations:vocations, location:location, tags: tags, lifestyle:lifestyle},
        success: function(data)
        {
            
            data = $.parseJSON(data);   

            if(data.error == 1 )
            {
                $('#rtmember').html( '');
                alertFunc('danger',  'Something went wrong, please try again');
            }
            else  if(data.error == 10 )
            {
                $('#rtmember').html( ''  );
                alertFunc('info',  data.errmsg);
            }
            else 
            {
                var html = "<table class='table table-colored table-bordered marg1'>";
                html += "<tr id='$rand-$id'>" +
                "<th>Know Information</th>" +
                "<th>Partner/User Who Knows the Contact</th>"  
                "</tr> " ;
    
                $.each(data.results, function (index, item) 
                {
                    client_name = item.client_name ;
                    client_profession = item.client_profession ;
                    client_phone =", <strong>Phone: </strong> " + item.client_phone  ;
                    client_email =  item.client_email ;
                    tags =  item.tags ;
                    if(item.ranking == null)
						rate =   0 ;
                    else 
						rate = item.ranking;
                   
                    html += "<tr id='$rand-$id'>" +
                        "<td>"  + client_name  + "<br/><strong>Email: </strong> " + 
                        client_email +  "<span id='spanknowphone" + item.knowid + "'>" + client_phone + "</span>" +
						"<span class='hidden' id='knowphone" + item.knowid + "'><input class='inp-xs' id='tbknowphone" + item.knowid + "' /><button class='btn-xs btnupdateknowphone' data-kid='" + item.knowid + "' ><i class='fa fa-check'></i></button></span> <span data-kid='" + item.knowid + "' id='btneditphone" + item.knowid + "' class='btn-xs btneditphone' title='Click to edit'><i class='fa fa-pencil'></i></span><br/>" +
                        "<strong>Profession:</strong> <span id='knowvocprint"+ item.knowid +"'>" + 
                        client_profession +  "</span> <span data-kid='" + item.knowid + "' id='btneditvoc" + item.knowid + "' class='btn-xs btneditvoc' title='Click to edit'><i class='fa fa-pencil'></i></span> <br/>" + 
						"<div class='hidden editvoc_box' id='knowvoc" + item.knowid + "'>" +
						"<div class='form-group'>" +
						"<label  >Select Vocation(s):</label>" +
						"<select  class='form-control chosen-select reversevocs' data-placeholder='Specify Vocations ...'  multiple id='dbvoca" + item.knowid + "' ></select>" +
						"</div>" +
						"<div class='form-group'>" +
						"<button class='btn btn-primary btnupdatevoc' data-kid='" + item.knowid + "' >Update</button> " + 
						"<button class='btn btn-danger btn_cancelvoc' >Cancel</button>"+
						"</div>" +  
						"</div>" +
						"<strong>City:</strong> <span id='knowcpr" + item.knowid + "'>" +  item.client_location + "</span> " +
						"<strong>ZIP:</strong> <span id='knowzpr" + item.knowid + "'>"  +  item.client_zip  + "</span>" + 
						"<div class='hidden edit_box' id='knowcityzip" + item.knowid + "'>" +
						"<h5>Update City &amp; Zip</h5>" +
						"<div class='form-group'>" +
						"<label  >Select City:</label>" +
						"<select class='form-control' id='dbknowcity" + item.knowid + "' ></select>" +
						"</div>" +
						"<div class='form-group'>" +
						"<label  >Zip Code:</label>" +
						"<input class='form-control' id='tbknowzip" + item.knowid + "' />" +
						"</div>" +
						"<div class='form-group'>" +
						"<button class='btn btn-primary btnupdateknowcz' data-kid='" + item.knowid + "' >Update</button> " + 
						"<button class='btn btn-danger btn_cancelcz' >Cancel</button>"+
						"</div>" + 
						"</div>" +
						" <span data-kid='" + item.knowid + "' id='btneditcz" + item.knowid + "' class='btn-xs btneditcz' title='Click to edit'><i class='fa fa-pencil'></i></span>" +
						"<br/>" + 
						"<strong>Tags:</strong> <span id='knowtagprint" + item.knowid + "'>" +  (tags === null   ?  "Not Specified"   :  tags ) + "</span>" +
						"<span class='hidden' id='knowtag" + item.knowid + "'><p>Select Tags:</p>" +
						"<select data-placeholder='Specify Tags ...'  multiple id='reversetrackedittags" + item.knowid + "'  class='form-control reversetrackedittags'></select><br/><input type='hidden' id='oldtags" +  item.knowid + "' value='" + tags +"' /><button class='btn-xs btnupdateknowtag' data-kid='" + item.knowid + "' >Update Tags</button></span> <span data-kid='" + item.knowid + "' id='btnedittag" + item.knowid + "' class='btn-xs btnedittag' title='Click to edit'><i class='fa fa-pencil'></i></span><br/>" +   "<br/>" + 
                        "<strong>User Rating:</strong> <span class='badge'>" + rate + "</span> " ;
						 
						html += "</td><td style='text-align:left !important'>"  + item.username  + "<br/>"  + 
                        "<strong>Email: </strong>" + item.user_email + " <strong>Phone:</strong> " + item.user_phone +"<br/>" +
                        " <strong>User Package:</strong>" +   item.user_pkg + 
						"<br/><button data-id='" + item.id + "' class='btn btn-sm vuconcount'>View Common Connections</button>" +
						"</td>" + 
                        "</tr> " ;
                });
				 html += "</table>";
				var pages = data.pages; 
               page = goto;
			   if(pages > 1)
				{
					prev =  (page == 1) ? 1 :  parseInt(page) -1;
					next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1;
					
					html += " <ul class='pagination reversetrackpager' ><li>" +
						"<a   data-func='prev' data-page='" + prev + "'>«</a></li>";
						 
						if( page > 50) 
						{
							html += "<li><a  data-func='previous' title='Show Previous 10 Records' data-pg='1' data-key='" + searchkey +"'  > ... </a></li>";
						
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
								 html += "<li class='" + active + "'><a  data-key='" + searchkey +"'  data-pg='" + j  + "' >" + j  + "</a></li>";
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
								html +=   "<li class='" + active + "'><a data-key='" + searchkey +"'  data-pg='" + i  + "'  >" + i  + "</a></li>";
							 }
						}
						
						if( parseInt(page)  < ( pages - 50 ) )
						{
							html +=  "<li><a data-func='next' title='Show last few pages' data-key='" + searchkey +"'  data-pg='" + pages + "'> ... </a></li>"; 
						}
						
						html += "<li> <input class= 'form-control rtk_gotopage'  type='text' style='width: 120px; height: 32px; margin-top: 2px; margin-right: 5px; float: left; display: inline-block;' placeholder= 'Go to page ...' > </li>";
						html += "<li> <input class='btn btn_rtk_gotopage' data-key='" + searchkey +"'  type='button'  value='Go' style='width: 50px; float: left; height: 32px; margin-top: 2px; display: inline-block;  background-color: #2e353d; color: #fff;'   > </li>";
						html += "<li><a  data-func='next' title='Next Page' data-key='" + searchkey +"' data-pg='" + next +  "'>»</a></li> "; 
						html += "<li><a  data-func='last' title='Last Page' data-key='" + searchkey +"' data-pg='" + pages +  "'>Last Page</a></li> ";
						
					  
					html += " </ul> ";
				}
                $('#rtmember').html( html );
            } 
        }  
    }); 
}

$(document).on('click', '.copyknowfields', function()
{
	event.preventDefault();
    waitFunc('enable');
	var sourceid= $(this).attr('data-kid');
	
	$.ajax({
        type: 'post',
        url: aurl + 'know/copyfields/',
        data: { id: sourceid },
        success: function(data)
		{
			waitFunc('');
			data = $.parseJSON(data); 
            alertFunc('success',   data.errmsg); 
        }
    });
}); 


$(document).on('click', '.btneditvoc', function()
{
	event.preventDefault();
    waitFunc('enable');
	var knowid = $(this).attr('data-kid'); 
	var crtvocs = $('#knowvocprint' + knowid).html(); 
	var temp = new Array();
	temp = crtvocs.split(',');  
	
	  
	$('#knowvoc' + knowid ).removeClass('hidden'); 
	var i=0;
	 
	
	$.ajax({
        type: 'post',
        url: aurl + 'vocations/',
        data: { id: 0 },
        success: function(data) {
         
		 
		 
          data = $.parseJSON(data); 
            var dropdown = $('#dbvoca' + knowid );
            dropdown.empty();
            $.each( data , function(key, value)
			{ 
				if(temp.length > 0)
				{
					for(var i = 0; i < temp.length; i++)
					{
					   if(temp[i] == value.voc_name) 
					   {
						   $("#dbvoca"+knowid ).append($('<option selected ></option>').val(value.voc_name).html(value.voc_name));
					   }
					   else 
					   {
						   $("#dbvoca"+knowid ).append($('<option   ></option>').val(value.voc_name).html(value.voc_name));
					   }
					} 
				}
				else 
				{
					$("#dbvoca"+knowid ).append($('<option  ></option>').val(value.voc_name).html(value.voc_name)); 
				}
				$("#dbvoca"+knowid ).trigger("chosen:updated");	
				 
            });  
            var config = {
				'.reversevocs': {},
				'.reversevocs-deselect': { allow_single_deselect: true },
				'.reversevocs-no-single': { disable_search_threshold: 10 },
				'.reversevocs-no-results': { no_results_text: 'Oops, nothing found!' },
				'.reversevocs-width': { width: "95%" }
			}
			for (var selector in config) {
				$(selector).chosen(config[selector]);
			}  
			
			waitFunc('');
        }
    });
	 
	$(this).addClass('hidden');   
});

$(document).on('click', '.btnupdatevoc', function()
{
	var knowid = $(this).attr('data-kid'); 
	$('#knowvoc' + knowid ).addClass('hidden'); 
	$('#btneditvoc' + knowid ).removeClass('hidden'); 
	var vocs =   $('#dbvoca' + knowid ).chosen().val() + ''; 
	$.ajax({
        type: 'post',
        url: aurl + 'member/know/updatevocation/',
        data: { vocs: vocs, knowid: knowid  },
        success: function(data)
		{
			data = $.parseJSON(data);
			waitFunc('');  
			$('#knowvocprint' + knowid ).html(  vocs);
			alertFunc('success',   data.errmsg); 
        } 
    });
	  
});

$(document).on('click', '.btn_cancelvoc', function()
{
	$('.editvoc_box'  ).addClass('hidden'); 
	$('.btneditvoc').removeClass('hidden');  
});


$(document).on('click', '.btnedittag', function()
{
	var knowid = $(this).attr('data-kid'); 
	$('#knowtag' + knowid ).removeClass('hidden');  
	$(this).addClass('hidden'); 
	 
	var oldtags = $('#oldtags' +knowid ).val(); 
	if(oldtags !== null)
	{
		var tagslist  = oldtags.split(',');
	} 
	 
	$.ajax({
        type: 'post',
        url: aurl + 'tags/',
        data: { tags:1 },
        success: function(data) {
         
          data = $.parseJSON(data);
       
            var dropdown = $('#reversetrackedittags' +knowid );
            dropdown.empty();
            $.each( data , function(key, value)
			{
				
				if(tagslist.length > 0)
				{
					for(var i = 0; i < tagslist.length; i++)
					{
					   if(tagslist[i] == value.tagname) 
					   {
						   $("#reversetrackedittags"+knowid ).append($('<option selected ></option>').val(value.tagname).html(value.tagname));
					   }
					   else 
					   {
						   $("#reversetrackedittags"+knowid ).append($('<option   ></option>').val(value.tagname).html(value.tagname));
					   }
					} 
				}
				else 
				{
					$("#reversetrackedittags"+knowid ).append($('<option  ></option>').val(value.tagname).html(value.tagname)); 
				}
				$("#reversetrackedittags"+knowid ).trigger("chosen:updated");	
				 
            });  
            var config = {
				'.reversetrackedittags': {},
				'.reversetrackedittags-deselect': { allow_single_deselect: true },
				'.reversetrackedittags-no-single': { disable_search_threshold: 10 },
				'.reversetrackedittags-no-results': { no_results_text: 'Oops, nothing found!' },
				'.reversetrackedittags-width': { width: "95%" }
			}
			for (var selector in config) {
				$(selector).chosen(config[selector]);
			}  
        }
    }); 
});


$(document).on('click', '.btnupdateknowphone', function()
{
	var knowid = $(this).attr('data-kid'); 
	$('#knowphone' + knowid ).addClass('hidden'); 
	$('#btneditphone' + knowid ).removeClass('hidden');
	 
	var phone = $('#tbknowphone' + knowid).val();
	  
	$.ajax({
        type: 'post',
        url: aurl + 'member/know/updatephone/',
        data: { phone: phone, knowid: knowid  },
        success: function(data)
		{
			data = $.parseJSON(data);
			waitFunc('');
			
			$('#spanknowphone' + knowid ).removeClass('hidden');  
			$('#spanknowphone' + knowid ).html(  ", <strong>Phone: </strong> " +  phone);
			alertFunc('success',   data.errmsg); 
        } 
    });
	 
});
$(document).on('click', '.btnupdateknowtag', function()
{
	var knowid = $(this).attr('data-kid'); 
	$('#knowtag' + knowid ).addClass('hidden'); 
	$('#btnedittag' + knowid ).removeClass('hidden');
	 
	var tag =   $('#reversetrackedittags' +knowid ).chosen().val() + '';
	 
	$.ajax({
        type: 'post',
        url: aurl + 'member/know/updatetags/',
        data: { tag: tag, knowid: knowid  },
        success: function(data)
		{
			data = $.parseJSON(data);
			waitFunc('');  
			$('#knowtagprint' + knowid ).html(  tag);
			alertFunc('success',   data.errmsg); 
        } 
    });
	  
});

$(document).on('click', '.btn_cancelcz', function()
{
	$('.edit_box'  ).addClass('hidden'); 
	$('.btneditcz'  ).removeClass('hidden'); 
});

$(document).on('click', '.btnupdateknowcz', function()
{
	var knowid = $(this).attr('data-kid'); 
	$('#knowcityzip' + knowid ).addClass('hidden'); 
	$('#btneditcz' + knowid ).removeClass('hidden'); 
	var city =   $('#dbknowcity' + knowid).val()  ;
	var zip =   $('#tbknowzip' + knowid).val()  ;
	 
	$.ajax({
        type: 'post',
        url: aurl + 'member/know/updatecityzip/',
        data: { city: city,zip:zip,  knowid: knowid  },
        success: function(data)
		{
			data = $.parseJSON(data);
			waitFunc('');  
			$('#knowcpr' + knowid ).html(  city);
			$('#knowzpr' + knowid ).html(  zip);
			alertFunc('success',   data.errmsg); 
        } 
    });
	  
});

$(document).on('click', '.btneditcz', function()
{
	var knowid = $(this).attr('data-kid'); 
	$('#knowcityzip' + knowid ).removeClass('hidden'); 
  
	$('#dbknowcity' + knowid).html($('#reversetracklocation').html());
	$(this).addClass('hidden');  
}); 


$(document).on('click', '.btneditphone', function()
{
	var knowid = $(this).attr('data-kid'); 
	$('#knowphone' + knowid ).removeClass('hidden');  
	$(this).addClass('hidden'); 
	
});
 
 $(document).on('click', '#btnsavetestimonial', function()
{ 
    event.preventDefault();
    waitFunc('enable');

    var summary = CKEDITOR.instances['testimonial_summary'].getData();
    var video  = $('#testimonial_video').val() ;
    var id  = $(this).data('id'); 
    $.ajax
    ({
        type: 'post',
        url: 'includes/ajax.php',
        data: { savetestimonial: 1 , summary: summary, video: video, testimonialid: id },
        success: function(data)
        { 
            waitFunc('');
            alertFunc('info', data )
 
            reloadtestimonial();
        }
    });
}) 

function reloadtestimonial()
{
    $.ajax
    ({
        type: 'post',
        url: 'includes/ajax.php',
        data: { reloadtestimonial: 1   },
        success: function(data)
        {  
           $('#divtestimonials').html(data); 
        }
    }); 
}

$(document).on('click', '.edittestimonial', function() {

    var testimonialid = $(this).data('id');

    var summary = $('.videosummary' + testimonialid).html();
    var videolink = $('.videolink' + testimonialid).html();
    $('#testimonial_video').val(videolink);
    CKEDITOR.instances['testimonial_summary'].setData(summary);

    $('#btnsavetestimonial').data('id', testimonialid)
})


$(document).on('click', '#btncanceltestimonial', function() {

     $('#testimonial_video').val('');
    CKEDITOR.instances['testimonial_summary'].setData('');
     $('#btnsavetestimonial').data('id', 0); 
})


$(document).on('click', '.deletestimonial', function() {

    var testimonialid = $(this).attr('data-id'); 
    confFunc('This action is irreversible and will delete all data related to this testimonial.<br/><br/> Are you sure you want to DELETE this testimonial?', function() {
        waitFunc('enable');
        $.ajax({
            type: 'post',
            url: 'includes/ajax.php',
            data: { delTestimonial: testimonialid },
            success: function(data) {
                if (data == 'user_error') {
                    alertFunc('danger', 'Something went wrong, please try again');
                } else {
                    alertFunc('success', 'Testimonial data deleted.'); 
                }
                reloadtestimonial();
                waitFunc('');
            },
            error: function() {
                waitFunc('');
                alertFunc('info', 'Something went wrong, please try again')
            }
        });
    });
});
$(document).on('click', '.btnsavesortingorder', function () 
{
	var tstrows = $('#divtestimonials > tr');
	waitFunc('enable');
	
	$('#divtestimonials > tr').each(function(i) 
	{
		var id = $(this).attr('data-id');  
		if(typeof id !== 'undefined')
		{
			$.ajax({
				type: 'post',
				url: aurl + 'testimonials/updateposition/',
				data: { pos:i,id:id },
				success: function (data) 
				{
					
				}
			}); 
		} 
    });
	waitFunc('');  
}) 



$(document).on('click', '.updTag', function() {
    var currTag = $('.editTag').val().trim(),
        currTagVal = $('.editTag').attr('data-val');
  
    if (currTag == '') {
        alertFunc('info', 'Please select a tag first');
        return
    }
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'tags/add/',
        data: { tagname: currTag, role: mrole, currTagVal: currTagVal }, 
        success: function(data) {
            data = $.parseJSON(data);
            
                        waitFunc('');
                        if (data.error != 0 ) {
                            alertFunc('danger',  data.errmsg );
                        } else {
                            getTags();
                            alertFunc('success',   data.errmsg);
                        }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
});

$(document).on('click', '.addTag', function() {
    var tagname = $('.tagname').val().trim();
   
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'tags/add/',
        data: { tagname: tagname, role: mrole, currTagVal: 0 },
        success: function(data) {
            data = $.parseJSON(data);

            waitFunc('');
            if (data.error != 0 ) {
                alertFunc('danger',  data.errmsg );
            } else {
                getTags();
                alertFunc('success',   data.errmsg);
            }
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('info',  'Something went wrong, please try again')
        }
    });
});

// Update/Edit Tags ********
$(document).on('change', '.fetchTag', function() {
    var currTag = $('.fetchTag option:selected').text();
    var currTagVal = $(this).val();
    if (currTagVal == 'null') {
        $('.editTag').val('');
        return;
    }
    $('.editTag').val(currTag).attr('data-val', currTagVal); 
});
// Update  Tag

// Get All vocation
function getTags() {
    $.ajax({
        type: 'post',
        url: aurl + 'tags/getall/', 
        success: function(data) {
            data = $.parseJSON(data);

            waitFunc('');
            if (data.error ==  1 ) {
                alertFunc('danger', 'Something went wrong, please try again');
            } else
            {
                html = '<option value="null">-Select Tag-</option>';
                $.each(data.results, function (index, item)
                {
                    html += '<option value="' + item.id +'">' + item.tagname +'</option>' ; 
                }) 

                $('.fetchTag').html( html );
            }
        },
        error: function() {
            waitFunc('');
            alertFunc('info', 'Something went wrong, please try again')
        }
    });
} 


$(document).on('click', '.cfg_fetchemails', function () 
{
	var page = $(this).attr('data-pg'); 
	if(typeof page == 'undefined' || page =='')
	{
		page =1;
	}  
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
				html += "<tr ><th>Email Heading</th><th>Action</th></tr>"  ; 
				$.each(data.results, function (index, item) 
				{
					html += "<tr id='row" + index + "'>" +  
					"<td>" + item.a + "</td>" +  
					"<td><button class='btn btn-primary btn-xs btne_email' data-id='"+item.id+"' data-a='"+item.a+"' data-b='"+item.b+"' >Edit</button></td>" + 
					"</tr>"; 
				});
				html += "</table>"; 
				$('#emailgrid').html(html); 
				
			} 
		}
	}); 
})

$(document).on('click', '.btnepsave', function () 
{
	var id = $(this).attr('data-id');
	
	if(typeof id === 'undefined' || id == '')
	{
		id=0;
	}
	  
	var heading = $('#emheading').val();
	var body =  CKEDITOR.instances['embody'].getData();  
	  
	$.ajax({
		type: 'post',
		url: aurl + 'emailsprogram/save/',
		data: { heading:heading,body:body , id:id },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			alertFunc('info',  data.errmsg  ); 
		}
	}); 
})
$(document).on('click', '.btne_email', function () 
{
	var id = $(this).attr('data-id');
	var a = $(this).attr('data-a');
	var b = $(this).attr('data-b');
	
	$('#emheading').val(a);
	CKEDITOR.instances['embody'].setData( b );
	$('.btnepsave').attr('data-id', id); 
})


$(document).on('click', '.actmempage li a', function () 
{
	var page1 = $(this).attr('data-pg1');
	var page2 = $(this).attr('data-pg2');
	var page3 = $(this).attr('data-pg3'); 
	var em_client = $(this).attr('data-client'); 
	pareparetimelinecanvas(page1, page2, page3, em_client); 
})

$(document).on('click', '#btn_srhclient', function () 
{
	var em_client = $('#em_client').val();
	var page = $(this).attr('data-pg'); 
	if(typeof page == 'undefined' || page =='')
	{
		page =1;
	}
	pareparetimelinecanvas(page, page,page, em_client);
}) 

function pareparetimelinecanvas(page1, page2,page3,  client='')
{
	 
	$('#tl_box').hide();
	$('#act_clients').empty();
	$('#inact_clients').empty();
	 
	$.ajax({
		type: 'post',
		url: aurl + 'emailprogram/allmembers/', 
		data: { page1:page1, page2:page2,page3:page3,  client:client  },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Email</th> <th>Select</th><th>Action</th></tr>"  ;  
			$.each(data.results, function (index, item) 
			{ 
				$('#act_clients').append( "<option value='" + item.a + "'>" + item.d + "</option>"  ); 
				user_picture = !( item.h ) ?  "images/no-photo.png"  : "https://mycity.com/assets/uploads/profiles/"  +  item.h ;   
				html += "<tr id='row" + index + "'>" + 
				"<td rowspan='2'><img src='"  + user_picture  +  "' alt='"  +  item.username   + "'  onerror='imgError(this);'   class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.d + "</td>" +
				"<td>" + item.b + "</td>" +  
				"<td><input type='checkbox'  name='cb_actmembers' value='" +  item.a  + "'> </td>" + 
				"<td>";
				html += "<div class='dropdown '><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" +
				"<ul class='dropdown-menu pull-right'> " +
				"<li><a  href='#tl_box' data-toggle='tab' class='btn_sl_acclient'  data-id='" + item.a + "' data-name='" + item.d + "'  >Email Timeline</a></li>" + 
				"<li><a  href='#menu71' data-toggle='tab' class='btn_slcvmclient'  data-id='" + item.a + "' data-name='" + item.d + "'  >Client Management</a></li>" +
				"<li><a  href='#' data-toggle='tab' class='btn_3tinvite'  data-id='" + item.a + "' data-name='" + item.d + "'  >Invite to 3 Touch Program</a></li>" +
				"</ul> " ;
				html +=  "</div></td></tr>";
				
				html += "<tr><td><strong>Snapshot:</strong></td><td colspan='5'>" + item.sh + "</td></tr>" ;  
				
			});
			
			if(data.page1 > 0)
			{			
				html  += "<tr><td colspan='3'></td><td colspan='2'> " +
				"<input type='button' class='btn btn-success btn_deac_acclient' data-s='10' value='Move to Member'  >" +
				" <input type='button' class='btn btn-danger btn_deac_acclient' data-s='100' value='Move to Ex-client'  > " +
				"</td></tr>";
			}
			
			var pages = data.page1;
			var prev =  page1 == 1 ? 1 :  parseInt(page1) -1;
			var next =  page1 ==  pages ?  pages :  parseInt(page1) + 1; 
			html  += "<tr><td colspan='5'><ul class='pagination actmempage'><li><a data-func='prev' data-pg1='" + prev + "' data-pg2='" + page2 + "' data-pg3='" + page3 + "' data-client='" + client + "'>«</a></li>";
			for( i=1;  i <= pages;  i++)
			{
				active =  i ==  page1 ? 'active' : '';
				html += "<li class='" + active + "'><a data-pg1='" + i + "' data-pg2='" + page2 + "' data-pg3='" + page3 + "' data-client='" + client + "'>" + i + "</a></li>";
			} 
			html += "<li><a data-func='next' data-pg1='" + next +  "' data-pg2='" + page2 + "' data-pg3='" + page3 + "' data-client='" + client + "'>»</a></li></ul></td></tr>";
			html += "</table>";	 	
			$('#actmembersgrid').html(html); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Email</th> <th>Select</th><th>Action</th></tr>"  ;  
			$.each(data.results_old, function (index, item) 
			{
				$('#inact_clients').append( "<option value='" + item.a + "'>" + item.d + "</option>"  );   
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td rowspan='2'><img src='"  + user_picture  +  "' alt='"  +  item.username   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.d + "</td>" +
				"<td>" + item.b + "</td>" +   
				"<td><input type='checkbox'  name='cb_actmembers'  value='" +  item.a  + "'> </td>" + 
				"<td>";
				html += "<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" +
				"<ul class='dropdown-menu'> " +
				"<li><a  href='#tl_box' data-toggle='tab' class='btn_sl_acclient'  data-id='" + item.a + "' data-name='" + item.d + "'  >Email Timeline</a></li>" + 
				"<li><a  href='#menu71' data-toggle='tab' class='btn_slcvmclient'  data-id='" + item.a + "' data-name='" + item.d + "'  >Client Management</a></li>" +
				"<li><a  href='#' data-toggle='tab' class='btn_3tinvite'  data-id='" + item.a + "' data-name='" + item.d + "'  >Invite to 3 Touch Program</a></li>" +
				"</ul> " ;
				html +=  "</div></td></tr>";
				
				html += "<tr><td><strong>Snapshot:</strong></td><td colspan='5'>" + item.sh + "</td></tr>" ;  
				 
			});
			if(data.page2 > 0)
			{
				html  += "<tr><td colspan='3'></td><td colspan='2'>" +
				" <input type='button' class='btn btn-primary btn_deac_acclient' data-s='1' value='Move to Active Member'  > " +
				" <input type='button' class='btn btn-success btn_deac_acclient' data-s='100' value='Move to Ex-client'  > " +
				" </td></tr>";
			}
			
			pages = data.page2; 
			var prev =  page2 == 1 ? 1 :  parseInt(page2) -1;
			var next =  page2 ==  pages ?  pages :  parseInt(page2) + 1; 
			html  += "<tr><td colspan='5'><ul class='pagination actmempage'><li><a data-client='" + client + "' data-func='prev' data-pg1='" + page1 + "' data-pg2='" + prev + "' data-pg3='" + page3 + "'>«</a></li>";
			for( i=1;  i <= pages;  i++)
			{
				active =  i ==  page2 ? 'active' : '';
				html += "<li class='" + active + "'><a data-client='" + client + "' data-pg1='" + page1 + "' data-pg2='" + i + "' data-pg3='" + page3 + "'>" + i + "</a></li>";
			}
			
			html += "<li><a data-client='" + client + "' data-func='next' data-pg1='" + page1 + "' data-pg2='" + next +  "' data-pg3='" + page3 + "'>»</a></li></ul></td></tr>";
			html += "</table>";	
			
			$('#inactmembersgrid').html(html); 
			
			
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Email</th><th>Select</th><th>Action</th></tr>"  ;  
			$.each(data.results_ex, function (index, item) 
			{
				$('#inact_clients').append( "<option value='" + item.a + "'>" + item.d + "</option>"  );   
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td rowspan='2'><img src='"  + user_picture  +  "' alt='"  +  item.username   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.d + "</td>" +
				"<td>" + item.b + "</td>" + 
				"<td><input type='checkbox'  name='cb_actmembers'  value='" +  item.a  + "'> </td>" + 
				"<td>";
				html += "<div class='dropdown pull-right'><a  href='#' class='dropdown-toggle btn btn-primary' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false' ><i class='fa fa-cog'></i> </a>" +
				"<ul class='dropdown-menu'> " +
				"<li><a  href='#tl_box' data-toggle='tab' class='btn_sl_acclient'  data-id='" + item.a + "' data-name='" + item.d + "'  >Email Timeline</a></li>" + 
				"<li><a  href='#menu71' data-toggle='tab' class='btn_slcvmclient'  data-id='" + item.a + "' data-name='" + item.d + "'  >Client Management</a></li>" +
				"<li><a  href='#' data-toggle='tab' class='btn_3tinvite'  data-id='" + item.a + "' data-name='" + item.d + "'  >Invite to 3 Touch Program</a></li>" +
				"</ul> " ;
				html +=  "</div></td></tr>";
				html += "<tr><td><strong>Snapshot:</strong></td><td colspan='5'>" + item.sh + "</td></tr>" ;  
			});
			
			if(data.page3 > 0)
			{
				html  += "<tr><td colspan='3'></td><td colspan='2'>" +
				" <input type='button' class='btn btn-primary btn_deac_acclient' data-s='1' value='Move to Active Member'  > " +
				" <input type='button' class='btn btn-success btn_deac_acclient' data-s='10' value='Move to Member'  > " +
				" </td></tr>";
			}
			
			pages = data.page3; 
			var prev =  page3 == 1 ? 1 :  parseInt(page3) -1;
			var next =  page3 ==  pages ?  pages :  parseInt(page3) + 1; 
			html  += "<tr><td colspan='5'><ul class='pagination actmempage'><li><a data-client='" + client + "' data-func='prev' data-pg1='" + page1 + "' data-pg2='" + page2 + "' data-pg3='" + prev + "'>«</a></li>";
			for( i=1;  i <= pages;  i++)
			{
				active =  i ==  page3 ? 'active' : '';
				html += "<li class='" + active + "'><a data-client='" + client + "' data-pg1='" + page1 + "' data-pg2='" + page2 + "' data-pg3='" + i + "'>" + i + "</a></li>";
			}
			
			html += "<li><a data-client='" + client + "' data-func='next' data-pg1='" + page1 + "' data-pg2='" +page2 +  "' data-pg3='" + next + "'>»</a></li></ul></td></tr>";
			html += "</table>";	 
			$('#exclientsgrid').html(html);   
		}
	});  
}

$(document).on('click', '.btn_sl_acclient', function () 
{
	$("#rescheduledate").datepicker({ dateFormat: 'yy-mm-dd' }); 
	var id   = $(this).attr('data-id');
	var name   = $(this).attr('data-name');
	preparetimeline(id, name);
})
 
$(document).on('click', '#btn_sl_incclient', function () 
{
	var id   = $('#inact_clients').chosen().val() + '';
	var name   = $("#inact_clients").chosen().find("option:selected" ).text();
	preparetimeline(id, name);
})

function preparetimeline(id, name)
{
	var eventitem;
	var startTime  ; 
	var nulitem ;
	var buttons;
	$('#tl_box').show(); 
	$("#em_cid").val( id ) ; 
	$('.email_select').attr('data-id',id );
	$('#search_client').modal('hide'); 
	$("#sp_nameselected").html( " to " + name ) ; 
	$("#timeline_events").html("");
	
	$('.btnassignemail').attr('data-id',id );
	$('.btnassignemail').attr('data-name',name );
	  
	
	//loading timelines   
	$('#emseqloading').html("<div ><img   src='../images/processing.gif' alt='Loading ...' width='160px' /></div>");
	$('#tl_box').hide( );
	$('#events-tl').empty(); 
	$.ajax({
		type: 'post',
		url: aurl + 'emailsprogram/fetchtimeline/',
		data: {  id : id },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			  
			if(data.count > 0)
			{ 
				$.each(data.results, function(index, item)
				{
					
					if(item.status == 0)
					{
						nulitem = "<li ><span></span>" ;
						buttons  = "<br/><button data-mid='" +  id + "' data-mname='" +  name + "' data-schdate='" +  item.d + "' data-id='" +  item.seqid + "' class='btn btn-primary btn-xs btnupdateschedule'>Change Schedule</button>" ;
						buttons += " <button data-mid='" +  id + "' data-mname='" +  name + "' data-schdate='" +  item.d + "' data-id='" +  item.seqid + "' class='btn btn-success btn-xs btnprocessseq'>Process Now</button>" ;
					}
					else 
					{
						buttons  = "";
						nulitem = "<li class='processed'><span></span>" ;
					}
					
					nulitem += "<div class='title'>Sequence #" + (index +1) + "</div>" + 
					"<div class='info'>"+ item.mail_heading ;
					nulitem += buttons + "</div>" + 
                    "<div class='time' >" +
					"<span>" + item.d + "</span>" + 
                    "</div>" +
					"</li>" ;
					$('#events-tl').append(nulitem);  
				}) 
				
				$('#events-tl').attr('data-seq', data.count); 
			} 
			else 
			{
				var d = new Date();
				var curr_date = d.getDate();
				var curr_month = d.getMonth() + 1;  
				var curr_year = d.getFullYear();
				
				nulitem = "<li><span></span>" +
                    "<div class='title'>Sequence #0</div>" +
                    "<div class='info'>No Email Assigned Yet</div>" + 
                    "<div class='time' >" +
                        "<span>" + curr_date + "-" + curr_month + "-" + curr_year + "</span>" + 
                    "</div>" +
					"</li>" ;
				
			    $('#events-tl').append(nulitem);
				$('#events-tl').attr('data-seq', 0); 				
			} 
			$('#emseqloading').html("");
			$('#tl_box').show( ); 
		}
	});	 
}

$(document).on('click', '.btn_slcvmclient', function () 
{
	var name = $(this).attr('data-name');
	var id = $(this).attr('data-id' );  
	  
	var page = $(this).attr('data-pg'); 
	if(typeof page == 'undefined' || page =='')
	{
		page =1;
	}
	reloadvoicemaillogs(page, page, name);
	 
	 
	$('.cfg_save_voicemail').attr('data-id', id);
	$('.cfg_save_voicemail').attr('data-name', name); 
	
	$('#vmevent-tl').attr('data-id', id );  
	$('#vmevent-tl').attr('data-name', name );   
	
	$('#vm_assigndate').datepicker({ dateFormat: 'yy-mm-dd' });
	
	$('#menu70').show();
	$('#vmevent-tl').empty();  
	preparevoicemailtimeline(id, name);
	 
})


$(document).on('click', '.btn_client_mgt', function () 
{
	var id = $(this).attr('data-id' );  
	var name= $(this).attr('data-name' );  
	$('.cfg_save_voicemail').attr('data-id', id);
	$('.cfg_save_voicemail').attr('data-name', name); 
	
	$('#vmevent-tl').attr('data-id', id );  
	$('#vmevent-tl').attr('data-name', name );   
	
	$('#vm_assigndate').datepicker({ dateFormat: 'yy-mm-dd' });
	
	$('#menu70').show();
	$('#vmevent-tl').empty();  
	preparevoicemailtimeline(id, name);
	 
});

 
$(document).on('click', '.cfg_save_voicemail', function () 
{ 

	var vmid = $(this).attr('data-vmid' );
	if(typeof vmid ==='undefined' || vmid =='')
	{
		vmid =0;
	} 
	var id = $(this).attr('data-id' ); 
	var name = $(this).attr('data-name' ); 
	var vm_assigndate = $('#vm_assigndate').val();
	var vm_description = $('#vm_description').val();
	
	var vm_schedulehr = $('#vm_schedulehr').val();
	var vm_schedulemin = $('#vm_schedulemin').val();
	var vm_scheduleper = $('#vm_scheduleper').val();
	 
	$.ajax({
		type: 'post',
		url: aurl + 'assignvoicemail/save/',
		data: {vmid:vmid, id:id,vm_assigndate:vm_assigndate , vm_description:vm_description, vm_schedulehr:vm_schedulehr, vm_schedulemin:vm_schedulemin,vm_scheduleper:vm_scheduleper },
		success: function (data) 
		{
			data = $.parseJSON(data);  
			alertFunc('success', data.errmsg); 
			preparevoicemailtimeline(id, name);
		}
	});    
})  
function preparevoicemailtimeline(id, name)
{
	//loading timelines
	$('#vmevent-loading').html("<div ><img   src='../images/processing.gif' alt='Loading ...' width='160px' /></div>");
	$('.tl-box').hide( );
	$('#vmevent-tl').empty();
	
	$.ajax({
		type: 'post',
		url: aurl + 'voicemaltrack/fetchtimeline/',
		data: {  id : id },
		success: function (data) 
		{
			data = $.parseJSON(data);
			 
			
			if(data.count > 0)
			{
				var k=0;
				$.each(data.results, function(index, item)
				{
					if(item.c == 0)
					{
						nulitem = "<li ><span></span>" ;
						buttons  = "<br/><hr/><button data-desc='" +  item.b + "'   data-adate='" +  item.a + "' data-id='" +  item.id + "' class='btn btn-primary btn-xs btnvm_edit'>Edit</button>" ;
						buttons += " <button data-desc='" +  item.b + "'   data-adate='" +  item.a + "' data-id='" +  item.id + "' class='btn btn-success btn-xs btnvm_completed'>Mark As Complete</button>" ;
						buttons += " <button data-name='" +  name + "'  data-id='" +  item.id + "'  data-mid='" +  item.mid + "'  class='btn btn-info btn-xs btncreatetaskform'>Set Employee Task</button>" ;
						
					}
					else 
					{
						buttons  = "";
						nulitem = "<li class='processed'><span></span>" ;
					}
				  
				 nulitem += "<div class='title'>Voicemail #" + (index +1) + "</div>" + 
				 "<div class='info'>"+ item.b ;
				 nulitem +=   buttons + "</div>" + 
                    "<div class='time' >" +
					"<span>" + item.a + "</span>" + 
                    "</div>" +
					" </li>" ;
					  
				 
					$('#vmevent-tl').append(nulitem);  
					k++;
				}) 
				
				$('#vmevent-tl').attr('data-seq', data.count); 
			} 
			else 
			{
				var d = new Date();
				var curr_date = d.getDate();
				var curr_month = d.getMonth() + 1;  
				var curr_year = d.getFullYear();
				
				nulitem = "<li><span></span>" +
                    "<div class='title'>Sequence #0</div>" +
                    "<div class='info'>No Voicemail Assigned Yet</div>" + 
                    "<div class='time' >" +
                        "<span>" + curr_date + "-" + curr_month + "-" + curr_year + "</span>" + 
                    "</div>" +
					"</li>" ;
				
			    $('#vmevent-tl').append(nulitem);
				$('#vmevent-tl').attr('data-seq', 0); 				
			} 
			$('#vmevent-loading').html("");
			$('.tl-box').show( ); 
		}
	});	 
}

 
$(document).on('click', '.btncreatetaskform', function() {
    var id = $(this).attr('data-id');
	var mid = $(this).attr('data-mid');
	var name = $(this).attr('data-name');
	$('#taskdate').datepicker({ dateFormat: 'yy-mm-dd' });
	$('.btn_assignemployee').attr('data-id', id); 
	$('.btn_assignemployee').attr('data-mid', mid); 
	$('.btn_assignemployee').attr('data-name', name); 
	$('#modalsetemptask').modal('show'); 
});

$(document).on('click', '.btn_assignemployee', function()
{
	var vmid =   $(this).attr('data-id')  ;
	var mid  = $(this).attr('data-mid' );  
	var name  = $(this).attr('data-name' );  
	var adate =   $('#taskdate').val()  ; ; 
	var taskdesc =   $('#taskdesc').val()  ; 
	var empid =   $('#empname').val()  ; 
	var receipent = $("#empname option:selected").text();
	
	 
	$.ajax({
		type: 'post',
		url: aurl + 'employee/savetask/',
		data: {vmid:vmid, mid:mid, adate:  adate, name:name,  taskdesc: taskdesc, eid:empid, receipent:  receipent},
		success: function (data) 
		{
			data = $.parseJSON(data); 		 
			$('#modalsetemptask').modal('hide');  
		}
	});
})



$(document).on('click', '.btn_3tinvite', function () 
{
	var cid = $(this).attr('data-id');
	$.ajax({
		type: 'post',
		url: aurl + 'member/joinprogram/',
		data: { id: cid, s : 0},
		success: function (data) 
		{
			data = $.parseJSON(data);
			alertFunc('info',  data.errmsg );  
		}
	}); 
}); 

$(document).on('click', '.showremindersummary', function(){
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'reminder/get/',
        data: { userid: mid },
        success: function(data) {
			 
         data = $.parseJSON(data) ;
         alertFunc('success', 'All your reminders are loaded!');
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
 

$(document).on('click', '.managezipcode', function () 
{ 
	var  city  =  $(this).attr('data-citystart')  ;  
	if(typeof city === 'undefined')
		city='a'; 
	var page = $(this).attr('data-page')  ;  
	if(typeof page === 'undefined')
		page='1';
	
	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'cities/getzipcodes/',
        data: { city: city, page: page },
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc(''); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th>City</th><th>Zip</th><th>Latitude</th><th>Longitude</th><th></th></tr>"  ; 
			$.each(data.results, function (index, item) 
			{
				html += "<tr id='row" + index + "'>" + 
				"<td>" + item.city + "</td>" +
				"<td>" + item.zip + "</td>" +
				"<td><input  id='lat" + item.id + "' type='input' value='" + item.latitude + "' /></td>" +
				"<td><input id='long" + item.id + "' type='input' value='" + item.longitude + "' /></td>" + 
				"<td><button data-id='" + item.id + "' data-lat='" + item.latitude + "' data-long='" +  item.longitude +"' class='btn btn-primary btn-xs btnupdatelatlong'>Update</button></td>" + 
				"</tr>"; 
            });
		html += "</table>"; 
		prev =  (page == 1) ? 1 :  parseInt(page) -1;
        next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1; 
		html += " <ul class='pagination cityname'><li>" +
            "<a class='managezipcode' data-func='prev' data-citystart='" + city + "' data-page='" + prev + "'>«</a></li>";
            for( i=1;  i<= data.pages;  i++)
			{
				active =  i == page ? 'active' : '';
				html +=  "<li class='" + active + "'><a data-citystart='" + city + "' class='managezipcode'  data-page='"+ 
				i + "'>" + i +"</a></li>";
            }
            html += "<li><a class='managezipcode' data-citystart='" + city + "' data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";  
			$('#uscityzicodes').html(html);
        }
    }); 
})

$(document).on('click', '.btnupdatelatlong', function () 
{
	var id = $(this).attr('data-id');
	var lat_old = $(this).attr('data-lat');
	var long_old = $(this).attr('data-long');
	var lat_new = $('#lat' + id).val(); 
	var long_new = $('#long' + id).val(); 
	
	if(lat_old == lat_new && long_old == long_new)
	{
		alertFunc('info', "No change in latitude and longitude detected!" );
		return;
	}
	
	$.ajax({
		type: 'post',
		url: aurl + 'cities/zipupdate/',
		data: { id: id, lat_:lat_new, long_:long_new },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			if(data.error == 0)
			{
				alertFunc('info',  data.errmsg  );
			}
		}
	}); 
})

$(document).on('click', '.reloadsettings', function() {
    
    $.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { loadsettings : 1  },
        success: function(data) 
		{ 
			$('#gridexistcvocs').html(data); 
        }
    });
}) 



$(document).on('click', '.filterlinkedincontact', function(){
	var searchkey =  $('#tblinkedincontact').val();
	getLinkedInImportedContacts (1, searchkey);
});

//LinkedIn Contact pagination
$(document).on('click', '.pagilinkedin li', function() {
    var page = $(this).find('a').attr('data-pg');
	var key = $(this).find('a').attr('data-key');
    getLinkedInImportedContacts(page, key);
});
$(document).on('click', '.linkedinimportlist', function() {
    getLinkedInImportedContacts (1, '');
}) 

function getLinkedInImportedContacts(page, searchkey) 
{
    waitFunc('enable');
    $.ajax({
        type: 'post',
        url:  aurl + 'knows/showallimported/',
        data: { goto: page, key:searchkey, userid:mid},
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
                    html  = `<table class='table table-colored table-alternate table-bordered'>
                    <tr><th>Name</th><th>Email</th><th>Profession</th><th>Company</th> <th>Action</th></tr>`;
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
                         
                    html += '<div class="modal mine-modal fade" id="selectlinkedinmail" tabindex="-1" role="dialog">'+
                    '<div class="modal-dialog">'+
                    '<div class="modal-content"> '+
                        '<div class="modal-header ">'+
                            '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'+
                                '<span aria-hidden="true">&times;</span>'+
                           ' </button>'+
                            '<h4 class="modal-title">Select Mail Template</h4>'+
                        '</div>'+
                       ' <div class="modal-body text-left" style="height: 450px; overflow-y: scroll">'+
                            '<div id="linkedinmails"></div>'+
                        '</div>'+
                       ' <div class="modal-footer"> '+
                           ' <div class="col-xs-12"> '+
                              '  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
                           ' </div>'+
                       ' </div> '+
                    '</div>'+
               ' </div>'+
            '</div>'; 
			
			$('#linkedinlist').html(html); 
			
			
			}
        },
        error: function()
		{
			waitFunc('');
			alertFunc('info', 'Something went wrong, please try again')
        }
    });
} 

$(document).on('click', '#btnsavehelp', function() 
{
    waitFunc('enable');
    var helptitle = $('#helptitle').val();
    var helpvideo = $('#helpvideo').val();
    var id = $(this).data('id');
    
    $.ajax({

        type: 'post',
        url: 'includes/ajax.php',
        data: { savehelpbutton: 1, helpvideo: helpvideo, helptitle: helptitle, id: id },
        success: function(data) 
        {
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
$(document).on('click', '.btnviewhighrankknows', function(){
	var memberid = $(this).attr('data-i') ;
	 showratedknows( memberid,  1  )
})


$(document).on('click', '.pagination.ratedknows li a', function(){
 
	var memberid = $(this).attr('data-i') ;
	var page = $(this).attr('data-page') ;
	showratedknows( memberid,  page  )
})
function showratedknows( memberid , page =1    )
{ 
 

	waitFunc('enable');
    $.ajax({
        type: 'post',
        url: aurl + 'member/knows/getrated/',
        data: { page: page,  ranking:'25'   },
        success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc(''); 
			html = "<table class='table table-responsive'>";
            html += "<tr ><th>Name</th><th>Profession</th><th>Email</th><th>Is invited?</th><th>Know Rating</th><th></th></tr>"  ; 
			$.each(data.results, function (index, item) 
            {

				html += "<tr id='row" + index + "'>" + 
				"<td>" + item.client_name + "</td>" +
				"<td>" + item.client_profession + "</td>" +
                "<td>" + item.client_email + "</td>" +
                    "<td>" + (item.isinvited == '1' ? '<span class="badge badge-red">Invited' : '<span class="badge">No Invited yet' ) + "</span></td>" +
				"<td>" + item.rate + "</td>" + 
				"<td><button data-id='" + item.id + "' data-email='" + item.client_email + "' data-name='" + item.client_name + "' data-voc='" + item.client_profession + "' class='btn btn-primary btn-xs btncomposeinvite'>Sent Invitation</button></td>" + 
				"</tr>"; 
            });
			html += "</table>"; 
			
			prev =  (page == 1) ? 1 :  parseInt(page) -1;
			next = (  page == data.pages ) ?  data.pages : parseInt(page) + 1;  
			 
			html += " <ul class='pagination ratedknows'><li>" +
            "<a data-i='" +  memberid + "' data-func='prev' data-page='" + prev + "'>«</a></li>";
            for( i=1;  i<= data.pages;  i++){
                
                  active =  i == page ? 'active' : '';
                  html +=  "<li class='" + active + "'><a data-i='" +  memberid + "' data-page='"+i   +"'>"+ i 
                +"</a></li>";
            }
            html += "<li><a data-i='" +  memberid + "' data-func='next' data-page='"+ next  +"'>»</a></li></ul> ";  
			  
			$('#topratedknows').html(html);
			alertFunc('info',  data.errmsg );
        },
        error: function( ) {
            waitFunc(''); 
            alertFunc('danger',  'Something went wrong, please try again')
        }
    });  
} 


$(document).on('click',  '.generatevcard', function()
{
	$('.vcarddet').html("<div class='text-center'><img   src='../images/processing.gif' alt='Loading ...' /></div>");
	$.ajax({
		type: 'post',
		url:  'includes/ajax.php',
		data: {  vcardon:'1'  },
		success: function(data) 
		{
			data = $.parseJSON(data);
			html = "<table class='table table-alternate table-bordered table-colored'>"; 
			html += '<tr  >' ;
			html += '<td colspan="2"><h4>Business Card Details</h4></td>' ;
			html += '</tr>' ;
			$.each(data , function (outindex, outitem)
			{  
				html += '<tr>' ;
				 
				if(outindex == 'formatted_name')
				{
					html += '<td>Name:</td>' ;
					html += '<td>' + outitem[0].item   +  
					'<input type="hidden" value="' + outitem[0].item + '" id="bcname">' +
					'</td>' ;
				}
				else
					if(outindex == 'email')
				{
					html += '<td>Email</td>' ;
					html += '<td>' + outitem[0].item   +
					'<input type="hidden" value="' + outitem[0].item + '" id="bcemail">' +
					'</td>' ;
				}
				else 
					if(outindex == 'organization')
				{
					html += '<td>Organization/Company:</td>' ;
					html += '<td>' + outitem[0].item.name   +
					'<input type="hidden" value="' + outitem[0].item.name + '" id="bcorg">' +
					'</td>' ;
				}
				
				else 
					if(outindex == 'telephone')
				{
					html += '<td>Telephone:</td>' ;
					html += '<td>' + outitem[0].item.number   +
					'<input type="hidden" value="' + outitem[0].item.number  + '" id="bcphone">' +
					'</td>' ;
				}
				else 
					if(outindex == 'url')
				{
					html += '<td>Website:</td>' ;
					html += '<td>' + outitem[0].item   +
					'<input type="hidden" value="' + outitem[0].item  + '" id="bcweb">' +
					'</td>' ;
				} 
				html += '</tr>' ; 
            });   
			html += '<tr  >' ;
			html += '<td colspan="2"> <button class="btn btn-primary btn-sm savevcardtoknow">Save As Know</button>  </td>' ;
			html += '</tr>' ; 
			html += "</table>"; 
			$('.vcarddet').html(html);
		}
	}); 
});

 

$(document).on('click',  '.savevcardtoknow', function()
{
	var bcname = $('#bcname').val();
	var bcemail = $('#bcemail').val();
	var bcorg = $('#bcorg').val();
	var bcphone = $('#bcphone').val();
	var bcweb = $('#bcweb').val(); 
	
	
	$.ajax({
		type: 'post',
		url: aurl + 'member/saveminimal/',
		data: {  bcname:bcname,  bcemail:bcemail, bcorg:bcorg, bcphone:bcphone, bcweb:bcweb},
		success: function(data) 
		{
			data = $.parseJSON(data);
			waitFunc(''); 
            alertFunc('info',   data.errmsg ) 
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

$(document).on('click', '.saveAbout', function() {
    var about_title = $('input[name="about_title"]').val().trim();
    var about_content = $('textarea[name="about_content"]').val().trim();
    savePagesContent(about_title, about_content, 'about', '0');
});

$(document).on("click", ".saveTagline", function()
{
	var tagline = $('input[name="tagline"]').val().trim();
    savePagesContent("", tagline, 'tagline', '15');
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

$(document).on('click', '.updPgContent', function() {
    var about_title_ed = $('input[name="about_title_ed"]').val().trim();
    var about_content_ed = $('textarea[name="about_content_ed"]').val().trim();
    var id = $('#editContent').attr('data-id');
    savePagesContent(about_title_ed, about_content_ed, '', id);
}); 


$(document).on('click', '.alink_loadmembers', function () 
{
	 reloadallmembers(1 );
}) 


$(document).on('click', '.btn_searchstaff', function () 
{
	var em_client = $('#tbstaffname').val();
	var page = $(this).attr('data-pg'); 
	if(typeof page == 'undefined' || page =='')
	{
		page =1;
	}
	reloadallmembers(page , em_client);
}) 


function reloadallmembers(page1 ,client='')
{
	$.ajax({
		type: 'post',
		url: aurl + 'members/profiles/all/', 
		data: { page1:page1, type:0,  client:client  },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Email</th> <th>Select</th> </tr>"  ;  
			$.each(data.results, function (index, item) 
			{
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td  ><img src='"  + user_picture  +  "' alt='"  +  item.username   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.d + "</td>" +
				"<td>" + item.b + "</td>" +  
				"<td><input type='checkbox'  name='cb_nonstaff' value='" +  item.a  + "'> </td>" + 
				"<td>";
				html += "</tr>" ;   
			}); 
			if(data.page1 > 0)
			{			
				html  += "<tr><td colspan='3'></td><td colspan='2'> " +
				"<input type='button' class='btn btn-success btn_addremmployee ' data-s='1' value='Add To Staff'  >" +
				 "</td></tr>";
			} 
			var pages = data.page1;
			var prev =  page1 == 1 ? 1 :  parseInt(page1) -1;
			var next =  page1 ==  pages ?  pages :  parseInt(page1) + 1; 
			html  += "<tr><td colspan='5'><ul class='pagination actmempage'><li><a data-func='prev' data-pg1='" + prev + "'   data-client='" + client + "'>«</a></li>";
			for( i=1;  i <= pages;  i++)
			{
				active =  i ==  page1 ? 'active' : '';
				html += "<li class='" + active + "'><a data-pg1='" + i + "' data-client='" + client + "'>" + i + "</a></li>";
			} 
			html += "<li><a data-func='next' data-pg1='" + next +  "'   data-client='" + client + "'>»</a></li></ul></td></tr>";
			html += "</table>";	 	
			$('#allnonstafss').html(html); 
		}
	});  
	$.ajax({
		type: 'post',
		url: aurl + 'members/profiles/all/', 
		data: { page1:page1, type: 1,  client:client  },
		success: function (data) 
		{
			data = $.parseJSON(data); 
			html = "<table class='table table-responsive'>";
			html += "<tr ><th></th><th>Name</th><th>Email</th> <th>Select</th> </tr>"  ;
			var i=0;			
			$.each(data.results, function (index, item) 
			{
				user_picture = !( item.h ) ?  "images/no-photo.png" :  "images/"  +  item.h;   
				html += "<tr id='row" + index + "'>" + 
				"<td  ><img src='"  + user_picture  +  "' alt='"  +  item.username   + "'  onerror='imgError(this);' class='img-rounded' height='55' width='55'></td>" +
				"<td>" + item.d + "</td>" +
				"<td>" + item.b + "</td>" +  
				"<td><input type='checkbox'  name='cb_staff' value='" +  item.a  + "'> </td>" + 
				"<td>";
				html += "</tr>" ;   
				i++;
			});
			
			if(i > 0)
			{
				html  += "<tr><td colspan='3'></td><td colspan='2'> " + 
				" <input type='button' class='btn btn-danger btn_addremmployee' data-s='0' value='Remove From Staff'  > " +
				"</td></tr>";
			} 
			html += "</table>";	 	
			$('#allstafss').html(html); 
		}
});

$(document).on('click', '.btn_addremmployee', function () 
{
	var state = $(this).attr('data-s'); 
	var deaclist = [];
	
	if(state == 1)
	{
		$('input[name=cb_nonstaff]').each(function()
		{
			if(this.checked )
				deaclist.push( $(this).val() ); 
		}); 
	}
	else if(state == 0)
	{
		$('input[name=cb_staff]').each(function()
		{
			if(this.checked )
				deaclist.push( $(this).val() ); 
		});
	} 
	
	if(deaclist.length > 0)
	{
		$.ajax({
			type: 'post',
				url: aurl + 'member/editstaff/',
				data: { ids : deaclist.join(","),role: mrole,   state: state },
				success: function(data) {
					 
					if (data == 'user_error') 
					{
						alertFunc('danger', 'Something went wrong, please try again');
					} else 
					{
						alertFunc('success', 'Changes are saved!'); 
					}
					waitFunc('');
					reloadallmembers(1 ); 					
				} 
		});  
	}  
})
} 

$(document).on('click', '.loadstaffactivities', function() 
{
	reloadstafflogs(1)
}) 

function reloadstafflogs(page)
{
	var i=0;
	$.ajax({
		type: 'post',
		url:  aurl + 'staff/activity/getlog/',
		data: {  mid: 0 , page: page },
		success: function(data)
		{
			data=$.parseJSON(data);  
			console.log(data);
			html = "<table class='table table-responsive'>" + 
				"<tr><th>Staff</th><th>Client Worked On</th><th>Client Package</th><th>Last Worked On</th><th>Details</th><th>Action</th></tr>";
				
				$.each(data.results, function(idx, item)
				{
					html  += "<tr><td>" + item.en + "</td><td>" + item.d + '</td><td>' + item.e + '</td><td>' + item.b + '</td><td>' + item.c + '</td>';
					  
					html  += '<td>' + '<button data-name="' + item.d + '"  data-id="' + item.a +  
					'" class="btn btn-primary btn-small vu_detaillog"><i class="fa fa-file"></i></button></td></tr>' ;
					i++;					
                }); 
			if(i > 0)	 
				$('#staffactivitylog').html(html);
			else
				$('#staffactivitylog').html('<div class="alertinfofix text-center">No staff activity log found!</div>');
		} 
	});  
}



$(document).on('click', '.show_duplicate_referrals', function() 
{
	var id = $(this).attr('data-user');
	refreshdupknows(id, 1)
})

function refreshdupknows(id, page)
{
	$.ajax({
		type: 'post',
		url:  'includes/ajax.php',
		data: { get_duplicate_referrals :  1,   mid: id , page: page},
		success: function(data) 
		{
			$('#referrals_duplicates').html(data);
		} 
	});
}

$(document).on('click', '.pagidupref li a', function()
{
	var page = $(this).attr('data-pg');
	var id = $('#userid').val(); 
	refreshdupknows(id, page);
});

$(document).on('click', '.pagidupref #klgopage', function()
{ 
    var pageno = $('#klgotopageno').val( ); 
    if (pageno <= 0) pagesize = 1; 
	var id = $('#userid').val(); 
    refreshdupknows(id, pageno);
}); 
 
$(document).on('click', '.remdupknows', function() 
{
	var pageno = $('#crpage').val( ); 
	var id = $('#userid').val();
	
	var list = []; 
	$('input[type=checkbox].select_id').each(function () 
	{
		if(this.checked)
		{
			list.push( $(this).attr('data-id')); 
		}
	});
	
	
	$.ajax({
		type: 'post',
		url:  'includes/ajax.php',
		data: { rem_duplicate_referrals :  1,   ids: JSON.stringify(list) },
		success: function(data) 
		{ 
			refreshdupknows(id, pageno); 
		} 
	}); 
	
	
	
})




