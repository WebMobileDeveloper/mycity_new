<?php

class MyCommonVocations extends CI_Model
{
	var $id ='';
	var $member_voc='';
	var $know_common_voc='';  
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database(); 
	}
	
	public function add($data)
	{
		$voc = $data['member_voc'];
		
		$rs = $this->db->query("select * from mc_common_vocation where member_voc='$voc'");
		if($rs->num_rows() > 0)
		{
			//update
			$this->db->where("member_voc", $voc );
			$this->db->update("mc_common_vocation",  array('know_common_voc' =>  $data['know_common_voc']   )    );
			return $this->db->affected_rows() > 0;
		}
		else 
		{
			//insert 
			$this->db->insert("mc_common_vocation", $data);
			$cvocid = $this->db->insert_id() ; 
			return $cvocid; 
		}
		
		
	}
	

	public function remove($id)
    {
		$this->db->where("id", $id);
		$this->db->delete('mc_common_vocation'); 
	}
	
	function get_common_vocations($knowid )
	{
		$sql_query_vocations =  "select client_profession from user_people where id = '$knowid' " ; 
		$rst = $this->db->query($sql_query_vocations); 
		$targetvocs = '';
		$targetvocs_arr = '';
		if($rst->num_rows() > 0)
		{
			$vocations = $rst->row()->client_profession ;
			$vocationlist = explode(',',  $vocations  ); 
			$vocation_where = ' where  ';
			$vocation_where .= "( FIND_IN_SET('". implode("', member_voc) OR FIND_IN_SET('", $vocationlist) . "', member_voc)) ";
			   
			$sql_query =  "select * from mc_common_vocation  "  . $vocation_where;
			$cvoc_rst = $this->db->query($sql_query); 
			$row_count = $cvoc_rst->num_rows();
			if( $row_count > 0 )
			{
				foreach($cvoc_rst->result() as $vrow)
				{
					$targetvocs_arr[] = $vrow->know_common_voc;
				}
				$targetvocs = implode(',', $targetvocs_arr);
			}  
		
		}
		$jsonresult = array('error' =>  '0' ,  'errmsg' =>    'Common vocations fetched!', 
		'common_vocs' => $targetvocs  ); 
	 
	return $jsonresult; 
	}
	
	function get_common_vocation_for_member($memberid )
	{
		$sql_query_vocations =  "select vocations from user_details where id = '$memberid' " ; 
		$rst = $this->db->query($sql_query_vocations); 
		$targetvocs = '';
		$targetvocs_arr = '';
		if($rst->num_rows() > 0)
		{
			$vocations = $rst->row()->vocations ;
			$vocationlist = array_filter(explode(',',  $vocations  ) ); 
			
			 
			$vocation_where = ' where  ';
			$vocation_where .= "( FIND_IN_SET('". implode("', member_voc) OR FIND_IN_SET('", $vocationlist) . "', member_voc)) ";
			   
			$sql_query =  "select * from mc_common_vocation  "  . $vocation_where;
			$cvoc_rst = $this->db->query($sql_query); 
			$row_count = $cvoc_rst->num_rows();
			if( $row_count > 0 )
			{
				foreach($cvoc_rst->result() as $vrow)
				{
					$targetvocs_arr[] = $vrow->know_common_voc;
				}
				$targetvocs = implode(',', $targetvocs_arr);
			}  
		
		}
		$jsonresult = array('error' =>  '0' ,  'errmsg' =>    'Common vocations fetched!', 
		'common_vocs' => $targetvocs  ); 
	 
	return $jsonresult; 
	}
	
	
	public function get_all(  )
    {
		$this->db->select("*" );
		$this->db->from('mc_common_vocation'); 
		$this->db->order_by("member_voc");
		$result  = $this->db->get(); 
		return  $result ; 
	}
	public function get_common_vocation_by_id( $id)
    {
		$this->db->select("*" );
		$this->db->from('mc_common_vocation'); 
		$this->db->where("id", $id);
		$result  = $this->db->get(); 
		return  $result ; 
	}
	
} 

?>