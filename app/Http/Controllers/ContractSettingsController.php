<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\ContractSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class ContractSettingsController extends Controller
{
    public function index()
    {
        $path = public_path() . '/images/sne.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $contractSetting = ContractSetting::first();
        $data = [
            'gaji_pokok' => 5000000,
            'tunjangan_jabatan' => 0,
            'tunjangan_bahasa' => 0,
            'tunjangan_keahlian' => 1000000,
            'tunjangan_transportasi' => 1000000,
            'tunjangan_lainnya' => 0,
            'total_gaji' => 7000000,
            'contract_number' => '2093/DSI/HRD/V/2026',
            'employee_name' => 'Anang Egga',
            'employee_position' => 'Software Engineer',
            'employee_address' => 'Jl. Contoh No. 123',
            'start_date' => '2022-01-01',
            'end_date' => '2022-12-31',
            'department' => 'Technology',
        ];
        $table_salaries = "
        <table style='width: 70%; font-size: 14px; margin: 5px auto;' class='bordered-table'>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Gaji Pokok</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['gaji_pokok']) . "</td>
            </tr>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Jabatan</td>
                <td style='width: 50%;' class='text-end'>" . number_format($data['tunjangan_jabatan']) . "</td>
            </tr>
            
            " . ($data['tunjangan_bahasa'] != 0 ? "<tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Bahasa</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_bahasa']) . "</td>
            </tr>" : '') . "
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Keahlian</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_keahlian']) . "</td>
            </tr>         
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Transportasi</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_transportasi']) . "</td>
            </tr>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Lainnya</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_lainnya']) . "</td>
            </tr>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-bold'>Total Gaji</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end text-bold'>" . number_format($data['total_gaji']) . "</td>
            </tr>
        </table>
        ";
        $contractSetting->contract_template = str_replace('{payment_detail}', $table_salaries, $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{employee_name}', $data['employee_name'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{employee_position}', $data['employee_position'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{employee_address}', $data['employee_address'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{department}', $data['department'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace(
            '{start_date}',
            Carbon::parse($data['start_date'])
                ->locale('id')
                ->isoFormat('DD MMMM YYYY'),
            $contractSetting->contract_template
        );
        $contractSetting->contract_template = str_replace(
            '{end_date}',
            Carbon::parse($data['end_date'])
                ->locale('id')
                ->isoFormat('DD MMMM YYYY'),
            $contractSetting->contract_template
        );
        $pdf = Pdf::loadView('pdf.preview-contract', [
            'contractSetting' => $contractSetting,
            'data' => $data,
            'image' => $image,
        ]);
        return $pdf->stream();
    }
    public function show($id)
    {
        $path = public_path() . '/images/sne.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $image = 'data:image/' . $type . ';base64,' . base64_encode($data);
        $contractSetting = ContractSetting::first();
        $data = Contract::find($id)->load('employee')->toArray();
        //dd($data);
        $table_salaries = "
        <table style='width: 70%; font-size: 14px; margin: 5px auto;' class='bordered-table'>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Gaji Pokok</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['gaji_pokok']) . "</td>
            </tr>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Jabatan</td>
                <td style='width: 50%;' class='text-end'>" . number_format($data['tunjangan_jabatan']) . "</td>
            </tr>
            
            " . ($data['tunjangan_bahasa'] != 0 ? "<tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Bahasa</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_bahasa']) . "</td>
            </tr>" : '') . "
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Keahlian</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_keahlian']) . "</td>
            </tr>         
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Transportasi</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_transportasi']) . "</td>
            </tr>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;'>Tunjangan Lainnya</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end'>" . number_format($data['tunjangan_lainnya']) . "</td>
            </tr>
            <tr>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-bold'>Total Gaji</td>
                <td style='width: 50%; padding-left: 10px; padding-right: 10px;' class='text-end text-bold'>" . number_format($data['total_gaji']) . "</td>
            </tr>
        </table>
        ";
        $contractSetting->contract_template = str_replace('{payment_detail}', $table_salaries, $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{employee_name}', $data['employee']['name'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{employee_position}', $data['snapshot_metadata']['position'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{employee_address}', $data['employee']['address'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace('{department}', $data['snapshot_metadata']['department'], $contractSetting->contract_template);
        $contractSetting->contract_template = str_replace(
            '{start_date}',
            Carbon::parse($data['start_date'])
                ->locale('id')
                ->isoFormat('DD MMMM YYYY'),
            $contractSetting->contract_template
        );
        $contractSetting->contract_template = str_replace(
            '{end_date}',
            Carbon::parse($data['end_date'])
                ->locale('id')
                ->isoFormat('DD MMMM YYYY'),
            $contractSetting->contract_template
        );
        $pdf = Pdf::loadView('pdf.contract', [
            'contractSetting' => $contractSetting,
            'data' => $data,
            'image' => $image,
        ]);
        return $pdf->stream();
    }
}
