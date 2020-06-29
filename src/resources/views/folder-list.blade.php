<ul class='folder-g'>
<div class="row">
	@foreach($folders as $k=>$v)
	<div class="col-md-6">
		  <li>
		  {{$v->folder_name}} 		  	    	
		<div class="action">
			<a data-title="{{$v->folder_name}}" class="edit-folder dg-btn dg-btn-primary dg-btn-sm">Edit</a>
			<a data-title="{{$v->folder_name}}" class="del-folder dg-btn dg-btn-delete dg-btn-sm">Delete</a>
		</div>
		<div class="m-clear"></div>
			</li>
	</div>
 
	@endforeach
</div>
</ul>