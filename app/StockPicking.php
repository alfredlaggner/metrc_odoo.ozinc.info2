<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\StockPicking
 *
 * @property int $id
 * @property int|null $ext_id
 * @property string|null $salesorder_number
 * @property string|null $date
 * @property string $name
 * @property string|null $state
 * @property int|null $product_id
 * @property string|null $product_name
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon $created_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereExtId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereProductName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereSalesorderNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\StockPicking whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class StockPicking extends Model
{
    protected $fillable = ['ext_id','name','date','state','date_done','salesorder_number','product_id','product_name'];
}
