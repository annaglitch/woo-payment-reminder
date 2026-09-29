# Woo Payment Reminder: upravená verze (návod)

Tohle je upravená kopie pluginu [nobodyguy/woo-payment-reminder](https://github.com/nobodyguy/woo-payment-reminder)
(verze **1.1.0**). Obsahuje i všechny opravy z originálu.

Co je jinak než v originálu:

- **Bankovní převod** → v e-mailu je QR platba z WPify Woo a odkaz na zálohovou fakturu
  z Fakturoidu. Tlačítko „Zaplatit“ tam není.
- **Karta / Apple Pay** → tlačítko „Zaplatit objednávku“.
- **Zaplacená objednávka** → ani QR, ani tlačítko.
- **Oslovení** v 5. pádě přes WPify Woo („Dobrý den, Petře,“).
- Texty e-mailu jsou rovnou česky (`templates/emails/`).
- V detailu objednávky přibyla akce **Poslat připomínku platby** (ruční odeslání).

> **Pozor při aktualizaci:** plugin instaluj vždy z tohoto repozitáře
> (annaglitch), ne z originálu. Kdybys nahrála zip z původního repozitáře,
> úpravy zmizí. Poznáš to podle verze: upravená je 1.1.0 a výš.

## 1. Instalace na web

1. Na GitHubu: **Code → Download ZIP** (z větve, kde jsou úpravy).
2. Zip rozbal a složku přejmenuj na `woo-payment-reminder` (GitHub k názvu
   přidává název větve), pak ji zase zabal do zipu.
3. WordPress → **Pluginy → Instalace pluginů → Nahrát plugin** → vyber zip →
   WordPress se zeptá, jestli nahradit stávající verzi → **Nahradit**.

Nastavení (počet dní, předmět…) zůstane zachované.

## 2. Nastavení v administraci

1. **WPify Woo → QR platba**: v seznamu platebních metod musí být *Bankovní převod*
   s vyplněným účtem. E-mail „Připomenutí platby“ v seznamu e-mailů u QR platby
   **nezaškrtávej**, šablona si QR vkládá sama.
   Kvůli Gmailu zapni **Uložit jako obrázek**.
2. **WPify Woo → Pátý pád v e-mailech**: modul zapnutý. Pole „Nahradit jméno“
   nech prázdné, výsledek pak bude „Dobrý den, Petře,“.
3. **WPify Woo Fakturoid**: výchozí umístění odkazu na doklad v e-mailech nastav
   na „Nezobrazovat“ (nebo aspoň ověř, že se v připomínce neobjeví dvakrát).
4. **WooCommerce → Nastavení → E-maily → Připomenutí platby**: typ e-mailu HTML.

## 3. Ověření ID platební metody pro převod

Plugin počítá s tím, že převod má ID `bacs` (standardní „Bankovní převod“
WooCommerce). Ověříš to v **WooCommerce → Nastavení → Platby**: klikni na převod
a podívej se do adresy stránky, na konec za `section=`. Pokud tam je něco jiného
než `bacs`, napiš mi to a upravím řádek v obou šablonách:

```php
$bank_transfer_methods = array( 'bacs' );
```

Všechny ostatní metody (karta, Apple Pay…) dostanou tlačítko „Zaplatit objednávku“.

## 4. Test

Testovací objednávky zakládej na **svůj e-mail**, připomínka jde vždy na
fakturační e-mail zákazníka. Odeslání: detail objednávky → vpravo
**Akce objednávky → Poslat připomínku platby** → Aktualizovat.

| Objednávka | Očekávaný výsledek |
| --- | --- |
| Převodem, jméno „Petr“, proforma už vystavená | „Dobrý den, Petře,“ + QR + odkaz na zálohovou fakturu, **žádné tlačítko** |
| Kartou, nezaplacená | tlačítko „Zaplatit objednávku“, **žádné QR** |
| Zaplacená (Zpracovává se / Dokončeno) | ani QR, ani tlačítko |

Nakonec zkontroluj e-mail v Gmailu (obrázek QR) a na mobilu, a že odkaz na
fakturu není v e-mailu dvakrát.
