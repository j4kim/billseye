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
                $data['iban']
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
                $data['currency'],
                $data['amount']
            )
        );

        if ($data['reference']) {
            $qrBill->setPaymentReference(
                QrBill\DataGroup\Element\PaymentReference::create(
                    QrBill\DataGroup\Element\PaymentReference::TYPE_SCOR,
                    QrBill\Reference\RfCreditorReferenceGenerator::generate($data['reference'])
                )
            );
        } else {
            $qrBill->setPaymentReference(
                QrBill\DataGroup\Element\PaymentReference::create(
                    QrBill\DataGroup\Element\PaymentReference::TYPE_NON
                )
            );
        }

        if ($data['additional-information']) {
            $qrBill->setAdditionalInformation(
                QrBill\DataGroup\Element\AdditionalInformation::create(
                    $data['additional-information']
                )
            );
        }

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
