<div class="m-insert m-box">
<h3 class="box-title">Insert Media</h3>
<div class="ins-left">
<div class="inner">
@if(count($images)==0)
	<div class="alert alert-info text-center">No More Images</div>
@endif
<ul class='file-list'>
	@foreach($images as $k=>$v)
		@php
			$image = $v->image;
			$allowed = explode("|","jpg|jpeg|gif|png");
			$exp=explode(".", $image);
			$file_type="image";
			$info = explode(".",$image);
			$file_ext = strtolower(end($info));
			if (in_array($file_ext, $allowed)){
				$f = explode(".",$image);
				$file_name = str_replace(".".end($f),"", $image); 
				$name = str_replace(".".end($f),"", $image);
				$name = str_replace("-", " ",$name);
				$name = str_replace("_", " ",$name);
				$name = str_replace("thumb","",$name);
				$name = ucwords($name);
				if (file_exists($up_path.$v->image)){
				$size = filesize($up_path.$image);
				$date = filemtime($up_path.$image);	
				if ($file_type=="image"){
					$pt = $up_path.$v->image;
					if (file_exists($pt)){
						list($width, $height) = getimagesize($pt);
						$dimension = "$width x $height";
					}else{
						$dimension = "100 x 100";
					}	
						
				}
				$thumbnail = $v->thumb_image;
		@endphp
		<li>
			<img src="{{$img_url}}{{$thumbnail}}" 
				data-file='{{$img_url}}{{$v->image}}' 
				data-ext='{{$file_ext}}'
				data-size='{{$size}}'
				data-dimension='{{$dimension}}'
				data-date='{{$date}}'
				data-type='{{$file_ext}}'
				data-name='{{$name}}' 
				class='m-ins-image' 
				data-id='{{$v->id}}'>
		</li>
		@php }} @endphp
	@endforeach
</ul>
</div>
</div>
</div>