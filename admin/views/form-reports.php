<?php
if ( ! defined( 'ABSPATH' ) )
        exit; // Exit if accessed directly
?>

<div class='mt30'>
        <div class='panel' style='width:98%;'>
                <div class='panel-body'>
                        <?php
                        require_once(SM_LB_PRO_DIR.'includes/Customer_Reports.php');
                        echo "<div class='header_title'><div class='leads-builder-heading'> " . esc_html('Tracked Events ')." </div></div>";
                        $data=new Customer_Reports();
                        $data->prepare_items();
                        echo "<div>";
                        $data->display();
                        echo "</div>";
                        ?>
                </div>
        </div>
</div>