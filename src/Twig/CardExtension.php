<?php

namespace App\Twig;

use App\Entity\Printer;
use App\Service\ContainerParametersHelper;
use Symfony\Component\Yaml\Yaml;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class CardExtension extends AbstractExtension
{

    private $notificationConfig = [];

    public function __construct(ContainerParametersHelper $helper)
    {
        $this->notificationConfig = Yaml::parseFile( $helper->getApplicationRootDir()  . "/config/notification.yaml");
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('getCardColor', [$this, 'getCardColor']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getCardColor', [$this, 'getCardColor']),
        ];
    }

    public function getCardColor(Printer $printer)
    {
        $res = "";
        if($this->notificationConfig['web']['enabled']) {
            $warningLevel = $this->notificationConfig['web']['tonerlevel']['warning'];
            $dangerLevel = $this->notificationConfig['web']['tonerlevel']['danger'];
            if($printer->getTonerBlack() <= $dangerLevel || ($printer->getisColorPrinter() && ($printer->getTonerMagenta()<=$dangerLevel || $printer->getTonerCyan()<=$dangerLevel || $printer->getTonerYellow()<=$dangerLevel))) {
                    $res = "text-white bg-danger";
            } elseif($printer->getTonerBlack() <= $warningLevel || ($printer->getisColorPrinter() && ($printer->getTonerMagenta()<=$warningLevel || $printer->getTonerCyan()<=$warningLevel || $printer->getTonerYellow()<=$warningLevel))) {
                    $res = "text-white bg-warning";
            }
        }

        return $res;
    }
}
