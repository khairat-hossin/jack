<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
       <div class="col-md-4">
      <?php if(get_option('contact_can_add_password') == 1 && $type != 'all_password'){ ?> 
        <a href="<?php echo site_url('team_password/team_password_client/add_'.$type.'?cate='.$cate); ?>" class="btn btn-info pull-left">
            <?php echo _l('add'); ?>
        </a>
      <?php } ?>
     </div>
     <div class="row col-md-12">
      <hr>
     </div>
      <div class="col-md-12">
        <table class="table dt-table">
          <thead>
            <th>#</th>
            <th><?php echo _l('name');  ?></th>
            <th><?php echo _l('type');  ?></th>
            <th><?php echo _l('effective_time');  ?></th>
            <th><?php echo _l('related_contract');  ?></th>
            <th><?php echo _l('options'); ?></th>                   
          </thead>
          <tbody>
            <?php foreach ($share as $key => $aRow) { ?>
              <tr>
                <td><?php  echo html_entity_decode($key+1); ?></td>
                <td><?php  echo html_entity_decode($this->team_password_model->get_name_obj($aRow['type'],$aRow['share_id'])); ?></td> 
                <td><?php  echo _l($aRow['type']); ?></td>
                <td><span class="label label-info"><?php  echo _dt($aRow['effective_time']); ?></span></td>
                <td><?php  $contracts = get_contract_relate_client($aRow['type'],$aRow['share_id']);
                if($contracts != ''){
                  $count = 0;
                  foreach($contracts as $ctr){
                    $contract = get_contract_id($ctr);
                    if($contract){
                      if($contract->client == get_client_user_id()){
                        if($count < (count($contracts) - 1)){
                          $br = '<br/><br/>';
                        }else{
                          $br = '';
                        }
                        echo '<a href="'.site_url('contract/'.$contract->id.'/'.$contract->hash).'" ><span class="label label-default"> '.get_company_name($contract->client).' - '. $contract->subject.'</span></a>'.$br;
                        $count ++;
                      }else{
                        echo '';
                      }
                    }else{
                      echo '';
                    } 
                  }
                }else{
                  echo '';
                }
                ?></td>
                <?php 
                $option = '';
                $option .= '<a href="'.site_url('team_password/team_password_client/view_share_client/'.$aRow['hash'].'/'.$aRow['type']).'" class="btn btn-success btn-icon mright5" >';
                $option .= '<i class="fa fa-eye"></i>';
                $option .= '</a>';
                $option .= '<button onclick="copyToClipboard(this);" data-hash="'.site_url('team_password/team_password_client/view_share_client/'.$aRow['hash'].'/'.$aRow['type']).'" class="btn btn-warning btn-icon" >';
                $option .= '<i class="fa fa-copy"></i>';
                $option .= '</button>';
                ?>
                <td><?php  echo html_entity_decode($option); ?></td>                       
              </tr>
            <?php } ?>
          </tbody>
          <tfoot>
          </tfoot>
        </table>
      </div>
   


