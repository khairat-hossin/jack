<div class="row">
 <div class="col-md-12">
    <div class="panel_s">
       <div class="panel-body">
          <div class="row">
             <div class="col-md-4 border-right">
              <h4 class="no-margin font-bold"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo _l($title); ?></h4>
              <hr />
            </div>
          </div>
           <?php 
           $arr_table = [];
           foreach ($field as $key => $value) {
           		if($key == 'project'){
           			foreach($value as $pj){
           				$arr_table[] = _l('project_'.$pj);
           			}
           		}
           		if($key == 'milestone'){
           			foreach($value as $mi){
           				$arr_table[] = _l('milestones_'.$mi);
           			}
           		}
           		if($key == 'task'){
           			foreach($value as $tsk){
           				$arr_table[] = _l('task_'.$tsk);
           			}
           		}
           }?>                   
         	<?php render_datatable($arr_table,'table_view_filter'.$id); ?>                   
       </div>
    </div>
 </div>
</div>

