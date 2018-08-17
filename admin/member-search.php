<?php 
include("header.php");
include_once 'includes/db.php';
include_once 'includes/functions.php';

//name : keyword, userid:mid, city: city, vocation: vocation , page: page, iszip:iszip, utype:utype

	$keyword  = $_POST['gskey'];  
	$city = $_POST['gscityorzip'];
	$vocation = $_POST['vocation'];
	$page = $_POST['page'];
	$page2 = $_POST['page2'];
	
	//member pages
	if( !isset($page))
	{
		$page =1;
	}
	$pagesize = 10 ;  
	$start = ($page-1)* $pagesize ;
	
	//know pages
	if( !isset($page2 ) )
	{
		$page2 =1;
	}   
	$start2 = ($page2-1)* $pagesize ;
	
	 
	
	$iszip =  is_numeric($city)  ? 1 : 0  ;
	$where_member_ids = array();
	 
	if($_POST['gscityorzip'] == "")
	{
		$where_city ='';
		$knowwhere_city ='';
	}
	else 
	{
		if($iszip == 1 )
		{
			$neighbours = find_neighbour_cities(  $city, '30');
			if(sizeof($neighbours ) > 0)
			{
				$neighbourzips = implode(",", $neighbours); 
				$where_city = "   zip in  (  " . $neighbourzips   . "  )  and ";
				$knowwhere_city = "   client_zip in  (  " . $neighbourzips   . "  )  and "; 
			}
			else 
			{
			  $where_city  = "  zip ='" .  $city . "' and "   ;
			  $knowwhere_city = " client_zip ='" .  $city . "' and "   ;
			}
		}
		else 
		{ 
			$cities = explode(",", $city); 
			$where_city  = " ( FIND_IN_SET('" . implode("',  cities ) OR FIND_IN_SET('", $cities) . "',  city ) )  and"   ;
			
			$knowwhere_city = " ( FIND_IN_SET('" . implode("',  client_location ) OR FIND_IN_SET('", $cities) . "',  client_location ) )  and"   ;
		}
	}
	 
	if($vocation != '')
	{
		$keys = explode(",", $vocation);
		$where_vocations  = " or ( FIND_IN_SET('" . implode("',  vocations ) OR FIND_IN_SET('", $keys) . "',  vocations ) ) "   ;  
		
		$knowwhere_vocations  = " and ( FIND_IN_SET('" . implode("',  client_profession ) OR FIND_IN_SET('", $keys) . "',  client_profession ) ) "   ;  
		
	}
	else if($keyword != '')
	{
		$keys = explode(",", $keyword);
		$where_vocations  = " or ( FIND_IN_SET('" . implode("',  vocations ) OR FIND_IN_SET('", $keys) . "',  vocations ) ) "   ; 
		$knowwhere_vocations  = " and ( FIND_IN_SET('" . implode("',  client_profession ) OR FIND_IN_SET('", $keys) . "',  client_profession ) ) "   ; 
	}
	
	if($keyword == '' && $city ==''  && $vocation == '')
	{
		$jsonresult = array('error' =>  '10' ,   'errmsg' =>  'Search parameter missing!' );
		$response->getBody()->write( json_encode($jsonresult) );
		return $response;   
	} 
	
	try
	{ 
	 
	 
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$sql_log_query =  "insert into  mc_global_search_log (user_id, keyword, city_zip) values (?,?, ?) " ;  
		$stmt = $pdo->prepare($sql_log_query);
		$stmt->execute(array('-1', $keyword, $city  ));
		 
		
		$sql_query_ids =  " select a.id as ui from mc_user as a inner join  user_details  as b on a.id=b.user_id  where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations )   "; 
		
		$rst = $pdo->query($sql_query_ids); 
		if($rst->rowCount() > 0 )
		{
			$member_ids = $rst->fetchAll(PDO::FETCH_ASSOC);
			$ids = array(); 
			foreach ($member_ids as $row )  
			{
				$where_member_ids[] = $row["ui"]; 
			}   
		}
		 
		$sql_query =  " select a.id as ui, 0 knid  , user_email as a, username  as b, user_role  as c, user_pkg  as d, user_phone  as e,  image  as f,  busi_name  as g,  user_type  as h, " .
			" busi_location_street  as i,  busi_location  as j,  busi_type  as k,  busi_hours  as l, busi_website  as m, current_company  as n, linkedin_profile  as o, " .
			" street  as p, city  as q, zip  as r, country  as s,  groups  as t,  target_clients  as u, target_referral_partners  as v, vocations  as w, about_your_self  as x , " .
			" 0 as isconnected, 0 as rating from mc_user as a inner join  user_details  as b on a.id=b.user_id  " .
			" where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations ) and a.username <> ''  order by username  limit $start, $pagesize "; 
			
			$sql_query_count = "select count(*) as reccnt from mc_user as a inner join  user_details  as b on a.id=b.user_id  " .
			" where $where_city a.id <> '1' and a.id <> '$userid' and ( a.username like '%$keyword%'  $where_vocations ) ";
			
	  
			$rst = $pdo->query($sql_query); 
			$membercount = $rst->rowCount();
			if( $membercount > 0 )
			{ 
				$members = $rst->fetchAll(PDO::FETCH_ASSOC);   
				
				for($i=0; $i < $rst->rowCount() ; $i++)
				{
					$sp =  $members[$i][ui]; 
					 
					
					$query_connect_check =  " select * from mc_member_connections where  status='1' and (  ( firstpartner='$userid' and secondpartner='$sp')  or ( firstpartner='$sp' and secondpartner='$userid') ) "  ; 
				
					$rstconcheck = $pdo->query($query_connect_check); 
					if($rstconcheck->rowCount() > 0 )
					{
						$members[$i][isconnected]  = '1'; 
					}
					 
					//calculating average rating
					$query_avg_rating =  "select sum(ranking) as ranking from mc_user_rating where  user_id='$sp' group by rated_by "  ;  
					$avgraters = $pdo->query($query_avg_rating); 
					if($avgraters->rowCount() > 0 )
					{
						$avgrate =0;
						$count= 0 ;
						foreach($avgraters as $ritem)
						{
							$avgrate .= $ritem['ranking'];
							$count++;
						}
						if($count > 0)
							$members[$i][rating]  = $avgrate / $count; 
					} 
				}
				//loggin search keyword
				$member_rating = usort($members, member_rating ); 
				$rst_count = $pdo->query($sql_query_count); 
				$result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);   
				$pages = ceil($result_count[0]['reccnt']/10); 
				$jsonresult = array( 'pages' => $pages , 'result' => $members ); 
			} 
		  
		  //loading knows
		    
		if($membercount == 0)
		{
			  
			$html = '<div style="padding: 10px; text-align:center; font-size: 14px; font-weight: bold">No matching member found!</div>' ; 
			  
			   
			$rsnameorvoc = $pdo->query("SELECT * FROM `groups` where islisted='1' and grp_name='$keyword' ORDER BY `grp_name` "); 
			 
			if( $rsnameorvoc->rowCount() == 0 )
			{ 
				 $membername = " and client_name like '%$keyword%' ";
				 
			}
			else 
			{
				$membername = " and find_in_set('$keyword', client_profession) ";
			}
			
		}
		else
		{
			$membername = " and find_in_set('$keyword', client_profession) ";	
		}			
			
			//fetching knows
			$sql_query = " select  k.user_id  as ui, k.id as knid , u.username as un, k.client_email as a  ,  k.client_name as b , 
		'na' c, 'na' d,  k.client_phone as e, 'na' f, 
		'na' g, 'na' h, 'na' i, 'na' j,  'na' k, 'na' l,  'na' m, 'na' n, 
		'na' o, 'na'  p,  k.client_location as q,  k.client_zip as r, 'na'  s, 
		'na' t, 'na'  u, 'na'  v, k.client_profession as w, 'na'  x ,  0 as isconnected, 0 as rating  
		from  user_people as k inner join mc_user as u on k.user_id=u.id  where $knowwhere_city " . 
		" (find_in_set('Rated 25', k.tags) or  find_in_set('Rated 6 Need to Contact', k.tags) ) " . 
		 $membername . " and k.id not in (select distinct user_id from mc_claimprofile_invite where isaccepted='1') ";
		 
		 
	    $sql_query_count = "select count(*) as reccnt from  user_people  where $knowwhere_city " . 
		" (find_in_set('Rated 25', tags) or  find_in_set('Rated 6 Need to Contact', tags) ) " .
		 $membername; 
		
		$rst_count = $pdo->query($sql_query_count); 
			$result_count = $rst_count->fetchAll(PDO::FETCH_ASSOC);   
			$pages = ceil($result_count[0]['reccnt']/10); 
			$jsonresult['know_pages'] =  $pages ;
			
			$rst = $pdo->query($sql_query);
			$know_result = array();
			if($rst->rowCount() > 0 )
			{ 
				$knows = $rst->fetchAll(PDO::FETCH_ASSOC); 
				for($i=0; $i < $rst->rowCount() ; $i++)
				{
					$know_id =  $knows[$i][knid];  	
					//calculating average rating
					$query_know_rating =  "  select sum(ranking) as total_rank from  user_rating  where user_id='$know_id' "  ;  
					$knowrating = $pdo->query($query_know_rating); 
					$know_rating = $knowrating->fetchAll(PDO::FETCH_ASSOC)[0]['total_rank']; 
					if(is_null( $know_rating ))
						$knows[$i][rating]  = "0";
					else 
						$knows[$i][rating]  =$know_rating ;
				} 
			
				$member_rating = usort($knows, member_rating ); 
				for($i= $start2 ; $i <  $rst->rowCount() && $i < ( $start2  + $pagesize) ; $i++)
				{
					$know_result[] = $knows[$i];  
				}
				
			}
			
		echo $start2 + $pagesize;
		 
		$jsonresult['knows'] =  $know_result ;  
		$jsonresult['errmsg'] = 'Member fetched successfully!';
		$jsonresult['error'] =  '0'  ;
		//printing knows 
		
		$knowhtml ='';
				foreach ($jsonresult['knows']  as $item)  
				{
					$user_picture =   !( $item['f'] ) ?  "images/no-photo.png" :  "images/no-photo.png"  ;
					$knowhtml .= '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">' ;
					$knowhtml .= "<img src='"  . $user_picture  .  "' alt='"  .  $item['b']   . 
					"' onerror='imgError(this);' class='img-rounded'  width='80'> " . '</div>' ;
					$knowhtml .= '<div class="col-xs-8 col-md-6"><strong>' . $item['b']  .'</strong>'  .   
					'<input type="hidden" value="' . $item['b'] . '" id="bcname"><br/>' . $item['w']  .'<br/>' ;
					
					 if( $item['q'] != '' &&  $item['q'] != 'null' )
						$knowhtml .= $item['q'] . " "  . $item['r'] .'<br/>'  ;
					 
					$knowrate =   ceil( $item['rating'] / 5 ) ;
				 $star = '';
					for(  $sc =0; $sc < 5; $sc++)
					{
						if( $sc < knowrate)
							$star  .= "<i class='fa fa-star orange'></i>";
						else 
							$star  .= "<i class='fa fa-star lgray'></i>";
					} 
					
					if( $knowrate ==  5)
					{
						$knowhtml .= "<br/><span class='badge badge-green'><i class='fa fa-sun-o'></i> Top Rated Know</span>" ;
						$knowhtml .= " <span class='badge badge-dark'>Rated by: " . $item['un'] ."</span>";
					}
					else if($knowrate > 0)
					{
						$knowhtml .= "<br/>" .	$star    ;
						$knowhtml .= " <span class='badge badge-dark'>Rated by: " . $item['un'] ."</span>";
					}
					else 
						$knowhtml .= "<br/><span class='badge badge-blue'>Non Rated Know</span>"  ;
					
					
					$knowhtml .= '</div> <div class="col-xs-4 col-md-4">';
					
					$knowhtml .= '<button type="button" data-type="k" data-id="' . $item['knid'] . '" data-name="' .$item['b'] . 
					'"  data-voc="' . $item['w'] . '" class="btn btn-primary btn-block join_mycity" ><i class="fa fa-envelope"></i> Click to Connect</button>'; 
  
					$knowhtml .= '</div></div></div>' ; 
				} 
				 
				$prev =  ($page2 == 1) ? 1 :  intval($page2) -1;
				$next = (  $page2 ==  $jsonresult['know_pages'] ) ?  $jsonresult['know_pages'] :  intval( $page2) + 1;  
				 
				$knowhtml .= " <ul class='pagination knowgslist'  ><li>" .
					"<a  data-keyword='" .  $keyword ."'  data-city='" .  $city ."'  data-vocation='" .  $vocation ."' data-func='prev' data-page='" . $page . "' data-page2='" . $prev . "'>«</a></li>";
					for( $i=1;  $i<= $jsonresult['know_pages'] ;  $i++){ 
						  $active =  $i == $page2 ? 'active' : '';
						  $knowhtml .=  "<li class='" . $active . "'><a data-keyword='" .  $keyword ."'  data-city='" .  $city ."'  data-vocation='" .  $vocation ."' data-page='" . $page . "' data-page2='".  $i  ."'>".  $i 
						."</a></li>";
					}
		$knowhtml .= "<li><a data-keyword='" .  $keyword ."'  data-city='" .  $city ."'  data-vocation='" .  $vocation ."' data-func='next' data-page='" . $page . "' data-page2='". $next  ."'>»</a></li></ul> ";
	 
	 
	  		 
			
 //member found
			$html = "";
	//refresh grid
	foreach ($jsonresult['result']  as $item) 
	{
		$user_picture =   !( $item['f'] ) ?  "images/no-photo.png" :  "images/"  .  $item['f'];
		$html .= '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">' ;
		$html .= "<img src='"  . $user_picture  .  "' alt='"  .  $item['b']   . 
		"' onerror='imgError(this);' class='img-rounded'  width='80'> " . '</div>' ;
		$html .= '<div class="col-xs-8 col-md-7"><strong>' . $item['b']  .'</strong>'  .   
		'<input type="hidden" value="' . $item['b'] . '" id="bcname"><br/>' . $item['w']  .'<br/>' ;
		
		if( $item['p'] != '' &&  $item['p'] !=  'null' )	
			$html .=	$item['p']  .'<br/>' ;
		if( $item['q'] != '' &&  $item['q'] != 'null' )
			$html .=	$item['q'] . " "  . $item['r'] .'<br/>'  ;
		$html .= $item['s'] ; 
			$mrate =   ceil( $item['rating'] / 5 ) ;
				 $star ='';
					for( $sc =0; $sc < 5; $sc++)
					{
						if($sc < $mrate)
							$star  .= "<i class='fa fa-star orange'></i>";
						else 
							$star  .= "<i class='fa fa-star lgray'></i>";
					}
					
					if( $mrate ==  5)
						$html .= "<br/><span class='badge badge-green'><i class='fa fa-sun-o'></i> Top Rated Member</span>"    ;
					else if( $mrate > 0)
						$html .= "<br/>" .	$star    ;
					else 
						$html .= "<br/><span class='badge badge-blue'>Non Rated Member</span>"  ;
					
					$html .= '</div> <div class="col-xs-4 col-md-3">';
					 
					if( $item['isconnected'] == 1 )
					{
						$html .= '<button type="button" data-id="' . $item['ui'] . '" data-name="' . $item['b'] . '" class="btn btn-primary btn-block join_mycity" ><i class="fa fa-envelope"></i> Message</button>'; 
					}
					else
					{
						$html .= '<button type="button" data-i="' . $item['ui'] . '"  data-name="' . $item['b'] . '"  class="btn btn-primary btn-solid btn-block join_mycity" ><i class="fa fa-envelope"></i> Connect</button>' ; 
					}
					 
					$html .= '</div></div></div>' ; 
	} 
				
				$prev =  ( $page == 1) ? 1 :  intval( $page) -1;
				$next = (  $page == $jsonresult['pages'] ) ?  $jsonresult['pages'] : intval( $page) + 1;  
				 
				$html .= " <ul class='pagination membergslist' ><li>" .
					"<a data-keyword='" .  $keyword ."'  data-city='" .  $city ."'  data-vocation='" .  $vocation ."' data-func='prev'  data-page='" . $prev . "' data-page2='" . $page2 . "'>«</a></li>";
					for( $i=1;  $i<= $jsonresult['pages'] ;  $i++){
						
						  $active =  $i == $page ? 'active' : '';
						  $html .=  "<li class='" . $active . "'><a data-keyword='" .  $keyword ."'  data-city='" .  $city ."'  data-vocation='" .  $vocation ."' data-page='". $i   ."' data-page2='" . $page2 . "'>". $i 
						."</a></li>";
					}
			$html .= "<li><a data-keyword='" .  $keyword ."'  data-city='" .  $city ."'  data-vocation='" .  $vocation ."' data-func='next' data-page='". $next  ."' data-page2='" . $page2 . "'>»</a></li></ul> ";
				
	 
	  
		  
	}
	catch(PDOException $e)
	{
		$jsonresult = array('error' =>  '1' ,  'errmsg' => $sql_query); 
	}
	 
if($jsonresult['error'] != 0)
{
	
	$knowhtml= $html = '<div style="padding: 10px; text-align:center; font-size: 14px; font-weight: bold">An error occured while processing your request. Please retry!</div>' ; 
}
 
?>

 <section id="main-section" class="secblue" >
        <div class="container  ">
            <div class="row">
			<div class='col-md-8 col-md-offset-2'>
				<h1 class='page-heading'>Members In MyCity</h1>
			</div>
			
			 <div class='col-md-8 col-md-offset-2'> 
			  <ul class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#membergrid" aria-controls="homein" role="tab" data-toggle="tab"> Members</a></li>
				<li role="presentation" ><a href="#knowgrid" aria-controls="conreqin" role="tab" data-toggle="tab"> Meet Other People</a></li>
			 </ul> 
			  <div class="tab-content">
				<div role="tabpanel" class="tab-pane active" id="membergrid">
					<div class="memberlist"><?php echo $html; ?></div>
				</div>  
				<div role="tabpanel" class="tab-pane  " id="knowgrid">
					<div class="knowlist"><?php echo $knowhtml; ?></div>
				</div> 
		  </div>
		  </div>
		  </div>
		  </div>
 	  
	 </section>  
	<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/5a7df79c4b401e45400cd301/default';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

<script>

$(document).on('click', '.pagination.membergslist li a', function()
{
	var page = $(this).attr('data-page');
	var page2 = $(this).attr('data-page2');
	var keyword = $(this).attr( 'data-keyword');
	var gscityorzip =$(this).attr('data-city');
	var vocation  =$(this).attr('data-vocation'); 
	 
    $('<form method="post" action="member-search.php"><input value="' + keyword + '" name="gskey" /><input value="' + gscityorzip + '" name="gscityorzip" /><input value="' + page + '" name="page"/><input value="' + page2 + '" name="page2"/></form>').appendTo('body').submit();
  
});
 
$(document).on('click', '.pagination.knowgslist li a', function()
{
	var page = $(this).attr('data-page');
	var page2 = $(this).attr('data-page2');
	var keyword = $(this).attr( 'data-keyword');
	var gscityorzip =$(this).attr('data-city');
	var vocation  =$(this).attr('data-vocation'); 
	 
    $('<form method="post" action="member-search.php"><input value="' + keyword + '" name="gskey" /><input value="' + gscityorzip + '" name="gscityorzip" /><input value="' + page + '" name="page"/><input value="' + page2 + '" name="page2"/></form>').appendTo('body').submit();
  
});


$(document).on('click', '.join_mycity', function()
{
	var name = $(this).attr('data-name');
	var utype = $(this).attr('data-type'); 
	var ki = $(this).attr('data-id');
	$('body > #confirm-box').remove();
	var modaltext ='<div class="modalbl modal fade" id="confirm-box" >' + 
	'<div class="modal-dialog  ">' +
	'<div class="modal-content"><div class="modal-header  "><i class="fa fa-warning yellow"></i> Message <button type="button" id="close-video" class="close" data-dismiss="modal" aria-hidden="true">×</button></div>' +
	'<div class="modal-body text-center"><p class="txtbg">We’re getting in touch with ' + name + '.</p> ' ; 
	<?php
	if ( !isset($_SESSION['user_id']))
	{
		?>
		modaltext += '<button type="button" id="btn-confirm" class="btn btn-primary btn-confirm btn-lg" data-confirm="no">Join here!</button>';  
	<?php
	}
	?>
	
	modaltext +='</div> </div> </div> </div>' ;  
	
	$('body').append(modaltext);
	$('#confirm-box').modal({ backdrop: 'static', keyboard: false, show: true }); // open lightbox
	$('#confirm-box .btn-confirm').click(function(e) {
		$('#confirm-box').modal('hide');
		window.location ='index.php';
	});
		
if(utype =='k')
 {
	 
	$.ajax({
        type: 'post',
        url: 'includes/ajax.php',
        data: { inviteknow : 1, n : name,  i:ki},
        success: function(data) 
		{  
			 
        }
    }); 
}	
	 
}); 


</script>

<?php include("footer.php") ?>
