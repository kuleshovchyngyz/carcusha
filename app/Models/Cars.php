<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $vendor
 * @property string $model
 * @property int $year
 * @property string $modification
 */
class Cars extends Model
{
    use HasFactory;
    /**
     * @var array
     */
    protected $fillable = ['vendor', 'model', 'year', 'modification'];
    /**
     * @var mixed
     */
    private $id;
    /**
     * @var mixed
     */
    private $botusername;
}
