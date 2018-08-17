 var targets_counts = 3;
    $(document).on('click', '#add_more_trargeted_clients', function(e) {
		 
        e.stopImmediatePropagation();
        targets_counts++;
        var target_client = $('select.target_client').parents('.form-group').clone();
		console.log(target_client);
        $('.targeted_client_append').append(target_client);
        $('select[name="targeted_clients[1]"]:last').attr('name', 'targeted_clients[' + target_client + ']');
    });
	
	
	var groups_counts = 1;
    $(document).on('click', '#add_more_groups', function(e) {
        e.stopImmediatePropagation();
        groups_counts++;
        var groups = $('.groups_append').find('.form-group:eq(0)').clone();
        $('.groups_append').append(groups);
    });

var targets_referral_count = 3;
    $(document).on('click', '#add_more_targeted_referral', function(e) {
        e.stopImmediatePropagation();
        targets_referral_count++;
        var target_client = $('select[name="targeted_referral_partners[1]"]').parents('.form-group').clone();
        $('.targeted_referral_append').append(target_client);
        $('select[name="targeted_referral_partners[1]"]:last').attr('name', 'targeted_referral_partners[' + targets_referral_count + ']');
    });	
	
	
 
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result)
                        .width(130)
                        .height(130);
                };
                reader.readAsDataURL(input.files[0]);
                $(".hideafter").hide();
                $("#blah").show();
            }
        }
  

	