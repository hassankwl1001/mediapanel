<?php
//dgaps\mediapanel\src\MediaPanelServiceProvider.php
namespace dgaps\mediapanel;
use Illuminate\Support\ServiceProvider;
class MediaPanelServiceProvider extends ServiceProvider {
	public function boot(){
		$this->loadRoutesFrom(__DIR__.'/routes/web.php');
	}
	public function register(){
		
		$this->app->make('dgaps\mediapanel\Http\Controllers\MediaPanelController');
		$this->loadViewsFrom(__DIR__.'/resources/views', 'mediapanel');
		$this->loadMigrationsFrom(__DIR__.'/Database/migrations');
	}
}
?>