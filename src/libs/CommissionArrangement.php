<?php

namespace RR\libs;

use RR\model\Sales;

class CommissionArrangement
{
    public $commission;
    public $taxes;
    public $numberInstallments;

    public function __construct(float $value, float $percentageCommission, object $taxes, ?int $saleId = null, int $numberInstallments = null)
    {
        $sale = (new Sales())->getItemById($saleId);
        $this->commission = $sale->commission_value ?? $value * ($percentageCommission / 100);
        $this->taxes = $taxes;
        $this->numberInstallments = $numberInstallments;
    }

    public function commission()
    {
        return (object)[
            'gross' => Util::maskMoney($this->commission),
            'real' => Util::maskMoney($this->commission - ($this->commission * ($this->taxes->real / 100))),
            'virtual' => Util::maskMoney($this->commission - ($this->commission * ($this->taxes->virtual / 100))),
        ];
    }

    public function taxes()
    {
        return (object)[
            'amount' => (object)[
                'real' => Util::maskMoney($this->commission * ($this->taxes->real / 100)),
                'virtual' => Util::maskMoney($this->commission * ($this->taxes->virtual / 100))
            ],
            'commission' => (object)[
                'real' => Util::maskMoney($this->commission - ($this->commission * ($this->taxes->real / 100))),
                'virtual' => Util::maskMoney($this->commission - ($this->commission * ($this->taxes->virtual / 100)))
            ],
        ];
    }

    public function seller(int $origin = 1, float $percentageSeller)
    {
        switch ($origin) {
            case '1':
                /**Bruto */
                $percentage['gross'] = $percentageSeller;
                break;
            case '2':
                /**Líquido Real */
                $percentage['gross'] = ((($this->commission - ($this->commission * ($this->taxes->real / 100))) * ($percentageSeller / 100)) * 100) / $this->commission;
                break;
            case '3':
                /**Líquido Virtual */
                $percentage['gross'] = ((($this->commission - ($this->commission * ($this->taxes->virtual / 100))) * ($percentageSeller / 100)) * 100) / $this->commission;
                break;
        }

        $percentage['real'] = ($this->commission * ($percentage['gross'] / 100) * 100) / ($this->commission - ($this->commission * ($this->taxes->real / 100)));
        $percentage['virtual'] = ($this->commission * ($percentage['gross'] / 100) * 100) / ($this->commission - ($this->commission * ($this->taxes->virtual / 100)));
        $amount = Util::maskMoney($this->commission * ($percentage['gross'] / 100));

        if (!empty($this->numberInstallments)) {
            $amount = Util::maskMoney(($this->commission * ($percentage['gross'] / 100)) / $this->numberInstallments);
        }

        return (object)[
            'percentage' => (object)[
                'calculation' => ($origin == 1 ? $percentage['gross'] : ($origin == 2 ? $percentage['real'] : $percentage['virtual'])),
                'gross' => $percentage['gross'],
                'real' => $percentage['real'],
                'virtual' => $percentage['virtual']
            ],
            'amount' => $amount,
        ];
    }

    public function positions(array $positions)
    {
        $positions = array_map(function ($position) {
            return (object)$position;
        }, $positions);

        foreach ($positions as $position) {
            $position->percentage = (object)[];

            switch ($position->origin_commission) {
                case '1':
                    /**Bruto */
                    $position->percentage->gross = floatval($position->percentage_commission);
                    break;
                case '2':
                    /**Líquido Real */
                    $position->percentage->gross = ((($this->commission - ($this->commission * ($this->taxes->real / 100))) * ($position->percentage_commission / 100)) * 100) / $this->commission;
                    break;
                case '3':
                    /**Líquido Virtual*/
                    $position->percentage->gross = ((($this->commission - ($this->commission * ($this->taxes->virtual / 100))) * ($position->percentage_commission / 100)) * 100) / $this->commission;
                    break;
            }

            $position->percentage->real = ($this->commission * ($position->percentage->gross / 100) * 100) / ($this->commission - ($this->commission * ($this->taxes->real / 100)));
            $position->percentage->virtual = ($this->commission * ($position->percentage->gross / 100) * 100) / ($this->commission - ($this->commission * ($this->taxes->virtual / 100)));
            $position->percentage->calculation = ($position->origin_commission == 1 ? $position->percentage->gross : ($position->origin_commission == 2 ? $position->percentage->real : $position->percentage->virtual));
            $position->amount = Util::maskMoney($this->commission * ($position->percentage->gross / 100));
        }

        return $positions;
    }

    public function branch(object $commissionSeller, array $positions)
    {
        $seller = $this->seller($commissionSeller->type, $commissionSeller->percentage);
        $positions = $this->positions($positions);

        $percentage['gross'] = 100 - ($seller->percentage->gross + array_reduce(
            array_column(array_column($positions, 'percentage'), 'gross'),
            function ($carry, $item) {
                return ($carry += $item);
            },
            0
        ));
        $amount['gross'] = Util::maskMoney($this->commission * ($percentage['gross'] / 100));

        $percentage['real'] = 100 - ($seller->percentage->real + array_reduce(
            array_column(array_column($positions, 'percentage'), 'real'),
            function ($carry, $item) {
                return ($carry += $item);
            },
            0
        ));
        $amount['real'] = Util::maskMoney(($this->commission - ($this->commission * ($this->taxes->real / 100))) * ($percentage['real'] / 100));

        $percentage['virtual'] = 100 - ($seller->percentage->virtual + array_reduce(
            array_column(array_column($positions, 'percentage'), 'virtual'),
            function ($carry, $item) {
                return ($carry += $item);
            },
            0
        ));
        $amount['virtual'] = Util::maskMoney(($this->commission - ($this->commission * ($this->taxes->virtual / 100))) * ($percentage['virtual'] / 100));

        return (object)[
            'percentage' => (object)[
                'gross' => $percentage['gross'],
                'real' => $percentage['real'],
                'virtual' => $percentage['virtual']
            ],
            'amount' => (object)[
                'gross' => $amount['gross'],
                'real' => $amount['real'],
                'virtual' => $amount['virtual']
            ],
        ];
    }
}
