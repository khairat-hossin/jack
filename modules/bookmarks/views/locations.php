<?php defined( 'BASEPATH') or exit( 'No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('bookmarks') ?>" class="btn btn-info pull-left display-block mright5">
                                <?php echo _l( 'bookmark_view_all_bookmarks'); ?>
                            </a>
                            <a href="#" onclick="init_bookmark_modal(0); return false;" class="btn btn-info pull-left display-block mright5">
                                <?php echo _l( 'bookmark_add_new_bookmark'); ?>
                            </a>
                            <a href="#" onclick="init_location_modal(0); return false;" class="btn btn-info pull-left display-block mright5">
                                <?php echo _l( 'bookmark_add_new_location'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading" />
                        <?php render_datatable(
							array( 
							_l( 'bookmark_location_name') , 
							_l( 'bookmark_location_description') ,   
							_l( 'bookmark_location_ordering') , 
							_l( 'bookmark_created_by') , 
							_l( 'bookmark_created_date') , 
							_l( 'bookmark_location_privacy') ,
							) , 'bookmark_locations'); 
						?> 
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php echo $this ->load ->view('bookmarks/modals/location_form') ?>
<?php echo $this ->load ->view('bookmarks/modals/bookmark_form') ?>
<script>
    $(function() {
		"use strict";
        initDataTable('.table-bookmark_locations', window.location.href, [], []);
    });
</script>
</body>

</html>