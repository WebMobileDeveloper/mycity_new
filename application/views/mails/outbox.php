<div class='col-md-9'>
    <div class='profile-item'>
        <h3>Outbox</h3>
        <hr/>
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="<?php echo($mailtype == 0 ? "active" : " "); ?>"><a href="<?php echo $base; ?>mails/outbox/0/"> Messages</a></li>
            <li role="presentation" class="<?php echo($mailtype == 10 ? "active" : ""); ?>"><a href="<?php echo $base; ?>mails/outbox/10/"> Connection Requests Sent</a></li>
        </ul>
        <?php if (!empty($inbox['result'])): ?>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="homein">
                    <div id='myinboxdc0'>
                        <?php

                        echo "<table class='table table-condensed'><thead><tr><th></th><th></th><th style='text-align: center;'>Opened</th><th>Receiver</th><th>Subject</th><th>Date</th><th>Action</th></tr></thead><tbody> ";
                        foreach ($inbox['result']->result() as $item) {
                            if ($item->emailstatus % 2 == 0) {
                                $estate = '<span class="badge">New</span>';
                                $bt = 'strong';
                            } else {
                                $estate = '    ';
                                $bt = '';
                            }
                            if ($item->emailstatus < 2) {
                                $estate1 = '<span class="badge">Unread</span>';
                            } else {
                                $estate1 = 'Read';
                            }


                            echo "<tr  data-id='$item->id'>
                                <td><input type='checkbox' class='delmail' data-id='$item->id'></td>
                                <td>$estate</td>
                                <td style='text-align: center;'>$estate1</td>
                                <td class='$bt' data-id='$item->id' class='readinmail'>$item->username</td>
                                <td class='$bt' data-id='$item->id' class='readinmail'>$item->subject</td>
                                <td class='$bt'>$item->senton</td>
                                <td><a href='" . $base . "mails/read/?type=out&mail=$item->id&return=" . current_url() . "' class='btn-primary btn'>Read</a>  
                                        <button data-id='$item->partnerid' class='btn btn-primary   btncomposedirectmail'> Message</button>
                                        <button data-id='$item->id' class='btn btn-danger btn_rem_mail'> Delete</button>
                                </td>
                            </tr>";
                        }
                        echo '</table>';
                        if (isset($links)) echo "<div style='text-align: center;'>" . $links . "</div>";
                        ?>
                    </div>
                </div>

                <div role="tabpanel" class="tab-pane  " id="conreqin">
                    <div id='myinboxdc10'></div>
                </div>
            </div>
        <?php else: ?>

            <p class='content medium'>Outbox is empty! </p>

        <?php endif; ?>
    </div>
</div>
</div> <!-- row -->
</div> <!-- container -->

<div class="modal fade mine-modal" id="refmailreader" tabindex="-1" role="dialog" aria-labelledby="refmailreaderbox">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="suggestedref">Mail Content</h4>
            </div>
            <div class="modal-body text-left" id='refmailreaderbox'>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>
            </div>
        </div>
    </div>
</div> 
