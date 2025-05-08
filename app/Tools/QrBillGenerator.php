<?php

namespace App\Tools;

use Sprain\SwissQrBill\DataGroup\Element\AdditionalInformation;
use Sprain\SwissQrBill\DataGroup\Element\CreditorInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentAmountInformation;
use Sprain\SwissQrBill\DataGroup\Element\PaymentReference;
use Sprain\SwissQrBill\DataGroup\Element\StructuredAddress;
use Sprain\SwissQrBill\PaymentPart\Output\DisplayOptions;
use Sprain\SwissQrBill\PaymentPart\Output\HtmlOutput\HtmlOutput;
use Sprain\SwissQrBill\QrBill;
use Sprain\SwissQrBill\Reference\RfCreditorReferenceGenerator;

class QrBillGenerator
{
    public static function generate(array $data)
    {
        $qrBill = QrBill::create();

        $qrBill->setCreditor(
            StructuredAddress::createWithStreet(
                ...$data['creditor']
            )
        );

        $qrBill->setCreditorInformation(
            CreditorInformation::create(
                $data['iban']
            )
        );

        if (@$data['debtor']['name']) {
            $qrBill->setUltimateDebtor(
                StructuredAddress::createWithStreet(
                    ...$data['debtor']
                )
            );
        }

        $qrBill->setPaymentAmountInformation(
            PaymentAmountInformation::create(
                $data['currency'],
                $data['amount']
            )
        );

        if ($data['reference']) {
            $qrBill->setPaymentReference(
                PaymentReference::create(
                    PaymentReference::TYPE_SCOR,
                    RfCreditorReferenceGenerator::generate($data['reference'])
                )
            );
        } else {
            $qrBill->setPaymentReference(
                PaymentReference::create(
                    PaymentReference::TYPE_NON
                )
            );
        }

        if ($data['additional-information']) {
            $qrBill->setAdditionalInformation(
                AdditionalInformation::create(
                    $data['additional-information']
                )
            );
        }

        $output = new HtmlOutput($qrBill, 'fr');

        try {
            $html = $output
                ->setDisplayOptions((new DisplayOptions)->setDisplayScissors(true))
                ->getPaymentPart();
        } catch (\Exception) {
            foreach ($qrBill->getViolations() as $violation) {
                print $violation->getMessage() . "\n";
            }
            exit;
        }

        return $html;
    }
}
