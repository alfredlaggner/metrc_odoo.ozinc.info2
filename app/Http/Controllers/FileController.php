<?php

namespace App\ Http\ Controllers;

use Illuminate\Support\Facades\Artisan;
use App\LicenseNumber;
use App\MetrcPackage;
use App\MetrcPlannedRoute;
use App\MetrcSalesOrder;
use App\MetrcOrderline;
use App\MetrcTmpSalesOrder;
use App\SalesOrder;
use Illuminate\ Http\ Request;
use App\ Http\ Controllers\ Controller;
use Illuminate\ Database\ Eloquent\ Model;
use Illuminate\ Support\ Facades\ Store;
use Illuminate\ Support\ Facades\ Storage;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\ Product;
use App\ SaleInvoice;
use App\ Customer;
use App\ Unit;
use App\ Contact;
use App\ Business;
use App\ Driver;
use App\ Vehicle;
use App\ User;
use App\ DriverLog;
use View;
use File;
use App\Mail\DriverlogUpdates;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Session;

class FileController extends Controller
{

    public
    function __construct()
    {
        $this->middleware('auth');
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    public
    function info(Request $request)
    {
        return view('directions');
    }

    function Start(Request $request)
    {
        return view('get_saleorder');
    }

    //  public function get_order($id = 0, $error_message = [])
    public function get_order(Request $request, $id = 0, $error_message = [])
    {
        //  dd("xxx");
        if ($id) {
            $metrc_order_line = MetrcOrderline::first();
    //        dd($metrc_order_line);
            $sale_order_full = $metrc_order_line->invoice_number;
            $order_id = $metrc_order_line->order_id;
            $saleorder_number = $metrc_order_line->order_id;
         //   dd($sale_order_full);

            $so = SalesOrder::where('sales_order', $sale_order_full)->first();
            //    $this->updateOrderLines($return_tag, $return_line_number);
        } else {
            $saleorder_number = $request->get('saleorder_number');

       //     abort_if(!$saleorder_number, 403, 'You must enter a sales order number');
            if (!$saleorder_number) return redirect(route('start'))->with('status', 'You must enter a sales order number');

            $so = MetrcSalesOrder::firstOrCreate(['saved_sales_order' => $saleorder_number]);
            $validatedData = $request->validate(['saleorder_number' => 'required|numeric']);
            $sale_order = explode(" ", $validatedData['saleorder_number']);
            $sale_order_full = "SO" . $sale_order[0];
            $order_id = intval($saleorder_number);
            $so = $this->import_salesorder("SO" . $order_id);
         //   abort_if(!$so, 403, 'Order not found. Try a different order number');
            if (!$so) return redirect(route('start'))->with('status', 'Order not found');

            $this->importOrderLines($so->ext_id);
        }

        $sales_lines = MetrcOrderline::get();
//dd($sales_lines);
        //    $so = SaleInvoice::where('order_id', $saleorder_number)->first();

        if ($so) {
            $customer_id = $so->customer_id;
            $plr = MetrcPlannedRoute::where('customer_id', $customer_id)->first();
            if ($plr) {
                $planned_route = $plr->planned_route;
                $driver_id = $plr->driver_id;
                $vehicle_id = $plr->vehicle_id;
            } else {
                $planned_route =  $sale_order_full . ' ' . $so->customer->name . "\r\n\r\n" . 'Fastest route according to legal requirements.';
                $driver_id = 0;
                $vehicle_id = 0;
            }

        } else {
            $customer_id = 0;
            $planned_route = '';
        }
        //   dd($so);
        //      dd($so->customer->license);
        /*        $licenses = LicenseNumber::where('bcc_license', $so->customer->license)->get();
                $license_number = '';
                foreach ($licenses as $license) {
                    $license_number = $license->bcc_license;
                    $license_exp = $license->license_exp;
                }*/
        $license_number = $so->customer->license;
        $license_exp = $so->customer->license_expiration;

        $customer_name = $so->customer->name;
        $customer_street = $so->customer->street;
        $customer_city = $so->customer->city;
        $customer_zip = $so->customer->zip;
        $customer_longitude = $so->customer->longitude;
        $customer_latitude = $so->customer->latitude;
        $customer_address = $customer_street . ',' . $customer_city . "," . 'CA';
        //      dd($customer_name);
//            dd($license_number);
        //      abort_if(!$license_number, 403, $so->customer->license . " - License Number for this customer not found");

        date_default_timezone_set('America/Los_Angeles');
        $est_leave = date('Y-m-d H:i', strtotime("+ 1 hours"));
        $est_arrive = date('Y-m-d H:i', strtotime("+ 4 hours"));

        $old_driver = '';
        $old_vehicle = '';
        $old_so = '';
        if ($request->session()->exists('driver')) {
            $old_driver = $request->session()->get('driver');
        }

        if ($request->session()->exists('vehicle')) {
            $old_vehicle = $request->session()->get('vehicle');
        }
        if ($request->session()->exists('so')) {
            $old_so = $request->session()->get('sales_orders');
        }
        //      dd($sales_lines);
        $view_saleslines = [];
        $sales_line_counter = 1;
        foreach ($sales_lines as $sales_line) {
            /*            echo (int)$sales_line->unit_price;
                        echo (int)$sales_line->unit_price;*/
            $total = number_format((float)$sales_line->unit_price * (float)$sales_line->metrc_quantity, 2);
            /*
                        echo $return_tag . "<br>";
                        echo $sales_line_counter . "<br>";
                        echo $return_line_number . "<br>";*/


            /*            if ($return_tag and $sales_line_counter == $return_line_number) {
                            $tag = $return_tag;
                            //    dd($tag);
                        } else {
                            $tag = '';
                        }*/
            //      dd($sales_line);
            $line = [
                'id' => $sales_line->id,
                'sale_order_full' => $sale_order_full,
                'product_id' => $sales_line->product_id,
                'tag' => $sales_line->metrc_tag,
                'metrc_package_created' => $sales_line->metrc_package_created,
                'name' => $sales_line->name,
                'code' => $sales_line->code,
                'quantity' => $sales_line->metrc_quantity,
                'uom' => $sales_line->metrc_uom,
                'price' => $sales_line->unit_price,
                'total' => $total,
                'line_number' => $sales_line_counter++,
            ];
            array_push($view_saleslines, $line);
        }
        //    dd($view_saleslines);
        $line_count = count($view_saleslines);
        //    dd($view_saleslines);
        //    dd($view_saleslines);

        $to_view = [
            'old_driver' => $old_driver,
            'old_vehicle' => $old_vehicle,
            'driver_id' => $driver_id,
            'vehicle_id' => $vehicle_id,
            'drivers' => User::where('license', '!=', NULL)->get(),
            'vehicles' => Vehicle::all(),
            'sales_orders' => $old_so,
            'saleorder_number' => $saleorder_number,
            'sale_order_full' => $sale_order_full,
            'planned_route' => $planned_route,
            'customer_id' => $customer_id,
            'customer_name' => $customer_name,
            'customer_street' => $customer_street,
            'customer_city' => $customer_city,
            'customer_longitude' => $customer_longitude,
            'customer_latitude' => $customer_latitude,
            'customer_zip' => $customer_zip,
            'est_leave' => $est_leave,
            'est_arrive' => $est_arrive,
            'license_number' => $license_number,
            'license_exp' => $license_exp,
            'customer_address' => $customer_address,
            'line_count' => $line_count
        ];
        //   dd("x1");

        return view('metrc.manifest', compact('to_view', 'view_saleslines', 'error_message'));
    }

    private function import_salesorder($sales_order)
    {
        MetrcTmpSalesOrder::truncate();
      //  dd($sales_order);
        Artisan::call('odoo:salesorders', ['sales_order' => $sales_order]);
        $so = MetrcTmpSalesOrder::first();
        return ($so);
    }

    public function make_manifests(Request $request)
    {
        //      return redirect('do_test');

       //    dd($request);


        $driver = User::find($request->get('driver'));
        $vehicle = Vehicle::find($request->get('vehicle'));
        $customer_id = $request->get('customer_id');
        $planned_route = $request->get('planned_route');
        $est_leave = $request->get('est_leave');
        $est_arrive = $request->get('est_arrive');
        $sale_orders = $request->get('saleorder_number');
        $sale_order_full = $request->get('sale_order_full');
        $license_number = $request->get('license_number');
        $return_value = $request->get('return_value');
        $tags = $request->get('tag');
        $ids = $request->get('id');

        for ($i = 0; $i < count($ids); $i++) {
            if ($tags[$i]) {
                MetrcOrderline::where('id', $ids[$i])->update([
                    'metrc_package_created' => $tags[$i],
                    //       'metrc_uom' => 'Each',
                ]);
            }
        }

        if ($return_value == 'abort') {
            return redirect("/");
        }

        MetrcPlannedRoute:: updateOrCreate(
            ['customer_id' => $customer_id],
            [
                'planned_route' => $planned_route,
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
            ]
        );


        session(['driver' => $request->get('user')]);
        session(['vehicle' => $request->get('vehicle')]);

        $sales_lines = MetrcOrderline::get();


        $business = Business::first();
// check for valid license number
        $metrc_sales_lines = $this->make_metrc_sales_lines($sales_lines);
//dd($metrc_sales_lines);
        $licenses = LicenseNumber::where('bcc_license', $license_number)->get();
        /** @var TYPE_NAME $bcc_license */
        $bcc_license = '';
        foreach ($licenses as $license) {
            $bcc_license = $license->bcc_license;
        }
        $data = [
            "Name" => "$sale_order_full",
            "TransporterFacilityLicenseNumber" => $business->adult_license_number,
            "Destinations" => [[
                "RecipientLicenseNumber" => $license_number,
                "PlannedRoute" => $planned_route,
                "EstimatedDepartureDateTime" => $est_leave,   // "2018-03-06T09:15:00.000",
                "EstimatedArrivalDateTime" => $est_arrive,  //"2018-03-06T21:00:00.000",
                "TransferTypeName" => "Wholesale Manifest",
                "Transporters" => [[
                    "TransporterFacilityLicenseNumber" => "C11-0000224-LIC",
                    "DriverOccupationalLicenseNumber" => "50",
                    "DriverName" => $driver->first_name . ' ' . $driver->last_name,
                    "DriverLicenseNumber" => $driver->license,
                    "PhoneNumberForQuestions" => $business->phone,
                    "VehicleMake" => $vehicle->make,
                    "VehicleModel" => $vehicle->model,
                    "VehicleLicensePlateNumber" => $vehicle->plate
                ]],
                "Packages" => $metrc_sales_lines
            ]]
        ];
        $data1 = json_encode($data, JSON_PRETTY_PRINT);

//dd($data1);
        $data2 = "[" . $data1 . "]";
        $data = ['body' => $data2];
//    dd($data);
        $client = new Client([
            'timeout' => 5.0,
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
            'auth' => ['6Qql8-KoIB7VuGFPDeUkZ2JnPLiAwzolmI4p-e2yM3w29mIz', 'myIsiMUHP3dD6qO0Yxpmfybn9E-YawVTZRoSGyelxk4M3EqM'], // oz
            'request.options' => ['exceptions' => false]
        ]);
        $license = 'C11-0000224-LIC';   //oz
        $message = '';
        $messages = '';
        $error_message = [];
        try {
            $response = $client->post('https://api-ca.metrc.com/transfers/v1/templates?licenseNumber=' . $license, $data);
            $rsp_body = $response->getBody()->getContents();
            if ($rsp_body == '')
                $message = 'Template for ' . $sale_order_full . ' created';
            else
                $message = "Other error";
            return redirect()->to(route('start'))->with('status', $message);

        } catch (GuzzleException $error) {
            $response = $error->getResponse();
            $response_info = json_decode($response->getBody()->getContents(), true);
            $message = $response_info;
            if (is_array($response_info)) {
                if (sizeof($response_info) == 1) {
                    $message_text = $response_info['Message'];
                    $message = "Metrc error: " . $message_text;
                    array_push($error_message, [$message]);
                    return redirect()->to(route('do_return', [1, $message]) . "#error_message");
                } else {
                    for ($i = 0; $i < sizeof($response_info); $i++) {
                        $message = "Metrc error: " . $response_info[$i]["message"];
                        array_push($error_message, [$message]);
                    }
                    for ($i = 0; $i < count($error_message); $i++) {
                        $messages = $messages . $error_message[$i][0] . '|';
                    }
                }
                return redirect()->to(route('do_return', [1, $messages]) . "#error_message");
            }
        }

    }


    public
    function updateOrderLines($return_tag, $return_line_number)
    {
        MetrcOrderline::updateOrCreate(
            [
                'line_number' => $return_line_number
            ],
            [
                'metrc_tag' => $return_tag
            ]
        );
    }

    public
    function importOrderLines($order_id)
    {
        //    dd($order_id);
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();
        $order_lines = $odoo
            ->where('order_id', '=', $order_id)
            ->fields(
                'id',
                'write_date',
                'name',
                'name_short',
                'price_subtotal',
                'move_ids',
                'invoice_lines',
                'untaxed_amount_to_invoice',
                'untaxed_amount_invoiced',
                'invoice_status',
                'margin',
                'qty_invoiced',
                'qty_to_invoice',
                'qty_delivered',
                'product_uom_qty',
                'price_unit',
                'product_uom',
                'create_date',
                'order_partner_id',
                'product_id',
                'order_partner_id',
                'order_id',
                'purchase_price',
                'salesman_id'
            )
            ->get('sale.order.line');
//dd($order_lines);

        \DB::table('metrc_orderlines')->delete();
        //   MetrcOrderline::delete();
//dd("xxx");

        for ($i = 0; $i < count($order_lines); $i++) {
            $metrc_package = MetrcPackage::where('product_id', $order_lines[$i]['product_id'][0])->first();
            if ($metrc_package) {
                $tag = $metrc_package->tag;
            } else {
                $tag = '';
            }
            $name_org = $order_lines[$i]['name'];
            $pos = strpos($name_org, ']');
            $code = '';
            if ($pos) {
                $name = substr($name_org, $pos + 2);
                $code = substr($name_org, 0, $pos + 2);
            } else {
                $name = $name_org;
            }

            $revenue = $order_lines[$i]['price_unit'];
            if ($revenue > 0.01) {
                $cost = $order_lines[$i]['purchase_price'];

                $gross_profit = bcsub($revenue, $cost, 3);

                if ($gross_profit != 0 and $revenue != 0 and $cost != 0) {
                    $margin = bcmul('100', bcdiv($gross_profit, $revenue, 3), 3);
                    // dd($margin);
                } else {
                    $margin = 0;
                };
            } else {
                $margin = 0;
            }
//dd($order_lines[$i]['id']);

            //   dd(substr($order_lines[$i]['order_id'][1], 2))  ;
            MetrcOrderline::updateOrCreate(
                [
                    'ext_id' => $order_lines[$i]['id']
                ],
                [
                    'line_number' => $i + 1,
                    'metrc_tag' => $tag,
                    'metrc_uom' => "Each",
                    'ext_id_shipping' => $order_lines[$i]['order_partner_id'][0],
                    'order_date' => $order_lines[$i]['create_date'],
                    'created_at' => $order_lines[$i]['create_date'],
                    'sales_person_id' => $order_lines[$i]['salesman_id'][0],
                    'product_id' => $order_lines[$i]['product_id'][0],
                    'order_id' => substr($order_lines[$i]['order_id'][1], 2),
                    //   'order_id' => $order_lines[$i]['order_id'][0],
                    'invoice_number' => $order_lines[$i]['order_id'][1],
                    //   'invoice_number' => "SO" . $order_lines[$i]['order_id'][0],
                    'customer_id' => $order_lines[$i]['order_partner_id'][0],
                    'name' => $name,
                    'code' => $code,
                    'quantity' => $order_lines[$i]['product_uom_qty'],
                    'metrc_quantity' => $order_lines[$i]['product_uom_qty'],
                    'cost' => $order_lines[$i]['purchase_price'],
                    'ext_id_unit' => $order_lines[$i]['product_uom'][1],
                    'unit_price' => $order_lines[$i]['price_unit'],
                    'margin' => $margin,
                    'amt_to_invoice' => $order_lines[$i]['untaxed_amount_to_invoice'] / 1.24,
                    'amt_invoiced' => $order_lines[$i]['untaxed_amount_invoiced'] / 1.24,
                    'price_subtotal' => $order_lines[$i]['price_subtotal'],
                    'invoice_status' => $order_lines[$i]['invoice_status'],
                    'odoo_margin' => $order_lines[$i]['margin'],
                    'qty_invoiced' => $order_lines[$i]['qty_invoiced'],
                    'qty_to_invoice' => $order_lines[$i]['qty_to_invoice'],
                    'qty_delivered' => $order_lines[$i]['qty_delivered'],
                ]);

        }
        return;
    }

    public
    function make_metrc_sales_lines($sales_lines)
    {
        //     dd($sales_lines);
        $metrc_sales_lines = [];
        $sales_line_counter = 1;
        //     dd($sales_lines);
        foreach ($sales_lines as $sales_line) {
            $total = $sales_line->metrc_quantity * $sales_line->unit_price;
            $line = [
                "PackageLabel" => $sales_line->metrc_package_created,
                "WholesalePrice" => $total,
                "UnitOfMeasure" => $sales_line->metrc_uom,
            ];
            array_push($metrc_sales_lines, $line);

        }
        /*        for ($i = 0; $i < count($sales_lines); $i++) {
                    $line = [
                        "PackageLabel" => $sales_lines[$i]['tag'],
                        "WholesalePrice" => $sales_lines[$i]['total']
                    ];
                }*/
        //   dd($metrc_sales_lines);
        return ($metrc_sales_lines);
    }

    function xxxmake_metrc_sales_lines($sales_lines)
    {
        $metrc_sales_lines = [];
        $sales_line_counter = 1;
        foreach ($sales_lines as $sales_line) {
            //      dd($sales_line);
            $metrc_package = MetrcPackage::where('product_id', $sales_line->product_id)->first();
            if ($metrc_package) {
                $tag = $metrc_package->tag;
            } else {
                $tag = $sales_line_counter++;
            }
            $line = [
                "PackageLabel" => $tag,
                "WholesalePrice" => $sales_line->price_subtotal
            ];
            array_push($metrc_sales_lines, $line);
        }
        //  dd($metrc_sales_lines);
        return ($metrc_sales_lines);
    }


    public
    function utf8_encode_deep(&$input)
    {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                self::utf8_encode_deep($value);
            }

            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));

            foreach ($vars as $var) {
                self::utf8_encode_deep($input->$var);
            }
        }
    }

    public
    function driver_log($driver, $vehicle, $order_id, $delivery_date, $sales_lines, $sales_orders)
    {
        $sale_invoice = $sales_lines->first();
        $so = $sales_orders->first();
        $sales_person_id = $sale_invoice->sales_person_id;
        $customer_id = $sale_invoice->ext_id_shipping;

        $driver_log = new DriverLog;
        $driver_log->vehicle_id = $vehicle->id;
        $driver_log->driver_id = $driver->id;
        $driver_log->saleinvoice_id = $order_id;
        $driver_log->salesperson_id = $sales_person_id;
        $driver_log->customer_id = $customer_id;
        $driver_log->delivery_date = $delivery_date;
        $driver_log->total = $so->amount_total;
        $driver_log->collected = $so->amount_total;
        $driver_log->order_date = $so->order_date;
        $driver_log->save();


    }


    public
    function messages()
    {
        return [
            'sale_orders.required' => 'Enter a valid sales order number',
        ];
    }

    public
    function printManifest($driver, $vehicle, $sale_order_number, $sales_lines)
    {
        \PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'enable_html5_parser' => true, 'orientation' => 'landscape']);
        foreach ($sales_lines as $p) {
            //    echo $p->code . "1<br>";
        }

        $productCount = $sales_lines->count();
        $test = env('app_testing');

        $pageTotal = 0;
        $printed = 0;
        $firstPageLines = 7;
        $attachedPageLines = 30;
        $pageLines = 0;
        $morePageCount = 0;

        $pageLines = 0;
        $morePageCount = 0;
        $isSamePageFirst = false;
        $isSamePageAttached = false;

        if ($productCount > $firstPageLines) {
            $pageTotal = 1;
        }
        $morePageLines = $productCount - $firstPageLines;
        if ($pageTotal or $morePageLines >= $attachedPageLines) {
            $pageTotal = ( int )($morePageLines / $attachedPageLines);
            if ($attachedPageLines % $morePageLines) {
                $pageTotal++;
            }
        }

        $totalLines = $productCount;
        $footerPageLines = 15;
        $leftover = 0;
        $onePageMore = 0;
        $firstPageTotal = $firstPageLines + $footerPageLines;
        $pageLines = $firstPageTotal;

        $attachedPageTotal = $attachedPageLines + $footerPageLines;
        $remainingLines = $totalLines - $firstPageTotal;
        $isAttachedPages = $remainingLines > 0 ? 1 : 0;
        $attachedPages = 1 + intval($remainingLines / $attachedPageTotal);
        if (!$isAttachedPages) {
            $isSamePageFirst = $totalLines <= $firstPageLines ? 'yes' : 'no';
        } else {
            $isSamePageAttached = $remainingLines <= $attachedPageLines ? 'yes' : 'no';
        }
        $data = [
            'test' => env('app_testing'),
            'all_products' => $sales_lines,
            'productCount' => $productCount,
            'invoice' => $sales_lines->first(),
            'business' => Business::first(),
            'driver' => $driver,
            'vehicle' => $vehicle,
            'pageCount' => 0,
            'pageTotal' => $pageTotal,
            'attachedPageLines' => $attachedPageLines,
            'pageAttached' => 0,
            'offset' => 0,
            'newoffset' => 0,
            'printed' => 0,
            'remainingLines' => $productCount,
            'footerLines' => 15,
            'firstPageTotal' => $firstPageTotal,
            'firstPageLines' => $firstPageLines,
            'attachedPageTotal' => $attachedPageTotal,
            'remainingLines' => $remainingLines,
            'isAttachedPages' => $isAttachedPages,
            'attachedPages' => $attachedPages,
            'totalLines' => $totalLines,
            'isSamePageFirst' => $isSamePageFirst,
            'isSamePageAttached' => $isSamePageAttached,
        ];

        // 			dd($data);
        return ($data);
    }

    public
    function getFromOdoo($id = '')
    {
        $odoo = new \Edujugon\Laradoo\Odoo();
        $odoo = $odoo->connect();
        $id = (int)$id;

        $picking = $odoo->where('sale_id', '=', $id)
            ->limit(1)
            ->fields(
                'scheduled_date'
            )
            ->get('stock.picking');
        $delivery_date = substr($picking[0]['scheduled_date'], 0, 10);
        return $delivery_date;

    }


    public
    function importSalesOrderIntoDB($order, $odoo)
    {
        //      dd($order_lines);
        $arrlen = count($order);
        //	echo $arrlen;
        for ($i = 0; $i < $arrlen; $i++) {
            //  echo $i;
            //   $product_id = $order_lines[$i]['product_id'][0];
            //    echo $product_id . "<br>";         //   dd($product_id);
            //  $product = $odoo->where('id', '=', $product_id)->fields('code')->get('product.product');

            //   dd($product);
            //  dd("Not a valid sale order!");

            $order_date = ($order[0]['date_order'] == true) ? date_format(date_create($order[0]['date_order']), "Y-m-d") : NULL;
            $arr[] = [
                'order_date' => $order_date,
                'salesperson_id' => $order[0]['user_id'][0],
                'sales_order' => $order[0]['display_name'],
                //     'customer_id' => $order[0]['order_partner_id'],
                'sales_order_id' => substr($order[0]['display_name'], 2),
            ];
        }
//dd($arr);

        if (!empty($arr)) {
            \
            DB::table('salesorders')->delete();
            \
            DB::table('salesorders')->insert($arr);
            //         Storage::delete('/public/sale.order.csv');
            return true;
        }
        return false();
    }


    public
    function importUsersIntoDB($users)
    {
        $arrlen = count($users);
//			echo $arrlen;
        for ($i = 0; $i < $arrlen; $i++) {
            if (!$users->isEmpty()) {
                $arr[] = [
                    'sales_person_id' => $users[$i]['id'],
                    'name' => $users[$i]['name'],
                    'email' => $users[$i]['email'],
                ];
            }
        }
        //		dd($arr);

        if (!empty($arr)) {
            \
            DB::table('salespersons')->delete();
            \
            DB::table('salespersons')->insert($arr);
            //         Storage::delete('/public/sale.order.csv');
            return true;
        }
        return false();
    }

    function strip_tags_content($text, $tags = '', $invert = FALSE)
    {

        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) and count($tags) > 0) {
            if ($invert == FALSE) {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif ($invert == FALSE) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }

    public

    function importUnitsIntoDB()
    {

        $path = storage_path('app/public/product.uom.csv');
        $data = \Excel::load($path)->get();
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $arr[] = [
                    'ext_id' => $value->id,
                    'name' => $value->name,
                ];

            }
            if (!empty($arr)) {
                \
                DB::table('units')->delete();
                \
                DB::table('units')->insert($arr);
                dd('Insert Units Records successfully.');
            }
        }
        dd('Request data does not have any files to import.');
    }

    public
    function importCustomersIntoDB($customer)
    {

        if (!$customer[0]['street2']) {
            $street2 = NULL;
        } else {
            $street2 = $customer[0]['street2'];
        }

        if ($customer) {
            $arr[] = [
                'ext_id' => $customer[0]['id'],
                'ext_id_contact' => $customer[0]['id'],
                'name' => preg_replace("/[^a-zA-Z0-9\s]/", " ", $customer[0]['display_name']),
                'street' => $customer[0]['street'],
                'street2' => $street2,
                'city' => $customer[0]['city'],
                'zip' => $customer[0]['zip'],
                'phone' => $customer[0]['phone'],
                'license' => substr($customer[0]['x_studio_field_mu5dT'], 0, 20),
            ];
        }
        //    dd($arr);
        if (!empty($arr)) {
            /*            \DB::table('customers')->delete();*/
            \DB::table('customers')->insert($arr);
            //        dd('Insert Customers Records successfully.');
            return true;
        }
        //    dd('Request data does not have any files to import.');
        return false;
    }

    public
    function ximportCustomersIntoDB()
    {

        $path = storage_path('app/public/res.partner.csv');
        $data = \Excel::load($path)->get();
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $arr[] = [
                    'ext_id' => $value->id,
                    'ext_id_contact' => $value->child_idsid,
                    'name' => $value->name,
                    'street' => $value->street,
                    'street2' => $value->street2,
                    'city' => $value->city,
                    'zip' => $value->zip,
                    'phone' => $value->phone,
                    'license' => $value->x_studio_field_mu5dt
                ];

            }
            if (!empty($arr)) {
                \
                DB::table('customers')->delete();
                \
                DB::table('customers')->insert($arr);
                //         dd('Insert Customers Records successfully.');
            }
        }
        dd('Request data does not have any files to import.');
    }

    public

    function importContactsIntoDB()
    {
        $path = storage_path('app/public/res.partner.csv');
        $data = \Excel::load($path)->get();
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $arr[] = [
                    'ext_id' => $value->id,
                    'name' => $value->name,
                    'phone' => $value->phone,
                    'customer_id' => $value->parent_idid,
                ];

            }
            if (!empty($arr)) {
                \
                DB::table('contacts')->delete();
                \
                DB::table('contacts')->insert($arr);
                dd('Insert Contacts Records successfully.');
            }
        }
        dd('Request data does not have any files to import.');
    }

    public

    function importProductsIntoDB()
    {

        $path = storage_path('app/public/product.template.csv');
        $data = \Excel::load($path)->get();
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $arr[] = [
                    'ext_id' => $value->id,
                    'name' => $value->name,
                    'description' => $value->name,
                ];

            }
            if (!empty($arr)) {
                \
                DB::table('products')->delete();
                \
                DB::table('products')->insert($arr);
                dd('Inserted Product Records successfully.');
            }
        }
        dd('Request data does not have any files to import.');
    }


    public

    function downloadExcelFile($type)
    {
        $products = Product::get()->toArray();
        return \ Excel::create('expertphp_demo', function ($excel) use ($products) {
            $excel->sheet('sheet name', function ($sheet) use ($products) {
                $sheet->fromArray($products);
            });
        })->download($type);
    }

    public
    function additional()
    {
        return view('print_manifest_edit');
    }
}
