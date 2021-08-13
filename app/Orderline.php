<?php

	namespace App;

	use Illuminate\Database\Eloquent\Model;
	use Laravel\Scout\Searchable;

	class Orderline extends Model
	{
	//	use Searchable;
		protected $table = 'manifest_orderlines';
		protected $fillable = ['invoice_number', 'ext_id', 'order_date', 'driver_log_id', 'order_id', 'ext_id_shipping', 'name', 'quantity', 'ext_id_unit', 'unit_price',
			'sales_person_id', 'code', 'cost', 'margin', 'quantity_corrected', 'updated_at', 'created_at'];

		public function customer()
		{
			return $this->belongsTo('App\Customer', 'ext_id_shipping', 'ext_id_contact');
		}

		public function sales_order()
		{
			return $this->belongsTo('App\SalesOrder', 'ext_id_shipping', 'ext_id_contact');
		}

		public function driverlog()
		{
			return $this->belongsTo('App\DriverLog', 'sale_order_id', 'ext_id');
		}

		public function unit()
		{
			return $this->belongsTo('App\Unit', 'ext_id_unit', 'ext_id');
		}

		public function products()
		{
			return $this->hasMany('App\Product', 'ext_id_unit', 'ext_id');
		}
		public function line_note()
		{
			return $this->hasOne('App\LineNote');
		}

        public function product()
        {
            return $this->hasOne('App\Product', 'ext_id', 'product_id');
        }


        public function contact()
		{
			return $this->hasManyThrough(
				'App\Contact',
				'App\Customer',
				'null', // Foreign key on users table...
				'customer_id', // Foreign key on posts table...
				'ext_id', // Local key on countries table...
				'ext_id' // Local key on users table...
			);
		}
	}
