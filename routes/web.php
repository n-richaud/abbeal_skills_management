<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/redirect', 'Auth\LoginController@redirectToProvider');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback');

Route::get('list', function() {
    $dir = '/';
    $recursive = true; // Get subdirectories also?
    $contents = collect(Storage::disk('google')->listContents($dir, $recursive));
    
    // get dirname
    $dirList = $contents->where('type', '=', 'dir');
    $files = $contents->where('type', '=', 'file');
    
    $dirListPl = $dirList->pluck('name', 'basename');
    // dd($dirListPl);
    $FileList = $files->mapWithKeys(function($file) use ($dirListPl){
        $filename = $file['filename'].'.'.$file['extension'];
        $dirOfFile = $file['dirname'];
        $path = $file['path'];
        $basename = $file['basename'];
        $dirname = $file['dirname'] = $dirListPl[$dirOfFile];

        // Use the path to download each file via a generated link..
        // Storage::cloud()->get($file['path']);
        return [$filename => ['path' => $path,'dirname' => $dirname,'basename' => $basename]];
    });
    
    return view('CV_list',['fileList'=>$FileList]);
});



Route::get('list-folder-contents', function() {
    // The human readable folder name to get the contents of...
    // For simplicity, this folder is assumed to exist in the root directory.
    $folder = 'Consultant';
    // Get root directory contents...
    $contents = collect(Storage::cloud()->listContents('/', false));
    // Find the folder you are looking for...
    $dir = $contents->where('type', '=', 'dir')
        ->where('filename', '=', $folder)
        ->first(); // There could be duplicate directory names!
    if ( ! $dir) {
        return 'No such folder!';
    }
    // Get the files inside the folder...
    $files = collect(Storage::cloud()->listContents($dir['path'], false))
        ->where('type', '=', 'file');
    return $files->mapWithKeys(function($file) {
        $filename = $file['filename'].'.'.$file['extension'];
        $path = $file['path'];
        // Use the path to download each file via a generated link..
        // Storage::cloud()->get($file['path']);
        return [$filename => $path];
    });
});


Route::get('get-file', function() {
    return Storage::cloud()->get('12ru6F-MbcBcYkWmUWosMiVKKvrb6NAoU/1-T2iY9Wa88WC-w73kpMTExwthESMOwlWDeC09j9jtRU');
});

Route::get('export/{basename}', function ($basename) {
    $service = Storage::cloud()->getAdapter()->getService();
    $mimeType = 'text/plain';
    $export = $service->files->export($basename, $mimeType);
    // dd($export->getBody()->getContents());
    $handle = fopen('php://temp', 'r');
    rewind($handle);
    dd(fpassthru($handle));
    return response($export->getBody()->getContents(), 200, $export->getHeaders());
})->name('export');