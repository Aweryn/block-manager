<?php
add_action('render_modal_new_block', 'modal_new_block');
function modal_new_block() {
  ?>
<!-- Modal -->
<div class="modal fade" id="modal-newblock" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Create new block</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	<form method="POST" action="<?= admin_url('admin-ajax.php'); ?>" id="action_new_block">
    <small id="blockInfo" class="form-text text-muted">
    Please enter the required information for registering new blocks. The block can be structured using custom fields after registering. See more from <a href="https://www.advancedcustomfields.com/resources/acf_register_block_type/" target="_blank">acf_register_block_type</a>
    </small>
  <div class="form-group row">
    <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="inputTitle" id="inputTitle" placeholder="Title" required>
    </div>
  </div>
  <div class="form-group row">
    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="inputName" id="inputName" placeholder="Name" required>
    </div>
  </div>
	<div class="form-group row">
    <label for="inputDesc" class="col-sm-2 col-form-label">Description</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="inputDesc" id="inputDesc" placeholder="Description">
    </div>
  </div>
	<div class="form-group row">
    <label for="inputCat" class="col-sm-2 col-form-label">Category</label>
    <div class="col-sm-10">
		<select class="custom-select" name="inputCat" name="inputCat" id="inputTitle">
			<option value="" selected>Choose category</option>
			<option value="custom-blocks">Custom blocks</option>
		</select>
    </div>
  </div>
	<input type="hidden" name="action" value="create_new_block" />
	<button class="btn btn-primary btn-db" type="submit">Create block</button>
	</form>
      </div>
    </div>
  </div>
</div>
  <?php
}
?>