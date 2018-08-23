<?php

class Mailbox extends CI_Model
{
    var $id = '';
    var $sender = '';
    var $receipent = '';
    var $subject = '';
    var $emailbody = '';
    var $emailstatus = '';
    var $senton = '';
    var $email_type = '';
    var $associatedmember = '';
    var $replyto = '';
    var $sender_id = '';
    var $receipent_id = '';


    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function add($data)
    {
        $this->db->insert("mc_mailbox", $data);
        $studentid = $this->db->insert_id();
        return $studentid;
    }

    public function update($data, $id)
    {
        $this->db->where("id", $id);
        $this->db->update("mc_mailbox", $data);
    }


    public function remove($id)
    {
        $this->db->select("*");
        $this->db->where("id", $id);
        $mail = $this->db->get("mc_mailbox");
        if($mail->num_rows()>0){
            if($mail->row()->emailstatus == 10){
                $this->db->where("id", $id);
                $this->db->delete('mc_mailbox');
            }else{
                $this->db->where("id", $id);
                $this->db->update("mc_mailbox", array('emailstatus' => 10));
            }
        }
    }

    public function get_mail($id)
    {
        $this->db->select("*");
        $this->db->where("id", $id);
        $mail = $this->db->get("mc_mailbox");
        return $mail;
    }

    function get_inbox($data)
    {
        $pagesize = 10;
        $receipent = $data['receipent'];
        $mailtype = $data['mailtype']; // 0 for direct email 10 for connection request
        $offset = $data['offset'];
        $offset *= $pagesize;
        $sql_query = "select a.subject, a.emailbody, a.senton, a.email_type,  a.emailstatus,  a.id, a.sender, a.receipent, b.id as partnerid, 
                      b.username, b.user_pkg, b.user_phone, b.user_email, 0 connect_status  
		              from  mc_mailbox as a 
                      inner join mc_user as b on a.sender=b.user_email 
                      where receipent='$receipent' and email_type='$mailtype' and emailstatus<>10 
                      order by a.senton desc, emailstatus limit $offset, $pagesize";

        //        where receipent='$receipent' and email_type='$mailtype' and b.user_status='1'

        $sql_query_count = "select count(*) as reccnt  
                      from  mc_mailbox as a 
                      inner join mc_user as b on a.sender=b.user_email 
                      where  receipent='$receipent' and email_type='$mailtype' and emailstatus<>10 ";
        //        where  receipent='$receipent' and email_type='$mailtype'  and b.user_status='1' ";
        $result_count = $this->db->query($sql_query_count);
        $num_rows = $result_count->row()->reccnt;
        if ($num_rows > 0) {
            $result = $this->db->query($sql_query);
            $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!', 'num_rows' => $num_rows,
                'result' => $result);
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No email found!',  'num_rows' => 0,'result' => null);
        }
        return ($jsonresult);
    }

    function get_outbox($data)
    {
        $pagesize = 10;
        $receipent = $data['receipent'];
        $mailtype = $data['mailtype']; // 0 for direct email 10 for connection request
        $offset = $data['offset'];
        $offset *= $pagesize;
        $sender = '';
        $sql_query = "select a.subject, a.emailbody, a.senton, a.email_type, a.emailstatus, a.id, a.sender,  a.receipent, b.id as partnerid, 
                          b.username, b.user_pkg, b.user_phone, b.user_email, 0 connect_status 
                      from  mc_mailbox as a 
                      inner join mc_user as b on a.receipent = b.user_email 
                      where sender='$receipent' and email_type='$mailtype' and emailstatus<>10 
                      order by a.senton desc,emailstatus  limit $offset, $pagesize";
//                      where sender='$receipent' and email_type='$mailtype' and b.user_status='1' order by a.senton desc limit $offset,10";

        $sql_query_count = "select count(*) as reccnt  from  mc_mailbox as a 
                      inner join mc_user as b on a.receipent = b.user_email 
                      where  sender='$receipent' and email_type='$mailtype' and emailstatus<>10 ";
//                      where  sender='$receipent' and email_type='$mailtype' and b.user_status='1' ";
        $result_count = $this->db->query($sql_query_count);
        $num_rows = $result_count->row()->reccnt;
        if ($num_rows > 0) {
            $result = $this->db->query($sql_query);
            $jsonresult = array('error' => '0', 'errmsg' => 'Mails are retrieved!', 'num_rows' => $num_rows, 'result' => $result);
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No email found!',  'num_rows' => 0,'result' => null);
        }
        return ($jsonresult);
    }


    public function send_mail_log($data)
    {
        $mailbody = $data['mailbody'];
        $originalbody = $data['mailbody'];
        $results = $this->db->query("select * from mc_user where id='" . $data['id'] . "' ");

        if ($results->num_rows() > 0) {
            $mailreceipent = $results->row();
            $receipentmail = $mailreceipent->user_email;
            $receipentname = $mailreceipent->username;
            if ($receipentmail !== NULL || $receipentmail != '') {
                $ds = DIRECTORY_SEPARATOR;
                $path = $this->config->item("site_path");
                if (file_exists($path . "templates/directemail.txt")) {
                    $filecontent = file_get_contents($path . "templates/directemail.txt");
                    $filecontent = str_replace("{receipent_name}", $receipentname, $filecontent);
                    $filecontent = str_replace("{mail_body}", $mailbody, $filecontent);
                    $filecontent = str_replace("{sender_name}", $data['username'], $filecontent);
                    $filecontent = str_replace("{sender_email}", $data['senderemail'], $filecontent);
                    $filecontent = str_replace("{sender_phone}", $data['senderphone'], $filecontent);
                    $filecontent = str_replace("{year}", date('Y'), $filecontent);
                    $mailbody = $filecontent;
                }

                //sending mail
                $this->send_mails($this->session->email, $this->session->name, $receipentmail, $data['subject'], $mailbody);

                //insert into mail log
                $insert_qry = "insert into mc_mailbox (sender, receipent, subject , emailbody , emailstatus , email_type , senton)
				VALUES (?,?, ?, ?, '0', '0', NOW() ) ";
                $this->db->query($insert_qry,
                    array($data['senderemail'], $receipentmail, $data['subject'], $originalbody));

                $jsonresult = array('error' => '0', 'errmsg' => "Email sent!");
            } else {
                $jsonresult = array('error' => '10', 'errmsg' => "Something went wrong. Please retry sending email!");
            }
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => "Something went wrong. Please retry sending email!");
        }
        return $jsonresult;
    }


    public function send_mails($sender, $sendername, $receipent, $subject, $mail, $cc1 = '', $cc2 = '')
    {
        $email_setting = array('mailtype' => 'html');
        $this->load->library('email', $email_setting);

        $this->email->from($sender, $sendername);
        $this->email->to($receipent);

        if ($cc1 != '')
            $this->email->cc($cc1);

        $this->email->subject($subject);
        $this->email->message($mail);
        $this->email->send();
    }

    function get_mail_count($userid, $email)
    {
        $sql_query = " select count(*) as totalreceived from mc_mailbox as a 
                       inner join mc_user as b on a.sender=b.user_email
                       where receipent= '$email' and emailstatus < 2 ";
        //emailstatus: 0:new  1: read by sender(outbox) 2: read by receiver(inbox) 3: read all
        $rst = $this->db->query($sql_query);
        $jsonresult[] = array(
            'error1' => '0',
            'errmsg' => 'Received email count fetched!',
            'count' => $rst->row()->totalreceived);
        return $jsonresult;
    }

}

?>