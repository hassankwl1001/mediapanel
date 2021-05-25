var track_page = 1;
var load_data = true;
$(document).ready(function(){
	var multiple = false;
	window.type = "";
	window.return = "";
	window.for = "";
	window.media = "";
	window.up = "";
	function sendData(url,obj){
		return $.ajax({
			"type":"POST",
			"global":false,
			"dataType":"html",
			"url":url,
			"data":obj
		});
	}
	
	function ___chosen(){
		$(".chosen-select-sl").select2();
	}
	
	//Get Size of Media
	function formatBytes(bytes,decimals) {			// Get Size of Media
	   if(bytes == 0) return '0 Bytes';
	   var k = 1000,
		   dm = decimals + 1 || 3,
		   sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
		   i = Math.floor(Math.log(bytes) / Math.log(k));
	   return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
	}
	
	function setHeight(){
		var mHeight = $(".mediaPanel").innerHeight();
		var inHeight = $(".m-insert .dg-m-row").innerHeight() + 80;
		$(".m-insert .inner").css("height", mHeight - inHeight);
	}
	
	// Open Media Panel
	$(document).on("click", ".insert-media", function(){
		var type, rt, fr;
		type = $(this).attr("data-type");
		rt = $(this).attr("data-return");
		fr = $(this).attr("data-for");
		window.type = type;
		window.return = rt;
		window.for = fr;
		window.link = $(this).attr("data-link");
		window.file = $(this).attr("data-file");
		var url = start_url+"/mediapanel";
		var data = {_token:_token,action:"_load"};
		sendData(url,data).done(function(resp){
			$("body").append(resp);
			setHeight();
			___chosen();
		});
	});
	
	// Close and Refresh Media Panel
	$(document).on("click", ".close-m-panel", function(){
		close_media_panel();
	});
	
	function close_media_panel(){
		$(".mediapanel-wraper").remove();
		track_page = 1;
		load_data = true;
	}
	
	function loadSubPages(method, into){
		var url = start_url+"/mediapanel/"+method;
		var data = {_token:_token,action:method};
		sendData(url,data).done(function(resp){
			$(into).html(resp);
			if(method=="upload" || method=="media"){
				___chosen();
				setHeight();
			}
		});
	}
	
	//Loads sub page on click panel nav bar
	$(document).on("click", ".mediaPanel .m-left ul li", function(){
		$(".mediaPanel .m-left ul li").removeClass("active");
		$(this).addClass("active");
		var _p = $(this).attr("data-for");
		var into = ".mediapanel-wraper .mediaPanel .m-right";
		if (_p==="images"){
			loadSubPages("media", into);
		}else if (_p==="folder"){
			loadSubPages("folders", into);
		}else if (_p==="upload"){
			loadSubPages("upload", into);
		}else if (_p==="video"){
			loadSubPages("video", into);
		}else if (_p==="setting"){
			loadSubPages("setting", into);
		}
	});
	
	function getFileInfo(size, type, obj){
		var info, default_size, default_type;
		info = obj;
		if(default_image_size===0){
			default_size = "Actual";
		}else{
			default_size = default_image_size;
		}
		var file_types = {};
		var image_info  = info.sizes[size][type];
		if(image_info.hasOwnProperty("size")){
			$(".f-info .size").html(formatBytes(image_info.size));	
		}
		$(".f-info .dimension").html(image_info.width+"x"+image_info.height);
		$(".url a").attr("href", image_info.url);
		$(".url a").attr("title", image_info.url);
		$(".url a").text("View Image");

		$(".f-info .img").html("<img src='"+image_info.url+"' style='max-height:120px; max-width:120px;'>");
		
	}
	
	$(document).on("click",".mediaPanel .m-insert ul.file-list li",function(){
		//For active outer li
		$(".mediaPanel .m-insert ul.file-list li").removeClass("active");
		$(this).addClass("active");
		var __img = $(this).find("img");
		// For Media Information
		var info = __img.closest("li").find("div").html();
		info = JSON.parse(info);
		if(info.hasOwnProperty("id")){
			info = info;
		}else{
			info = JSON.parse(info);
		}
		
		var get = {};
		var src, file, size, demension, date, type, name, id, folder, title, alt, sizes,file_types, dm_sizes, default_size, default_type;
		
		type = __img.attr("data-type");
		file_types = {};
		folder = info.folders;
		if(default_image_size===0){
			default_size = "Actual";
		}else{
			default_size = default_image_size;
		}
		
		if(typeof info.sizes[default_size]=="undefined"){
			default_size = "Actual";
		}
		file_types = Object.keys(info.sizes[default_size]);
	
		if(default_image_type==1){
			
			if (file_types.length==1){
				default_type = file_types[0];
			}else{
				for(var x = 0; x < file_types.length; x++){
					if(file_types[x]!="webp"){
						default_type = file_types[x];
					}
				}
			}
		}else{
			if (file_types.length==1){
				default_type = file_types[0];
			}else{
				for(var x = 0; x < file_types.length; x++){
					if(file_types[x]=="webp"){
						default_type = file_types[x];
					}
				}	
			}
			
		}
		getFileInfo(default_size, default_type, info);
		var image_info  = info.sizes[default_size][default_type];
		/*
		$(".f-info .img").html("<img src='"+src+"' style='max-height:120px; max-width:120px;' data-id='"+id+"'>");
		*/
		$(".f-info .title").html(info.title);
		if(default_title===1){
			$(".alt-form input[name='img-title']").val(info.title);	
		}
		if(default_alt_text===1){
			$(".alt-form input[name='img-alt']").val(info.alt);
		}
		if(default_caption===1){
			$(".alt-form input[name='img-caption']").val(info.caption);
		}
		if(default_desp===1){
			$(".alt-form input[name='img-description']").val(info.description);
		}
		$(".f-info .date").html(info.created_at);
		//$(".f-info .size").html(formatBytes(image_info.size));
		//$(".f-info .dimension").html(image_info.width+"x"+image_info.height);
		$(".f-info").css("display", "block");
		$(".f-info .del-fu").attr("data-id", info.id);
		//$(".url a").attr("href", image_info.url);
		//$(".url a").text(image_info.url);
		$('.alt-form ._ch_folder').find('option').each(function(){
			$(this).prop('selected',false);
		});
		$("select[name='sizes']").html("");
		$("select[name='sizes']").append("<option value='0'>Actual ("+formatBytes(image_info.size)+")</option>");
		var sl = "";
		$.each(info.sizes, function(k, v){
			var sz = "";
			if(k!="Actual"){
				sz = k;
				var zs = formatBytes(v[default_type]["size"]);
				var hgt = v[default_type]["height"];
				var wdh = v[default_type]["width"];
				if(default_size==sz){
					sl = "selected";
				}else{
					sl = "";
				}
				k = wdh+"x"+hgt;
				$("select[name='sizes']").append("<option value='"+sz+"' "+sl+">"+k+" px ("+zs+")</option>");	
				
			}
			
		});
		$("select[name='sizes']").append("<option value='custom'>Custom</option>");
		
		$(".img-types div").html("");
		
		$(".f-info .types").html(file_types.join(" , "));
			$.each(file_types, function(i, v){
				$(".img-types div").append("<label><input type='radio' name='types' value = '"+v+"' > "+v+"</label>");
			});

		if(default_image_type == 0){
			if($(".img-types div label").length==1){
				$(".img-types div label input").prop("checked", true);
			}else{
				$(".img-types div label").each(function(){
					var vi = $(this).find("input").val();
					if(vi=="webp"){
						$(this).find("input").prop("checked", true);
					}
				});	
			}
		}else if(default_image_type == 1){
			$(".img-types div label").each(function(){
				var vi = $(this).find("input").val();
				if(vi!="webp"){
					$(this).find("input").prop("checked", true);
				}
			});
		}
		
		
		$(".img-types input[type='radio']").click(function(){
			var t = $(this).val();
			var d = $(".m-ins-image").closest(".active").find("div").html();
			d = JSON.parse(d);
			if(d.hasOwnProperty("id")){
				d = d;
			}else{
				d = JSON.parse(d);
			}
			
			if(d.hasOwnProperty("sizes")){
				$(".dg-form-control[name='sizes']").html("");
				var zs = formatBytes(d.sizes["Actual"][t].size);
				var h = "<option value='0'>Actual ("+zs+")</option>";
				$(".dg-form-control[name='sizes']").append(h);
				var sl = "";
				for(size in d.sizes){
					zs = formatBytes(d.sizes[size][t].size);
					if(size!="Actual"){
						if(default_size==size){
							sl = "selected";
						}else{
							sl = "";
						}
						h = "<option value='"+size+"' "+sl+" >"+size+" px ("+zs+")</option>";
						$(".dg-form-control[name='sizes']").append(h);
					}
				}
				$(".dg-form-control[name='sizes']").append("<option value='custom'>Custom</option>");
			}
		});
		
		var a = $('.alt-form ._ch_folder').find('option').each(function(){
	  		var op_val = $(this).attr('value');
			for(var nn=0; nn < folder.length; nn++){
				if(op_val == folder[nn]){
					$("._ch_folder option[value='"+op_val+"']").prop("selected", true);
				}
			}
	 	 });
		$(".chosen-select-sl").trigger("chosen:updated");
	});
	
	$(document).on("change", ".mediaPanel .alt-form select[name='sizes']", function(){
		var v = $(this).val();
		if(v==="custom"){
			$(".dg-m-cus-size").show();
		}else{
			var sz = $(this).val();
			if(sz=="0"){
				sz = "Actual";
			}
			var tp = $(".alt-form input[name='types']:checked").val();
			var info = $(".file-list .active div").html();
			info = JSON.parse(info);
			getFileInfo(sz, tp, info);
			$(".dg-m-cus-size").hide();
			$(".dg-m-cus-size input").val("");
		}
	});
	
	$(document).on("change", "#mediaUpload", function(){
		var v = $(this).val();
		var validExts = new Array(".png",".jpg",".jpeg",".gif",".bmp", ".webp");
		var fileExt = v;

		fileExt = fileExt.substring(fileExt.lastIndexOf('.'));
		fileExt = fileExt.toLowerCase();
		if (validExts.indexOf(fileExt) < 0) {
		  alert("Invalid file selected, valid files are of " +
				   validExts.toString() + " types.");
			$(this).val("");
		  return false;
		}else{
			var a= $('.media-folder-u option:selected').length;
			if (a>0) {
				$('.chosen-choices').find('li.search-choice').remove();
				if(fileExt===".webp" && webp_supported===false){
					var wf = window.confirm("Server is not webp supported.\nImage will not be converted into different sizes.\nDo you want to continue?");
					if(wf){
						upload_u_file();
					}
				}else{
					upload_u_file();	
				}
			}else{
				alert("Please Select Folder Name");
				$(this).val("");
			}
			
		}
			
	});
	
	function upload_u_file(){
		var f;
		var gif_img = preloader;
		$("#f_upload_form").ajaxForm({
			beforeSend:function(){
				$("<div class='img-ld' style='text-align:center;'><img src="+gif_img+"><br><span class='__percent_val'>0%</span></div>").insertBefore(".upload-f");
			},
			complete: function(response) { // on complete
				$("#f_upload_form").resetForm();
				f = response.responseText;
				$(".mediapanel-wraper .mediaPanel .m-right").html(f);
				$(".mediaPanel .m-left li").removeClass("active");
				$(".mediaPanel .m-left li[data-for='images']").addClass("active");
				___chosen();
			}
			,uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				console.log(percentVal);
				$(".__percent_val").html(percentVal);
			}
		}).submit();
	}
	
	// Return Media to specified position
	$(document).on("click", ".insert-m-to", function(){	
		var elm = $(".mediaPanel .f-info .url a");
		window.media = elm.attr("href");
		if (window.for=="link"){
			date_for_link();
		}else if(window.for=="display"){
			data_for_display();
		}else if(window.for=="editor"){
			data_for_editor();	
		}
		return false;
	});
	
	function date_for_link(){
		if (window.type=="image"){
			var link = window.media;
			var img_title = $(".alt-form input[name='img-title']").val();
			var img_alt = $(".alt-form input[name='img-alt']").val();
			var tl = (img_title=="" || img_title==undefined) ? "" : "title='"+img_title+"'";
			var al = (img_alt=="" || img_alt==undefined) ? "" : "alt='"+img_alt+"'";
			
			if (window.return!="" && window.return!="undefined"){
				var target = $(window.return).get(0).tagName;
				if (target=="INPUT" || target=="input"){
					$(window.return).val(link);
				}else{
					$(window.return).html(link);	
				}
			}
		}
		close_media_panel();
	}
	
	function data_for_display(){
		if (window.type=="image"){
			var link = window.media;
			var img_title = $(".alt-form input[name='img-title']").val();
			var img_alt = $(".alt-form input[name='img-alt']").val();
			var tl = (img_title=="" || img_title==undefined) ? "" : "title='"+img_title+"'";
			var al = (img_alt=="" || img_alt==undefined) ? "" : "alt='"+img_alt+"'";
			
			if (window.return!="" && window.return!="undefined"){
				var target = $(window.return).get(0).tagName;
				if (target!="INPUT" || target!="input"){
					var img = "<img src='"+link+"' "+ tl +" "+ al +">";
					if (window.link!="undefined" || window.link!=""){
						$("input[name='"+window.link+"']").val(link);	
					}
					$(window.return).html(img);
					 $(".f-info .alt-form input[name='img-title']").val("");
					 $(".f-info .alt-form input[name='img-alt']").val("");
				}
			}
		}
		close_media_panel();	
	}
	
	function data_for_editor(){
		if (window.type=="image"){
			var link = window.media;
			var img_title = $(".alt-form input[name='img-title']").val();
			var img_alt = $(".alt-form input[name='img-alt']").val();
			var tl = (img_title=="" || img_title==undefined) ? "" : "title='"+img_title+"'";
			var al = (img_alt=="" || img_alt==undefined) ? "" : "alt='"+img_alt+"'";
						
			if (window.return!="" && window.return!="undefined"){
				var target = $(window.return).get(0).tagName;
				var img = "<img src='"+link+"' "+ tl +" "+ al +">";
				window.return = window.return.replace("#","");
				tinyMCE.get(window.return).execCommand("mceInsertContent",false,img);
				$(".f-info .alt-form input[name='img-title']").val("");
				$(".f-info .alt-form input[name='img-alt']").val("");
			}
		}
		close_media_panel();
	}
	
	// Insert Video into Editor
	$(document).on("click", ".v-submit", function(){
		var source = $("select[name='source']").val();
		var embed = $("input[name='embed']").val();
		var url = start_url+"/mediapanel/insertVideo";
		var data = {_token:_token, "source":source, "embed":embed };
		sendData(url,data).done(function(resp){
			window.return = window.return.replace("#","");
			tinyMCE.get(window.return).execCommand("mceInsertContent",false,resp);
		});
		close_media_panel();
		return false;
	});
	
	// Delete Image;
	$(document).on("click", ".del-fu", function(){
		var f = $(this).attr("data-id");
		var r = confirm("Do you want to delete file?");
		if (r===true){
			var url = start_url+"/mediapanel/_delMedia";
			var data = {_token:_token, "t" : f};
			sendData(url,data).done(function(resp){
				if (resp==="yes"){
					$(".f-info .img").html("");
					$(".f-info .title").html("");
					$(".f-info .date").html("");
					$(".f-info .size").html("");
					$(".f-info .dimension").html("");
					$(".file-list li.active").remove();
					$(".f-info").css("display", "none");
				}
			});
		}
	});
	
	// Create Folder
	$(document).on("click", ".v-submit-f", function(){
		var f = $(".f-gal").val();
		var inp = $(this).attr("data-input");
		if (f==""){
			alert("Please enter folder name!");	
		}else{
			var data = {_token:_token, t:f, s:inp};
			var url = start_url+"/mediapanel/storefolder";
			sendData(url, data).done(function(j){
				$(".f-gal").val("");
				$(".m-folder .folderlist").html(j);	
			});
			$(this).attr("data-input", "new");
		}
	});
	
	// Delete Folder
	$(document).on("click", ".del-folder",function(){
		var t = $(this).attr("data-title");
		if (window.confirm("Do you want to delete folder?")){
			$(this).parent().parent().remove();
			var url = start_url+"/mediapanel/_delFolder";
			var data = {_token:_token,"t":t};
			sendData(url, data).done(function(d){
				d = d;
			});
		}
	});
	
	// Edit Folder
	$(document).on("click", ".edit-folder", function(){
		var t = $(this).attr("data-title");	
		$(".f-gal").val(t);
		$(".v-submit-f").attr("data-input", t);
	});
// search images from folders
	$(document).on("keyup",".search_images",function(e){
		if(e.which===13){
			var g = $(".image-from-folder").val();
			var v = $(this).val();
			var url = start_url+"/mediapanel/_search_images";
			var data = {_token:_token,"f":v, g:g};
			sendData(url, data).done(function(j){
				$(".mediaPanel .m-box .ins-left .inner").html(j);
			});
			$(".mediaPanel .f-info").hide();
			track_page = 1;
			load_data = true;
		}
	});

	$(document).on("click",".up-m-to",function(){
		var selData = $(".m-ins-image").closest(".active").find("div").html();
		selData = JSON.parse(selData);
		if(selData.hasOwnProperty("id")){
			selData = selData;
		}else{
			selData = JSON.parse(selData);
		}
		var image_id = selData.id;
		var data = {};
		data._token = _token;
		data.id = image_id;
		data.folder = $(".alt-form ._ch_folder ").val();
		data.title= $(".alt-form input[name='img-title']").val();
		data.alt = $(".alt-form input[name='img-alt']").val();
		data.caption = $(".alt-form input[name='img-caption']").val();
		data.description = $(".alt-form input[name='img-description']").val();
		data.size = $(".alt-form  input[name='img-cus-size']").val()
		var url = start_url+"/mediapanel/_update_opt";
		
		sendData(url, data).done(function(j){
			var m = JSON.parse(j);		
			if (m.resp == "error"){
				window.alert(m.msg);
			}else if (m.resp=="success"){
				window.alert(m.msg);
				$(".dg-m-cus-size").hide();
				$("select[name='sizes']").html("");
				var default_type = $(".img-types input:checked").val();
				var zs = formatBytes(m.data["sizes"]["Actual"][default_type]["size"]);
				$("select[name='sizes']").append("<option value='Actual'>Actual ("+zs+")</option>");
				$.each(m.data["sizes"], function(k, v){
					 zs = formatBytes(v[default_type]["size"]);
					if(k!="Actual"){
						$("select[name='sizes']").append("<option value='"+k+"'>"+k+" px ("+zs+")</option>");	
					}
				});
				$("select[name='sizes']").append("<option value='custom'>Custom</option>");
				$("input[name='img-cus-size']").val("");
				$(".m-ins-image").closest(".active").find("div").html(JSON.stringify(m.data));
			}
		});
		
		track_page = 1;
		load_data = true;
	});	
	$(document).on("change",".image-from-folder",function(){
		var v = $(this).val();
		var url = start_url+"/mediapanel/_changeFolder";
		var data = {_token:_token,"f":v};
		sendData(url, data).done(function(j){
			$(".mediaPanel .m-box .ins-left .inner").html(j);
			$(".___loadMore").show();
			$(".inner .dg-m-alert-info").remove();
		});
		$(".mediaPanel .f-info").hide();
		track_page = 1;
		load_data = true;
	});
	
	$(document).on("click", ".___loadMore", function(){
		var _el = $(this);
		if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
			if (load_data==true){
				track_page++;
				var url = start_url+"/mediapanel/load_images";
				$(".mediaPanel .m-box .ins-left .inner").addClass("load-data");
				var folder = $(".image-from-folder").val();
				var obj = {"f":folder, "page":track_page,"_token":_token};
				loading = true; 
				sendData(url, obj).done(function(data){
					if(data == "No More Images"){
						load_data = false;
						data = "<div class='dg-m-alert dg-m-alert-info'>No More Images</div>";
						_el.closest("div").html(data);
					}else{
						$(".mediaPanel .m-box .ins-left .inner .file-list").append(data);	
					}
				});
			}
		}
	});
	
	$(document).on("click", ".dg-m-s-m", function(){
		$(".dg-m-setting-f .dg-r-lf").append("<div><input type='number' name='size' placeholder='Enter Width in Pixel'></div>");
		return false;
	});
	
	$(document).on("click", ".dg-m-s-s", function(){
		var data = [];
		$(".dg-r-lf input").each(function(){
			var size = 0;
			if($(this).val().trim()!==""){
				size = parseInt($(this).val());
			}
			if(size > 0){
				data.push(size);
			}
		});
		$(".dfSizes").html("");
		for(var n=0; n < data.length; n++){
			$(".dfSizes").append('<input type="radio" name="image_size" value="'+data[n]+'">   '+data[n]+'     ');
		}
		if(data.length > 0){
			var data = {_token:_token, data:data};
			var url = start_url+"/mediapanel/storesizes";
			sendData(url, data).done(function(j){
				alert("Data has been saved.");	
			});
		}else{
			alert("Please enter sizes");
		}
	});
	
	$(document).on("click", ".dg-m-s-gs", function(){
		var data = {};
		data.auto_webp = $('input[name="auto_webp"]:checked').length > 0 ? 1 : 0;
		data.default_title = $('input[name="image_title"]:checked').val();
		data.default_alt_text = $('input[name="alt_text"]:checked').val();
		data.default_desp = $('input[name="image_description"]:checked').val();
		data.default_caption = $('input[name="image_caption"]:checked').val();
		data.default_image_size = $('input[name="image_size"]:checked').val();
		data.default_image_type = $('input[name="image_type"]:checked').val();
		auto_webp = data.auto_webp;
		default_title = parseInt(data.default_title);
		default_alt_text = parseInt(data.default_alt_text);
		default_desp = parseInt(data.default_desp);
		default_caption = parseInt(data.default_caption);
		default_image_size = parseInt(data.default_image_size);
		default_image_type = parseInt(data.default_image_type);
		var data = {_token:_token, data:data};
		var url = start_url+"/mediapanel/storesetting";
		sendData(url, data).done(function(j){
			alert("Data has been saved.");	
		});
		
	});
	
});
