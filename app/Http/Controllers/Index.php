<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cassandra;


class Index extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index($slug = "None")
     {
        $content=file_get_contents("http://".getenv('CONSUL_HOST').":8500/v1/kv/categories");
        $data=get_object_vars(json_decode($content)[0]);
        $categories=(array)json_decode(base64_decode($data["Value"], true));

        $catarr =[];

        foreach ($categories as $category => $publication){
          array_push($catarr, $category);
        }


        $cluster   = Cassandra::cluster()
                  ->withContactPoints(env('CASSANDRA_HOST'))// connects to localhost by default
                  ->build();
        $keyspace  = env('CASSANDRA_KEYSPACE');
        $session   = $cluster->connect($keyspace);
        if(!in_array($slug, $catarr)){
         $statement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
             "SELECT url, topimage, category, toTimestamp(timeuuid), publication, title, articletext, summary FROM article LIMIT 1000"
         );
        } else {
         $statement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
             "SELECT url, topimage, category, toTimestamp(timeuuid), publication, title, articletext, summary FROM article_category WHERE category='".$slug."' LIMIT 1000"
         );
        }
        $future    = $session->executeAsync($statement);  // fully asynchronous and easy parallel execution
        $result    = $future->get();

        $rows = [];

        foreach ($result as $row) {
           $row['orignal_url']=$row['url'];
           $row['url']=env('APP_URL').'/articles/'.urlencode($row['url']);
           $row['topimage']=env('APP_URL').'/assets/'.urlencode($row['topimage']);
           array_push ( $rows, $row );
        }

 return $rows;
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
