<?php

namespace Billseye;

use Sprain\SwissQrBill as QrBill;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;

class QrBillGenerator
{
    public static function generate()
    {
        $qrBill = QrBill\QrBill::create();

        $qrBill->setCreditor(
            QrBill\DataGroup\Element\StructuredAddress::createWithStreet(
                'Joaquim Perez',
                'Rue Neuve',
                '3',
                '2300',
                'La Chaux-de-Fonds',
                'CH'
            )
        );

        $qrBill->setCreditorInformation(
            QrBill\DataGroup\Element\CreditorInformation::create(
                'CH0709000000125733166'
            )
        );

        $qrBill->setUltimateDebtor(
            QrBill\DataGroup\Element\StructuredAddress::createWithStreet(
                'Pia-Maria Rutschmann-Schnyder',
                'Grosse Marktgasse',
                '28',
                '9400',
                'Rorschach',
                'CH'
            )
        );

        $qrBill->setPaymentAmountInformation(
            QrBill\DataGroup\Element\PaymentAmountInformation::create(
                'CHF',
                250
            )
        );

        $qrBill->setPaymentReference(
            QrBill\DataGroup\Element\PaymentReference::create(
                QrBill\DataGroup\Element\PaymentReference::TYPE_SCOR,
                QrBill\Reference\RfCreditorReferenceGenerator::generate('42')
            )
        );

        $qrBill->setAdditionalInformation(
            QrBill\DataGroup\Element\AdditionalInformation::create(
                'Software development'
            )
        );

        $output = new HtmlOutput($qrBill, 'fr');

        $html = $output->getPaymentPart();

        return $html;
    }
}
