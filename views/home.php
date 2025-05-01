<?php $this->layout('layout'); ?>

<form action="generate-bill">
    <h2>Creditor</h2>

    <p>
        <label for="creditor-name">name</label>
        <input type="text" name="creditor[name]" id="creditor-name" value="<?= @$_SESSION['creditor']['name'] ?>" required>
    </p>

    <p>
        <label for="creditor-street">street</label>
        <input type="text" name="creditor[street]" id="creditor-street" value="<?= @$_SESSION['creditor']['street'] ?>" required>
    </p>

    <p>
        <label for="creditor-buildingNumber">buildingNumber</label>
        <input type="text" name="creditor[buildingNumber]" id="creditor-buildingNumber" value="<?= @$_SESSION['creditor']['buildingNumber'] ?>">
    </p>

    <p>
        <label for="creditor-postalCode">postalCode</label>
        <input type="text" name="creditor[postalCode]" id="creditor-postalCode" value="<?= @$_SESSION['creditor']['postalCode'] ?>" required>
    </p>

    <p>
        <label for="creditor-city">city</label>
        <input type="text" name="creditor[city]" id="creditor-city" value="<?= @$_SESSION['creditor']['city'] ?>" required>
    </p>

    <p>
        <label for="creditor-country">country</label>
        <input type="text" name="creditor[country]" id="creditor-country" value="<?= @$_SESSION['creditor']['country'] ?>" required>
    </p>

    <p>
        <label for="iban">IBAN</label>
        <input type="text" name="iban" id="iban" value="<?= @$_SESSION['iban'] ?>" required>
    </p>

    <h2>Debtor</h2>

    <p>
        <label for="debtor-name">name</label>
        <input type="text" name="debtor[name]" id="debtor-name" value="<?= @$_SESSION['debtor']['name'] ?>">
    </p>

    <p>
        <label for="debtor-street">street</label>
        <input type="text" name="debtor[street]" id="debtor-street" value="<?= @$_SESSION['debtor']['street'] ?>">
    </p>

    <p>
        <label for="debtor-buildingNumber">buildingNumber</label>
        <input type="text" name="debtor[buildingNumber]" id="debtor-buildingNumber" value="<?= @$_SESSION['debtor']['buildingNumber'] ?>">
    </p>

    <p>
        <label for="debtor-postalCode">postalCode</label>
        <input type="text" name="debtor[postalCode]" id="debtor-postalCode" value="<?= @$_SESSION['debtor']['postalCode'] ?>">
    </p>

    <p>
        <label for="debtor-city">city</label>
        <input type="text" name="debtor[city]" id="debtor-city" value="<?= @$_SESSION['debtor']['city'] ?>">
    </p>

    <p>
        <label for="debtor-country">country</label>
        <input type="text" name="debtor[country]" id="debtor-country" value="<?= @$_SESSION['debtor']['country'] ?>">
    </p>

    <h2>Payment</h2>

    <p>
        <label for="currency">currency</label>
        <input type="text" name="currency" id="currency" value="<?= @$_SESSION['currency'] ?? 'CHF' ?>" required>
    </p>

    <p>
        <label for="amount">amount</label>
        <input type="number" name="amount" id="amount" value="<?= @$_SESSION['amount'] ?? 0 ?>" required>
    </p>

    <p>
        <label for="reference">reference</label>
        <input type="text" name="reference" id="reference" value="<?= @$_SESSION['reference'] ?>">
    </p>

    <p>
        <label for="additional-information">additional-information</label>
        <input type="text" name="additional-information" id="additional-information" value="<?= @$_SESSION['additional-information'] ?>">
    </p>

    <p>
        <button>Generate bill</button>
    </p>
</form>