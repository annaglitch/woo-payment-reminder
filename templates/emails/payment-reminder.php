<?php
/**
 * Payment reminder – HTML version.
 *
 * Texts are translated in languages/woo-payment-reminder-cs_CZ.po (or via Loco Translate).
 * Can be overridden in the theme: <theme>/woocommerce/emails/payment-reminder.php
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
    <?php printf(
        __( 'We noticed that your order <strong>#%s</strong> is still awaiting payment.', 'woo-payment-reminder' ),
        esc_html( $order->get_order_number() )
    ); ?>
</p>

<?php if ( $show_qr || $show_pay_button ) : ?>
<p><?php _e( 'To complete your purchase, please proceed with the payment at your earliest convenience.', 'woo-payment-reminder' ); ?></p>
<?php endif; ?>

<?php if ( $show_qr ) : ?>

    <p><?php _e( 'You can pay by bank transfer to our account. The easiest way is to scan the QR code below in your banking app.', 'woo-payment-reminder' ); ?></p>

    <?php do_action( 'wpify_woo_render_qr_code', $order ); ?>

    <?php do_action( 'wpify_woo_fakturoid_render_link', $order ); ?>

<?php elseif ( $show_pay_button ) : ?>

    <p>
        <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"
           style="display: inline-block; background-color: #0071a1; color: #ffffff; padding: 10px 15px; text-decoration: none; font-weight: bold;">
            <?php _e( 'Pay Now', 'woo-payment-reminder' ); ?>
        </a>
    </p>

<?php endif; ?>

<p><?php _e( 'If you have already completed the payment, please ignore this message.', 'woo-payment-reminder' ); ?></p>

<p><?php _e( 'Thank you for shopping with us.', 'woo-payment-reminder' ); ?></p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
