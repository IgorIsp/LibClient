<?php


namespace LibClient;


class ServiceFactory
{
    /**
     * @var RbcService $rbc
     */
    private $rbc;
    /**
     * @var SbrService $sbr
     */
    private $sbr;

    /**
     * @var array
     */
    public $needCurrencies;

    /**
     * @var \DateTime $date
     */
    public $date;

    /**
     * @return Service
     */
    public function createRbcService(): Service
    {
        $dateStr = $this->date->format('Y-m-d');
        $links = [];
        foreach ($this->needCurrencies as $currency) {
            $links[$currency] =
                "https://cash.rbc.ru/cash/json/converter_currency_rate/?currency_from={$currency}&currency_to=RUR&source=cbrf&sum=1&date={$dateStr}";
        }
        $this->rbc = new RbcService($links, $this->needCurrencies);
        return $this->rbc;
    }

    /**
     * @return Service
     */
    public function createSbrService(): Service
    {
        $this->sbr = new SbrService(
            'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $this->date->format('d/m/Y'),
            $this->needCurrencies
        );
        return $this->sbr;
    }

    /**
     * @return array
     */
    public function getCalcAverage(): array
    {
        $sumAveragerCurrencies = array_fill_keys($this->needCurrencies, '');
        foreach ($this->needCurrencies as $currency) {
            $sumAveragerCurrencies[$currency] =
                ($this->sbr->getSumCurrencies()[$currency] + $this->rbc->getSumCurrencies()[$currency]) / 2;
        }
        return $sumAveragerCurrencies;
    }
}
