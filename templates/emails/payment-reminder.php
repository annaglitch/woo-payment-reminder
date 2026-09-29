<?php
/**
 * Připomínka platby – HTML verze.
 *
 * Šablonu jde přepsat v šabloně webu: <theme>/woocommerce/emails/payment-reminder.php
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * ID platebních metod pro bankovní převod. Pokud převod na webu běží
 * přes jinou metodu (ID najdeš v adrese nastavení platby, &section=...),
 * přidej ji sem, např. array( 'bacs', 'jina_metoda' ).
 */
$bank_transfer_methods = array( 'bacs' );

$is_bank_transfer = in_array( $order->get_payment_method(), $bank_transfer_methods, true );

// Převod: objednávka čeká ve stavu "Čeká na vyřízení" (on-hold), proto is_paid() a ne needs_payment().
$show_qr = $is_bank_transfer && ! $order->is_paid();

// Karta / Apple Pay: platební stránka funguje jen pro objednávky, které čekají na online platbu.
$show_pay_button = ! $is_bank_transfer && $order->needs_payment();
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php
/*
 * Oslovení musí zůstat přesně takhle (text 'Hi %s,' z domény 'woocommerce'):
 * modul WPify Woo "Pátý pád v e-mailech" hledá v hotovém e-mailu právě tento
 * text a nahradí ho oslovením v 5. pádě ("Dobrý den, Petře,").
 */
if ( $order->get_billing_first_name() ) : ?>
<p><?php printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?></p>
<?php endif; ?>

<p>
    vaše objednávka <strong>č. <?php echo esc_html( $order->get_order_number() ); ?></strong>
    na <?php echo wp_kses_post( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ); ?>
    zatím čeká na zaplacení.
</p>

<?php if ( $show_qr ) : ?>

    <p>Platbu můžete poslat převodem na náš účet. Nejjednodušší je naskenovat QR kód níže v aplikaci své banky, údaje se vyplní samy.</p>

    <?php do_action( 'wpify_woo_render_qr_code', $order ); ?>

    <?php do_action( 'wpify_woo_fakturoid_render_link', $order ); ?>

<?php elseif ( $show_pay_button ) : ?>

    <p>Objednávku můžete dokončit jedním kliknutím, kartou nebo přes Apple Pay.</p>

    <p>
        <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"
           style="display: inline-block; background-color: #0071a1; color: #ffffff; padding: 10px 15px; text-decoration: none; font-weight: bold;">
            Zaplatit objednávku
        </a>
    </p>

<?php endif; ?>

<p>Pokud jste už zaplatili, tento e-mail prosím ignorujte. Platba se k nám mohla jen ještě nedostat.</p>

<p>Děkujeme za váš nákup.</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
