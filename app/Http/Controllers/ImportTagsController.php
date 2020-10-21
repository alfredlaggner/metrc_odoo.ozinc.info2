<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\MetrcTag;
use Maatwebsite\Excel\Facades\Excel;
use \App\Imports\MetrcTagsCollection;
use \App\Imports\ImportMetrcPacket;
use Illuminate\Support\Facades\DB;
use \App\MetrcSourcePacket;
use \App\MetrcPackage;

class ImportTagsController extends Controller
{
    public function index()
    {
        $tags = MetrcTag::limit(50)->orderBy('updated_at', 'desc')->get();
        return view('imports.tag_import', compact('tags'));
    }

    public function import_tags(Request $request)
    {
        $request->validate(['import_file' => 'required']);

        $path1 = $request->file('import_file')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        DB::table('metrc_tags')->delete();
        Excel::import(new MetrcTagsCollection, $path);

        return redirect('/')->with('status', 'Tags imported!');
    }

    public function import_packets(Request $request)
    {
        $request->validate(['import_file' => 'required']);
        $path1 = $request->file('import_file')->store('temp');
        $path = storage_path('app') . '/' . $path1;
        Excel::import(new ImportMetrcPacket, $path);
        $msps = MetrcSourcePacket::get();
        foreach ($msps as $msp) {
            MetrcPackage::where('tag',$msp->label)
                ->update(['source_tag' => $msp->source_packet]);
        }
        return redirect('/')->with('status', 'Packets imported!');
    }

    public function source_packets()
    {
        Excel::import(new ImportMetrcPacket, 'metrc_source_packets.xlsx');
    }


}
