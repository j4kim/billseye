<?php $this->layout('layout'); ?>

<form action="generate-bill">
    <h2>Creditor</h2>

    <p>
        <label for="creditor-name">Nom</label>
        <input type="text" name="creditor[name]" id="creditor-name">
    </p>

    <p>
        <label for="creditor-street">street</label>
        <input type="text" name="creditor[street]" id="creditor-street">
    </p>

    <p>
        <label for="creditor-buildingNumber">buildingNumber</label>
        <input type="text" name="creditor[buildingNumber]" id="creditor-buildingNumber">
    </p>

    <p>
        <label for="creditor-postalCode">postalCode</label>
        <input type="text" name="creditor[postalCode]" id="creditor-postalCode">
    </p>

    <p>
        <label for="creditor-city">city</label>
        <input type="text" name="creditor[city]" id="creditor-city">
    </p>

    <p>
        <label for="creditor-country">country</label>
        <input type="text" name="creditor[country]" id="creditor-country">
    </p>

    <h2>Creditor</h2>

    <p>
        <label for="debtor-name">Nom</label>
        <input type="text" name="debtor[name]" id="debtor-name">
    </p>

    <p>
        <label for="debtor-street">street</label>
        <input type="text" name="debtor[street]" id="debtor-street">
    </p>

    <p>
        <label for="debtor-buildingNumber">buildingNumber</label>
        <input type="text" name="debtor[buildingNumber]" id="debtor-buildingNumber">
    </p>

    <p>
        <label for="debtor-postalCode">postalCode</label>
        <input type="text" name="debtor[postalCode]" id="debtor-postalCode">
    </p>

    <p>
        <label for="debtor-city">city</label>
        <input type="text" name="debtor[city]" id="debtor-city">
    </p>

    <p>
        <label for="debtor-country">country</label>
        <input type="text" name="debtor[country]" id="debtor-country">
    </p>

    <p>
        <button>Generate bill</button>
    </p>
</form>