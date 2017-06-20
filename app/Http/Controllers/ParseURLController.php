<?php

namespace App\Http\Controllers;

use App\Parse_URL;

use Illuminate\Http\Request;
use Exception;
use DB;
use App\User;

class ParseURLController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function list(Request $request){
      return  $request->user()->parse_urls()->get();
    }

    public function wipeRecords(){
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      DB::table('new_classifieds')->truncate();
      DB::table('classifieds')->truncate();
      DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
          'url' => 'url',
      ]);

        $user = $request->user();
        try{
          $url = Parse_url::create([
            'url' => $request->url,
          ]);
        }catch (Exception $e){
            $errorCode = $e->errorInfo[1];

            if($errorCode == 1062){
                $url = Parse_url::where('url', $request->url)->first();
            }
        }

      if(!$user->parse_urls->contains($url->id)){
          return $user->parse_urls()->save($url, ['name' => $request->name]);
      }else{
        return "";
      }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Parse_URL  $parse_URL
     * @return \Illuminate\Http\Response
     */
    public function show(Parse_URL $parse_URL)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Parse_URL  $parse_URL
     * @return \Illuminate\Http\Response
     */
    public function edit(Parse_URL $parse_URL)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Parse_URL  $parse_URL
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Parse_URL $parse_URL)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Parse_URL  $parse_URL
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parse_URL $parse_URL)
    {
        //
    }

    public function delete(Request $request){
      $parseURL = Parse_URL::find($request->id);
      $user = $request->user();
      $user->parse_urls()->detach($parseURL);
    }
}
