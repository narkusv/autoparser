<?php

namespace App\Http\Controllers;



use DB;
use Illuminate\Http\Request;
use App\Parse_URL;
use App\Classified;
use App\New_classified;
use Goutte\Client;

class ClassifiedController extends Controller
{


  /**
   * Start parsing all results from database and store them into individual stuff.
   * Routine task.
  */

  public function list($id){
    return Parse_URL::find($id)->classifieds()->get();
  }

  public function list_new_classifieds(Request $request){
    $results = DB::table('new_classifieds')
              ->join('classifieds', 'classifieds.id', '=', 'new_classifieds.classified_id')
              ->where('new_classifieds.user_id', $request->user()->id)
              ->orderBy('new_classifieds.created_at')
              ->get();


    //$results =  New_classified::where('user_id', $request->user()->id)->get();
    New_classified::where('user_id', $request->user()->id)->delete();
    return $results;
  }

  public function preview_new_classifieds(Request $request){
    $results = DB::table('new_classifieds')
              ->join('classifieds', 'classifieds.id', '=', 'new_classifieds.classified_id')
              ->where('new_classifieds.user_id', $request->user()->id)
              ->orderBy('new_classifieds.created_at')
              ->limit(6)
              ->get();


    //$results =  New_classified::where('user_id', $request->user()->id)->get();
    //New_classified::where()->delete();
    return $results;
  }

  public function parseAll()
  {
    $client = new Client();
    $urls = Parse_URL::all();
    foreach ($urls as $url) {
        $url->url .= '&f_50=atnaujinimo_laika_asc';
        if(strstr(parse_url($url->url, PHP_URL_HOST), 'autogidas.lt')){
          $this->parseIndividual($url, $url->url, $client);
      }
    }
  }

  /**
  * Parse results of an individual query
  */
  public function parseIndividual(Parse_URL $parse_url, $url, Client $client)
  {
    try{

      $crawler = $client->request('GET', $url);

      $crawler->filter('.item-link')->each(function ($node) use($parse_url) {

        $itemUri = $node->link()->getUri();
        $parseId = $parse_url->id;

        $items = DB::table('classifieds')->where('url', $itemUri)->where('parse_url_id', $parseId)->count();

        if($items == 0){
          $classified = Classified::create([
             'url' => $node->link()->getUri(),
             'name' => $node->filter('.item-title')->text(),
             'img' => $node->filter('.image > img')->image()->getUri(),
           ]);

          $classifiedId = $classified->id;

           //for all users that has this parse_urls
           $users =  $parse_url->users()->get();

           $newClassifieds = [];
           foreach($users as $user){
            $newClassified = new New_classified();
            $newClassified->user_id = $user->id;
            $newClassified->parse_url_id= $parseId;
            $newClassified->classified_id = $classifiedId;

            $newClassified->created_at = new \DateTime();
            $newClassified->updated_at = $newClassified->created_at;

            $newClassifieds[] = $newClassified->attributesToArray();
           }

           New_classified::insert($newClassifieds);
           $parse_url->classifieds()->save($classified);


           echo("Inserted" . $itemUri);
           //notify all users that record has been inserted, via email? Create another table to store notification records?


        }


      });


      $link =  $crawler->filter('.next-page-block > a');

      if($link->count() > 0){
        $this->parseIndividual($parse_url, $link->first()->link()->getUri(), $client);
      }

    }catch(\Guzzle\Http\Exception\ConnectException $e){
        dd($e);
    }catch (\Exception $e) {
        dd($e);

    }
  }


}
