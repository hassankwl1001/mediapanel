<?php
// dgaps\mediapanel\src\routes\web.php
Route::group(['namespace' => 'hassankwl1001\mediapanel\Http\Controllers', 'middleware' => ['web']], function(){
	Route::match(["get", "post"],'mediapanel', 'MediaPanelController@index');
	Route::match(["get", "post"],'mediapanel/media', 'MediaPanelController@media');
	Route::match(["get", "post"],'mediapanel/folders', 'MediaPanelController@createFolder');
	Route::match(["get", "post"],'mediapanel/storefolder', 'MediaPanelController@storefolder');
	Route::match(["get", "post"],'mediapanel/video', 'MediaPanelController@insertVideo');
	Route::match(["get", "post"],'mediapanel/upload', 'MediaPanelController@uploadMedia');
	Route::match(["get", "post"],'mediapanel/_upload', 'MediaPanelController@_upload');
	Route::match(["get", "post"],'mediapanel/setting', 'MediaPanelController@settings');
	Route::match(["get", "post"],'mediapanel/storesizes', 'MediaPanelController@storesizes');
	Route::match(["get", "post"],'mediapanel/_update_opt', 'MediaPanelController@_update_opt');
	Route::match(["get", "post"],'mediapanel/_search_images', 'MediaPanelController@_search_images');
	Route::match(["get", "post"],'mediapanel/_changeFolder', 'MediaPanelController@_changeFolder');
	Route::match(["get", "post"],'mediapanel/storesetting', 'MediaPanelController@storesetting');
	Route::match(["get", "post"],'mediapanel/_delMedia', 'MediaPanelController@_delMedia');
	Route::match(["get", "post"],'mediapanel/insertVideo', 'MediaPanelController@_insertVideo');
	Route::match(["get", "post"],'mediapanel/_delFolder', 'MediaPanelController@_delFolder');
	Route::match(["get", "post"],'mediapanel/load_images', 'MediaPanelController@more_images');
	Route::match(["get", "post"],'mediapanel/crop-sec', 'MediaPanelController@cropSec')->name("crop-sec");
});
?>