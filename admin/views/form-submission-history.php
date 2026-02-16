<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
?>
<div class='mt30'>
        <div class='panel' style='width:98%;'>
                <div class='panel-body'>
                        <?php
                                require_once(SM_LB_PRO_DIR.'includes/form_submission.php');
                                 echo "<div class='header_title'><div class='leads-builder-heading'> " . esc_html('Form Submissions ')." </div></div>";
                                $data=new Form_Reports();
                                 echo '<form id="wpse-list-table-form" method="post">';
                                $page  = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
                                $paged = filter_input( INPUT_GET, 'paged', FILTER_SANITIZE_NUMBER_INT );
                                 printf( '<input type="hidden" name="page" value="%s" />', $page );
                                 printf( '<input type="hidden" name="paged" value="%d" />', $paged );
                                $data->prepare_items();
                                 echo "<div  class='form-submission-details'>";
                                $data->display();
                                 echo "</div>";
                                 echo '</form>';
                                 echo '</div>';
                        ?>
                </div>
        </div>
</div>