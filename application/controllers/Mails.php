<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mails extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library(array('session', 'pagination'));

    }

    public function index()
    {
        if (!$this->session->has_userdata('id')) {
            redirect('/login', 'refresh');
        }
        $uid = $this->session->id;

        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['title'] = "Inbox";

        $this->load->model('Members');
        $pagedata['member'] = $this->Members->getprofile($uid);
        $this->load->model('Helpbuttons');
        $helpbuttons = $this->Helpbuttons->getbuttons();

        $button_array = array();
        foreach ($helpbuttons->result() as $row) {
            array_push($button_array, array('id' => $row->id,
                'helptitle' => $row->helptitle,
                'helpvideo' => $row->helpvideo));
        }

        $mailtype = $this->uri->segment(3);
        if ($mailtype == null) {
            $mailtype = 0;
        }
        $offset = $this->uri->segment(4);
        if (!intval($offset)) {
            $offset = 0;
        } else {
            $offset = intval($offset) - 1;
        }

        $pagedata['help_data_buttons'] = $button_array;

        $this->load->model('Mailbox');
        $this->load->model('Memberconnection');

        //remove mail if necessary
        if ($this->input->post("mailid") > 0) {
            $mailid = $this->input->post("mailid");
            $this->Mailbox->remove($mailid);
        }

        $search_filter = array(
            'receipent' => $this->session->email,
            'mailtype' => $mailtype,
            'offset' => $offset
        );

        $inbox = $this->Mailbox->get_inbox($search_filter);
        if ($inbox['num_rows'] > 0):
            foreach ($inbox['result']->result() as $mitem) {
                $mitem->connect_status = $this->Memberconnection->get_status(array('source' => $this->session->id, 'target' => $mitem->partnerid));
            }
        endif;

        $pagedata['mailtype'] = $mailtype;
        if ($this->input->post("btn_send_email") == 'send_email') {
            $data = array(
                'id' => $this->input->post("receipentid"),
                'subject' => $this->input->post("membermailsubject"),
                'mailbody' => $this->input->post("mailbody"),
                'username' => $this->session->name,
                'senderemail' => $this->session->email,
                'senderphone' => $this->session->phone
            );
            $inbox = $this->Mailbox->send_mail_log($data);
            redirect('/mails/inbox', 'refresh');
        }
        $pagedata['inbox'] = $inbox;

        //      ++++++++++++++++ pagination  start   +++++++++++++
        $pager_config['base_url'] = $this->config->item('base_url') . 'mails/inbox/' . $mailtype . '/';
        $pager_config['total_rows'] = $inbox['num_rows'];
        $pager_config['per_page'] = 10;
        $pager_config['uri_segment'] = 4;
        // custom paging configuration
        $pager_config['num_links'] = 5;
        $pager_config['use_page_numbers'] = TRUE;
        $pager_config['reuse_query_string'] = TRUE;

        $pager_config['full_tag_open'] = "<ul class='pagination'>";
        $pager_config['full_tag_close'] = "</ul>";
        $pager_config['cur_page'] = $offset + 1;
//        $pager_config['anchor_class'] = "page";

        $pager_config['first_link'] = '&Lt;';
        $pager_config['first_tag_open'] = '<li class="first-link">';
        $pager_config['first_tag_close'] = '</li>';

        $pager_config['last_link'] = '&Gt;';
        $pager_config['last_tag_open'] = '<li class="last-link">';
        $pager_config['last_tag_close'] = '</li>';

        $pager_config['next_link'] = '&gt;';
        $pager_config['next_tag_open'] = '<li class="nextlink">';
        $pager_config['next_tag_close'] = '</li>';

        $pager_config['prev_link'] = '&lt;';
        $pager_config['prev_tag_open'] = '<li class="prevlink">';
        $pager_config['prev_tag_close'] = '</li>';

        $pager_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $pager_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";

        $pager_config['num_tag_open'] = '<li class="numlink">';
        $pager_config['num_tag_close'] = '</li>';

        $this->pagination->initialize($pager_config);
        $pagedata["links"] = $this->pagination->create_custom_links();
        //       =================  pagination  end    =====================


        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('template/navigation_side', $pagedata);
        $this->load->view('mails/inbox', $pagedata);
        $this->load->view('common/mail_composer', $pagedata);
        $this->load->view('template/footer', $pagedata);

    }

    public function outbox()
    {
        if (!$this->session->has_userdata('id')) {
            redirect('/login', 'refresh');
        }
        $uid = $this->session->id;
        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['title'] = "Outbox";

        $this->load->model('Members');
        $pagedata['member'] = $this->Members->getprofile($uid);
        $this->load->model('Helpbuttons');
        $helpbuttons = $this->Helpbuttons->getbuttons();

        $button_array = array();
        foreach ($helpbuttons->result() as $row) {
            array_push($button_array, array('id' => $row->id,
                'helptitle' => $row->helptitle,
                'helpvideo' => $row->helpvideo));
        }
        $pagedata['help_data_buttons'] = $button_array;
        $this->load->model('Mailbox');


        //remove mail if necessary
        if ($this->input->post("mailid") > 0) {
            $mailid = $this->input->post("mailid");
            $this->Mailbox->remove($mailid);
        }


        $mailtype = $this->uri->segment(3);
        if ($mailtype == null) {
            $mailtype = 0;
        }
        $offset = $this->uri->segment(4);
        if (!intval($offset)) {
            $offset = 0;
        } else {
            $offset = intval($offset) - 1;
        }


        $inbox = $this->Mailbox->get_outbox(array(
                'receipent' => $this->session->email,
                'mailtype' => $mailtype,
                'offset' => $offset)
        );

        $this->load->model("Memberconnection");
        if ($inbox['num_rows'] > 0):
            foreach ($inbox['result']->result() as $mitem) {
                $mitem->connect_status = $this->Memberconnection->get_status(array('source' => $this->session->id, 'target' => $mitem->partnerid));
            }
        endif;

        $pagedata['mailtype'] = $mailtype;
        if ($this->input->post("btn_send_email") == 'send_email') {
            $data = array(
                'id' => $this->input->post("receipentid"),
                'subject' => $this->input->post("membermailsubject"),
                'mailbody' => $this->input->post("mailbody"),
                'username' => $this->session->name,
                'senderemail' => $this->session->email,
                'senderphone' => $this->session->phone
            );
            $inbox = $this->Mailbox->send_mail_log($data);
            redirect('/mails/inbox', 'refresh');
        }
        $pagedata['inbox'] = $inbox;


        //      ++++++++++++++++ pagination  start   +++++++++++++
        $pager_config['base_url'] = $this->config->item('base_url') . 'mails/outbox/' . $mailtype . '/';
        $pager_config['total_rows'] = $inbox['num_rows'];
        $pager_config['per_page'] = 10;
        $pager_config['uri_segment'] = 4;
        // custom paging configuration
        $pager_config['num_links'] = 5;
        $pager_config['use_page_numbers'] = TRUE;
        $pager_config['reuse_query_string'] = TRUE;

        $pager_config['full_tag_open'] = "<ul class='pagination'>";
        $pager_config['full_tag_close'] = "</ul>";
        $pager_config['cur_page'] = $offset + 1;
        $pager_config['anchor_class'] = "page";

        $pager_config['first_link'] = '&Lt;';
        $pager_config['first_tag_open'] = '<li>';
        $pager_config['first_tag_close'] = '</li>';

        $pager_config['last_link'] = '&Gt;';
        $pager_config['last_tag_open'] = '<li class="lastlink">';
        $pager_config['last_tag_close'] = '</li>';

        $pager_config['next_link'] = '&gt;';
        $pager_config['next_tag_open'] = '<li class="nextlink">';
        $pager_config['next_tag_close'] = '</li>';

        $pager_config['prev_link'] = '&lt;';
        $pager_config['prev_tag_open'] = '<li class="prevlink">';
        $pager_config['prev_tag_close'] = '</li>';

        $pager_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $pager_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";

        $pager_config['num_tag_open'] = '<li class="numlink">';
        $pager_config['num_tag_close'] = '</li>';

        $this->pagination->initialize($pager_config);
        $pagedata["links"] = $this->pagination->create_custom_links();
        //       =================  pagination  end    =====================

        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('template/navigation_side', $pagedata);
        $this->load->view('mails/outbox', $pagedata);
        $this->load->view('common/mail_composer', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }


    public function read()
    {
        if (!$this->session->has_userdata('id')) {
            redirect('/login', 'refresh');
        }
        $uid = $this->session->id;

        if ($this->input->get('type')) {
            $pagedata['type'] = $this->input->get('type');
        } else {
            redirect('/mails/inbox', 'refresh');
        }


        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['title'] = ($pagedata['type'] == 'in') ? "Inbox" : "Outbox";
        $this->load->model('Members');
        $pagedata['member'] = $this->Members->getprofile($uid);
        $this->load->model('Helpbuttons');
        $helpbuttons = $this->Helpbuttons->getbuttons();

        $button_array = array();
        foreach ($helpbuttons->result() as $row) {
            array_push($button_array, array('id' => $row->id,
                'helptitle' => $row->helptitle,
                'helpvideo' => $row->helpvideo));
        }
        $pagedata['help_data_buttons'] = $button_array;
        $this->load->model('Mailbox');


        $mailid = $this->input->get("mail");
        $mail_to_read = $this->Mailbox->get_mail($mailid);
        $curr_status = $mail_to_read->result()[0]->emailstatus;
        //emailstatus: 0:new  1: read by sender(outbox) 2: read by receiver(inbox) 3: read all
        if ($pagedata['type'] == 'in' && $curr_status < 2) {
            $curr_status += 2;
            $this->Mailbox->update(array('emailstatus' => $curr_status), $mailid);
        } else if ($pagedata['type'] == 'out' && $curr_status % 2 == 0) {
            $curr_status += 1;
            $this->Mailbox->update(array('emailstatus' => $curr_status), $mailid);
        }

        $this->load->model("Memberconnection");
        $pagedata['mail_details'] = $mail_to_read;
        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('template/navigation_side', $pagedata);
        $this->load->view('mails/reader', $pagedata);
        $this->load->view('common/mail_composer', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }
}
