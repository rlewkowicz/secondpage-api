<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cassandra;
use DOMDocument;
use DOMXPath;

class Articles extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
      $cluster   = Cassandra::cluster()
                 ->withContactPoints(env('CASSANDRA_HOST'))// connects to localhost by default
                 ->build();
      $keyspace  = env('CASSANDRA_KEYSPACE');
      $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace
      $statement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
          'SELECT html FROM article where url=\''.$slug.'\''
      );
      $future    = $session->executeAsync($statement);  // fully asynchronous and easy parallel execution
      $result    = $future->get();

      $rows = [];

      foreach ($result as $row) {
        // return $row['html'];
        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $row['html']);
        libxml_use_internal_errors(false);
        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query("//@src");
        foreach($nodes as $node) {
          if(substr( $node->value, 0, 4 ) === "http"){
            $node->value = env('APP_URL').'/assets/'.urlencode($node->value);
            if (strpos($node->value, '.js') !== false){
                $node->value = 'localhost-broken';
            }
          }
        }
        $nodes = $xpath->query("//@itemid");
        foreach($nodes as $node) {
          if(substr( $node->value, 0, 4 ) === "http"){
            $node->value = env('APP_URL').'/assets/'.urlencode($node->value);
          }
        }
        $nodes = $xpath->query("//@srcset");
        foreach($nodes as $node) {
          $urls = explode(",",$node->value);
          foreach($urls as &$url) {
            $url = explode(" ",$url);
            if(empty($url[0])){
              unset($url[0]);
              $url=array_values($url);
            }
            $url[0] = env('APP_URL').'/assets/'.urlencode($url[0]);
            $url = implode(" ",$url);
            unset($url);
          }
          $node->value = implode(", ", $urls);
        }
        return $dom->saveHTML();
      }
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
