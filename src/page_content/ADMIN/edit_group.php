<?php
require_once "./resources/library/groups.php";
$groupDbAdapter = new groupDataAdapter($dsn, $user_name, $pass_word);

$GRP_id = $header_GET_array[0];

$where_perams = '{"GRP_id| =": "'.$GRP_id.'"}';
$where_object = json_decode($where_perams);

$groups = $groupDbAdapter->SelectWhereJSON($where_object);

	foreach ($groups as $key => $value) {
	//for($i=0;$i<mysqltng_num_rows($matrixList_res);$i++){
		$GRP_name = $value->GRP_name;
		
	}
?>
<section>
  <h1>Edit Group (IN DEVELOPMENT)</h1>
  <p>Doesn't update user related group name data.</p>
  <a class="btn btn-primary"  href="./?I=<?php echo pg_encrypt("ADMIN-list_groups",$pg_encrypt_key,"encode") ?>" />Go Back to List</a>
  <hr>
  <div class="info">
    <p>&nbsp;</p>
  </div>
  <div class="row">
    <div class="col-lg-6">
      <div class="panel panel-green">
        <div class="panel-heading">
          <h2 class="panel-title">GROUP DETAILS</h2>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <form action="./?I=<?php echo $_GET['I']; ?>" method="post" enctype="multipart/form-data">
              <input type="hidden" id="post_type" name="post_type" value="<?php echo pg_encrypt("qryGROUP-edit_group_qry",$pg_encrypt_key,"encode") ?>" />
              <input type="hidden" name="group_id" value="<?php echo pg_encrypt($GRP_id,$pg_encrypt_key,"encode"); ?>">

              <label>Group Name</label>
              <input name="group_name" type="text" value="<?php echo $GRP_name; ?>" class="form-control">
              
              <button type="submit" class="btn btn-danger">EDIT GROUP</button>
            </form>
            <hr>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
