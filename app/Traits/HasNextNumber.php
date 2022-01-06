<?php

namespace App\Traits;

trait HasNextNumber
{
    public static function bootHasNextNumber()
    {
        static::creating(function ($model) {
            $column = $model->getConfigNextNumber()['column'];
            if (!$model->{$column}) {
                $model->{$column} = $model->getDraftNextNumber();
            }
        });
    }

    public function getDraftNextNumber(): string
    {
        return 'DRAFT-'. \Illuminate\Support\Str::uuid()->toString();
    }

    public function setNextNumber(): string
    {
        $config = $this->getConfigNextNumber();

        $column = $config['column'];
        $prefix = $config['prefix'];
        $period = $config['period'];
        $digits = $config['digits'];
        $separator = $config['separator'];

        $strtime = date($period, strtotime(date('Y-m-d')));

        $prenumber = $prefix ? "$prefix$separator"  : "";
        $prenumber.= $period ? ($strtime . $separator) : "";

        $next = self
            ::selectRaw('MAX(REPLACE(number, "'.$prenumber.'", "") * 1) AS N')
            ->where('number','LIKE', $prenumber.'%')->get()->max('N');

        $next = $next ? (int) str_replace($prenumber, '', $next) : 0;
        $next++;

        $number = $prenumber . str_pad($next, $digits, '0', STR_PAD_LEFT);

        $this->{$column} = $number;
        $this->save();

        return $number;
    }
}
