<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Business extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->library(array('session', 'pagination'));
        $this->load->model(array('Knows', 'Members'));
    }

    public function index()
    {
        if (!$this->session->has_userdata('id')) {
            redirect('/login', 'refresh');
        }
        $uid = $this->session->id;

        if (intval($this->uri->segment(3))) {
            $offset = $this->uri->segment(3);
            $offset = ($offset > 0) ? $offset - 1 : $offset;
        } else {
            $offset = 0;
        }
        $this->session->set_userdata(array('bs_search_offset' => $offset));

        $this->load->model("MyGlobalSearchLog");
        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['profile_img'] = $this->config->item('profile_img');
        $pagedata['site_path'] = $this->config->item('site_path');
        $pagedata['title'] = "Search Members";
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
        $iszip = (is_int($this->input->post('gscityorzip')) == true ? 1 : 0);

        if ($this->input->post("connect_req") == "send") {
            $connect_data = array(
                'partnerid' => $this->input->post('partnerid'),
                'useremail' => $this->input->post('useremail'),
                'user_id' => $uid,
                'connect_req' => $this->input->post('connect_req'));
            $req_result = $this->Members->request_connection($connect_data);
            $pagedata['req_result'] = $req_result;
            $this->session->set_userdata("error_code", $req_result['error']);
            $this->session->set_userdata("msg_error", $req_result['errmsg']);

            redirect('/business/search/' . $offset, 'refresh');
        }

//        if ($this->input->post("btn_send_email") == 'send_email') {
//            $this->load->model("Members");
//            $receipent = $this->Members->getprofile($this->input->post('receipentid'));
//
//            $receipent_email = $receipent->row()->user_email;
//            $ds = DIRECTORY_SEPARATOR;
//            $path = $this->config->item("site_path");
//            if (file_exists($path . "templates/black_template_03.txt")) {
//                $filecontent = file_get_contents($path . "templates/black_template_03.txt");
//                $filecontent = str_replace("{mail_body}", $this->input->post("mailbody"), $filecontent);
//                $filecontent = str_replace("{partner}", $receipent->row()->username, $filecontent);
//                $filecontent = str_replace("{salutation}", $this->session->name, $filecontent);
//                $filecontent = str_replace("{year}", date('Y'), $filecontent);
//                $mailbody = $filecontent;
//            }
//            send_email($receipent_email, $this->session->email,
//                $this->session->name,
//                $this->input->post("membermailsubject"),
//                $mailbody);
//
//            $this->load->model('Mailbox');
//            $data = array(
//                'id' => $this->input->post("receipentid"),
//                'subject' => $this->input->post("membermailsubject"),
//                'mailbody' => $this->input->post("mailbody"),
//                'username' => $this->session->name,
//                'senderemail' => $this->session->email,
//                'senderphone' => $this->session->phone
//            );
//            $inbox = $this->Mailbox->send_mail_log($data);
//            redirect('/business/search/' . $offset, 'refresh');
//        }




        if ($this->input->post("btn_global_search") == "global_search" && $this->input->post('gskey') != '') {
            $offset = 0;
            $search_data = array(
                'bs_search_key' => $this->input->post('gskey'),
                'bs_search_city' => $this->input->post('gscityorzip'),
                'bs_search_vocation' => $this->input->post('gskey'),
                'bs_search_searched_members' => '',
                'bs_search_offset' => $offset,
                'bs_search_userid' => $uid,
                'bs_search_usertype' => 1,
                'bs_search_iszip' => $iszip);
            $this->session->set_userdata($search_data);
        } else {
            $search_data = array(
                'bs_search_key' => $this->session->bs_search_key,
                'bs_search_city' => $this->session->bs_search_city,
                'bs_search_vocation' => $this->session->bs_search_vocation,
                'bs_search_searched_members' => $this->session->bs_search_searched_members,
                'bs_search_offset' => $offset,
                'bs_search_userid' => $this->session->bs_search_userid,
                'bs_search_usertype' => 1,
                'bs_search_iszip' => $this->session->bs_search_iszip);
        }
        $this->MyGlobalSearchLog->add(array(
            'user_id' => $search_data['bs_search_userid'],
            'keyword' => $search_data['bs_search_key'],
            'city_zip' => $search_data['bs_search_iszip']
        ));


        $nearest_knows = $this->Knows->search_nearest($search_data);

        $this->session->set_userdata(array('bs_search_searched_members' => $nearest_knows['memberids']));

        $pagedata['nearest_knows'] = $nearest_knows;
        $pagedata['offset'] = $offset+1;

        //load questions
        $this->load->model("Questions");
        $pagedata['allquestions'] = $this->Questions->get_questions_bytype('rating');

        //      ++++++++++++++++ pagination  start   +++++++++++++
        $pager_config['base_url'] = $this->config->item('base_url') . 'business/search/';
        $pager_config['total_rows'] = $nearest_knows['member_count'];
        $pager_config['per_page'] = 10;
        $pager_config['uri_segment'] = 3;
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
        $this->load->view('business/index', $pagedata);
        $this->load->view('common/mail_composer', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }

    public function knows()
    {
        if (!$this->session->has_userdata('id')) {
            redirect('/login', 'refresh');
        }
        $uid = $this->session->id;

        if (intval($this->uri->segment(4))) {
            $offset = $this->uri->segment(4);
            $offset = ($offset > 0) ? $offset - 1 : $offset;
        } else {
            $offset = 0;
        }
//        echo $offset;
//        exit();
        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['profile_img'] = $this->config->item('profile_img');
        $pagedata['site_path'] = $this->config->item('site_path');
        $pagedata['title'] = "Search Members";
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

        if ($this->input->post("connect_req") == "send") {
            $page = $this->input->post('page');
            $connect_data = array(
                'partnerid' => $this->input->post('partnerid'),
                'useremail' => $this->input->post('useremail'),
                'user_id' => $uid,
                'connect_req' => $this->input->post('connect_req'));
            $req_result = $this->Members->request_connection($connect_data);
            $pagedata['req_result'] = $req_result;
            $this->session->set_userdata("error_code", $req_result['error']);
            $this->session->set_userdata("msg_error", $req_result['errmsg']);
            redirect('/business/search/knows/' . $page, 'refresh');
        }

        if ($this->input->post("bcp") == "claim_profile") {
            $page = $this->input->post('page');
            $pc_data = array(
                'knowid' => $this->input->post('knowid'),
                'email' => $this->input->post('email'),
                'user_id' => $uid,
                'name' => $this->input->post('name'));
            $req_result = $this->Members->send_claim_profile_invite($pc_data);

            $this->session->set_userdata("msg_error", $req_result['errmsg']);
            redirect('/business/search/knows/' . $page, 'refresh');
        }
        $search_data = array(
            'bs_search_key' => $this->session->bs_search_key,
            'bs_search_city' => $this->session->bs_search_city,
            'bs_search_vocation' => $this->session->bs_search_vocation,
            'bs_search_searched_members' => $this->session->bs_search_searched_members,
            'bs_search_offset' => $offset,
            'bs_search_userid' => $this->session->bs_search_userid,
            'bs_search_usertype' => 2,
            'bs_search_iszip' => $this->session->bs_search_iszip);




        $nearest_knows = $this->Knows->search_nearest($search_data);
        $pagedata['nearest_knows'] = $nearest_knows;
        $pagedata['offset'] = $offset+1;
        $pagedata['member_offset'] = $this->session->bs_search_offset;
        //load questions
        $this->load->model("Questions");
        $pagedata['allquestions'] = $this->Questions->get_questions_bytype('rating');

        //      ++++++++++++++++ pagination  start   +++++++++++++
        $pager_config['base_url'] = $this->config->item('base_url') . 'business/search/knows';
        $pager_config['total_rows'] = $nearest_knows['know_count'];
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
        $this->load->view('business/member_knows', $pagedata);
        $this->load->view('common/mail_composer', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }


    public function nearby()
    {
        $uid = $this->session->id;
        if ($this->session->role == 'admin') {
            redirect('/dashboard', 'refresh');
        }

        if (!$this->session->has_userdata('id')) {
            redirect('/login', 'refresh');
        }

        $uid = $this->session->id;
        $offset = (intval($this->uri->segment(3)) ? $this->uri->segment(3) : 0);
        $offset2 = (intval($this->uri->segment(4)) ? $this->uri->segment(4) : 0);
        $pagedata['offset2'] = $offset2;
        $pagedata['base'] = $this->config->item('base_url');
        $pagedata['site'] = $this->config->item('site_url');
        $pagedata['image'] = $this->config->item('image');
        $pagedata['css'] = $this->config->item('css');
        $pagedata['js'] = $this->config->item('js');
        $pagedata['asset'] = $this->config->item('asset');
        $pagedata['title'] = "Nearby Members";

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
        $iszip = (is_int($this->input->post('gscityorzip')) == true ? 1 : 0);
        $search_data = array('uid' => $uid);
        $this->load->model("Mc_Business");
        $nearest_knows = $this->Mc_Business->search_nearby($search_data);
        $pagedata['nearest_knows'] = $nearest_knows;
        $pager_config['base_url'] = $this->config->item('base_url') . 'business/search';
        $pager_config['per_page'] = 10;
        $pager_config['full_tag_open'] = "<ul class='pagination'>";
        $pager_config['full_tag_close'] = "</ul>";
        $pager_config['num_tag_open'] = '<li>';
        $pager_config['num_tag_close'] = '</li>';
        $pager_config['cur_tag_open'] = "<li class='disabled'><li class='active'><a href='#'>";
        $pager_config['cur_tag_close'] = "<span class='sr-only'></span></a></li>";
        $pager_config['next_tag_open'] = "<li>";
        $pager_config['next_tagl_close'] = "</li>";
        $pager_config['prev_tag_open'] = "<li>";
        $pager_config['prev_tagl_close'] = "</li>";
        $pager_config['first_tag_open'] = "<li>";
        $pager_config['first_tagl_close'] = "</li>";
        $pager_config['last_tag_open'] = "<li>";
        $pager_config['last_tagl_close'] = "</li>";
        $pagedata['pager_config'] = $pager_config;
        $this->load->view('template/head', $pagedata);
        $this->load->view('template/header', $pagedata);
        $this->load->view('template/common_header', $pagedata);
        $this->load->view('template/navigation_side', $pagedata);
        $this->load->view('business/nearby', $pagedata);
        $this->load->view('template/footer', $pagedata);
    }

}
