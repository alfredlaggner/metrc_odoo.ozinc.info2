<?php

namespace App\Http\Controllers;

use App\Business;
use App\LicenseNumber;
use App\MetrcCategoryToUom;
use App\MetrcItem;
use App\MetrcTag;
use App\MetrcUom;
use App\MetrcStrain;
use App\MetrcOrderline;
use App\Product;
use App\ProductProduct;
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
        // dd($name);
        $quantity = $request->get("quantity");
        $uom = $request->get("uom");
        $line_number = $request->get("line_number");
        $sale_order_full = $request->get("sale_order_full");
        $source_packages = MetrcPackage::search($name)->get();
//dd($source_packages->toArray());
        $tags = MetrcTag::orderby('tag')
            ->where('is_used', false)
            ->get();
        $strains = MetrcStrain::orderby('name')
            ->get();
        $uoms = MetrcUom::all();

        $orderline = MetrcOrderline::where('id', $id)->first();
        $scanned_tag = $orderline->metrc_package_created;
        $package_or_edit = "P";
        $tag = '';
        return view('metrc.make_package', compact('id', 'name', 'scanned_tag', 'tag', 'quantity', 'uom', 'uoms', 'strains', 'source_packages', 'new_package', 'line_number', 'sale_order_full', 'tags'));
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
        //    dd($request);
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
        $item = $request->get('item');
        $item_found = MetrcItem::where('name', 'like', $item)->first();
        if (!$item_found) {
            //  dd($item);
            $this->create_new_item($request);
        }

        //   dd("after item created");
        $new_package = $request->validate(['new_package' => 'required']);
        $line_number = $request->get('line_number');
        //  $scanned_tag = $request->get('scanned_tag');
        $source_package = $request->validate(['source_package' => 'required']);
        $source_uom = $request->get('source_uom');
        $source_quantity = $request->get('source_quantity');
        $quantity = $request->validate(['quantity' => 'required']);
        $uom = $request->validate(['uom' => 'required']);
        $id = $request->get('id');
        $package = MetrcPackage::where('tag', $source_package)->first();
        $this->updateOrderLine($request);
        $actual_date = $today = Carbon::today()->toDateString();
//$product = ProductProduct::where('ext_id' =)
        //    $item = $package->item;
        /*        $new_uom = 'Grams';
                echo "Date: " . $actual_date . "<br>";;
                echo "Item Name: " . $item . "<br>";
                echo "Quantity: " . $quantity . "<br>";
                echo "Source_Uom: " . $source_uom . "<br>";
                echo "Unit of Measure: " . $uom;
                dd($package);*/

        $quantity = $quantity['quantity'];
        $sp = MetrcPackage::where('tag', $source_package['source_package'])->first();
        $source_uom = $sp->uom;
        //    dd($source_uom);
        $tag = $sp->tag;
        $unit_size = 0;
        /*        echo $tag .'<br>';
                echo $source_uom;*/
        if ($source_uom == "Grams") {
            $orderline = MetrcOrderline::where('id', $line_id)->first();
            $unit_size = $orderline->unit_size;
            if ($unit_size > 1) {
                $source_quantity = $unit_size * $quantity;
            }
        }
        echo ($source_package['source_package']) . '<br>';
        echo $quantity . '<br>';
        echo $source_quantity;
/*        $this->update_source_package($source_package['source_package'],$source_quantity, $quantity);
dd( 'check' . $source_package['source_package']);*/

        //     echo $unit_size;
        $packages_create = [
            'Tag' => $new_package['new_package'],
            "Quantity" => $quantity,
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
                    "Quantity" => $source_quantity,
                    "UnitOfMeasure" => $source_uom
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
                } /*elseif (sizeof($response_info) == 1) {
                    //   dd($response_info);
                    $message = $response_info["Message"];
                    array_push($error_message, [$message]);
                }*/ else {
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
        $this->update_source_package($source_package['source_package'],$source_quantity);
    }

    public function update_source_package($source_package,$source_quantity, $quantity)
    {
        $sp = MetrcPackage::where('tag', $source_package)->update(['quantity' => $quantity - $source_quantity]);
    }

    public function create_new_item($request)
    {
//dd($request);

        $item = $request->get('item');
        $id = $request->get('id');
        $uom_long = $request->get('uom');


        $order_line = MetrcOrderline::where('id', $id)->first();
        $product = ProductProduct::where('ext_id', $order_line->product_id)->first();
        echo $order_line->product_id;
        $category = $product->category;
        /*        echo
                dd($category);*/
        $uom = MetrcCategoryToUom::where('category', $category)->first();
        $unit_of_measure = $uom->uom;

        $category_name = "";
        $short_uom = $product->uom;
        $unit_size = $product->unit_size;
        $quantity_type = "WeightBased";
        $type = "Buds";
        if ($category == "Flower") {
            $uom_long = "Grams";
            $type = "Buds";
            $quantity_type = "CountBased";
            $case_qty = $product->case_qty;
            //      dd(round($unit_size,2) == 3.50);
            if (round($unit_size, 2) == 3.50) {
                $category_name = "Flower (packaged eighth - each)";
            } elseif (round($unit_size) == 7.00) {
                $category_name = $category . " (packaged quarter - each)";
            }
            $item_create = ["ItemCategory" => $category_name,
                "Name" => $item,
                "Type" => $type,
                "QuantityType" => $quantity_type,
                "UnitOfMeasure" => $unit_of_measure,
                "UnitWeightUnitOfMeasure" => $uom_long,
                "UnitWeight" => $unit_size,
                "Strain" => $request->get('id'),
            ];

        } elseif ($category == "Concentrate") {
            $uom_long = "Grams";
            $type = "Buds";
            $quantity_type = "WeightBased";
            $case_qty = $product->case_qty;
            $category_name = "Flower (packaged eighth - each)";

            $item_create = ["ItemCategory" => $category_name,
                "Name" => $item,
                "Type" => $type,
                "QuantityType" => $quantity_type,
                "UnitOfMeasure" => $unit_of_measure,
                "UnitWeightUnitOfMeasure" => $uom_long,
                "UnitWeight" => $unit_size,
            ];

            //   dd("edible");


        } elseif ($category == "Edible") {
            $uom_long = "Grams";
            $type = "InfusedEdible";
            $quantity_type = "CountBased";
            $case_qty = $product->case_qty;
            $category_name = "Edible (weight - each)";

            $item_create = ["ItemCategory" => $category_name,
                "Name" => $item,
                "Type" => $type,
                "QuantityType" => $quantity_type,
                "UnitOfMeasure" => $unit_of_measure,
                "UnitWeightUnitOfMeasure" => $uom_long,
                "UnitWeight" => $unit_size,
            ];

        } elseif ($category == "Tincture") {
            $uom_long = "Milliliters";
            $type = "Concentrate";
            $quantity_type = "CountBased";
            $category_name = "Tincture (volume - each)";

            $item_create = ["ItemCategory" => $category_name,
                "Name" => $item,
                "Type" => $type,
                "QuantityType" => $quantity_type,
                "UnitOfMeasure" => $unit_of_measure,
                "UnitWeightUnitOfMeasure" => $uom_long,
                "UnitVolume" => $unit_size,
            ];


        } elseif ($category == "Vape") {
            $item_create = [];
            $uom_long = "Milliliters";
            $type = "Concentrate";
            $quantity_type = "CountBased";
            $category_name = "Vape Cartridge (volume - each)";

            $item_create = ["ItemCategory" => $category_name,
                "Name" => $item,
                "Type" => $type,
                "QuantityType" => $quantity_type,
                "UnitOfMeasure" => $unit_of_measure,
                "UnitWeightUnitOfMeasure" => $uom_long,
                "UnitVolume" => $unit_size,
            ];

        } elseif ($category == "Topical") {
            $item_create = [];
            $uom_long = "Milliliters";
            $type = "Concentrate";
            $quantity_type = "InfusedNonEdible";
            $category_name = "Topical (weight - each)";

            $item_create = ["ItemCategory" => $category_name,
                "Name" => $item,
                "Type" => $type,
                "QuantityType" => $quantity_type,
                "UnitOfMeasure" => $unit_of_measure,
                "UnitWeightUnitOfMeasure" => $uom_long,
                "UnitVolume" => $unit_size,
            ];

        }

        /*            echo "Product: " . $product->name . '<br>';
                    echo "Category: " . $category_name . '<br>';
                    echo "Quantity_type: " . $quantity_type . '<br>';
                    echo "Short Uom: " . $short_uom . '<br>';
                    echo "Uom : " . $unit_of_measure . '<br>';
                    echo "Size: " . round($unit_size, 2) . '<br>';*/


        $data1 = json_encode($item_create, JSON_PRETTY_PRINT);
        $data2 = "[" . $data1 . "]";
        $data = ['body' => $data2];
//dd($data);
        $client = new Client(['timeout' => 60.0,
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'auth' => ['6Qql8-KoIB7VuGFPDeUkZ2JnPLiAwzolmI4p-e2yM3w29mIz', 'myIsiMUHP3dD6qO0Yxpmfybn9E-YawVTZRoSGyelxk4M3EqM'], // oz
            'request.options' => ['exceptions' => true]]);

        $headers = ['headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'auth' => ['6Qql8-KoIB7VuGFPDeUkZ2JnPLiAwzolmI4p-e2yM3w29mIz', 'myIsiMUHP3dD6qO0Yxpmfybn9E-YawVTZRoSGyelxk4M3EqM'], // oz];
        ];

        $license = 'C11-0000224-LIC';   //oz
        $message = '';
        $messages = '';
        $error_message = [];
        try {
            $response = $client->post('https://api-ca.metrc.com/items/v1/create?licenseNumber=' . $license, $data);
            $rsp_body = $response->getBody()->getContents();
            if ($rsp_body == '') {
                $message = 'Item ' . $item . ' created';
//dd($message);
                //   MetrcTag::where('tag', '=', $new_package['new_package'])->update(['is_used' => 1, 'used_at' => Carbon::now()]);

                return redirect()->to(route('make_package_return', [$id, $message]) . "#error_message");
            } else {
                $message = "Other error";
            }

        } catch
        (GuzzleException $error) {
            $response = $error->getResponse();
            $response_info = json_decode($response->getBody()->getContents(), true);
            //    dd(is_array($response_info));
            $message = $response_info;
            //   dd($message);
            if (is_array($response_info)) {
                if (count($response_info) == 0) {
                    $message = $response_info[0]["message"];
                    array_push($error_message, [$message]);
                } /*elseif (sizeof($response_info) == 1) {
                    //   dd($response_info);
                    $message = $response_info["Message"];
                    array_push($error_message, [$message]);
                }*/ else {
                    for ($i = 0; $i < sizeof($response_info); $i++) {
                        $message = $response_info[$i]["message"];
                        array_push($error_message, [$message]);
                    }
                }
            }
            for ($i = 0; $i < count($error_message); $i++) {
                $messages = $messages . $error_message[$i][0] . '|';
            }
            //                          dd($messages);
            return redirect()->to(route('make_package_return', [$id, $messages]) . "#error_message");
        }
        //         dd("success");
    }

    public
    function updateOrderLine($request)
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

    public
    function removeOrderLine($request)
    {
        $id = intval($request->get('id'));
        $ol = MetrcOrderline::where('id', $id)->delete();
    }

    public
    function search_package()
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

    public
    function edit_sales_line(Request $request, $id)
    {
        MetrcOrderline::find($id)->first()->update([
//
        ]);

    }
}
