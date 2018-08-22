<?php

class Members extends CI_Model
{
    var $id = '';
    var $user_email = '';
    var $user_pass = '';
    var $username = '';
    var $user_role = '';
    var $is_employee = '';
    var $user_pkg = '';
    var $user_phone = '';
    var $image = '';
    var $createdOn = '';
    var $user_status = '';
    var $group_status = '';
    var $resPWToken = '';
    var $resPWExp = '';
    var $publicprofile = '';
    var $profileisvisible = '';
    var $tags = '';
    var $signup_type = '';
    var $busi_name = '';
    var $user_type = '';
    var $busi_location_street = '';
    var $busi_location = '';
    var $busi_type = '';
    var $busi_hours = '';
    var $busi_website = '';
    var $privacyoption = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    public function add($data)
    {
        $memberid = 0;
        $this->db->select("count(*) as rcnt");
        $this->db->from("mc_user");
        $this->db->where("user_email", $data['user_email']);
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            $member_row = $result->row();
            if ($member_row->rcnt == 0) {
                $this->db->insert("mc_user", $data);
                $memberid = $this->db->insert_id();
            }
        }
        return $memberid;
    }

    public function claim_profile($data)
    {
        $email = $data['email'];
        $password = $data['password'];

        $this->db->set('user_pass', $password);
        $this->db->where('user_email', $email);
        $result = $this->db->update('mc_user');
        return $result;
    }

    public function update($data, $id)
    {
        $this->db->where("id", $id);
        $this->db->update("mc_user", $data);
    }

    public function remove($id)
    {
        $this->db->where("id", $id);
        $this->db->delete("mc_user");
        $this->db->where("user_id", $id);
        $this->db->delete("user_details");
    }

    public function update_url($data)
    {
        $id = $data['id'];
        $sql_query = "select username from  mc_user where id='" . $id . "' and ( user_shortcode='' or user_shortcode is null ) ";
        $profile_rs = $this->db->query($sql_query);

        if ($profile_rs->num_rows() > 0) {
            $profile = $profile_rs->row();
            $name = $profile->username;
            if ($name != '') {
                $username_parts = explode(' ', $name);
                $short_code_name = implode('-', $username_parts);
                $ecount_rs = $this->db->query("select count(*) as cnt from mc_user where id <> '" . $id . "'  and user_shortcode='$short_code_name'");
                $ecount = $ecount_rs->row()->cnt;
                if ($ecount > 0) {
                    $short_code_name = $short_code_name . "-" . ($ecount > 9 ? $ecount : "0" . $ecount);
                }
                $this->db->where("id", $id);
                $this->db->update("mc_user",
                    array('user_shortcode' => strtolower($short_code_name)));
                $error = 'Profile URL is updated!';
            } else {
                $error = 'Signup is incomplete!';
            }
        } else {
            $error = 'Profile not found!';
        }
        return $error;
    }


    public function delete($id)
    {
        $this->db->where("id", $id);
        $this->db->delete('mc_user');
    }

    function check_duplicate($email)
    {
        $this->db->select("count(*) as ecnt");
        $this->db->from("mc_user");
        $this->db->where("mc_user.user_email", $email);
        $account = $this->db->get();
        if ($account->num_rows() > 0) {
            $cnt = $account->row()->ecnt;

            if ($cnt > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function get_shortcode($id)
    {
        $user_shortcode = '';
        $this->db->where("id", $id);
        $this->db->select("user_shortcode");
        $this->db->from("mc_user");
        $result = $this->db->get();
        if ($result->num_rows() > 0) {
            $user_shortcode = $result->row()->user_shortcode;
        }
        return $user_shortcode;
    }

    function get_groups()
    {
        $query = $this->db->query("select * from groups");
        $result = [];
        foreach ($query->result() as $row) {
            $result['' . $row->id] = $row->grp_name;
        }

        return $result;
    }

    function crete_transaction($id_shortcode)
    {
        $name = str_replace("-", " ", $id_shortcode);
        echo "start transaction <br>";
        $this->db->trans_start();


        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS people  AS ( SELECT * FROM user_people );");
        echo "create people <br>";
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS mycity29_maindb.ud AS(
            select m.*, d.street, d.city, d.zip, d.current_company, d.linkedin_profile, d.country, d.groups, d.target_clients, d.target_referral_partners, d.vocations,
              d.about_your_self, d.upd_public_private, d.upd_reminder_email, d.lcid, d.keywords, d.meta
            from mc_user as m
            inner join user_details as d on m.id = d.user_id );");
        echo "create rr <br>";
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS rr AS(
            SELECT ku.id, ku.client_email, kr.question_id, kr.ranking
            FROM user_people as ku
            inner join user_rating as kr on kr.user_id = ku.id
            where ku.client_name = '$name' );");
        echo "create rrrr <br>";
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS rrrr AS(
              select rating,qst_ranking, client_email
              from (
                    select id, client_email, sum(ranking) as rating, GROUP_CONCAT(CONCAT(question_id,':',ranking)) as qst_ranking
                    from  rr
                    group by id
                    order by rating desc
              ) as rrr  group by client_email );");
        echo "create t_r <br>";
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS t_r AS(
              select user_id,rated_by, sum(ranking) as rating,  GROUP_CONCAT(CONCAT(question_id,':',ranking)) as qst_ranking
              from  mc_user_rating as rr
              group by user_id,rated_by
              order by rating desc  );");
        echo "create profile <br>";
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS profiles AS(
              select p.id as pid, p.client_name,
                  GROUP_CONCAT(DISTINCT p.client_profession ORDER BY p.client_profession ASC  SEPARATOR ',') as client_profession,
                  GROUP_CONCAT(DISTINCT p.client_phone ORDER BY p.client_phone ASC  SEPARATOR ',') as client_phone,
                  GROUP_CONCAT(DISTINCT p.client_location ORDER BY p.client_location ASC  SEPARATOR ',') as client_location,
                  GROUP_CONCAT(DISTINCT p.client_zip ORDER BY p.client_zip ASC  SEPARATOR ',') as client_zip,
                  GROUP_CONCAT(DISTINCT p.user_group ORDER BY p.user_group ASC  SEPARATOR ',') as user_group,
                  GROUP_CONCAT(DISTINCT p.company ORDER BY p.company ASC  SEPARATOR ',') as company,
                  GROUP_CONCAT(DISTINCT p.tags ORDER BY p.tags ASC  SEPARATOR ',') as peopel_tags,
                  GROUP_CONCAT(DISTINCT IF(p.isinvited = 1, CONCAT(p.user_id,'-->',p.isinvited),NULL) ORDER BY p.user_id ASC  SEPARATOR ',') as isinvited,
                  GROUP_CONCAT(DISTINCT p.user_id ORDER BY p.user_id ASC  SEPARATOR ',') as known_user_id,
                  p.client_email,
                  u.user_email as known_user_email, u.username as known_username, u.user_role as known_user_role, u.user_pkg as known_user_pkg, u.user_phone as known_user_phone,
                  ud.*
              from people as p
              left join mc_user as u on p.user_id = u.id
              left join ud on p.client_email = ud.user_email
              where p.client_name = '$name'
              group by client_email  );");
        echo "create tt_r <br>";
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS tt_r AS(
              select user_id, rating, qst_ranking from  t_r
              group by user_id );");
        echo "create id_shortcode <br>";
        $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS $id_shortcode AS(
              select profiles.id, profiles.client_email, IF (tt_r.rating is not null,tt_r.rating, rrrr.rating) as rating,
            IF (tt_r.qst_ranking is not null,  tt_r.qst_ranking, rrrr.qst_ranking) as qst_ranking ,
            profiles .*, IF (profiles.user_shortcode = '$id_shortcode', true,false) as profile_exist
            from profiles
            left join  rrrr on(profiles.id IS NULL and profiles.client_email = rrrr.client_email )
            left join  tt_r on(profiles.id is not null and profiles.id = tt_r.user_id)
            order by profile_exist desc, rating desc;");
        $this->db->trans_complete();
        echo "create table finished <br>";
    }

    function drop_transaction($id_shortcode)
    {
        echo "start drop <br>";
        $this->db->trans_start();
        $this->db->query("DROP TABLE IF EXISTS people;");
        $this->db->query("DROP TABLE IF EXISTS ud;");
        $this->db->query("DROP TABLE IF EXISTS rr;");
        $this->db->query("DROP TABLE IF EXISTS rrrr;");
        $this->db->query("DROP TABLE IF EXISTS t_r;");
        $this->db->query("DROP TABLE IF EXISTS profiles;");
        $this->db->query("DROP TABLE IF EXISTS tt_r;");
        $this->db->query("DROP TABLE IF EXISTS $id_shortcode;");
        $this->db->trans_complete();
        echo "complete drop <br>";
    }

    function getprofile_count_from_all($id_shortcode)
    {
//        $query = "select count(*) as reccnt
//                from( select p.id as pid, p.client_name,
//                          GROUP_CONCAT(DISTINCT p.client_profession ORDER BY p.client_profession ASC  SEPARATOR ',') as client_profession,
//                          GROUP_CONCAT(DISTINCT p.client_phone ORDER BY p.client_phone ASC  SEPARATOR ',') as client_phone,
//                          GROUP_CONCAT(DISTINCT p.client_location ORDER BY p.client_location ASC  SEPARATOR ',') as client_location,
//                          GROUP_CONCAT(DISTINCT p.client_zip ORDER BY p.client_zip ASC  SEPARATOR ',') as client_zip,
//                          GROUP_CONCAT(DISTINCT p.user_group ORDER BY p.user_group ASC  SEPARATOR ',') as user_group,
//                          GROUP_CONCAT(DISTINCT p.company ORDER BY p.company ASC  SEPARATOR ',') as company,
//                          GROUP_CONCAT(DISTINCT p.tags ORDER BY p.tags ASC  SEPARATOR ',') as peopel_tags,
//                          GROUP_CONCAT(DISTINCT IF(p.isinvited = 1, CONCAT(p.user_id,'-->',p.isinvited),NULL) ORDER BY p.user_id ASC  SEPARATOR ',') as isinvited,
//                          GROUP_CONCAT(DISTINCT p.user_id ORDER BY p.user_id ASC  SEPARATOR ',') as known_user_id,
//                          p.client_email,
//                          u.user_email as known_user_email, u.username as known_username, u.user_role as known_user_role, u.user_pkg as known_user_pkg, u.user_phone as known_user_phone,
//                          ud.*
//                      from user_people as p
//                      left join mc_user as u on p.user_id = u.id
//                      left join
//                           (
//                              select m.id, m.user_email, m.user_pass, m.username, m.user_role, m.is_employee, m.user_pkg, m.user_phone, m.image, m.createdOn, m.user_status, m.group_status, m.resPWToken,
//                                 m.resPWExp, m.publicprofile, m.profileisvisible, m.tags, m.signup_type, m.busi_name, m.user_type, m.busi_location_street, m.busi_location, m.busi_type,
//                                 m.busi_hours, m.busi_website, m.privacyoption, m.verified, m.user_shortcode,
//                                 d.street, d.city, d.zip, d.current_company, d.linkedin_profile, d.country, d.groups, d.target_clients, d.target_referral_partners, d.vocations,
//                                 d.about_your_self, d.upd_public_private, d.upd_reminder_email, d.lcid, d.keywords, d.meta
//                             from mc_user as m
//                             inner join user_details as d on m.id = d.user_id
//                           ) as ud on p.client_email = ud.user_email
//                      where p.client_name = '$name'
//                      group by client_email
//                ) as profiles
//
//                left join (
//                              select rating,qst_ranking, client_email
//                              from (
//                                    select id, client_email, sum(ranking) as rating,
//                                           GROUP_CONCAT(CONCAT(question_id,':',ranking)) as qst_ranking
//                                    from (
//                                          SELECT ku.id, ku.client_email, kr.question_id, kr.ranking
//                                          FROM user_people as ku
//                                          inner join user_rating as kr on kr.user_id = ku.id
//                                    ) as rr
//                                    group by id
//                                    order by rating desc
//                              ) as rrr
//                              group by client_email
//                ) as rrrr on (profiles.id IS NULL and profiles.client_email = rrrr.client_email )
//
//                left join (
//                       select user_id, rating, qst_ranking
//                       from (
//                           select user_id,rated_by, sum(ranking) as rating,
//                           GROUP_CONCAT(CONCAT(question_id,':',ranking)) as qst_ranking
//                           from  mc_user_rating as rr
//
//                           group by user_id,rated_by
//                           order by rating desc
//                        ) as t_r
//                        group by user_id
//                ) as tt_r on (profiles.id is not null  and profiles.id = tt_r.user_id)";
        $this->crete_transaction($id_shortcode);
        $result_count = $this->db->query("select count(*) as reccnt from $id_shortcode");
        $pages = $result_count->row()->reccnt;
        return $pages;
    }

    function getprofile_from_all($id_shortcode, $offset)
    {
        $name = str_replace("-", " ", $id_shortcode);
        $query = "select profiles.id, profiles.client_email, IF(tt_r.rating is not null,tt_r.rating, rrrr.rating) as rating, IF(tt_r.qst_ranking is not null, tt_r.qst_ranking,rrrr.qst_ranking) as 
                          qst_ranking , profiles.*, IF(profiles.user_shortcode='$id_shortcode', true,false) as profile_exist
                  from(
                        SELECT p.id as pid, p.client_name, p.client_email,
                                        GROUP_CONCAT(DISTINCT p.client_profession SEPARATOR ',') as client_profession,
                                        GROUP_CONCAT(DISTINCT p.client_phone SEPARATOR ',') as client_phone,
                                        GROUP_CONCAT(DISTINCT p.client_location SEPARATOR ',') as client_location,
                                        GROUP_CONCAT(DISTINCT p.client_zip SEPARATOR ',') as client_zip,
                                        GROUP_CONCAT(DISTINCT p.user_group SEPARATOR ',') as user_group,
                                        GROUP_CONCAT(DISTINCT p.company SEPARATOR ',') as company,
                                        GROUP_CONCAT(DISTINCT p.tags SEPARATOR ',') as peopel_tags,
                                        GROUP_CONCAT(DISTINCT IF(p.isinvited = 1, CONCAT(p.user_id,'-->',p.isinvited),NULL)  SEPARATOR ',') as isinvited,
                                        GROUP_CONCAT(DISTINCT p.user_id SEPARATOR ',') as known_user_id,
                                        u.user_email as known_user_email, u.username as known_username, u.user_role as known_user_role,
                                        u.user_pkg as known_user_pkg, u.user_phone as known_user_phone,
                                        ud.*
                        from ( select * from user_people where client_name = '$name') as p
                        left join mc_user as u on p.user_id = u.id
                        left join (
                                      select m.id, m.user_email, m.user_pass, m.username, m.user_role, m.is_employee, m.user_pkg, m.user_phone, m.image, m.createdOn, m.user_status, m.group_status, m.resPWToken,
                                          m.resPWExp, m.publicprofile, m.profileisvisible, m.tags, m.signup_type, m.busi_name, m.user_type, m.busi_location_street, m.busi_location, m.busi_type,
                                          m.busi_hours, m.busi_website, m.privacyoption, m.verified, m.user_shortcode,
                                          d.street, d.city, d.zip, d.current_company, d.linkedin_profile, d.country, d.groups, d.target_clients, d.target_referral_partners, d.vocations,
                                          d.about_your_self, d.upd_public_private, d.upd_reminder_email, d.lcid, d.keywords, d.meta
                                     from mc_user as m
                                     inner join user_details as d on m.id = d.user_id
                                   ) as ud on p.client_email = ud.user_email
                        group by client_email
                  ) as profiles
                    
                  left join (
                            select rating,qst_ranking, client_email
                            from (
                                select id, client_email, sum(ranking) as rating,  GROUP_CONCAT(CONCAT(question_id,':',ranking)) as qst_ranking
                                from (
                                      SELECT ku.id, ku.client_email, kr.question_id, kr.ranking
                                      FROM user_people as ku
                                      inner join user_rating as kr on kr.user_id = ku.id
                                      ) as rr
                                group by id
                                order by rating desc
                                ) as rrr
                            group by client_email
                   ) as rrrr on (profiles.id IS NULL and profiles.client_email = rrrr.client_email )                    
                        left join (
                            select user_id, rating, qst_ranking
                            from (
                              select user_id,rated_by, sum(ranking) as rating,
                              GROUP_CONCAT(CONCAT(question_id,':',ranking)) as qst_ranking
                              from  mc_user_rating as rr
                    
                              group by user_id,rated_by
                              order by rating desc
                            ) as t_r
                        group by user_id
                  ) as tt_r on (profiles.id is not null  and profiles.id = tt_r.user_id)
                  order by profile_exist desc, rating desc ";
        $result = $this->db->query($query);
        $query->free_result();
        return $result->result();
    }


    function getprofile($id_shortcode)
    {
        if (intval($id_shortcode) > 0) {
            $this->db->where("mc_user.id ='" . $id_shortcode . "'");
        } else {
            $this->db->where("mc_user.user_shortcode ='" . $id_shortcode . "'");
        }

        $this->db->select("*, '' keywords, '' meta, 'not specified' user_id , 'not specified' street, ''  city, '' zip, 
		'not specified'  current_company, 'not specified'  linkedin_profile, 'not specified' country, ''  groups, 
		'not specified'  target_clients, 'not specified'  target_referral_partners, 'not specified'  vocations, 'not specified'   about_your_self,
		'not specified' upd_public_private, 'not specified'  upd_reminder_email, ''  createdOn, '' lcid , 'not specified' group_names , '' linkedin_profile ");
        $this->db->from("mc_user");

        $result = $this->db->get();
        foreach ($result->result() as $row) {
            $mid = $row->id;
            $query_user_details = "select * from user_details where user_id = '" . $mid . "'";
            $user_details = $this->db->query($query_user_details);

            if ($user_details->num_rows() > 0) {
                $udetails = $user_details->row();
                $group_ids = $udetails->groups;

                if ($group_ids != ''):

                    //remove unwanted comma in cells
                    $group_ids = explode(',', $group_ids);
                    $group_ids = array_filter($group_ids, 'strlen');
                    $group_ids = implode(',', $group_ids);

                    $sql_query_group = "select group_concat(grp_name) as group_names FROM groups where id in  (" . $group_ids . ") ";
                    $group_query = $this->db->query($sql_query_group);
                    $group_row = $group_query->row();
                    $row->group_names = $group_row->group_names;
                endif;

                $row->street = $udetails->street;
                $row->city = $udetails->city;
                $row->zip = $udetails->zip;
                $row->current_company = $udetails->current_company;
                $row->linkedin_profile = $udetails->linkedin_profile;
                $row->country = $udetails->country;
                $row->groups = $udetails->groups;
                $row->target_clients = $udetails->target_clients;
                $row->target_referral_partners = $udetails->target_referral_partners;
                $row->vocations = $udetails->vocations;
                $row->about_your_self = $udetails->about_your_self;
                $row->upd_public_private = $udetails->upd_public_private;
                $row->upd_reminder_email = $udetails->upd_reminder_email;
                $row->createdOn = $udetails->createdOn;
                $row->lcid = $udetails->lcid;
                $row->linkedin_profile = $udetails->linkedin_profile;
                $row->keywords = $udetails->keywords;
                $row->meta = $udetails->meta;

            }
        }
//        echo "<pre>";
//        print_r($result);
//        echo "</pre>";
//        exit();
        return $result;
    }

    function get_profile_by_email($email)
    {
        $this->db->where("user_email", $email);
        $this->db->select("*");
        $this->db->from("mc_user");
        $result = $this->db->get();
        return $result;
    }


    function login($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $rememberme = $data['rememberme'];
        $switcher = (isset($data['switcher']) ? $data['switcher'] : 'off');

        $sql_query = "select * from  mc_user where   user_email='" . $email . "'  and user_pass = '" . md5($password) . "' and user_status='1'  ";
        $profile_rs = $this->db->query($sql_query);

        if ($profile_rs->num_rows() > 0) {
            $profile = $profile_rs->row();
            $token = $profile->id . strtotime(date('Y-m-d H:i:s'));
            if ($rememberme == 1) {
                $retoken = md5($profile->id . $_SERVER['REMOTE_ADDR'] . date('d-m-Y H:i:s'));
                $profile_arr = array('id' => $profile->id,
                    'role' => $profile->user_role,
                    'package' => $profile->user_pkg,
                    'phone' => $profile->user_phone,
                    'name' => $profile->username,
                    'email' => $profile->user_email,
                    'isemployee' => $profile->is_employee,
                    'image' => $profile->image,
                    'status' => $profile->user_status,
                    'profile' => $profile->publicprofile,
                    'visibility' => $profile->profileisvisible,
                    'signupmode' => $profile->signup_type,
                    'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'),
                    'retoken' => $retoken,
                    'switcher' => $switcher
                );
                $login_data = array(
                    'userid' => $profile->id,
                    'logintime' => date('Y-m-d H:i:s'),
                    'token' => md5($token),
                    'remembertoken' => $retoken
                );
            } else {
                $profile_arr = array(
                    'id' => $profile->id,
                    'role' => $profile->user_role,
                    'package' => $profile->user_pkg,
                    'phone' => $profile->user_phone,
                    'name' => $profile->username,
                    'email' => $profile->user_email,
                    'image' => $profile->image,
                    'isemployee' => $profile->is_employee,
                    'status' => $profile->user_status,
                    'profile' => $profile->publicprofile,
                    'visibility' => $profile->profileisvisible,
                    'signupmode' => $profile->signup_type,
                    'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'),
                    'retoken' => '0',
                    'switcher' => $switcher
                );

                $login_data = array(
                    'userid' => $profile->id,
                    'logintime' => date('Y-m-d H:i:s'),
                    'token' => md5($token)
                );
                //$pdo->query("insert into mc_login_log (userid,logintime, token  ) values ('". $profile->id."', NOW(), '".md5($token)."'  )");
            }
            $this->db->insert("mc_login_log", $login_data);
            $loginid = $this->db->insert_id();
        } else {
            $profile_arr = array('id' => '0');
        }
        return $profile_arr;
    }


    function login_from_session($data)
    {
        $id = $data['id'];
        $rememberme = $data['rememberme'];
        $sql_query = "select * from  mc_user where id='" . $id . "'  ";
        $profile_rs = $this->db->query($sql_query);

        $switcher = (isset($data['switcher']) ? $data['switcher'] : 'off');


        if ($profile_rs->num_rows() > 0) {
            $profile = $profile_rs->row();
            $token = $profile->id . strtotime(date('Y-m-d H:i:s'));
            if ($rememberme == 1) {
                $retoken = md5($profile->id . $_SERVER['REMOTE_ADDR'] . date('d-m-Y H:i:s'));
                $profile_arr = array('id' => $profile->id, 'role' => $profile->user_role, 'package' => $profile->user_pkg, 'phone' => $profile->user_phone, 'name' => $profile->username, 'email' => $profile->user_email, 'image' => $profile->image,
                    'status' => $profile->user_status, 'profile' => $profile->publicprofile, 'visibility' => $profile->profileisvisible,
                    'signupmode' => $profile->signup_type, 'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'), 'retoken' => $retoken, 'switcher' => $switcher);

                $login_data = array(
                    'userid' => $profile->id,
                    'logintime' => date('Y-m-d H:i:s'),
                    'token' => md5($token),
                    'remembertoken' => $retoken
                );
            } else {
                $profile_arr = array('id' => $profile->id, 'role' => $profile->user_role, 'package' => $profile->user_pkg,
                    'phone' => $profile->user_phone, 'name' => $profile->username, 'email' => $profile->user_email, 'image' => $profile->image,
                    'status' => $profile->user_status, 'profile' => $profile->publicprofile, 'visibility' => $profile->profileisvisible,
                    'signupmode' => $profile->signup_type, 'token' => md5($token),
                    'expires' => strtotime(date('Y-m-d') . ' +1 day'), 'retoken' => '0', 'switcher' => $switcher);
                $login_data = array(
                    'userid' => $profile->id,
                    'logintime' => date('Y-m-d H:i:s'),
                    'token' => md5($token)
                );
                //$pdo->query("insert into mc_login_log (userid,logintime, token  ) values ('". $profile->id."', NOW(), '".md5($token)."'  )");
            }

            $this->db->insert("mc_login_log", $login_data);
            $loginid = $this->db->insert_id();
        } else {
            $profile_arr = array('id' => '0');
        }
        return $profile_arr;
    }

    public function join_program($data)
    {
        $id = $data['id'];
        $sql_query = "select username from  mc_user where id='" . $id . "' ";
        $profile_rs = $this->db->query($sql_query);

        if ($profile_rs->num_rows() > 0) {
            $profile = $profile_rs->row();
            $name = $profile->username;
            if ($name != '') {
                $username_parts = explode(' ', $name);
                $short_code_name = implode('-', $username_parts);
                $this->db->where("id", $id);
                $this->db->update("mc_user", array('user_shortcode' => $short_code_name));
                $error = 'Profile URL is updated!';
            } else {
                $error = 'Signup is incomplete!';
            }
        } else {
            $error = 'Profile not found!';
        }
        return $error;
    }

    public function update_profile($data, $user_id)
    {
        $this->db->where("id", $user_id);
        $this->db->update("mc_user", $data);
    }

    public function update_password($user_id, $old, $new)
    {
        $result = $this->db->query("select count(*) as rcnt from mc_user where id='$user_id' and user_pass='$old'");

        if ($result->row()->rcnt == 1) {
            $data = array('user_pass' => $new);
            $this->db->where("id", $user_id);
            $this->db->update("mc_user", $data);
            return "Password updated successfully";
        } else {
            return "Password could not be updated. Please retry.";
        }
    }

    public function request_connection($data)
    {
        $receipentid = $data['partnerid'];
        $user_id = $data['user_id'];
        $useremail = $data['useremail'];
        $requestid = 0;
        $token = md5($receipentid);
        $tokenlength = strlen($receipentid);
        $token = $receipentid . $token;
        $subject = "New Connection Request Request via MyCity.com";

        $results = $this->db->query("select * from mc_member_connections where  (firstpartner='$user_id' and secondpartner='$receipentid') and  request_type='1'  ");
        if ($results->num_rows() > 0) {
            $jsonresult = array('error' => '10', 'errmsg' => "A request for connection exists!");
            $this->db->query("update  mc_member_connections set requestdate= NOW() where  
			firstpartner=? and secondpartner=? ",
                array($user_id, $receipentid));
        } else {
            $this->db->query("insert into mc_member_connections (firstpartner, secondpartner,request_type, requestdate ) values (?,?, '1', NOW() )  ",
                array($user_id, $receipentid));
            $requestid = $this->db->insert_id();
        }

        $rslt = $this->db->query("select * from mc_user where id='$receipentid' ");
        if ($rslt->num_rows() > 0):
            $row = $rslt->row();
            $receipentmail = $row->user_email;
            $receipentname = $row->username;
            $ds = DIRECTORY_SEPARATOR;
            $path = $this->config->item('site_path');
            $mailbody = "";
            if (file_exists($path . "templates/black_template_02.txt")) {
                $template_part = file_get_contents($path . "templates/black_template_02.txt");
            }

            if (file_exists($path . "templates/connection_request.txt")) {
                $mail_body = file_get_contents($path . "templates/connection_request.txt");
                $mail_body = str_replace("{tokenid}", $token, $mail_body);
                $mail_body = str_replace("{tokenlength}", $tokenlength, $mail_body);
                $mail_body = str_replace("{year}", date('Y'), $mail_body);
            }
            $mailbody = str_replace("{mail_body}", $mail_body, $template_part);
            //insert into mail log

            $this->db->query("insert into mc_mailbox (sender, receipent, subject , emailbody , emailstatus , email_type , senton) VALUES (?,?, ?, ?, '0', '10', NOW() )",
                array($useremail, $receipentmail, $subject, $mail_body));

            send_email($receipentmail, $useremail, $this->session->name, $subject, $mailbody);

        endif;

        $jsonresult = array('error' => '0', 'mail' => $mailbody, 'errmsg' => "Member connection request placed successfully!", 'requestid' => $requestid);
        return $jsonresult;
    }


    //request connection in DB without sending email
    public function request_connection_in_db($data)
    {
        $receipentid = $data['partnerid'];
        $user_id = $data['user_id'];

        $results = $this->db->query("select * from mc_member_connections where  
		(firstpartner='$user_id' and secondpartner='$receipentid') and  request_type='1'  ");
        if ($results->num_rows() > 0) {
            $jsonresult = array('error' => '10', 'errmsg' => "A request for connection exists!");
            $this->db->query("update  mc_member_connections set requestdate= NOW(), status='1', approvedon=NOW() where  
			firstpartner=? and secondpartner=? ",
                array($user_id, $receipentid));
        } else {
            $this->db->query("insert into mc_member_connections 
			(firstpartner, secondpartner,request_type, requestdate, status, approvedon ) values (?,?, '1', NOW(), '1', NOW() )  ",
                array($user_id, $receipentid));
            $requestid = $this->db->insert_id();
        }

    }


    function get_rated_partners($data)
    {
        $size = $data['size'];
        $userid = $data['userid'];
        $vocation = $data['vocation'];
        $group = $data['group'];
        $goto = $data['goto'];
        $sql_query = " SELECT *, 0 as rate FROM user_people WHERE user_id = '$userid' AND client_profession='$vocation'
		AND  FIND_IN_SET('$group',  user_group ) > 0  ";
        $i = 0;
        $result = $this->db->query($sql_query);
        if ($result->num_rows() > 0) {
            foreach ($result->result() as $item) {
                $grouplist = $item->user_group;
                if ($grouplist != '') {
                    $grpQry = "select group_concat(grp_name) as groupnames from groups where id in ($grouplist)";
                    $rstGroups = $this->db->query($grpQry);
                    if ($rstGroups->num_rows() > 0) {
                        $item->user_group = $rstGroups->row()->groupnames;
                    }
                }
                //get ratings
                $knowid = $item->id;
                $rateQry = "select sum(ranking) user_ranking from user_rating where  user_id  = '$knowid'";
                $rstRate = $this->db->query($rateQry);
                if ($rstRate->num_rows() > 0) {
                    $item->rate = $rstRate->row()->user_ranking;
                }
            }
            $jsonresult = array('error' => '0', 'errmsg' => 'Partners information retrieved!', 'result' => $result);
        } else
            $jsonresult = array('error' => '10', 'errmsg' => "No partner information found!");

        return $jsonresult;
    }


    public function send_claim_profile_invite($data)
    {
        $id = $data['knowid'];
        $mid = $data['user_id'];
        $name = $data['name'];
        $to = $data['email'];
        $subject = "Claim your MyCity.com Profile. People are waiting to connect with you.";
        $from = "bob@mycity.com";
        $token = md5($id);
        $tokenlength = strlen($id);
        $token = $id . $token;
        $isender = $mid;
        $isenderlength = strlen($isender);
        $isenderhash = md5($isender);

        $ds = DIRECTORY_SEPARATOR;
        $path = $this->config->item('site_path');
        $jsonresult = '';

        //get sender profile
        $memberprofile = $this->getprofile($mid)->row();
        $pimage_path = $this->config->item('site_path') . $this->config->item('profile_img') . $memberprofile->image;

        if (file_exists($pimage_path)) {
            $profileimage = $this->config->item('base_url') . $this->config->item('profile_img') . $memberprofile->image;
        } else {
            $profileimage = $this->config->item('base_url') . $this->config->item('image') . "no-photo.png";
        }

        $template_part = '{mail_body}';
        if (file_exists($path . "templates/profile_invite_by_member_02.txt")) {
            $mailbody = file_get_contents($path . "templates/profile_invite_by_member_02.txt");
            $mailbody = str_replace("{receipent}", $name, $mailbody);
            $mailbody = str_replace("{sender}", $this->session->name, $mailbody);
            $mailbody = str_replace("{tokenid}", $token, $mailbody);
            $mailbody = str_replace("{tokenlength}", $tokenlength, $mailbody);
            $mailbody = str_replace("{tokenlengthhash}", md5($tokenlength), $mailbody);
            $mailbody = str_replace("{csid}", $isender . md5($isenderlength), $mailbody);
            $mailbody = str_replace("{clen}", $isenderlength, $mailbody);
            $mailbody = str_replace("{chash}", $isenderhash, $mailbody);
            $mailbody = str_replace("{name}", $memberprofile->username, $mailbody);
            $mailbody = str_replace("{vocations}", $memberprofile->vocations, $mailbody);
            $mailbody = str_replace("{city}", $memberprofile->city, $mailbody);
            $mailbody = str_replace("{profile-url}", $this->config->item('base_url') . 'profile/' . $memberprofile->user_shortcode, $mailbody);
            $mailbody = str_replace("{profile-picture}", $profileimage, $mailbody);
            $template_part = str_replace("{mail_body}", $mailbody, $template_part);

            send_email($to, $from, "Bob Friedenthal", $subject, $template_part);

            // insert profile invite email
            $rst = $this->db->query("select count(*) as ecount 
			from mc_claimprofile_invite 
			where user_id='$id' and member_id='$mid'");
            if ($rst->num_rows() > 0) {
                if ($rst->row()->ecount == 0) {
                    $stmt = $this->db->query("insert into mc_claimprofile_invite (user_id, member_id, invitedate ) VALUES (? ,?, NOW() )", array($id, $mid));
                }
            }
            //update profile claim
            $sql_query = "update user_people  set isinvited='1' where id= ?";
            $stmt = $this->db->query($sql_query, array($id));
            $jsonresult = array('error' => '0', 'mail' => $template_part, 'errmsg' => "Connection request sent!");
        } else {
            $jsonresult = array('error' => '10', 'mail' => '', 'errmsg' => "Email template missing. Please consult admin for assistance.");
        }
        return $jsonresult;
    }

    public function get_profile_by_login_log($token)
    {

        $sql = "select * from  mc_user where  id =   (select distinct userid from  mc_login_log where token='$token' ) ";
        $result = $this->db->query($sql);
        return $result;
    }

    //get all recently connected members
    function get_connected_members($data)
    {
        $userid = $data['userid'];
        $offset = $data['offset'];
        $size = $data['size'];
        $num_rows = 0;

        $sourcezip = 0;
        $udet = $this->db->query("select zip from  user_details where user_id='$userid'");

        if ($udet->num_rows() > 0) {
            $sourcezip = $udet->row()->zip;
        }

        $sql_query = "(select firstpartner as mid from  mc_member_connections where secondpartner='$userid' and status='1'  order by approvedon desc ) " .
            " union " .
            " ( select secondpartner as mid from  mc_member_connections where firstpartner='$userid'  and status='1' order by approvedon desc )";
        $mids = $this->db->query($sql_query);
        if ($mids->num_rows() > 0) {
            $num_rows = $mids->num_rows();
            $midlist = '';
            foreach ($mids->result() as $item) {
                $midlist .= $item->mid . ",";
            }
            $midlist .= "0";
            $sql_query = "select u.id as id, user_email ,  username ,  user_phone ,  publicprofile ,  
			profileisvisible , image,  current_company ,  linkedin_profile ,  city ,  zip ,  country ,  groups ,  
			target_clients ,  target_referral_partners ,  vocations ,  about_your_self, 0 as isconnected, 0 as rating, 0 distance
			from mc_user as u inner join user_details as d on u.id=d.user_id 
			where u.id !='$userid' and u.id !='1'  and u.id in  (" . $midlist . ") order by find_in_set( u.id, '" . $midlist . "' ) ";
            $allmembers = $this->db->query($sql_query);

            $resulttant_list = array();
            if ($allmembers->num_rows() > 0) {
                foreach ($allmembers->result() as $item) {
                    $distance = 0;
                    if ($sourcezip == 0) {
                        $resulttant_list[] = $item;
                    } else {
                        if ($item->zip != '') {
                            $zipqry = "select * from mc_city_geolocation where zip in ('" . $item->zip . "', '" . $sourcezip . "' ) ";
                            $rsgeolocs = $this->db->query($zipqry);
                            if ($rsgeolocs->num_rows() == 2) {
                                $geolocs = $rsgeolocs->row(0);
                                $latitude1 = $geolocs->latitude;
                                $longitude1 = $geolocs->longitude;
                                $geolocs = $rsgeolocs->row(1);
                                $latitude2 = $geolocs->latitude;
                                $longitude2 = $geolocs->longitude;
                                $theta = $longitude1 - $longitude2;
                                $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
                                $distance = acos($distance);
                                $distance = rad2deg($distance);
                                $distance = $distance * 60 * 1.1515 * 1.609344;
                                $item->distance = $distance;
                            }
                            if ($distance < 20)
                                $resulttant_list[] = $item;
                        }
                    }
                }
            }
            $jsonresult = array('error' => '0', 'num_rows' => $num_rows,
                'errmsg' => "Member fetched successfully.", 'results' => $resulttant_list);
        } else
            $jsonresult = array('error' => '10', 'num_rows' => 0, 'errmsg' => 'No member suggestion found!');

        return $jsonresult;
    }


    public function performance($userid)
    {
        $previous_week = strtotime("-1 week +1 day");
        $start_week = strtotime("last sunday midnight", $previous_week);

        $lstart_week = strtotime("last sunday midnight", $previous_week);
        $lend_week = strtotime("next saturday", $start_week);
        $last_week_start = date("Y-m-d", $lstart_week);
        $last_week_end = date("Y-m-d", $lend_week);

        $last_week_start = date("Y-m-d", $start_week);
        $last_week_end = date('Y-m-d', strtotime($last_week_start . ' +7 day'));

        $d = strtotime("today");
        $start_week = strtotime("last sunday midnight", $d);
        $current_week_start = date("Y-m-d", $start_week);
        $current_week_end = date('Y-m-d', strtotime($current_week_start . ' +6 day'));
        $jsonresult['start_week'] = date('Y-m-d', $start_week);
        $jsonresult['current_week_end'] = date('Y-m-d', strtotime($current_week_start . ' +6 day'));

        $sql_query = "select count(*) as refcount from  referralsuggestions  where partnerid='$userid'";
        $rst = $this->db->query($sql_query);

        if ($rst->num_rows() > 0) {
            $lastweekrefsra = $this->db->query("select count(*) as refcount from referralsuggestions  
			where partnerid='$userid'  and (	entrydate >= '$last_week_start' and 	entrydate <='$last_week_end' ) ");

            $currentweekrefsra = $this->db->query("select count(*) as refcount from referralsuggestions  
				where partnerid='$userid'  and (	entrydate >= '$current_week_start' and 	entrydate <='$current_week_end' ) ");

            $lastweekrefcnt = $lastweekrefsra->row()->refcount;
            $currentweekcnt = $currentweekrefsra->row()->refcount;

            if ($lastweekrefcnt > 0)
                $cweekgrowthpc = round(((($currentweekcnt - $lastweekrefcnt) / $lastweekrefcnt) * 100), 2);
            else
                $cweekgrowthpc = 0;

            $jsonresult['lastweekrefcnt'] = $lastweekrefcnt;
            $jsonresult['currentweekcnt'] = $currentweekcnt;
            $jsonresult['currentweekgrowthpc'] = $cweekgrowthpc;


            $lastweekrefsmailra = $this->db->query("select count(*) as refcount from referralsuggestions 
				where partnerid='$userid' and emailstatus='1'  and (	entrydate >= '$last_week_start' and 	entrydate <='$last_week_end' ) ");
            $currentweekrefsmailra = $this->db->query("select count(*) as refcount from referralsuggestions 
				where partnerid='$userid' and emailstatus='1' and (	entrydate >= '$current_week_start' and 	entrydate <='$current_week_end' ) ");


            $lastweekrefsmailcnt = $lastweekrefsmailra->row()->refcount;
            $currentweekrefsmailcnt = $currentweekrefsmailra->row()->refcount;

            if ($lastweekrefsmailcnt > 0)
                $cweekemailgrowthpc = round(((($currentweekrefsmailcnt - $lastweekrefsmailcnt) / $lastweekrefsmailcnt) * 100), 2);
            else
                $cweekemailgrowthpc = 0;

            $jsonresult['lastweekrefsmailcnt'] = $lastweekrefsmailcnt;
            $jsonresult['currentweekrefsmailcnt'] = $currentweekrefsmailcnt;
            $jsonresult['cweekemailgrowthpc'] = $cweekemailgrowthpc;

            //group referral counts
            $gcrowra = $this->db->query("select group_concat(user_group) as user_groups from user_people where user_id='$userid'  
			and user_group <> ''");
            if ($gcrowra->num_rows() > 0) {
                if ($gcrowra->row()->user_groups != '') {
                    $grouparr = explode(',', $gcrowra->row()->user_groups);
                    $grouparr = array_filter($grouparr);
                    sort($grouparr);
                    $linkgroups = (array_unique($grouparr));
                    $groupids = implode(', ', $linkgroups);

                    $groupnamers = $this->db->query("select group_concat(grp_name) as groupnames from  groups where id in (" . $groupids . ")");
                    $jsonresult['groupcount'] = sizeof($linkgroups);
                    $jsonresult['groupnames'] = $groupnamers->row()->groupnames;

                    /* Trigger Mail Logic */
                    $triggermails = $this->db->query("select count(*) as mailcnt from mailbox where sender='$userid' ");
                    $triggermailscount = 0;
                    if ($triggermails->num_rows() > 0) {
                        $triggermailscount = $triggermails->row()->mailcnt;
                    }

                    $jsonresult['triggermailscount'] = $triggermailscount;
                } else {
                    $jsonresult['triggermailscount'] = 0;
                }
            }
        } else {
            $jsonresult = array('error' => '10', 'errmsg' => 'No performance report could be generated!');
        }

        return $jsonresult;
    }


    function get_related_member()
    {


    }

    function search_members($data)
    {
        $limit = $data['limit'];
        $zipCode = $data['srchZipCode'];
        $voc = $data['locateVoc'];
        $name = $data['ref_name'];
        $nameparts = explode(' ', $name);
        $lifestyle = $data['lifestyle'];
        $entrydate = $data['entrydate'];
        $city = $data['city'];
        $offset = $data['offset'];
        $tag = $data['tag'];
        $phone = $data['phone'];
        $email = $data['email'];
        $member_id = $data['uid'];
        $j = 0;
        for ($i = 0; $i < strlen($phone); $i++) {
            if (is_numeric($phone[$i])) {
                $phonenumber[$j] = $phone[$i];
                $phonenumberdot[$j] = $phone[$i];
                $j++;
                if ($j == 3 || $j == 7) {
                    $phonenumber[$j] = "-";
                    $phonenumberdot[$j] = ".";
                    $j++;
                }
            }
        }
        $phonenumber = '';
        $phonenumberdot = '';
        if ($phonenumber != null)
            $phone = implode("", $phonenumber);
        if ($phonenumberdot != null)
            $phonedot = implode("", $phonenumberdot);

        $where = '';

        if ($zipCode != '') {
            $where .= " AND client_zip = '" . $zipCode . "' ";
        }

        if ($lifestyle != '') {
            $where .= " AND (";
            for ($i = 0; $i < sizeof($lifestyle); $i++) {
                $where .= " FIND_IN_SET('" . $lifestyle[$i] . "', client_lifestyle) ";
                if ($i < sizeof($lifestyle) - 1) {
                    $where .= " OR ";
                }
            }
            $where .= ")";
        }

        if ($voc != '') {
            $where .= " AND (";
            for ($i = 0; $i < sizeof($voc); $i++) {
                $where .= " FIND_IN_SET('" . $voc[$i] . "', client_profession)  ";
                //checking there is fuzzy search key
                $q = $this->db->query("select * from  mc_fuzzy_map  where  input_text = '" . $voc[$i] . "'");
                if ($q->num_rows() > 0) {
                    $j = 0;
                    $where .= " OR ";
                    foreach ($q->result() as $row) {
                        $mapped_text = $row->mapped_text;
                        $where .= " FIND_IN_SET('" . $mapped_text . "', client_profession)  ";
                        if ($j < $q->num_rows() - 1) {
                            $where .= " OR ";
                        }
                        $j++;
                    }
                }
                if ($i < sizeof($voc) - 1) {
                    $where .= " OR ";
                }
            }
            $where .= ")";
        }


        if ($city != '') {
            $where .= " AND (";
            for ($i = 0; $i < sizeof($city); $i++) {
                $where .= " FIND_IN_SET('" . $city[$i] . "', client_location)  ";
                if ($i < sizeof($city) - 1) {
                    $where .= " OR ";
                }
            }
            $where .= ")";
        }

        if ($tag != '') {
            $where .= " AND (";
            for ($i = 0; $i < sizeof($tag); $i++) {
                $where .= " FIND_IN_SET('" . $tag[$i] . "', tags)  ";
                if ($i < sizeof($tag) - 1) {
                    $where .= " OR ";
                }
            }
            $where .= ")";
        }

        if ($entrydate != '') {
            $where .= " AND date(entrydate) =  '" . $entrydate . "' ";
        }

        if ($this->session->role == 'admin') {
            if ($phone != '') {
                $where .= " AND ( user_phone like  '$phone%' or user_phone like '$phonedot%' ) ";
            }

            if ($email != '') {
                $where .= " AND user_email =  '" . $email . "' ";
            }
        } else {
            if ($phone != '') {
                $where .= " AND (client_phone like  '$phone%' or client_phone like '$phonedot%' ) ";
            }
        }
        if ($this->session->role == 'admin') {
            if ($name != '') {
                $nameparts = explode(' ', $name);
                $nameparts = array_filter($nameparts);
                $where .= "AND  username LIKE '$name%'    ";
            }
            //echo   getMyCityUsersAdmin($page, $where , $voc, $name );
            $result = '';
        } else {
            if ($name != '') {
                $where .= " AND client_name LIKE '$name%'";
            }
            //echo searchReferences($user_id, $page,$where, $voc, $name);
            $query = "select * from user_people as a inner join " .
                " (SELECT user_id, sum(ranking) as rank FROM user_rating  group by user_id order by rank) as b " .
                "  on a.user_id=b.user_id where a.user_id='" . $this->session->id . "' $where and rank >='20' order by entrydate desc LIMIT  $offset, $limit ";
            $result = $this->db->query($query);

            $sql_query_count = "select count(*) as reccnt from user_people
		 where user_id  ='$member_id' $where  ";
            $result_count = $this->db->query($sql_query_count);
            $num_rows = $result_count->row()->reccnt;
            if ($result->num_rows() > 0) {
                $jsonresult = array('error' => '0', 'num_rows' => $num_rows, 'errmsg' => "Connections are fetched!",
                    'results' => $result);
                return $jsonresult;
            }

        }

        return false;
    }

    public function invite_to_program($data)
    {
        $eid = $data['id'];
        $state = $data['s'];
        $ppid = $data['ppid'];

        if ($ppid != '' && $ppid > 0) {
            //update
            $sql_query = "update mc_program_client set status='$state' where id= '$ppid'  ";
            $jsonresult = array('error' => '0', 'errmsg' => 'Program joined successfully!');
        } else {
            //insert after checking
            $sql_query = "select count(*) as reccnt from mc_program_client where client_id='$eid'";
            $rst = $this->db->query($sql_query);
            if ($rst->num_rows() > 0) {
                $count = $rst->row()->reccnt;
                if ($count == 0) {
                    if ($state != '' && $state != 0) {
                        $sql_query = "insert into mc_program_client (client_id, program_id, join_date, status ) values ( ?, ?, NOW(), '1' )";
                    } else {
                        $sql_query = "insert into mc_program_client (client_id, program_id, join_date ) values ( ?, ?, NOW() )";
                    }
                    $stmt = $this->db->query($sql_query, array($eid, '1'));

                    //email notification

                    /*

                    $ds = DIRECTORY_SEPARATOR;
                    $path =  $_SERVER['DOCUMENT_ROOT'].$ds    ;
                    if(  file_exists( $path."templates/black_template_01.txt" ) )
                    {
                        $template_part = file_get_contents( $path."templates/black_template_01.txt" ) ;
                    }

                    if(  file_exists( $path."templates/3touch_program_alert.txt" ) )
                    {
                        $mailbody = file_get_contents( $path."templates/3touch_program_alert.txt" ) ;
                    }

                    $mailheading ="New Member Signup in 3 Touch Program";
                    $mailbody  = str_replace("{mail_body}",  $mailbody  , $template_part ) ;
                    sendemail(  "admin@mycity.com" , $mailheading , $mailbody, $mailbody) ;
                    */
                    $jsonresult = array('error' => '0', 'mail' => $mailbody, 'errmsg' => 'Program invite sent successfully!');
                } else {
                    $jsonresult = array('error' => '10', 'errmsg' => 'Program invite already sent!');
                }
            }
        }
        return $jsonresult;
    }

    function get_all_members($data)
    {
        $where = '';
        if ($data['username'] != '') {
            $where = " and  username like '" . $data['username'] . "%'";
        }
        if ($data['createdOn'] != '') {
            $where = " and  date(createdOn) = '" . $data['createdOn'] . "'";
        }
        if ($data['user_email'] != '') {
            $where = " and   user_email  = '" . $data['user_email'] . "'";
        }
        if ($data['user_phone'] != '') {
            $where = " and   user_phone  = '" . $data['user_phone'] . "'";
        }
        if ($data['tags'] != '') {
            $where = "and (find_in_set('" . implode("',  tags ) OR FIND_IN_SET('", $data['tags']) . "',  tags ))";
        }

        /*
            if($data['city'] != '')
            {
                $where = " and   city  = '".$data['city']."'";
            }
            if($data['zip'] != '')
            {
                $where = " and   zip  = '".$data['zip']."'";
            }
            if($data['vocations'] != '')
            {
                $where = " and   vocations  = '".$data['vocations']."'";
            }
        */

        $offset = $data['offset'];
        $all_members = $this->db->query("select mc_user.* , street,  city ,  zip , 
		current_company ,  linkedin_profile ,  country , (SELECT grp_name FROM groups WHERE id = (SELECT user_details.groups FROM user_details WHERE user_id = mc_user.id limit 1)) as  groups ,  target_clients ,  target_referral_partners , 
		vocations ,  about_your_self ,  upd_public_private ,  upd_reminder_email 
		from mc_user inner join user_details on mc_user.id = user_details.user_id 
		where user_role <> 'admin' and (username is not null and username <> '' )  " . $where . "  order by username limit $offset,10");

        $sql_query_count = "select  count(*) as cnt from mc_user inner join user_details on mc_user.id = user_details.user_id 
		where user_role <> 'admin'  and (username is not null and username <> '' )   $where ";
        $result_count = $this->db->query($sql_query_count);
        $num_rows = $result_count->row()->cnt;

        $jsonresult = array('error' => '0', 'num_rows' => $num_rows, 'errmsg' => 'All members fetched!', 'results' => $all_members);
        return $jsonresult;
    }

    function get_all_staffs()
    {
        $this->db->select("*");
        $this->db->from("mc_user");
        $this->db->where("is_employee", "1");
        $this->db->where("id <> ", "1");
        $result = $this->db->get();
        return $result;
    }

    function get_all_users()
    {
        $this->db->select("*");
        $this->db->from("mc_user");
        $this->db->where("is_employee <>", "1");
        $this->db->where("id <> ", "1");
        $result = $this->db->get();
        return $result;
    }

    function get_all_users_with_name()
    {
        $this->db->select("*");
        $this->db->from("mc_user");
        $this->db->where("username <> '' and username is not null");
        $this->db->where("id <> ", "1");
        $this->db->where("user_status", "1");
        $this->db->order_by("username", "asc");
        $result = $this->db->get();
        return $result;
    }

    function get_login_log()
    {
        $this->db->select("*");
        $this->db->from("mc_user");
        $this->db->where("is_employee <>", "1");
        $this->db->where("id <> ", "1");
        $result = $this->db->get();
        return $result;
    }


    function get_incomplete_signup($data)
    {
        $offset = $data['offset'];
        $limit = $data['limit'];
        $sql_query = "select id,username, user_email, date(createdOn) as createdon from  mc_user 
		where (user_pass is null or user_pass='') and (username is null or username ='') and (user_phone is null or user_phone='') 
		limit $offset, $limit";

        $sql_query_count = "select  count(*) as reccnt from  mc_user  where (user_pass is null or user_pass='') and (username is null or username ='') and (user_phone is null or user_phone='')";


        $jsonresult = array('error' => '10',
            'errmsg' => 'Unfinished signups not found!',
            'num_rows' => 0, 'results' => null);


        $rst = $this->db->query($sql_query);
        if ($rst->num_rows() > 0) {
            $rst_count = $this->db->query($sql_query_count);

            $num_rows = $rst_count->row()->reccnt;
            $jsonresult = array('error' => '0', 'errmsg' => 'Unfinished signups fetched!',
                'num_rows' => $num_rows, 'results' => $rst);
        }


        return $jsonresult;
    }
}

?>