<?php
/**
 * Připomínka platby – textová verze (override pro child theme).
 * Použije se jen tehdy, když je e-mail nastavený na formát "Prostý text".
 * QR kód ani odkaz na fakturu se do textové verze nevkládají.
 *
 * Umístění na webu:
 * wp-content/themes/<child-theme>/woocommerce/emails/plain/payment-reminder.php
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$bank_transfer_methods = array( 'bacs' );

$is_bank_transfer = in_array( $order->get_payment_method(), $bank_transfer_methods, true );
$show_pay_link    = ! $is_bank_transfer && $order->needs_payment();

if ( $order->get_billing_first_name() ) {
    printf( esc_html__( 'Hi %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) );
    echo "\n\n";
}

echo 'vaše objednávka č. ' . esc_html( $order->get_order_number() ) . ' zatím čeká na zaplacení.' . "\n\n";

if ( $show_pay_link ) {
    echo 'Objednávku můžete zaplatit tady:' . "\n";
    echo esc_url( $order->get_checkout_payment_url() ) . "\n\n";
}

echo 'Pokud jste už zaplatili, tento e-mail prosím ignorujte. Platba se k nám mohla jen ještě nedostat.' . "\n\n";
echo 'Děkujeme za váš nákup.' . "\n";
