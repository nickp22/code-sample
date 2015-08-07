<?php
/*
Template Name: Online Bill Pay Template
*/

get_header();
$h1 = get_field('h1',$post->ID);

$uniq_id = uniqid();
$generated_time = gmdate("Y-m-d\TH:i:s\Z");
//$reference_number = date('mdY').strtotime("now");

wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style("wp-jquery-ui-dialog");

?>

<script type="text/javascript">
    jQuery(document).ready( function($) {
        $('#submit_button').click(function(e) {
            var amount              = $('#amount').val();
            var account_number      = $('#account_number').val();
            var patient_name        = $('#patient_name').val();
            var state               = $('#state').val();
            var select_title        = $('#state option:selected').text();
            var comments            = $('#comments').val();
            var encounter           = $('#encounter').val();

            if( select_title !== 'Choose State' && patient_name && amount && account_number ) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo get_site_url();?>" + "/page-online-bill-pay-ajax.php",
                    data: "encounter" + encounter + "comments=" + comments + "state=" + state + "&account_number=" + account_number + "&patient_name=" + patient_name + "&amount=" + amount + "&uniqid=<?php echo $uniq_id;?>&signed_date_time=<?php echo $generated_time;?>",
                    dataType : "json",
                    success:function( data ) {
                        if( data.status == "true" ) {
                            $('#signature_hidden').val(data.key);
                            $('#amount_hidden').val(amount);
                            $('#reference_number_hidden').val(account_number);
                            $('#patient_name_hidden').val(patient_name);
                            $('#state_hidden').val(state);
                            $('#comments_hidden').val(comments);
                            $('#encounter_hidden').val(encounter);
                            $('#form').submit();
                        }
                    }
                });
                return true;
            } else {
                state           == 0 ? $('#select_msg').show() : $('#select_msg').hide();
                account_number  == '' ? $('#account_msg').show() : $('#account_msg').hide();
                patient_name    == '' ? $('#patient_msg').show() : $('#patient_msg').hide();
                amount          == '' ? $('#amount_msg').show() : $('#amount_msg').hide();
                return false;
            }
        });
        // image enlarging popup handler
        $('#image').on('click', function() {
            $('body').append('<div id="dialog" style="display:none;" title="Example"><img src="/bill_example.jpg" width="800" height="450" /></div>');
            $('#dialog').dialog( {
                'modal' : true,
                'autoOpen' : true,
                'closeOnEscape' : false,
                'height' : "600",
                'width' : "900",
                open: function(event, ui) { $(this).closest('.ui-dialog').find('.ui-dialog-titlebar-close').hide(); },
                'buttons' : [ { 'text' : 'Close', 'class' : 'button-primary', 'click' : function() { $(this).dialog('close'); } } ]
            }).dialog('open');
            $("#dialog").siblings('div.ui-dialog-titlebar').remove();
        });
    });
</script>

<div id="content" class="clearfix row default-page">
    <div id="main" class="col-xs-12 clearfix" role="main">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
            <!-- <header>
            </header> -->
            <section class="row post_content">
                <div class="col-xs-12"><h1 class="page-title-with-box"><?php echo !empty($h1)?$h1:get_the_title(); ?></h1></div>
                <div class="internal_page col-xs-12">
                        <div class="row">
                        <div class="breadcrumbs col-xs-12"><?php if(function_exists('bcn_display')) { bcn_display(); }?></div>
                        <div class="col-xs-12 col-sm-8">
                            <?php if($post->post_content != '') :
                                  	  the_content();
                             	  endif;?>

                            <?php $title = get_field('page_headline'); ?>
                                <h2><?php echo $title; ?></h2>
                                <?php if( have_rows('sub_headlines') ): ?>
                                <?php while( have_rows('sub_headlines') ): the_row();
                                    // vars
                                    $sub_title = get_sub_field('headline');
                                    $text = get_sub_field('content');
                                    $link_text = get_sub_field('link_text');
                                    $link_url = get_sub_field('link_url');
                                    ?>
                                    <div class="default_info">
                                        <div class="default_content">
                                            <h3><?php echo $sub_title; ?></h3>
                                            <?php echo $text; ?>
                                            <?php
                                                if($sub_title == "Online Bill Pay"):
                                            ?>
                                            <div style="margin-bottom:20px;"> <!-- main container div for pay bill form -->
                                                <div style="float:left;">
                                                    <div style="margin-bottom:10px;">
                                                        <div style="float:left; margin-right:50px;">Select State:</div>
                                                        <div style="float:left; margin-right:10px;">
                                                            <select style="width:185px; margin-bottom:10px; !important" id="state" name="state">
                                                                <option value="0">Choose State</option>
                                                                <option value="AZ">Arizona</option>
                                                                <option value="CO">Colorado</option>
                                                                <option value="KS">Kansas</option>
                                                                <option value="MO">Missouri</option>
                                                                <option value="NM">New Mexico</option>
                                                                <option value="NC">North Carolina</option>
                                                                <option value="OH">Ohio</option>
                                                                <option value="OK-OKC">Oklahoma - OKC</option>
                                                                <option value="OK-Tulsa">Oklahoma - Tulsa</option>
                                                                <option value="TX">Texas</option>
                                                                <option value="VA">Virginia</option>
                                                            </select>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                        <div id="select_msg" style="margin-left:150px; font-size:12px; color:red; display:none;">Please select State</div>
                                                    </div>

                                                    <div>
                                                        <div style="float:left; margin-right:42px;">Patient Name:</div>
                                                        <div style="float:left; margin-right:10px;"><input style="margin-bottom:10px; !important" type="text" maxlength="100" id="patient_name" name="patient_name"></div>
                                                        <div style="clear:both;"></div>
                                                        <div id="patient_msg" style="margin-left:150px; font-size:12px; color:red; display:none;">Please enter Patient Name</div>
                                                    </div>

                                                    <div>
                                                        <div style="float:left; margin-right:66px;">Account #:</div>
                                                        <div style="float:left; margin-right:10px;"><input style="margin-bottom:10px; !important" type="text" id="account_number" name="account_number"></div>
                                                        <div style="clear:both;"></div>
                                                        <div id="account_msg" style="margin-left:150px; font-size:12px; color:red; display:none;">Please enter Account Number</div>
                                                    </div>

                                                    <div>
                                                        <div style="float:left; margin-right:29px; margin-bottom:10px;">Encounter #:<br>(Not Mandatory)</div>
                                                        <div style="float:left;"><input style="margin-bottom:10px; !important" type="text" id="encounter" maxlength="50" name="encounter"></div>
                                                        <div style="clear:both;"></div>
                                                    </div>

                                                    <div>
                                                        <div style="float:left; margin-right:29px;">Comments:<br>(Not Mandatory)</div>
                                                        <div style="float:left; margin-right:10px;"><textarea style="margin-bottom:10px; resize:none; border:solid 1px #ccc; !important" maxlength="150" rows="3" cols="23.5" id="comments" name="comments"></textarea></div>
                                                        <div style="clear:both;"></div>
                                                    </div>

                                                    <div>
                                                        <div style="float:left; margin-right:10px;">Total Amount Due:</div>
                                                        <div style="float:left; margin-right:10px;"><input style="margin-bottom:10px; !important" type="text" id="amount" name="amount"></div>
                                                        <div style="clear:both;"></div>
                                                        <div id="amount_msg" style="margin-left:150px; font-size:12px; color:red; display:none;">Please enter Amount</div>
                                                    </div>
                                                </div>

                                                <div id='image' style="float:right; margin-top:118px; cursor:pointer; !important"><a>Where I can find this information?</a></div>
                                            </div>
                                            <br style="clear:both;">
                                            <div style="margin-top:20px;">
                                                <form id="form" action="https://secureacceptance.cybersource.com/pay" method="post">
                                                    <input name="access_key" value="174fe0e27bfb3907b8915444e59ba599" type="hidden"/>
                                                    <input name="profile_id" value="nextcar" type="hidden"/>
                                                    <input type="hidden" name="transaction_uuid" value="<?php echo $uniq_id;?>">
                                                    <input type="hidden" value="access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,amount,transaction_type,reference_number,currency" name="signed_field_names">
                                                    <input type="hidden" id="unsigned_field_names_hidden" value="merchant_defined_data1,merchant_defined_data2,merchant_defined_data4,merchant_defined_data5" name="unsigned_field_names">
                                                    <input type="hidden" value="<?php echo $generated_time;?>" name="signed_date_time">
                                                    <input type="hidden" value="en" name="locale">
                                                    <input type="hidden" id="signature_hidden" name="signature"/>
                                                    <input type="hidden" value="sale" name="transaction_type"/>
                                                    <input type="hidden" id="reference_number_hidden" name="reference_number"/>
                                                    <input type="hidden" value="USD" name="currency"/>
                                                    <input type="hidden" id="amount_hidden" name="amount"/>
                                                    <input type="hidden" id="state_hidden" name="merchant_defined_data1"/>
                                                    <input type="hidden" id="patient_name_hidden" name="merchant_defined_data2"/>
                                                    <!-- <input type="hidden" id="account_number_hidden" name="req_reference_number"/> -->
                                                    <input type="hidden" id="encounter_hidden" name="merchant_defined_data4"/>
                                                    <input type="hidden" id="comments_hidden" name="merchant_defined_data5"/>
                                                </form>

                                                <input style="border-top-style:none; border-bottom-style:none; border-left-style:none;
                                                border-right-style:none; border-top-width: medium;color:white; background: none repeat scroll 0 0 #0084b9;
                                                line-height: 1; padding: 8px 20px; width:35%; -webkit-appearance:none; margin-top:20px; !important"
                                                 type="button" id="submit_button" value="Go To Online Bill Pay >" />
                                            </div>

                                            <?php endif;?>

                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                        <?php get_sidebar('bill-pay'); ?>
                    </div>
                </div>
                <?php
                    $bottom_title = get_field('bottom_area_title');
                    if($bottom_title != ''){
                        $left_content = get_field('bottom_area_left_text'); 
                        $left_link = get_field('bottom_area_left_link_text');
                        $left_url = get_field('bottom_area_left_link_url');

                        $right_content = get_field('bottom_area_right_text');
                        $right_link = get_field('bottom_area_right_link_text');
                        $right_url = get_field('bottom_area_right_link_url');
                ?>
                    <div class="col-xs-12 bottom-link-area">
                        <div class="row">
                            <div class="col-xs-12"><h3><?php echo $bottom_title; ?></h3></div>
                            <?php
                            if(!empty($left_content) && !empty($right_content)){
                            ?>
                                <div class='bottom-link-area-col col-xs-12 col-sm-6'>
                                    <?php echo $left_content;?>
                                    <?php echo !empty($left_url)?"<a href='$left_url'>$left_link</a>":''; ?>
                                </div>
                                <div class='bottom-link-area-col col-xs-12 col-sm-6'>
                                    <?php echo $right_content;?>
                                    <?php echo !empty($right_url)?"<a href='$right_url'>$right_link</a>":''; ?>
                                </div>
                            <?php }elseif(!empty($left_content) && empty($right_content)){?>
                                 <div class='bottom-link-area-col col-xs-12'>
                                    <?php echo $left_content;?>
                                    <?php echo !empty($left_url)?"<a href='$left_url'>$left_link</a>":''; ?>
                                </div>                                   
                            <?php }elseif(!empty($right_content) && empty($left_content)){?>
                                <div class='bottom-link-area-col col-xs-12'>
                                    <?php echo $right_content;?>
                                    <?php echo !empty($right_url)?"<a href='$right_url'>$right_link</a>":''; ?>
                                </div>
                            <?php }?>
                        </div>
                        <img src="/wp-content/themes/wordpress-bootstrap-master/images/shadow-flipped.png" class="shadow_flipped">	
                    </div>
                <?php }?>
            </section> <!-- end article header -->
            <footer>
                <p class="clearfix"><?php the_tags('<span class="tags">' . __("Tags","wpbootstrap") . ': ', ', ', '</span>'); ?></p>
            </footer> <!-- end article footer -->
        </article> <!-- end article -->
        <?php endwhile; ?>
        <?php else : ?>
        <article id="post-not-found">
            <header>
                <h1><?php _e("Not Found", "wpbootstrap"); ?></h1>
            </header>
            <section class="post_content">
                <p><?php _e("Sorry, but the requested resource was not found on this site.", "wpbootstrap"); ?></p>
            </section>
            <footer>
            </footer>
        </article>
        <?php endif; ?>
    </div> <!-- end #main -->
    <?php //get_sidebar(); // sidebar 1 ?>
</div> <!-- end #content -->
<?php get_footer(); ?>