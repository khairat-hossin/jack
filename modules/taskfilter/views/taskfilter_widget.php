
<?php 
$this->load->model('taskfilter/taskfilter_model');
$filter_widget = $this->taskfilter_model->get_filter_widget(get_staff_user_id(),'taskfilter');
foreach ($filter_widget as $filter) { ?>
<div class="widget" id="widget-<?php echo basename(__FILE__,".php"); ?>" data-name="<?php echo _l('taskfilter_widget'); ?>">
<div class="panel_s user-data">
  <div class="panel-body">
    <div class="widget-dragger"></div>
     <?php $data['field'] = $this->taskfilter_model->view_data_filter_helper($filter['rel_id']); 
           $data['id'] = $filter['rel_id'];
           $data['title'] = $this->taskfilter_model->get_name_taskfilter($filter['rel_id']);
     ?>
     <?php $this->load->view('view_filter', $data); ?>
     <!-- <?php include_once(APPPATH . 'views/admin/reports/view_filter.php') ?> -->
    </div>
  </div>
   
</div>
<?php } ?>
 <script>
   // initDataTable('.table-table_view_filter3', admin_url+'taskfilter/view_filter_table/3');
  window.addEventListener('load',function(){
  <?php
  foreach ($filter_widget as $filter) {
   $id = $filter['rel_id'];
    ?>
      initDataTable('.table-table_view_filter<?php echo $id; ?>', admin_url+'taskfilter/view_filter_table/'+<?php echo $id; ?>+'');
   <?php } ?>
});
</script> 


