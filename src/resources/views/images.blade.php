<div class="m-insert m-box">
	<div class="ins-left">
		<h3 class="dg-m-heading">Insert Media</h3>
		<div class="dg-m-row row m-0">
			<div class="form-group"> 
				<select name="folder" class="dg-form-control  form-control media-folder-u image-from-folder chosen-select-sl">
					<option value="">All</option>
					@foreach($folder as $k=>$v)
						@php
							$sl = ($folder_c==$v->id) ? "selected" : "";
						@endphp
						<option value="{{$v->id}}" {{$sl}}>{{$v->folder_name}}</option>
					@endforeach
				</select> &nbsp;
				</div>
				<div class="form-group col-6">
				<input type="text" placeholder="Search Here..." class="dg-form-control  form-control search_images float-right">
				</div>
		</div>
		<div class="inner">
			@include("mediapanel::images_list", compact("images"))
		</div>
	</div>
	<div class="ins-right">
		<div class="f-info">
			<h4 class="dg-m-heading">Media Information</h4>
			<div class="img"><img src="" alt=""></div>
			<div class="title"></div>
			<div class="date"></div>
			<span class="size"></span> || 			
			<span class="dimension"></span> ||
			<b><span class="types"></span> </b>
			<br>
			<span class="url"><a href="#" target="_blank" style='color:blue;'>Show Image</a></span>
			<span class="del del-fu"><a href="#">Delete Permanently</a></span>
			<div class="msgs"></div>
			<div class="alt-form">
			 	<div class="dg-form-group">
					<label> Select Folder</label>
					<select name="folder[]" class="form-control _ch_folder media-folder-u image-from-cat chosen-select-sl" multiple="multiple" autocomplete="off">
						@foreach($folder as $k=>$v)
							@php
								$sl = ($folder_c==$v->id) ? "selected" : "";
							@endphp
							<option value="{{$v->id}}" {{$sl}}>{{$v->folder_name}}</option>
						@endforeach
					</select>
				</div>
				 <div class="dg-form-group">
					 <label>Title</label>
					 <input type="text" name="img-title" class="dg-form-control form-control"/>
				 </div>

				 <div class="dg-form-group">
					 <label>Alt</label>
					 <input type="text" name="img-alt" class="dg-form-control form-control"/>
				 </div>
				 
				 <div class="dg-form-group">
					 <label>Caption</label>
					 <input type="text" name="img-caption" class="dg-form-control form-control"/>
				 </div>
				 
				 <div class="dg-form-group">
					 <label>Description</label>
					 <input type="text" name="img-description" class="dg-form-control form-control"/>
				 </div>
				 
				 <div class="dg-form-group">
					 <label>Sizes <small>(in width)</small></label>
					 <select class="dg-form-control form-control" name="sizes">
					 	<option value="actual">Actual</option>
					 </select>
				 </div>
				 
				 <div class="dg-form-group img-types">
					 <label>Types</label>
					 <div></div>
				 </div>
				 
				 <div class="dg-form-group dg-m-cus-size" style="display: none;">
					 <label>Custom Size</label>
					 <input type="number" placeholder="Image Size Eg: 250" name="img-cus-size" class="dg-form-control form-control"/>
				 </div>

				 <button class="dg-btn dg-btn-primary up-m-to">Update</button>
				 <button class="dg-btn dg-btn-primary insert-m-to">Insert</button>
			</div>
		</div>
	</div>
</div>
<style>.chosen-container{width:100% !important;}</style>
</div>