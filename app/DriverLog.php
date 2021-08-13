<?php

	namespace App;

	use Illuminate\Database\Eloquent\Model;
	use App\SaleInvoice;
	use Illuminate\Notifications\Notifiable;

	class DriverLog extends Model
	{
		use Notifiable;
		protected $guard_name = 'web'; // or whatever guard you want to use

		public function driver()
		{
			return $this->belongsTo('App\Driver');
		}

		public function user()
		{
			return $this->belongsTo('App\User');
		}

		public function customer()
		{
			return $this->belongsTo('App\Customer', 'customer_id', 'ext_id');
		}

		public function vehicle()
		{
			return $this->belongsTo('App\Vehicle');
		}

		public function driver_notes()
		{
			return $this->hasMany('App\DriverNote');
		}

		public function salesperson()
		{
			return $this->belongsTo('App\Salesperson', 'salesperson_id', 'sales_person_id');
		}

		public function order_lines()
		{
			return $this->hasMany('App\SaleInvoice', 'order_id', 'saleinvoice_id');
		}

		public function salesorder()
		{
			return $this->hasOne('App\SalesOrder', 'sales_order_id', 'saleinvoice_id');
		}
	}
