# Upravený e-mail „Připomínka platby“: návod k nasazení

Soubory v této složce **nejsou součástí pluginu**. Nahrávají se do child theme,
takže je aktualizace pluginu nepřepíše.

## 1. Nahrání souborů

Přes FTP nebo správce souborů na hostingu nahraj:

| Soubor z repozitáře | Kam na web |
| --- | --- |
| `child-theme/woocommerce/emails/payment-reminder.php` | `wp-content/themes/<tvůj-child-theme>/woocommerce/emails/payment-reminder.php` |
| `child-theme/woocommerce/emails/plain/payment-reminder.php` | `wp-content/themes/<tvůj-child-theme>/woocommerce/emails/plain/payment-reminder.php` |

Složky `woocommerce/emails/plain` v child theme nejspíš ještě nejsou, vytvoř je.

Kontrola: **WooCommerce → Nastavení → E-maily → Připomenutí platby**. Dole u šablony
musí být napsáno, že ji přepisuje tvoje šablona (theme).

Pokud child theme nemáš (aktivní je přímo „Kadence“), nejdřív ho vytvoř, třeba
oficiálním Kadence Child Theme ze stránek Kadence. Bez child theme by se úprava
při aktualizaci šablony smazala.

## 2. Nastavení v administraci

1. **WPify Woo → QR platba**: v seznamu platebních metod musí být *Bankovní převod*
   s vyplněným účtem. Zaškrtávat e-mail „Připomenutí platby“ v seznamu e-mailů
   **není potřeba** (a nedělej to, QR by se jinak mohl zobrazit dvakrát).
   Kvůli Gmailu zapni **Uložit jako obrázek**.
2. **WPify Woo → Pátý pád v e-mailech**: modul zapnutý. Pole „Nahradit jméno“
   nech prázdné, výsledek pak bude „Dobrý den, Petře,“.
3. **WPify Woo Fakturoid**: výchozí umístění odkazu na doklad v e-mailech nastav
   na „Nezobrazovat“ (nebo aspoň ověř, že se v připomínce neobjeví dvakrát).
4. **WooCommerce → Nastavení → E-maily → Připomenutí platby**: typ e-mailu HTML.

## 3. Ověření ID platebních metod

Šablona počítá s tím, že převod má ID `bacs` (standardní „Bankovní převod“
WooCommerce). Ověříš to v **WooCommerce → Nastavení → Platby**: klikni na převod
a podívej se do adresy stránky, na konec za `section=`. Pokud tam je něco jiného
než `bacs`, uprav v obou šablonách řádek:

```php
$bank_transfer_methods = array( 'bacs' );
```

Všechny ostatní metody (karta, Apple Pay…) dostanou tlačítko „Zaplatit objednávku“.

## 4. Test

| Objednávka | Očekávaný výsledek |
| --- | --- |
| Převodem, jméno „Petr“, proforma už vystavená | „Dobrý den, Petře,“ + QR + odkaz na zálohovou fakturu, **žádné tlačítko** |
| Kartou, nezaplacená | tlačítko „Zaplatit objednávku“, **žádné QR** |
| Zaplacená (Zpracovává se / Dokončeno) | ani QR, ani tlačítko |

Testovací objednávky zakládej na **svůj e-mail**, připomínka jde vždy na
fakturační e-mail zákazníka.

Plugin sám neumí připomínku poslat ručně. Na test proto přidej na konec
`functions.php` v child theme tento kousek kódu:

```php
// Ruční odeslání připomínky platby z detailu objednávky (Akce objednávky).
add_filter( 'woocommerce_order_actions', function ( $actions ) {
    $actions['wpr_send_payment_reminder'] = 'Poslat připomínku platby';
    return $actions;
} );
add_action( 'woocommerce_order_action_wpr_send_payment_reminder', function ( $order ) {
    WC()->mailer(); // načte e-maily, aby připomínka byla připravená
    do_action( 'send_payment_reminder_email', $order->get_id() );
} );
```

Pak v detailu objednávky vpravo vybereš **Akce objednávky → Poslat připomínku
platby** a klikneš na Aktualizovat. Po testu ho můžeš zase smazat, nebo nechat.

Nakonec zkontroluj e-mail v Gmailu (obrázek QR) a na mobilu, a že odkaz na
fakturu není v e-mailu dvakrát.
