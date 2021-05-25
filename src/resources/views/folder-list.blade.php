<ul class='folder-g'>
<div class="row m-0">
	@foreach($folders as $k=>$v)
	<div class="col-md-6">
		  <li>
		  {{$v->folder_name}} 		  	    	
		<div class="action">
			<a data-title="{{$v->folder_name}}" class="edit-folder dg-btn dg-btn-sm">
			<img src="{{url('/vendor/hassankwl1001/mediapanel/src/resources/assets/edit.png')}}" width="15px;">
			</a>
			<a data-title="{{$v->folder_name}}" class="del-folder dg-btn dg-btn-sm">
			<img src="{{url('/vendor/hassankwl1001/mediapanel/src/resources/assets/trash.png')}}" width="15px;">
			</a>
		</div>
		<div class="m-clear"></div>
			</li>
	</div>
 
	@endforeach
</div>
</ul>