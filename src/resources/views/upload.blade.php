<div class="m-upload m-box">

<div class="ins-left">
	<h3 class="dg-m-heading">Upload Media</h3>
	<div class="inner">
    	<form id="f_upload_form" method="post" enctype="multipart/form-data"
                 	action="{{url('/mediapanel/_upload')}}" autocomplete="off">
             <select name="folder[]" class="form-control media-folder-u chosen-select-sl" autocomplete="off" multiple="multiple" data-placeholder="Select Folder">
             	@foreach($folder as $k=>$v)
					<option value="{{$v->id}}">{{$v->folder_name}}</option>
				@endforeach
            </select>
            @csrf
            <div class="upload-f">
                <div class="label">Upload Image</div>
                        <div class="upf">
                            <input type="file" name="ufile" id="mediaUpload" accept="image/*"/>
                    </div>
            </div>
		</form>           
	</div>
</div>
<style>.chosen-container{width:100% !important;}</style>
</div>