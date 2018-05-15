<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cassandra;

class CassandraTest extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $cluster   = Cassandra::cluster()
                 ->withContactPoints(env('CASSANDRA_HOST'))// connects to localhost by default
                 ->build();
      $keyspace  = env('CASSANDRA_KEYSPACE');
      $session   = $cluster->connect($keyspace);        // create session, optionally scoped to a keyspace
      $statement = new Cassandra\SimpleStatement(       // also supports prepared and batch statements
          'SELECT html, assets FROM article limit 1'
      );
      $future    = $session->executeAsync($statement);  // fully asynchronous and easy parallel execution
      $result    = $future->get();                      // wait for the result, with an optional timeout

      foreach ($result as $row) {
          return $row['html'];
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
