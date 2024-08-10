<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <div class="col-md-4 border-right">
                      <h4 class="no-margin font-bold"><i class="fa fa-filter" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
                      <hr />
                    </div>
                  </div>
                   <div class="_buttons">
                        <a href="#" onclick="new_taskfilter(); return false;" class="btn btn-info pull-left display-block">
                            <?php echo _l('new_taskfilter'); ?>
                        </a>
                    </div>
                   <br><br><br>
                  <?php render_datatable(array(
                        '#'._l('id'),
                        _l('taskfilter_name'),
                        _l('creator'),
                        _l('options')
                        ),'table_taskfilter'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="modal fade" id="taskfilter" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('taskfilter/taskfilters'),array('id'=>'taskfilter-form')); ?>
        <div class="modal-content" style="width: 150%;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_taskfilter'); ?></span>
                    <span class="add-title"><?php echo _l('new_taskfilter'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('filter_name','taskfilter_name'); ?>
                        <?php echo form_hidden('creator',get_staff_user_id()); ?>
                    </div>
                </div>
                <h4 class="bold"><?php echo _l('display_field'); ?></h4>
                <table class="table no-margin">
                  <tr>
                    <td class="bold"><?php echo _l('project'); ?></td>                        
                  </tr>
                  <tr>
                    <td>
                      
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="name" name="project_name" value="" >
                           <label for="project_name"><?php echo _l('project_name'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="clientid" name="project_clientid" value="" >
                           <label for="project_clientid"><?php echo _l('task_related_to'); ?></label>
                        </div>
                        
                    </td>
                    <td>
                      <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="start_date" name="project_start_date" value="" >
                           <label for="project_start_date"><?php echo _l('project_start_date'); ?></label>
                        </div>
                      <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="deadline" name="project_deadline" value="" >
                           <label for="project_deadline"><?php echo _l('project_deadline'); ?></label>
                        </div>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="status" name="project_status" value="" >
                           <label for="project_status"><?php echo _l('project_status'); ?></label>
                        </div>
                        
                    </td>
                  </tr>
                </table>
                <table class="table no-margin">
                  <tr> 
                     <td class="bold"><?php echo _l('project_milestones'); ?></td>                      
                  </tr>
                  <tr>
                    <td>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="milestone_name" name="milestone_name" value="">
                           <label for="milestone_name"><?php echo _l('milestone_name'); ?></label>
                        </div>                                  
                    </td>
                    <td>
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" id="milestone_due_date" name="milestone_due_date" value="">
                           <label for="milestone_due_date"><?php echo _l('milestone_due_date'); ?></label>
                        </div>
                    </td>
                  </tr>                     
                </table>
                <table class="table no-margin">
                  <tr>                           
                     <td class="bold"><?php echo _l('tasks'); ?></td>                        
                  </tr>
                  <tr>
                    <td>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_name" name="task_name" value="" >
                       <label for="task_name"><?php echo _l('tasks_dt_name'); ?></label>
                    </div>                                  
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_status" name="task_status" value="" >
                       <label for="task_status"><?php echo _l('task_status'); ?></label>
                    </div>
                     <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_priority" name="task_priority" value="" >
                       <label for="task_priority"><?php echo _l('priority'); ?></label>
                    </div>
                  </td>
                  <td>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_startdate" name="task_startdate" value="" >
                       <label for="task_startdate"><?php echo _l('task_single_start_date'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_duedate" name="task_duedate" value="" >
                       <label for="task_duedate"><?php echo _l('task_duedate'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_assigned" name="task_assigned" value="" >
                       <label for="task_assigned"><?php echo _l('task_assigned'); ?></label>
                    </div>
                  </td>
                  <td>
                  <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_spent_hour" name="task_spent_hour" value="" >
                       <label for="task_spent_hour"><?php echo _l('project_timesheet_time_spend'); ?></label>
                    </div>
                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" id="task_addedfrom" name="task_addedfrom" value="" >
                       <label for="task_addedfrom"><?php echo _l('staff_notes_table_addedfrom_heading'); ?></label>
                    </div>
                  </td>
                  </tr>
                </table>

                <h4 class="bold"><?php echo _l('filter_condition'); ?></h4>
                <hr/>
                <div class="row">
                  <div class="col-md-6">
                  <label for="select_parent_department"><?php echo _l('time'); ?></label>
                  <select name="time" id="time" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value=""></option>
                      <option value="today"><?php echo _l('today'); ?></option>
                      <option value="xday"><?php echo _l('xday_next'); ?></option>
                      <option value="this_week"><?php echo _l('this_week'); ?></option>
                      <option value="last_week"><?php echo _l('last_week'); ?></option>
                      <option value="next_week"><?php echo _l('next_week'); ?></option>
                      <option value="this_month"><?php echo _l('this_month'); ?></option>
                      <option value="last_month"><?php echo _l('last_month'); ?></option>
                      <option value="next_month"><?php echo _l('next_month'); ?></option>
                      <option value="day_to_day"><?php echo _l('day_to_day'); ?></option>
                  </select>
                  <br><br>
                  <div class="hide" id="xday_div">
                    <label for="xday"><?php echo _l('xday_next'); ?>:</label>
                    <input type="number" name="xday" min="1" max="31" class="form-control" id="xday">
                  </div>
                  <div class="hide" id="day_to_day_div">
                    <?php echo render_date_input('from_date','from_date'); ?>
                    <?php echo render_date_input('to_date','to_date'); ?>
                  </div>
                </div>
                  <div class="col-md-6">
                  <label for="select_parent_department"><?php echo _l('priority'); ?></label>
                  <select name="priority[]" id="priority" multiple="true" data-actions-box="true" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value="1"><?php echo _l('task_priority_low'); ?></option>
                      <option value="2"><?php echo _l('task_priority_medium'); ?></option>
                      <option value="3"><?php echo _l('task_priority_high'); ?></option>
                      <option value="4"><?php echo _l('task_priority_urgent'); ?></option>
                  </select>
                  </div>
                
                </div>
                <div class="row">
                  <div class="col-md-6">
                  <label for="status"><?php echo _l('status'); ?></label>
                  <select name="status[]" id="statuss" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                      <option value="not_started(true)"><?php echo _l('not_started'); ?></option>
                      <option value="not_started(late)"><?php echo _l('not_started(late)'); ?></option>
                      <option value="in_process(true)"><?php echo _l('in_process'); ?></option>
                      <option value="in_process(late)"><?php echo _l('in_process(late)'); ?></option>
                      <option value="complete(true)"><?php echo _l('complete'); ?></option>
                      <option value="complete(late)"><?php echo _l('complete(late)'); ?></option>
                  </select>
                </div>
                 <div class="col-md-6">
                  <label for="select_parent_department"><?php echo _l('ticket_assigned'); ?></label>
                  <select name="assigned[]" id="assigned" class="selectpicker" multiple="true" data-actions-box="true" data-width="100%" data-none-selected-text="<?php echo _l('ticket_settings_none_assigned'); ?>">
                    <?php foreach($staff as $s) { ?>
                      <option value="<?php echo $s['staffid']; ?>"><?php echo $s['firstname']; ?></option>
                      <?php } ?>
                  </select>
                </div>
                </div>
            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button id="sm_btn" type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
</div>
<?php init_tail(); ?>
</body>
</html>
<script>
  _validate_form($('#taskfilter-form'),{filter_name:'required'});
  $('#time').change(function() {
    if(this.value == 'xday'){
      $('#xday_div').removeClass('hide');
    }else{
      $('#xday_div').addClass('hide');
    }
    if(this.value == 'day_to_day'){
      $('#day_to_day_div').removeClass('hide');
    }else{
      $('#day_to_day_div').addClass('hide');
    }
  });
  initDataTable('.table-table_taskfilter', admin_url+'taskfilter/taskfilter_table');
  function new_taskfilter(){
    $('#taskfilter').modal('show');
    $('.edit-title').addClass('hide');
    $('.add-title').removeClass('hide');
    $('#additional').html('');
  }
  function edit_taskfilter(invoker,id){
    $.post(admin_url+'taskfilter/get_edit_taskfilter_data/'+id+'').done(function(response){
      response = JSON.parse(response);
      $.each(response.project, function(key,value){
        $("#"+value+"").prop('checked', true);
      });
      $.each(response.milestone, function(key,value){
        $("#milestone_"+value+"").prop('checked', true);
      });
      $.each(response.task, function(key,value){
        $("#task_"+value+"").prop('checked', true);
      });
      $("#priority").val(response.priority).change();
      $("#statuss").val(response.status).change();
      $("#assigned").val(response.assigned).change();
      $("#time").val(response.time).change();
      $("#xday").val(response.xday);
      $("#from_date").val(response.from_day);
      $("#to_date").val(response.to_day);
    });
    $('#additional').append(hidden_input('id',id));
    $('#taskfilter input[name="filter_name"]').val($(invoker).data('filter_name'));
    $('#taskfilter').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}
</script>
