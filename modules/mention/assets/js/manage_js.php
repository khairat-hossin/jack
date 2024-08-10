<script>
$(function () {
  "use strict";
  if(<?php echo html_entity_decode($postid); ?> != 0){
    see_details_isn(<?php echo html_entity_decode($postid); ?>);
  };
});
</script>