<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LetterController extends Controller
{
    //
    public function leaveRequestShow($id)
    {
        $leaveRequest = LeaveRequest::find($id);
        $path = public_path() . '/images/sne.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $pdf = Pdf::loadView('pdf.leave_request', [
            'leaveRequest' => $leaveRequest,
            'image' => $image,
        ]);
        return $pdf->stream();
        //return view('pdf.leave_request', compact('leaveRequest', 'image'));
    }
}
