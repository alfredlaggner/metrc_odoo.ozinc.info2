<?php

namespace App\Http\Controllers;

use App\Business;
use App\LicenseNumber;
use App\MetrcItem;
use App\MetrcTag;
use App\MetrcUom;
use App\MetrcOrderline;
use Carbon\Carbon;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;
use App\MetrcPackage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/*use GuzzleHttp\Psr7\Request;*/

use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Constraint\IsFalse;

class MetrcPackageController extends Controller
{
    public function update_tag(Request $request)
    {

        $tag = $request->get('tag');
        $id = $request->get('id');

        MetrcOrderline::updateOrCreate(
            [
                'id' => $request->get('id')
            ],
            [
                'metrc_package_created' => $request->get('tag'),
            ]
        );

        return response()->json(['success' => true, 'tag' => $tag, 'id' => $id]);
    }

    public function make_package(Request $request)
    {
        //  dd($request);

        $id = $request->get('id');
        $new_package = $request->get('metrc_package_created');
        $name = $request->get("name");
        $quantity = $request->get("quantity");
        $uom = $request->get("uom");
        $line_number = $request->get("line_number");
        $sale_order_full = $request->get("sale_order_full");
        $source_packages = MetrcPackage::search($name)->get();

        $tags = MetrcTag::orderby('tag')->get();
        $uoms = MetrcUom::all();

        $orderline = MetrcOrderline::where('id', $id)->first();
        $scanned_tag = $orderline->metrc_package_created;
        $package_or_edit = "P";
        $tag = '';
        return view('metrc.make_package', compact('id', 'name', 'scanned_tag', 'tag', 'quantity', 'uom', 'uoms', 'source_packages', 'new_package', 'line_number', 'sale_order_full', 'tags'));
    }

    public function edit_orderline(Request $request)
    {
        //  dd($request);

        $id = $request->get('id');
        $new_package = $request->get('metrc_package_created');
        $name = $request->get("name");
        $quantity = $request->get("quantity");
        $uom = $request->get("uom");
        $line_number = $request->get("line_number");
        $sale_order_full = $request->get("sale_order_full");
        $source_packages = MetrcPackage::search($name)->get();
        $tags = MetrcTag::orderby('tag')->get();
        $uoms = MetrcUom::all();

        $orderline = MetrcOrderline::where('id', $id)->first();
        $scanned_tag = $orderline->metrc_package_created;
        $package_or_edit = "P";
        $tag = '';
        return view('metrc.edit_orderline', compact('id', 'name', 'scanned_tag', 'tag', 'quantity', 'uom', 'uoms', 'source_packages', 'new_package', 'line_number', 'sale_order_full', 'tags'));
    }

    public function update_orderline(Request $request)
    {
        $id = $request->get('id');
        $action = $request->get('action');
        $line_number = $request->get('line_number');
        $line_id = $request->get('id');

        if ($action == 'save') {
            MetrcOrderline::updateOrCreate(
                [
                    'id' => $id,
                ],
                [
                    'metrc_package_created' => $request->get('scanned_tag'),
                    'metrc_quantity' => $request->get('quantity'),
                    'metrc_uom' => $request->get('uom'),
                ]
            );
            return redirect()->to(route('make_package_return', ['id' => $line_id, 'error_message' => 'Orderline ' . $line_number . ' updated']) . "#error_message");

        } elseif ($action == 'remove') {
            $this->removeOrderLine($request);
            return redirect()->to(route('make_package_return', ['id' => $line_id, 'error_message' => 'Orderline ' . $line_number . ' deleted']) . "#error_message");
        } elseif ($action == 'discard') {
            return redirect()->to(route('make_package_return', ['id' => $line_id, 'error_message' => 'Orderline ' . $line_number . ' not changed']) . "#error_message");
        }


    }

    public function create_package(Request $request)
    {
        $action = $request->get('action');
        $line_number = $request->get('line_number');
        $line_id = $request->get('id');
        if ($action == 'save') {
            $this->updateOrderLine($request);
            return redirect()->to(route('make_package_return', ['id' => $line_id, 'error_message' => 'Orderline ' . $line_number . ' saved']) . "#error_message");

        } elseif ($action == 'remove') {
            $this->removeOrderLine($request);
            return redirect()->to(route('make_package_return', ['id' => $line_id, 'error_message' => 'Orderline ' . $line_number . ' deleted']) . "#error_message");
        } elseif ($action == 'discard') {
            return redirect()->to(route('make_package_return', ['id' => $line_id, 'error_message' => 'Orderline ' . $line_number . ' not changed']) . "#error_message");
        }

        $new_package = $request->validate(['new_package' => 'required']);
        $line_number = $request->get('line_number');
        //  $scanned_tag = $request->get('scanned_tag');
        $source_package = $request->validate(['source_package' => 'required']);
        $quantity = $request->validate(['quantity' => 'required']);
        $uom = $request->validate(['uom' => 'required']);
        $item = $request->get('item');
        $id = $request->get('id');
        $package = MetrcPackage::where('tag', $source_package)->first();

        $this->updateOrderLine($request);

        $item = $package->item;
        $new_uom = 'Grams';
        $actual_date = $today = Carbon::today()->toDateString();
        /*        echo "Date: "  .  $actual_date . "<br>";;
                echo "Source Package: " . $source_package . "<br>";
                echo "New Package: " . $new_package . "<br>";
                echo "Item Name: " . $item . "<br>";
                echo "Quantity: " . $quantity . "<br>";
                echo "Unit of Measure: " . $uom;*/
        //   $item = "Royal Tree Hybrid Flower Granimals 3.5g";
        //     dd($package);
        $packages_create = [
            'Tag' => $new_package['new_package'],
            "Quantity" => $quantity['quantity'],
            "UnitOfMeasure" => $uom['uom'],
            "ActualDate" => $actual_date,
            "Note" => "API",
            "Item" => $item,
            "IsProductionBatch" => false,
            "ProductionBatchNumber" => null,
            "IsDonation" => false,
            "ProductRequiresRemediation" => false,
            "UseSameItem" => false,
            "Ingredients" =>
                [[
                    "Package" => $source_package['source_package'],
                    "Quantity" => $quantity['quantity'],
                    "UnitOfMeasure" => $uom['uom'],
                ]]];
        //  dd($packages_create);
        $data1 = json_encode($packages_create, JSON_PRETTY_PRINT);
        //    dd($data1);
        $data2 = "[" . $data1 . "]";
        $data = ['body' => $data2];
        //   dd($data);
        $client = new Client([
            'timeout' => 60.0,
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'auth' => ['6Qql8-KoIB7VuGFPDeUkZ2JnPLiAwzolmI4p-e2yM3w29mIz', 'myIsiMUHP3dD6qO0Yxpmfybn9E-YawVTZRoSGyelxk4M3EqM'], // oz
            'request.options' => ['exceptions' => true]]);

        $headers = [
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'auth' => ['6Qql8-KoIB7VuGFPDeUkZ2JnPLiAwzolmI4p-e2yM3w29mIz', 'myIsiMUHP3dD6qO0Yxpmfybn9E-YawVTZRoSGyelxk4M3EqM'], // oz
        ];

        $license = 'C11-0000224-LIC';   //oz
        $message = '';
        $messages = '';
        $error_message = [];
        try {
            $response = $client->post('https://api-ca.metrc.com/packages/v1/create?licenseNumber=' . $license, $data);
            $rsp_body = $response->getBody()->getContents();
            if ($rsp_body == '') {
                $message = 'Package created';

                //     dd($new_package . '/' . $saleorder_number . '/'. $line_number);

                MetrcTag::where('tag', '=', $new_package['new_package'])->update(['is_used' => 1, 'used_at' => Carbon::now()]);

                return redirect()->to(route('make_package_return', [$id, $message]) . "#error_message");
            } else {
                $message = "Other error";
            }

        } catch (GuzzleException $error) {
            $response = $error->getResponse();
            $response_info = json_decode($response->getBody()->getContents(), true);
            //    dd(is_array($response_info));
            $message = $response_info;
            //    dd($message);
            if (is_array($response_info)) {
                if (count($response_info) == 0) {
                    $message = $response_info[0]["message"];
                    array_push($error_message, [$message]);
                } else {
                    //     dd($response_info);
                    for ($i = 0; $i < sizeof($response_info); $i++) {
                        $message = $response_info[$i]["message"];
                        array_push($error_message, [$message]);
                    }
                }
            }
            for ($i = 0; $i < count($error_message); $i++) {
                $messages = $messages . $error_message[$i][0] . '|';
            }
            $new_package = $new_package['new_package'];
            /*            echo $line_number . "<br>";
                        echo $messages . "<br>";
                    //    dd("xxx");
                        dd($messages);*/
            return redirect()->to(route('make_package_return', [$id, $messages]) . "#error_message");

        }

        //    dd($message);
    }

    public function updateOrderLine($request)
    {

        MetrcOrderline::updateOrCreate(
            [
                'id' => $request->get('id')
            ],
            [
                'metrc_tag' => $request->get('source_package'),
                'metrc_package_created' => $request->get('new_package'),
                'metrc_quantity' => $request->get('quantity'),
                'metrc_uom' => $request->get('uom'),
            ]
        );
    }

    public function removeOrderLine($request)
    {
        $id = intval($request->get('id'));
        $ol = MetrcOrderline::where('id', $id)->delete();
    }

    public function search_package()
    {
        $client = new Client([
            'base_uri' => "https://api-ca.metrc.com",
            'timeout' => 2.0,
        ]);
        $headers = [
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'auth' => ['4w9TaWS-WuFYilK5r91lmKC2LwuGHr0q0nkvYM3axVo1z1Fo', 'IgLHZR3M-5DjNPsXinfVZJ7PWQfm31CxGD8aFC8dZMbzJP5i'],
        ];

        $license = 'C11-0000224-LIC';
        $new_package = '1A4060300003C35000016859';

        $response = $client->request('GET', '/packages/v1/' . $new_package . '?licenseNumber=' . $license, $headers);
        $items = json_decode($response->getBody()->getContents());
        dd(($items));
    }

    public function edit_sales_line(Request $request, $id)
    {
        MetrcOrderline::find($id)->first()->update([
//
        ]);

    }
}
