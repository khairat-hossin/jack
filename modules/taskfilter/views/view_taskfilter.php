<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
   	<?php include_once('view_filter.php') ?>  	
   	</div>
</div>
<?php init_tail(); ?>
</body>
</html>
<script>
  initDataTable('.table-table_view_filter'+<?php echo $id; ?>, admin_url+'taskfilter/view_filter_table/'+<?php echo $id; ?>+'');
</script>