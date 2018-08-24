<div class='col-md-9'>

    <?php
    if ($this->session->msg_error) {
        echo "<p class='  alertinfofix text-center'> " . $this->session->msg_error . "</p>";
        $this->session->unset_userdata('msg_error');
    }
    if ($nearest_knows['know_count'] > 0) { ?>

        <div class='profile-item'>
            <h2>Knows of Members in Search Result</h2>
            <div class='hr-sm '></div>
            <div class='marg2'></div>
            <form method='post' style='display:inline' action='<?php echo $base; ?>business/search/<?php echo $member_offset; ?>'>
                <button type="submit" name='vu_member' value='1' class='btn btn-sm vuconcount' style='color:#000'>Members</button>
            </form>
            <form method='post' style='display:inline' action='<?php echo $base; ?>business/search/knows/<?php echo $offset; ?>'>
                <button type="submit" name='vu_know' value='1' class='btn btn-primary btn-sm vuconcount'>Their Knows</button>
            </form>

            <hr/>

            <div id="conreqlist0 marg2">

                <div role="tabpanel" class="tab-pane" id="knowgrid">
                    <div class="knowlist">
                        <?php

                        $knowhtml = '';
                        $user_picture = '';
//                        echo "<pre>";
//                        var_dump($nearest_knows);
//                        echo "</pre>";
//                        exit();
                        foreach ($nearest_knows['knows'] as $item) {
                            if ($item->mem_photo != '' && file_exists($site_path . $profile_img . $item->mem_photo))
                                $user_picture = $base . $profile_img . $item->mem_photo;
                            else
                                $user_picture = $base . $image . "no-photo.png";

                            $knowhtml .= '<div class="search-box"><div class="row"><div class="col-xs-4 col-md-2">';
                            $knowhtml .= "<img src='" . $user_picture . "' alt='" . $item->b . "' onerror='imgError(this);' class='img-rounded'  width='80'> " . '</div>';
                            $knowhtml .= '<div class="col-xs-8 col-md-6"><strong>' . $item->b . '</strong>' .
                                '<input type="hidden" value="' . $item->b . '" id="bcname"><br/>' . $item->w . '<br/>';

                            if ($item->q != '' && $item->q !== null)
                                $knowhtml .= $item->q . " " . $item->r . '<br/>';
                            $knowrate = ceil($item->rating / 5);
                            $star = '';
                            for ($sc = 0; $sc < 5; $sc++) {
                                if ($sc < $knowrate)
                                    $star .= "<i class='fa fa-star orange'></i>";
                                else
                                    $star .= "<i class='fa fa-star lgray'></i>";
                            }

                            if ($knowrate == 5) {
                                $knowhtml .= "<br/><span class='badge badge-green'><i class='fa fa-sun-o'></i> Top Rated Know</span>";
                                $knowhtml .= " <span class='badge badge-dark'>Rated by: " . $item->un . "</span>";
                            } else if ($knowrate > 0) {
                                $knowhtml .= "<br/>" . $star;
                                $knowhtml .= " <span class='badge badge-dark'>Rated by: " . $item->un . "</span>";
                            } else
                                $knowhtml .= "<br/><span class='badge badge-blue'>Non Rated Know</span>";

                            $knowhtml .= '</div> <div class="col-xs-4 col-md-4">';
                            if ($item->requestsent == 0) {
                                if ($item->ismember_connected == 0) {
                                    $knowhtml .= '<button type="submit" data-pg="' . $offset . '" name="btn_send_connect_req" data-id="' . $item->knid . '" data-name="' . $item->b .
                                        '" data-email="' . $item->a . '" data-voc="' . $item->w . '" class="btn btn-primary btn-block btncomposeknowinvitemail" ><i class="fa fa-envelope"></i> Click to Connect</button>';

                                } else if ($item->ismember_connected == 1) {
                                    $knowhtml .= '<button type="button" data-i="' . $item->mem_id . '" sub-pg="knows/"  data-pg="' . $offset . '" data-tgt="know" class="btn btn-primary btn-solid btn-block btnconnect" ><i class="fa fa-envelope"></i> Connect</button>';
                                } else if ($item->ismember_connected == 10) {
                                    $knowhtml .= '<button type="button"  data-id="' . $item->mem_id . '" class="btn btn-primary btn-block btncomposedirectmail" ><i class="fa fa-envelope"></i> Message</button>';
                                }
                            } else {
                                $knowhtml .= '<p class="box-01">You have sent connect message.</p>';
                            }
                            $knowhtml .= '</div></div></div>';
                        }
                        echo $knowhtml;
                        if (isset($links)) echo "<div style='text-align: center;'>" . $links . "</div>";
                        ?>
                    </div>
                </div>
            </div>

        </div>
    <?php } else { ?>
        <div class='profile-item'>
            <h2>Search Result</h2>
            <div class='hr-sm'></div>
            <p class='content medium'>No matching member found! </p>
        </div>
    <?php } ?>
    <div class='modal' id='memberratingmodal' tabindex='-1' role='dialog' aria-labelledby='memberratingmodal'>
        <div class='modal-dialog '>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span></button>
                    <h4 class='modal-title'>Rate Member</h4>
                </div>
                <div class='modal-body text-left  '>
                    <div id='member_rating_box'>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button class='btn btn-success' data-mid='0' id='btnsavememberrating'>Save</button>
                </div>
            </div>
        </div>
    </div>

</div> <!-- row -->
</div> <!-- container -->  