<div class="m-folder m-box row">
<div class="ins-left col-12 col-lg-12">
	<h3 class="dg-m-heading">Create Folder</h3>
	<div class="inner">
       <div class="row">
		   <div class="dg-form-group form-group col-12 col-lg-6">
			  <label class="req">Folder Title</label>
			  <input type="text" name="title" class="dg-form-control form-control f-gal"  autocomplete="off">
		   </div>
		   <div class="form-group">
			   <label for=""></label>
			  <input type="submit" name="v-submit-u" class="dg-btn dg-btn-primary btn btn-primary form-control v-submit-f" data-input='new'>
		   </div>
		 </div> 
		   <div class="folderlist" style="margin-top: 5%;">
		   		@include("mediapanel::folder-list")
		   </div>
         
	</div>
</div>
</div>