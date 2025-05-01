<?php

namespace Billseye;

use Sprain\SwissQrBill as QrBill;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;

class QrBillGenerator
{
    public static function generate(array $data)
    {
        $qrBill = QrBill\QrBill::create();

        $qrBill->setCreditor(
            QrBill\DataGroup\Element\StructuredAddress::createWithStreet(
                ...$data['creditor']
            )
        );

        $qrBill->setCreditorInformation(
            QrBill\DataGroup\Element\CreditorInformation::create(
                'CH0709000000125733166'
            )
        );

        if ($data['debtor']['name']) {
            $qrBill->setUltimateDebtor(
                QrBill\DataGroup\Element\StructuredAddress::createWithStreet(
                    ...$data['debtor']
                )
            );
        }

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

        try {
            $html = $output->getPaymentPart();
        } catch (\Exception) {
            foreach ($qrBill->getViolations() as $violation) {
                print $violation->getMessage() . "\n";
            }
            exit;
        }

        return $html;
    }
}
