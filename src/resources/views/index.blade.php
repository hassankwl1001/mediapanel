<script>
	var start_url = "{{url('/')}}"; 
	var preloader = "{{url('/')}}/vendor/hassankwl1001/mediapanel/src/resources/assets/loading.gif";
	var _token = "@php echo csrf_token(); @endphp";
	
	@php
	if(extension_loaded('gd')){
		$info = gd_info();
		if($info["WebP Support"]){
			echo "var webp_supported = true;";
		}
	}else{
		echo "var webp_supported = false;";
	}
	if(count($settings)==0){
		echo "
		var auto_webp = 0;
		var default_title = 1;
		var default_alt_text = 1;
		var default_desp = 1;
		var default_caption = 1;
		var default_image_size = 0;
		var default_image_type = 1;";
	}else{
		foreach($settings as $k=>$v){
			echo "var $k = $v;";	
		}
	}
	@endphp
</script>
<!-- CHOOSEN-->
<script src="{{url('/vendor/hassankwl1001/mediapanel/src/resources/assets/chosen.js')}}" defer></script>
<link rel="stylesheet" href="{{url('/vendor/hassankwl1001/mediapanel/src/resources/assets/chosen.css')}}" type="text/css">

<link rel="stylesheet" href="{{url('/vendor/hassankwl1001/mediapanel/src/resources/assets/media.css')}}?{{rand(1,99999)}}" type="text/css">
<script src="{{url('/vendor/hassankwl1001/mediapanel/src/resources/assets/form.js')}}" defer></script>
<script src="{{url('/vendor/hassankwl1001/mediapanel/src/resources/assets/media.js')}}" defer></script>




