<script>
	var start_url = "{{url('/')}}"; 
	var preloader = "{{url('/')}}/packages/dgaps/mediapanel/src/resources/assets/loading.gif";
	var _token = "@php echo csrf_token(); @endphp";
	var webp_supported = false;
	var auto_webp = 0;
	var default_title = 1;
	var default_alt_text = 1;
	var default_desp = 1;
	var default_caption = 1;
	var default_image_size = 0;
	var default_image_type = 1;
	@php
	if(extension_loaded('gd')){
		$info = gd_info();
		if($info["WebP Support"]){
			echo "webp_supported = true;";
		}
	}
	foreach($settings as $k=>$v){
		echo "$k = $v;";	
	}
	@endphp
</script>
<link rel="stylesheet" href="{{url('/packages/dgaps/mediapanel/src/resources/assets/media.css')}}?{{rand(1,99999)}}" type="text/css">
<script src="{{url('/packages/dgaps/mediapanel/src/resources/assets/form.js')}}" defer></script>
<script src="{{url('/packages/dgaps/mediapanel/src/resources/assets/media.js')}}" defer></script>



