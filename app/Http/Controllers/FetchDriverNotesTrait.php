<?php
/**
 * Created by PhpStorm.
 * User: alfre
 * Date: 11/15/2018
 * Time: 4:51 PM
 */

namespace App\Http\Controllers;

use App\DriverLog;
use App\Note;
use App\DriverNote;
use App\LineNote;
use App\Nova\SaleInvoice;
use App\SaleInvoiceNote;
trait FetchDriverNotesTrait
{
    public function fetchDriverNotes($log_id)
    {
        $notes = Note::all();
        $driver_notes = DriverNote::orderby('id', 'desc')
            ->where('driver_log_id', '=', $log_id)
            ->limit(1)->get();
        $seleted_note_ids = [];
        $selected_notes = [];
        if ($driver_notes->count()) {
            foreach ($driver_notes as $dn) {
                $seleted_note_ids = explode(',', $dn->selected);
            };
            foreach ($notes as $note) {
                if (in_array($note->id, $seleted_note_ids))
                    $selected_notes[] = $note->note;
            }
        }
		return $selected_notes;
    }
    public function fetchLineNotes($sales_line_id)
    {
        $line_notes = LineNote::all();
        $driverlogs = SaleInvoice::find($sales_line_id);
$selected_notes = $driverlogs->order_lines->
        $seleted_note_ids = [];
        $seleted_notes = [];
        if ($line_notes->count()) {
            foreach ($saleinvoice_notes as $dn) {
                $seleted_note_ids = explode(',', $dn->selected);
            };
        //    dd($seleted_note_ids);
            foreach ($line_notes as $line_note) {
                if (in_array($line_note->id, $seleted_note_ids))
                    $selected_notes[] = $line_note->note;
            }
        }

      //  dd($selected_notes);

        return $selected_notes;
    }

}