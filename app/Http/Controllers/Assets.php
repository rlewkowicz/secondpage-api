<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Cassandra;


class Assets extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // private function writeStream($resultobject, $session) {
    //
    // }

    // public function index($slug)
    // {
    //   $cluster   = Cassandra::cluster()
    //              ->withContactPoints(env('CASSANDRA_HOST'))// connects to localhost by default
    //              ->build();
    //   $keyspace  = env('CASSANDRA_KEYSPACE');
    //   $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace
    //   $objectstatement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
    //       'SELECT object_id, chunk_count FROM image WHERE image_url=\''.(string)$slug.'\''
    //   );
    //   $futureobject    = $session->executeAsync($objectstatement);  // fully asynchronous and easy parallel execution
    //   $resultobject    = $futureobject->get();
    //
    //   $response = new StreamedResponse();
    //   $callback = function ($resultobject) use ($session){
    //     // foreach (range(1, (int)$resultobject[0]['chunk_count']) as $number) {
    //     //     return $number;
    //     // }
    //     $chunkstatement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
    //         'SELECT data, chunk_size FROM blob_chunk WHERE object_id=\''.$resultobject[0]['object_id'].'\''
    //     );
    //     $out = fopen('php://output', 'w');
    //     $resultchunk = $session->execute($chunkstatement);  // fully asynchronous and easy parallel execution
    //     fwrite($out, $resultchunk[0]['data'], $resultchunk[0]['chunk_size']);
    //     fclose($out);
    //   }
    //   $response->setCallback($callback($resultobject));
    //   return $response;
    //
    //
    //
    //
    //
    // }

    /**
     * Show the form for creating a new resource.
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
      $objectstatement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
          'SELECT object_id, chunk_count, content_type FROM image WHERE image_url=\''.(string)$slug.'\''
      );
      $futureobject    = $session->executeAsync($objectstatement);  // fully asynchronous and easy parallel execution
      $resultobject    = $futureobject->get();

      $response = new StreamedResponse(function() use($slug, $session, $resultobject){
          // Open output stream
          $handle = fopen('php://output', 'w');
          //


          foreach (range(1, (int)$resultobject[0]['chunk_count']) as $number) {
            $chunkstatement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
                'SELECT data, chunk_size FROM blob_chunk WHERE object_id=\''.$resultobject[0]['object_id'].'\' AND chunk_id='.$number
            );
            // dd($resultobject[0]['object_id']);
            $resultchunk = $session->execute($chunkstatement);
            $data = hex2bin(substr($resultchunk[0]['data'],2));
            fwrite($handle, $data);
          }

          fclose($handle);
      }
      , 200, [
                'Content-Type' => $resultobject[0]['content_type'],
                // 'Content-Disposition' => 'attachment; filename="export.jpeg"',
            ]
          );

      return $response;

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
